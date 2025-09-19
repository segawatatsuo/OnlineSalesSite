<?php

namespace App\Admin\Actions\Order;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\AmazonPayService;

class CaptureCharge extends RowAction
{
    public $name = '売上確定';

    public function handle(Model $model, Request $request)
    {
        try {
            $amazonPay = app(AmazonPayService::class);

            // ✅ charge_id をチェック
            if (!$model->amazon_chargeId) {
                return $this->response()->error('Charge ID が存在しません。');
            }

            // ✅ Amazon Pay にリクエスト
            $response = $amazonPay->captureCharge($model->amazon_chargeId, $model->total_price);

            // ✅ レスポンスの JSON 本体をパース
            $body = $response['response'] ?? null;
            $data = is_string($body) ? json_decode($body, true) : $body;

            if (!$data || !isset($data['statusDetails']['state'])) {
                return $this->response()->error('Amazon Pay レスポンス異常: ' . json_encode($response));
            }

            // ✅ 成功判定は statusDetails.state
            if ($data['statusDetails']['state'] === 'Captured') {
                $model->status = \App\Models\Order::STATUS_CAPTURED;
                $model->save();

                return $this->response()->success('売上を確定しました！')->refresh();
            }

            return $this->response()->error('売上確定に失敗しました。Amazon 状態: ' . $data['statusDetails']['state']);
        } catch (\Exception $e) {
            return $this->response()->error('エラー: ' . $e->getMessage());
        }
    }
}
