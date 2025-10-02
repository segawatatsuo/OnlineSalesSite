<?php

namespace App\Services;

use Square\SquareClient;
use Square\Environments;
use Square\Payments\Requests\GetPaymentsRequest;

use Square\Payments\Requests\CompletePaymentRequest;
use Square\Exceptions\ApiException;
use Square\Apis\PaymentsApi;

use Square\Payments\Requests\ListPaymentsRequest;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Payments\Requests\UpdatePaymentRequest;
use Square\Payments\Requests\CancelPaymentRequest;

use Square\Environment;




class SquareService
{
    protected $client;

    public function __construct()
    {
        $this->client = new SquareClient(
            token: env('SQUARE_ACCESS_TOKEN'),
            options: [
                'baseUrl' => (env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox')
                    ? Environments::Sandbox->value
                    : Environments::Production->value,
            ]
        );
    }
    /*
    public function capturePayment(string $paymentId)
    {
        return $this->client->payments->completePayment(
            new CompletePaymentRequest([
                'paymentId' => $paymentId,
            ])
        );
    }
    */
    public function getPayment(string $paymentId)
    {
        try {
            $request = new GetPaymentsRequest(['paymentId' => $paymentId]);
            $response = $this->client->payments->get($request);

            // エラーチェック: getErrors()がnullでない、または空でない場合はエラー
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            // 成功時はgetPayment()でPaymentオブジェクトを取得
            return $response->getPayment();
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    // 別の安全なgetPaymentメソッド
    public function getPaymentSafe(string $paymentId)
    {
        try {
            $request = new GetPaymentsRequest(['paymentId' => $paymentId]);
            $response = $this->client->payments->get($request);

            // 直接レスポンスからデータを取得
            if ($response && method_exists($response, 'getPayment')) {
                return $response->getPayment();
            }

            // または getResult() メソッドがあるか確認
            if ($response && method_exists($response, 'getResult')) {
                $result = $response->getResult();
                if ($result && method_exists($result, 'getPayment')) {
                    return $result->getPayment();
                }
                return $result;
            }

            // 直接レスポンスを返す
            return $response;
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    public function listPayments(?string $beginTime = null, ?string $endTime = null, ?string $sortOrder = null, ?string $cursor = null, ?string $locationId = null, ?int $total = null, ?string $last4 = null, ?string $cardBrand = null, ?int $limit = null)
    {
        try {
            // ListPaymentsRequestオブジェクトを配列で作成
            $requestData = [];
            if ($beginTime) $requestData['beginTime'] = $beginTime;
            if ($endTime) $requestData['endTime'] = $endTime;
            if ($sortOrder) $requestData['sortOrder'] = $sortOrder;
            if ($cursor) $requestData['cursor'] = $cursor;
            if ($locationId) $requestData['locationId'] = $locationId;
            if ($total) $requestData['total'] = $total;
            if ($last4) $requestData['last4'] = $last4;
            if ($cardBrand) $requestData['cardBrand'] = $cardBrand;
            if ($limit) $requestData['limit'] = $limit;

            $request = new ListPaymentsRequest($requestData);
            $response = $this->client->payments->list($request);

            // エラーチェック
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            return $response;
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    public function createPayment(string $sourceId, string $idempotencyKey, array $amountMoney, ?string $locationId = null, ?array $additionalData = [])
    {
        try {
            // CreatePaymentRequestオブジェクトを配列で作成
            $requestData = [
                'sourceId' => $sourceId,
                'idempotencyKey' => $idempotencyKey,
                'amountMoney' => $amountMoney
            ];

            if ($locationId) $requestData['locationId'] = $locationId;

            // 追加データをマージ
            $requestData = array_merge($requestData, $additionalData);

            $request = new CreatePaymentRequest($requestData);
            $response = $this->client->payments->create($request);

            // エラーチェック
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            return $response->getPayment();
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    public function updatePayment(string $paymentId, array $updateData)
    {
        try {
            $requestData = array_merge(['paymentId' => $paymentId], $updateData);
            $request = new UpdatePaymentRequest($requestData);
            $response = $this->client->payments->update($request);

            // エラーチェック
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            return $response->getPayment();
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    public function cancelPayment(string $paymentId)
    {
        try {
            $request = new CancelPaymentRequest(['paymentId' => $paymentId]);
            $response = $this->client->payments->cancel($request);

            // エラーチェック
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            return $response->getPayment();
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    public function completePayment(string $paymentId)
    {
        try {
            $request = new CompletePaymentRequest(['paymentId' => $paymentId]);
            $response = $this->client->payments->complete($request);

            // エラーチェック
            $errors = $response->getErrors();
            if ($errors && !empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return method_exists($error, 'getDetail') ? $error->getDetail() : (string)$error;
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }

            return $response->getPayment();
        } catch (ApiException $e) {
            throw new \Exception('Square API Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('General Error: ' . $e->getMessage());
        }
    }

    // デバッグ用メソッド：SquareClientの構造を確認
    public function debugClientStructure()
    {
        $reflection = new \ReflectionClass($this->client);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        $publicProps = [];
        foreach ($properties as $prop) {
            $publicProps[] = $prop->getName();
        }

        return [
            'class' => get_class($this->client),
            'public_properties' => $publicProps,
            'payments_property_exists' => property_exists($this->client, 'payments'),
            'payments_property_type' => $this->client->payments ? get_class($this->client->payments) : 'null',
            'payments_methods' => $this->getPaymentsMethods()
        ];
    }

    private function getPaymentsMethods()
    {
        if (!property_exists($this->client, 'payments') || !$this->client->payments) {
            return ['payments property not available'];
        }

        $reflection = new \ReflectionClass($this->client->payments);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $paymentMethods = [];
        foreach ($methods as $method) {
            $paymentMethods[] = $method->getName();
        }

        return $paymentMethods;
    }

    // より安全なgetPaymentメソッド
    /*
    public function getPaymentSafe(string $paymentId)
    {
        try {
            // paymentsプロパティの存在確認
            if (!property_exists($this->client, 'payments')) {
                throw new \Exception('payments property does not exist on SquareClient');
            }

            if (!$this->client->payments) {
                throw new \Exception('payments property is null');
            }

            // getPaymentメソッドの存在確認
            if (!method_exists($this->client->payments, 'getPayment')) {
                throw new \Exception('getPayment method does not exist on payments client');
            }

            $response = $this->client->payments->getPayment($paymentId);

            if ($response->isSuccess()) {
                return $response->getResult()->getPayment();
            } else {
                $errors = $response->getErrors();
                $errorMessages = array_map(function ($error) {
                    return $error->getDetail();
                }, $errors);
                throw new \Exception('Square API Error: ' . implode(', ', $errorMessages));
            }
        } catch (\Exception $e) {
            throw new \Exception('Payment retrieval error: ' . $e->getMessage());
        }
    }
        */
}
