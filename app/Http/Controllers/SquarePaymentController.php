<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Types\Money;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Types\Currency;
use Square\Exceptions\SquareApiException;
use Square\Exceptions\ApiException;
use Square\SquareClientBuilder;
use Square\Authentication\BearerAuthCredentialsBuilder;
use Square\Environments;
use Square\Models\Error;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\ShippingFeeService;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;
use Illuminate\Support\Facades\Session;


class SquarePaymentController extends Controller
{
    protected $client;
    protected $locationId;
    protected $cartService;
    protected $shippingFeeService;

    public function __construct(CartService $cartService, ShippingFeeService $shippingFeeService)
    {
        $this->client = new SquareClient(
            config('square.access_token'),
            config('square.environment')
        );
        $this->locationId = config('square.location_id');
        $this->cartService = $cartService;
        $this->shippingFeeService = $shippingFeeService;
    }

    public function checkout()
    {
        $prefecture = session('address')['delivery_add01'] ?? null;
        $cart = $this->cartService->getCartItems(null, $prefecture);
        $totalAmount = $cart['total'];
        return view('square.checkout', compact('totalAmount'));
    }

    /**
     * 与信のみ実行（注文登録）
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'token'  => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        $token  = $request->input('token');
        $amount = (int) $request->input('amount');

        try {
            // ✅ Square Client 初期化 (v42対応)
            $environment = env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox' ? 'sandbox' : 'production';

            $client = new SquareClient(
                token: env('SQUARE_ACCESS_TOKEN'),
                options: [
                    'baseUrl' => (env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox')
                        ? Environments::Sandbox->value
                        : Environments::Production->value
                ]
            );

            // ✅ 金額オブジェクト (v42)
            $money = new Money();
            $money->setAmount($amount);
            $money->setCurrency('JPY');

            // ✅ 支払いリクエスト (v42)
            $paymentReq = new CreatePaymentRequest([
                'sourceId'       => $token,
                'idempotencyKey' => (string) Str::uuid(),
                'amountMoney'    => $money,
                'autocomplete'   => false, // 与信のみ
            ]);

            // ✅ API 呼び出し (v42)
            $response = $client->payments->create($paymentReq);


            // === カート & 住所情報 ===
            $cart = session('cart', []);
            $address = session('address', []);
            if (empty($cart)) {
                throw new \Exception('カート情報が空です。');
            }

            // ✅ 成功判定
            if ($response->getErrors() === null) {
                $payment = $response->getPayment();
                $paymentId = $payment->getId();

                DB::beginTransaction();

                // === 顧客作成 ===
                $customer = Customer::create([
                    'sei'         => $address['order_sei'] ?? 'ゲスト',
                    'mei'         => $address['order_mei'] ?? '',
                    'email'       => $address['order_email'] ?? 'guest_' . uniqid() . '@example.com',
                    'phone'       => $address['order_phone'] ?? null,
                    'zip'         => $address['order_zip'] ?? null,
                    'input_add01' => $address['order_add01'] ?? null,
                    'input_add02' => $address['order_add02'] ?? null,
                    'input_add03' => $address['order_add03'] ?? null,
                ]);

                // === 配送先作成 ===
                if (($address['same_as_orderer'] ?? '1') === '1') {
                    $delivery = Delivery::create($customer->toArray());
                } else {
                    $delivery = Delivery::create([
                        'sei'         => $address['delivery_sei'] ?? '',
                        'mei'         => $address['delivery_mei'] ?? '',
                        'email'       => $address['delivery_email'] ?? '',
                        'phone'       => $address['delivery_phone'] ?? '',
                        'zip'         => $address['delivery_zip'] ?? '',
                        'input_add01' => $address['delivery_add01'] ?? '',
                        'input_add02' => $address['delivery_add02'] ?? '',
                        'input_add03' => $address['delivery_add03'] ?? '',
                    ]);
                }

                // === 注文作成 ===
                $order = Order::create([
                    'order_number'      => Order::generateOrderNumber(),
                    'customer_id'       => $customer->id,
                    'delivery_id'       => $delivery->id,
                    'total_price'       => $amount,
                    'delivery_time'     => $address['delivery_time'] ?? null,
                    'delivery_date'     => $address['delivery_date'] ?? null,
                    'your_request'      => $address['your_request'] ?? null,
                    'square_payment_id' => $paymentId,
                    'status'            => Order::STATUS_AUTH, // 与信済
                ]);

                // === 注文明細作成 ===
                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item['product_id'],
                        'product_code' => $item['product_code'],
                        'name'         => $item['name'],
                        'quantity'     => $item['quantity'],
                        'price'        => $item['price'],
                        'subtotal'     => $item['price'] * $item['quantity'],
                    ]);
                }

                DB::commit();

                // === メール送信（省略可） ===
                try {
                    Mail::to($customer->email)->send(new OrderConfirmed($order, $customer, $delivery));
                } catch (\Exception $e) {
                    \Log::error('顧客向け注文確認メール送信失敗', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                try {
                    Mail::to('segawa82@nifty.com')->send(new OrderNotification($order, $customer, $delivery));
                } catch (\Exception $e) {
                    \Log::error('ショップ向け注文通知メール送信失敗', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // === セッション削除 ===
                Session::forget(['cart', 'address']);
                // === 与信完了メッセージ ===
                return response()->json([
                    'message'    => '手続きが完了しました。',
                    'payment_id' => $paymentId,
                ]);
            } else {
                // ✅ エラー時
                return response()->json([
                    'errors' => $response->getErrors(),
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Square Payment Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * 管理画面から売上確定
     */
    public function capturePayment($paymentId)
    {
        try {
            // ✅ 支払いを完了（売上確定）
            $response = $this->client->payments->complete($paymentId);

            // ここに来た時点で成功
            $payment = $response->getPayment();

            // DB更新
            $order = Order::where('square_payment_id', $paymentId)->first();
            if ($order) {
                $order->update(['status' => 'captured']);
            }

            return response()->json([
                'message' => '売上確定しました',
                'payment' => $payment,
            ]);
        } catch (ApiException $e) {
            // APIエラー
            return response()->json([
                'error' => $e->getMessage(),
                'details' => $e->getErrors(), // Square APIの詳細エラー
            ], 400);
        } catch (\Exception $e) {
            // その他エラー
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
