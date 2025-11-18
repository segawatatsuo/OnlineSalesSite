<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryTime;
use App\Models\ShippingFee;
use App\Services\CartService;
use App\Services\ShippingFeeService;
use App\Mail\OrderThanksMail;
use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;
use App\Http\Requests\OrderCustomerRequest;
use App\Models\DeliveryAddress;

class OrderController extends Controller
{
    protected $cartService;
    protected $shippingFeeService;

    public function __construct(CartService $cartService, ShippingFeeService $shippingFeeService)
    {
        $this->cartService = $cartService;
        $this->shippingFeeService = $shippingFeeService;
    }


    public function create(Request $request, CartService $cartService)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('top')->with('warning', 'カートが空です。');
        }

        $deliveryTimes = DeliveryTime::pluck('time');
        $user = auth()->user();

        if ($user && $user->user_type === 'corporate') {

            // 最新データを取得
            //$corporateCustomer = $user->corporateCustomer->refresh();
            $corporateCustomer = Auth::user()->corporateCustomer()->with('orders')->first();
            $corporate_customer_id = $corporateCustomer->id;

            // 🚩 デフォルトお届け先を取得（なければ最初の住所）
            $defaultAddress = $corporateCustomer->defaultDeliveryAddress ?? $corporateCustomer->deliveryAddresses()->first();
            // 配送料計算に使う都道府県
            $prefecture = $defaultAddress ? $defaultAddress->add01 : $corporateCustomer->order_add01;
            // カート情報を取得
            $cartData = $cartService->getCartItems($user, $prefecture);
            //お届け先に指定された住所
            $deliveryAddress = $corporateCustomer->deliveryAddresses->where('is_default', 1)->first();

            /*addressに全部まとめる*/
            $address['order_company_name'] = $corporateCustomer['order_company_name'] ? $corporateCustomer['order_company_name'] : '';
            $address['order_department'] = $corporateCustomer['order_department'] ? $corporateCustomer['order_department'] : '';
            $address['order_sei'] = $corporateCustomer['order_sei'] ? $corporateCustomer['order_sei'] : '';
            $address['order_mei'] = $corporateCustomer['order_mei'] ? $corporateCustomer['order_mei'] : '';
            $address['order_email'] = $corporateCustomer['order_email'] ? $corporateCustomer['order_email'] : '';
            $address['order_phone'] = $corporateCustomer['order_phone'] ? $corporateCustomer['order_phone'] : '';
            $address['order_zip'] = $corporateCustomer['order_zip'] ? $corporateCustomer['order_zip'] : '';
            $address['order_add01'] = $corporateCustomer['order_add01'];
            $address['order_add02'] = $corporateCustomer['order_add02'];
            $address['order_add03'] = $corporateCustomer['order_add03'];

            $address['delivery_company_name'] = $deliveryAddress['company_name'] ? $deliveryAddress['company_name'] : '';
            $address['delivery_department'] = $deliveryAddress['department'] ? $deliveryAddress['department'] : '';
            $address['delivery_sei'] = $deliveryAddress['sei'] ? $deliveryAddress['sei'] : '';
            $address['delivery_mei'] = $deliveryAddress['mei'] ? $deliveryAddress['mei'] : '';
            $address['delivery_email'] = $deliveryAddress['email'] ? $deliveryAddress['email'] : '';
            $address['delivery_phone'] = $deliveryAddress['phone'] ? $deliveryAddress['phone'] : '';
            $address['delivery_zip'] = $deliveryAddress['zip'] ? $deliveryAddress['zip'] : '';
            $address['delivery_add01'] = $deliveryAddress['add01'];
            $address['delivery_add02'] = $deliveryAddress['add02'];
            $address['delivery_add03'] = $deliveryAddress['add03'];

            session([
                'corporate_customer_id' => $corporate_customer_id,
                'address' => $address,
                'shipping_fee' => $cartData['shipping_fee'],
                'subtotal' => $cartData['subtotal']
            ]);
            //dd(Session::all());
            return view('order.corporate_confirm', [
                'user' => $user,
                'corporateCustomer' => $corporateCustomer,
                'deliveryAddresses' => $corporateCustomer->deliveryAddresses, // 全住所
                'selectedAddress' => $defaultAddress, // デフォルト住所
                'cart' => $cartData['items'],
                'subtotal' => $cartData['subtotal'],
                'shipping_fee' => $cartData['shipping_fee'],
                'total' => $cartData['total'],
                'deliveryTimes' => $deliveryTimes,
            ]);
        }

        // 個人顧客用（変更なし）
        $prefecture = null;
        $cartData = $cartService->getCartItems($user, $prefecture);
        session(['shipping_fee' => $cartData['shipping_fee']]);

        return view('order.create', [
            'items' => $cartData['items'],
            'subtotal' => $cartData['subtotal'],
            'shipping_fee' => $cartData['shipping_fee'],
            'total' => $cartData['total'],
            'deliveryTimes' => $deliveryTimes,
        ]);
    }


    public function confirm(OrderCustomerRequest $request) //FormRequestを依存注入する
    {
        $validatedData = $request->validated();
        $getCartItems = $this->cartService->getCartItems(null, $validatedData["delivery_add01"]);
        session(['address' => $validatedData]);
        return view('order.confirm', compact('getCartItems', 'validatedData'));
    }


    public function complete()
    {
        return view('order.complete');
    }

    public function modify($type)
    {
        $user = auth()->user();
        return view('order.modify_address', compact('type', 'user'));
    }
}
