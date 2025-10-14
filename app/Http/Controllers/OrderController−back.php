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

        $deliveryTimes = DeliveryTime::pluck('time'); // 配送時間帯のtimeカラムの値のみを取得

        $user = auth()->user();

        if ($user && $user->user_type === 'corporate') {


            // データベースをリロードして最新のデータを取得
            $user->corporateCustomer->refresh();


            $prefecture = $user->corporateCustomer->delivery_add01;
            $corporat_customer = $user->corporateCustomer;
            $corporate_customer_id = $user->corporateCustomer->id;

            session(['address' => $corporat_customer]);
            session(['corporate_customer_id' => $corporate_customer_id]);


            $cart = $this->cartService->getCartItems($user, $prefecture);
            session(['shipping_fee' => $cart['shipping_fee']]);

            $getCartItems = $this->cartService->getCartItems(null, $prefecture);
            return view('order.corporate_confirm', [
                'user' => $user,
                'cart' => $cart['items'],
                'subtotal' => $cart['subtotal'],
                'shipping_fee' => $cart['shipping_fee'],
                'total' => $cart['total'],
                'deliveryTimes' => $deliveryTimes,
                'getCartItems' => $getCartItems
            ]);
        }

        $prefecture = null;
        $cart = $this->cartService->getCartItems($user, $prefecture);
        session(['shipping_fee' => $cart['shipping_fee']]);


        return view('order.create', [
            'items' => $cart['items'],
            'subtotal' => $cart['subtotal'],
            'shipping_fee' => $cart['shipping_fee'],
            'total' => $cart['total'],
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
