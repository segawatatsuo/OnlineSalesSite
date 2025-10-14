<?php

namespace App\Http\Controllers;

use App\Models\CorporateCustomer;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;

class DeliveryAddressController extends Controller
{
    /**
     * お届け先追加一覧
     */
    public function index($corporateCustomerId)
    {
        $customer = CorporateCustomer::with('deliveryAddresses')->findOrFail($corporateCustomerId);
        return view('delivery_addresses.index', compact('customer'));
    }

    /**
     * 登録処理
     */
    public function store(Request $request, $corporateCustomerId)
    {

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'department'   => 'nullable|string|max:255',
            'sei'          => 'nullable|string|max:255',
            'mei'          => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'zip'          => 'nullable|string|max:10',
            'add01'        => 'nullable|string|max:255',
            'add02'        => 'nullable|string|max:255',
            'add03'        => 'nullable|string|max:255',
            'is_default'   => 'nullable|boolean',
        ]);


        $customer = CorporateCustomer::findOrFail($corporateCustomerId);

        // デフォルト指定された場合は他を解除
        if (!empty($validated['is_default'])) {
            $customer->deliveryAddresses()->update(['is_default' => false]);
        }

        $customer->deliveryAddresses()->create($validated);

        return redirect()->route('delivery-addresses.index', $customer->id)
            ->with('success', 'お届け先を追加しました。');
    }

    /**
     * デフォルト変更
     */
    public function setDefault($corporateCustomerId, $addressId)
    {
        $customer = CorporateCustomer::findOrFail($corporateCustomerId);

        $customer->deliveryAddresses()->update(['is_default' => false]);
        $customer->deliveryAddresses()
                 ->where('id', $addressId)
                 ->update(['is_default' => true]);

        return redirect()->back()->with('success', 'デフォルトのお届け先を変更しました。');
    }

    /**
     * 削除
     */
    public function destroy($corporateCustomerId, $addressId)
    {
        $address = DeliveryAddress::where('corporate_customer_id', $corporateCustomerId)
            ->findOrFail($addressId);

        $address->delete();

        return redirect()->back()->with('success', 'お届け先を削除しました。');
    }
}
