<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:order,delivery',
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'sei' => 'nullable|string|max:255',
            'mei' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'order_zip' => 'nullable|string|max:20',
            'order_add01' => 'required|string|max:255',
            'order_add02' => 'nullable|string|max:255',
            'order_add03' => 'nullable|string|max:255',

            // prepareForValidationでマージされるキー
            'zip' => 'nullable|string|max:20',
            'add01' => 'required|string|max:255',
            'add02' => 'nullable|string|max:255',
            'add03' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',

            'is_main' => 'nullable|boolean',
        ];
    }
    /*
    ｜prepareForValidation() は、
    ｜FormRequest が使われて バリデーションが実行される直前 に
    ｜Laravelの内部処理（ValidatesWhenResolvedTrait）によって自動的に呼ばれます。
    ｜キー名を変更する処理を行います。これでcreateしたいテーブルのカラム名と同じになります。
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->tel,
            'zip'   => $this->order_zip,
            'add01' => $this->order_add01,
            'add02' => $this->order_add02,
            'add03' => $this->order_add03,
        ]);
    }
}
