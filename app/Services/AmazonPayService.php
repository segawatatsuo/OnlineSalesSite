<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use Amazon\Pay\API\Constants\Environment;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;

class AmazonPayService
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->config = [
            'public_key_id' => config('amazonpay.public_key_id'),
            'private_key' => config('amazonpay.private_key_path'),
            'region' => config('amazonpay.region'),
            'sandbox' => config('amazonpay.sandbox'),
        ];

        $this->client = new Client($this->config);
    }

    /**
     * 売上確定（Capture）
     */
    public function captureCharge(string $chargeId, int $amount): array
    {
        $payload = [
            'captureAmount' => [
                'amount'       => $amount,
                'currencyCode' => 'JPY',
            ],
        ];

        $headers = [
            'x-amz-pay-idempotency-key' => 'capture-' . bin2hex(random_bytes(16)), // ✅ ドットなし
        ];

        $response = $this->client->captureCharge($chargeId, $payload, $headers);

        if (is_string($response)) {
            $response = json_decode($response, true);
        } elseif (isset($response['response']) && is_string($response['response'])) {
            $response['response'] = json_decode($response['response'], true);
        }

        return $response;
    }


    /*AmazonPayService で与信レスポンスから chargeId を取得*/
    public function authorizeCharge(string $chargePermissionId, int $amount): array
    {
        $response = $this->client->authorizeCharge(
            $chargePermissionId,
            [
                'authorizationReferenceId' => uniqid('auth_'),
                'chargeAmount' => [
                    'amount'       => $amount,
                    'currencyCode' => 'JPY',
                ],
                'captureNow' => false, // ここで即売上にしない
            ],
            []
        );

        // レスポンスの JSON を decode
        $data = json_decode($response['response']['body'], true);

        // chargeId を返す
        return [
            'chargeId' => $data['chargeId'] ?? null,
            'status'   => $data['statusDetails']['state'] ?? null,
            'raw'      => $data,
        ];
    }


    public function getCharge(string $chargeId): array
    {
        return $this->client->getCharge($chargeId);
    }
    /**
     * 決済セッションを作成
     */
    public function createPayload($amount, $merchantReferenceId = null)
    {
        $merchantReferenceId = $merchantReferenceId ?: 'Order_' . time();

        // セッションに金額を保存（セキュリティのため）
        session(['payment_amount' => $amount]);

        $payload = [
            'webCheckoutDetails' => [
                'checkoutResultReturnUrl' => route('amazon-pay.complete'), //AmazonがcheckoutSessionIdを持った状態でamazon-pay/completeにリダイレクトしてくるのでコントローラのcomplete()を実行できます
                'checkoutCancelUrl' => route('amazon-pay.cancel'),
                'checkoutMode' => 'ProcessOrder',
            ],
            'storeId' => config('amazonpay.store_id'),
            'chargePermissionType' => 'OneTime',
            'merchantMetadata' => [
                'merchantReferenceId' => $merchantReferenceId,
                'merchantStoreName' => config('amazonpay.store_name'),
                'noteToBuyer' => '料金のお支払いです',
            ],
            'paymentDetails' => [
                'paymentIntent' => 'Authorize', // 与信のみ
                'chargeAmount' => [
                    'amount' => (string)$amount,
                    'currencyCode' => 'JPY',
                ],
            ],
            'scopes' => ['name', 'email'],
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $signature = $this->client->generateButtonSignature($payloadJson);

        return [
            'payloadJson' => $payloadJson,
            'signature' => $signature,
            'publicKeyId' => config('amazonpay.public_key_id'),
            'merchantId' => config('amazonpay.merchant_id'),
            'sandbox' => config('amazonpay.sandbox'),
        ];
    }


    /**
     * completeCheckoutSession()用の金額設定を生成
     */
    public function chargeAmount($amount): array
    {
        return [
            'chargeAmount' => [
                'amount' => (string) $amount,
                'currencyCode' => 'JPY',
            ],
        ];
    }

    /*仮注文*/
    public function pendingPayment(string $amazonCheckoutSessionId): array
    {

        \Log::info('AmazonPay pendingPayment() 開始', ['amazonCheckoutSessionId' => $amazonCheckoutSessionId]);

        try {
            $idempotencyKey = uniqid('amazonpay_', true);

            // createPayload() で保存しておいた金額を取得
            $amount = session('payment_amount');
            $shipping_fee = session('shipping_fee') ;
            // サービスクラスのメソッドで 金額と通貨 を生成
            $amount_jpy = $this->chargeAmount($amount);

            $response = $this->client->completeCheckoutSession(
                $amazonCheckoutSessionId,
                json_encode($amount_jpy), // JSON化して渡す
                ['x-amz-pay-idempotency-key' => $idempotencyKey]
            );

            \Log::info('AmazonPay pendingPayment() 結果', ['raw' => $response]);


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
                'company_name' => $address['order_company_name'] ?? '',
                'department' => $address['order_department'] ?? '',
                'sei'        => $address['order_sei'] ?? '',
                'mei'        => $address['order_mei'] ?? '',
                'email'      => $address['order_email'] ?? $address['email'],
                'phone'      => $address['order_phone'] ?? '',
                'zip'        => $address['order_zip'] ?? null,
                'input_add01' => $address['order_add01'] ?? null,
                'input_add02' => $address['order_add02'] ?? null,
                'input_add03' => $address['order_add03'] ?? null,
            ]);

            // === 配送先作成 ===
            /*
            if (($address['same_as_orderer'] ?? '1') === '1') {
                $delivery = Delivery::create($customer->toArray());
            } else {
            */
            $delivery = Delivery::create([
                'company_name' => $address['delivery_company_name'] ?? '',
                'department' => $address['delivery_department'] ?? '',
                'sei'        => $address['delivery_sei'] ?? '',
                'mei'        => $address['delivery_mei'] ?? '',
                'email'      => $address['delivery_email'] ?? '',
                'phone'      => $address['delivery_phone'] ?? '',
                'zip'        => $address['delivery_zip'] ?? '',
                'input_add01' => $address['delivery_add01'] ?? '',
                'input_add02' => $address['delivery_add02'] ?? '',
                'input_add03' => $address['delivery_add03'] ?? '',
            ]);
            //}

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
                'shipping_fee'   => $shipping_fee ?? 0,

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


    /**
     * 決済をキャンセル
     */
    public function cancelPayment($amazonCheckoutSessionId)
    {
        return $this->client->cancelCheckoutSession($amazonCheckoutSessionId);
    }


    public function cancelCharge(string $chargeId): array
    {
        $response = $this->client->cancelCharge(
            $chargeId,
            ['cancellationReason' => 'Order canceled by merchant'],
            [] // options
        );

        return json_decode($response['response']['body'], true);
    }
}
