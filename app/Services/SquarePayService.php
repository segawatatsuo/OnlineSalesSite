<?php

namespace App\Services;

use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Models\Money;
use Square\Models\CreatePaymentRequest;

use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;


class SquarePayService
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


    /*仮注文*/
    public function pendingPayment(string $amazonCheckoutSessionId): array
    {

        \Log::info('AmazonPay pendingPayment() 開始', ['amazonCheckoutSessionId' => $amazonCheckoutSessionId]);

        try {
            $idempotencyKey = uniqid('amazonpay_', true);

            // createPayload() で保存しておいた金額を取得
            $amount = session('payment_amount');
            // サービスクラスのメソッドで 金額と通貨 を生成
            $amount_jpy = $this->chargeAmount($amount);

            $response = $this->client->completeCheckoutSession(
                $amazonCheckoutSessionId,
                json_encode($amount_jpy), // JSON化して渡す
                ['x-amz-pay-idempotency-key' => $idempotencyKey]
            );

            \Log::info('AmazonPay pendingPayment() 結果', ['raw' => $response]);
            //dd(json_decode($response['response'], true));

            $checkoutSession = json_decode($response['response'], true);
            // 与信ID（後でキャプチャに必要）
            $chargePermissionId = $checkoutSession['chargePermissionId'];

            // 与信取引のID
            $chargeId = $checkoutSession['chargeId'];

            // === セッションからカート & 住所を取得 ===
            $cart = session('cart', []);
            $address = session('address', []);
            if (empty($cart)) {
                throw new \Exception('カート情報が空です。');
            }

            $corporate_customer_id = session('corporate_customer_id', null);

            DB::beginTransaction();

            // === 顧客作成 ===
            $customer = Customer::create([
                'sei'        => $address['order_sei'] ?? 'ゲスト',
                'mei'        => $address['order_mei'] ?? '',
                'email'      => $address['order_email'] ?? ($response['buyer']['email'] ?? 'guest_' . uniqid() . '@example.com'),
                'phone'      => $address['order_phone'] ?? ($response['buyer']['phone'] ?? null),
                'zip'        => $address['order_zip'] ?? null,
                'input_add01' => $address['order_add01'] ?? null,
                'input_add02' => $address['order_add02'] ?? null,
                'input_add03' => $address['order_add03'] ?? null,
            ]);

            // === 配送先作成 ===
            if (($address['same_as_orderer'] ?? '1') === '1') {
                $delivery = Delivery::create($customer->toArray());
            } else {
                $delivery = Delivery::create([
                    'sei'        => $address['delivery_sei'] ?? '',
                    'mei'        => $address['delivery_mei'] ?? '',
                    'email'      => $address['delivery_email'] ?? '',
                    'phone'      => $address['delivery_phone'] ?? '',
                    'zip'        => $address['delivery_zip'] ?? '',
                    'input_add01' => $address['delivery_add01'] ?? '',
                    'input_add02' => $address['delivery_add02'] ?? '',
                    'input_add03' => $address['delivery_add03'] ?? '',
                ]);
            }

            // === 注文作成 ===
            $order = Order::create([
                'order_number'   => Order::generateOrderNumber(),
                'customer_id'    => $customer->id,
                'delivery_id'    => $delivery->id,
                'total_price'    => $amount,
                'delivery_time'  => $address['delivery_time'] ?? null,
                'delivery_date'  => $address['delivery_date'] ?? null,
                'your_request'   => $address['your_request'] ?? null,
                'amazon_checkout_session_id' => $amazonCheckoutSessionId,
                'amazon_charge_id' => $response['chargeId'] ?? null,

                // ✅ ここを追加
                'amazon_chargePermissionId' => $chargePermissionId,
                'amazon_chargeId' => $chargeId,
                'status'         => Order::STATUS_AUTH, // 与信済
                'corporate_customer_id'   => $corporate_customer_id,
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

            return [
                'order'    => $order,
                'customer' => $customer,
                'delivery' => $delivery,
                'amazon_chargePermissionId' => $chargePermissionId,
                'amazon_chargeId' => $chargeId,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('AmazonPay pendingPayment エラー: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
