<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use App\Services\CartService;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Session;
use App\Services\ShippingFeeService;

class CartController extends Controller
{
    protected $cartService;
    protected $shippingFeeService;

    public function __construct(CartService $cartService, ShippingFeeService $shippingFeeService)
    {
        $this->cartService = $cartService;
        $this->shippingFeeService = $shippingFeeService;
    }

    public function index()
    {
        $user = auth()->user();
        $result = $this->cartService->getCartItems($user);

        $cart       = $result['items'];
        $subtotal   = $result['subtotal'];
        $shipping   = $result['shipping_fee'];
        $total      = $result['total'];

        $category = session()->get('category');
        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'total', 'category'));
    }

    //public function add(Request $request)
    public function store(Request $request)
    {
        $product = ProductJa::findOrFail($request->product_id);
        $quantity = max((int) $request->input('quantity', 1), 1);
        $user = auth()->user();
        $this->cartService->addProduct($product, $quantity, $user);
        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $this->cartService->updateQuantity($request->product_id, $request->quantity);
        return redirect()->route('cart.index')->with('message', 'カートを更新しました。');
    }

    //public function remove(Request $request)
    public function destroy(Request $request)
    {
        $this->cartService->removeProduct($request->product_id);
        return redirect()->route('cart.index')->with('message', '商品を削除しました');
    }

}
