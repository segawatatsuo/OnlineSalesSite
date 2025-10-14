<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorporateCustomerController extends Controller
{
    public function update(Request $request, $corporateCustomerId)
    {

        $user = Auth::user();
        $corporateCustomer = $user->corporateCustomer;
        $delivery_address = $user->corporateCustomer->deliveryAddresses()->firstWhere('is_default', 1);
        $type = $request->input('type'); //<input type="hidden" name="type" value="deliveryかorder">
        // URLのIDと本人のIDが一致するかチェック（セキュリティ対策）
        if (!$corporateCustomer || $corporateCustomer->id != $corporateCustomerId) {
            abort(403, '不正なアクセスです。');
        }
        if ($type == 'delivery') {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'department'   => 'nullable|string|max:255',
                'sei'          => 'required|string|max:255',
                'mei'          => 'required|string|max:255',
                'zip'          => 'required|string|max:20',
                'add01'        => 'required|string|max:255',
                'add02'        => 'required|string|max:255',
                'add03'        => 'required|string|max:255',
                'phone'        => 'required|string|max:50',
            ]);
            $delivery_address->update($validated);
            return redirect()->back()->with('success', 'お届け先情報を更新しました。');
        }
        if ($type == 'order') {
            $validated = $request->validate([
                'order_company_name' => 'required|string|max:255',
                'order_department'   => 'nullable|string|max:255',
                'order_sei'          => 'required|string|max:255',
                'order_mei'          => 'required|string|max:255',
                'order_zip'          => 'required|string|max:20',
                'order_add01'        => 'required|string|max:255',
                'order_add02'        => 'required|string|max:255',
                'order_add03'        => 'required|string|max:255',
                'order_phone'        => 'required|string|max:50',
            ]);
            $corporateCustomer->update($validated);
            return redirect()->back()->with('success', 'ご注文者情報を更新しました。');
        }
        /*
        $validated = $request->validate([
            'order_company_name' => 'required|string|max:255',
            'order_department'   => 'nullable|string|max:255',
            'order_sei'          => 'required|string|max:255',
            'order_mei'          => 'required|string|max:255',
            'order_zip'          => 'required|string|max:20',
            'order_add01'        => 'required|string|max:255',
            'order_add02'        => 'required|string|max:255',
            'order_add03'        => 'required|string|max:255',
            'order_phone'        => 'required|string|max:50',
        ]);

        $corporateCustomer->update($validated);

        return redirect()->back()->with('success', 'ご注文者情報を更新しました。');
        */
    }

}
