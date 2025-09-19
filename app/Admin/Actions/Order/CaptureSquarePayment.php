<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Exceptions\ApiException;
use Square\Environments;
use Square\Payments\Requests\CompletePaymentRequest;

class CaptureSquarePayment extends RowAction
{
    public $name = '売上確定';

    public function handle(Model $model, Request $request)
    {
        try {
            $square = new SquareClient(
                token: env('SQUARE_ACCESS_TOKEN'),
                options: [
                    'baseUrl' => (env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox')
                        ? Environments::Sandbox->value
                        : Environments::Production->value
                ]
            );

            //$paymentsApi = $square->getPaymentsApi();
            $paymentsApi = $square->payments;

            // 与信済みの payment を売上確定
            $paymentId = $model->square_payment_id; // ← データベースに保存しておいた Payment ID
            $response = $square->payments->complete(
                new CompletePaymentRequest([
                    'paymentId' => $paymentId,
                ]),
            );

            $payment = $response->getPayment();
            $status  = $payment->getStatus();

            if ($status === 'COMPLETED') {
                $model->status = \App\Models\Order::STATUS_CAPTURED;
                $model->save();
                return $this->response()->success('Squareの売上を確定しました！')->refresh();
            } else {
                return $this->response()->error('Square 売上確定に失敗しました: ' . $status);
            }
        } catch (ApiException $e) {
            return $this->response()->error('Square API エラー: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->response()->error('エラー: ' . $e->getMessage());
        }
    }
}
