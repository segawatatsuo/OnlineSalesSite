<?php

namespace App\Http\Controllers;

use App\Models\CorporateCustomer;
use App\Models\CorporateCustomerAddress;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAddressRequest;

class CorporateCustomerAddressController extends Controller
{
    /**
     * 住所一覧表示
     */
    public function edit($corporateCustomerId, $type)
    {
        \Log::info('CorporateCustomerAddressController@edit called', [
            'id' => $corporateCustomerId,
            'type' => $type
        ]);

        $customer = CorporateCustomer::findOrFail($corporateCustomerId);
        $addresses = $customer->addresses()->where('type', $type)->get();

        return view('corporate_customers.addresses.index', compact('customer', 'type', 'addresses'));
    }

    /**
     * 新規登録画面表示
     */
    public function create($corporateCustomerId, $type)
    {
        $customer = CorporateCustomer::findOrFail($corporateCustomerId);

        return view('corporate_customers.addresses.create', compact('customer', 'type'));
    }

    /**
     * 新規登録処理
     */
    public function store(StoreAddressRequest $request, $corporateCustomerId)
    {
        \Log::info('Store method called', [
            'customer_id' => $corporateCustomerId,
            'request_data' => $request->all()
        ]);

        $customer = CorporateCustomer::findOrFail($corporateCustomerId);

        $validated = $request->validated();

        // 既存の住所数を確認
        $existingCount = $customer->addresses()
            ->where('type', $validated['type'])
            ->count();


        // 初回登録の場合は自動的にメインにする
        $isMain = $existingCount === 0 ? true : ($validated['is_main'] ?? false);

        if ($isMain) {
            // 同typeの既存メイン解除
            $customer->addresses()
                ->where('type', $validated['type'])
                ->update(['is_main' => false]);
        }

        // 新規登録
        $address = $customer->addresses()->create(array_merge($validated, [
            'is_main' => $isMain,
        ]));


        // corporate_customersテーブル更新(メインの場合のみ)
        if ($isMain) {
            $this->syncMainAddress($customer, $address);
        }

        return redirect()
            ->route('corporate_customers.addresses.edit', [$customer->id, $validated['type']])
            ->with('success', '住所を登録しました');
    }

    /**
     * メイン住所の切り替え
     */
    public function selectMain($corporateCustomerId, $addressId)
    {
        $customer = CorporateCustomer::findOrFail($corporateCustomerId);
        $address = $customer->addresses()->findOrFail($addressId);

        // 同typeの既存メインを解除
        $customer->addresses()
            ->where('type', $address->type)
            ->update(['is_main' => false]);

        // 選択した住所をメインに
        $address->update(['is_main' => true]);

        // corporate_customersテーブルを同期
        $this->syncMainAddress($customer, $address);

        return redirect()
            ->route('corporate_customers.addresses.edit', [$customer->id, $address->type])
            ->with('success', 'メイン住所を更新しました');
    }

    /**
     * メイン住所をcorporate_customersテーブルに同期
     */
    private function syncMainAddress($customer, $address)
    {
        if ($address->type === 'order') {
            $customer->update([
                'order_company_name' => $address->company_name,
                'order_department' => $address->department,
                'order_sei' => $address->sei,
                'order_mei' => $address->mei,
                'order_phone' => $address->phone,
                'order_zip' => $address->zip,
                'order_add01' => $address->add01,
                'order_add02' => $address->add02,
                'order_add03' => $address->add03,
                'order_tel' => $address->tel,
                'order_fax' => $address->fax,
            ]);
        } else {
            $customer->update([
                'delivery_company_name' => $address->company_name,
                'delivery_department' => $address->department,
                'delivery_sei' => $address->sei,
                'delivery_mei' => $address->mei,
                'delivery_phone' => $address->phone,
                'delivery_zip' => $address->zip,
                'delivery_add01' => $address->add01,
                'delivery_add02' => $address->add02,
                'delivery_add03' => $address->add03,
                'delivery_tel' => $address->tel,
                'delivery_fax' => $address->fax,
            ]);
        }
    }
}
