<?php

namespace App\Http\Controllers;

use App\Models\ProductJa;
use App\Models\TopPage;
use Illuminate\Support\Facades\Auth;

class ProductJaController extends Controller
{
    public function category($category)
    {
        $user = Auth::user();

        session()->put('category', $category);

        $topPageItem = TopPage::where('category', 1)->first();

        $baseQuery = ProductJa::with(['category', 'mainImage'])
            ->whereHas('category', function ($query) use ($category) {
                $query->where('brand', $category);
            })
            ->where('not_display', 0);


        if ($user && $user->user_type === 'corporate') {
            $baseQuery = $baseQuery->where('wholesale', 1);

        } else {
            $baseQuery = $baseQuery->where(function ($query) {
                $query->whereNull('wholesale')
                      ->orWhere('wholesale', 0);
            });
        }

        // ★大分類ごとにまとめる
        $productsByMajor = (clone $baseQuery)
            ->get()
            ->groupBy('major_classification');

        return view('products.category', [
            'category' => $category,
            'productsByMajor' => $productsByMajor,
            'topPageItem' => $topPageItem,
        ]);
    }

    public function show($category, $id)
    {
        $product = ProductJa::with(['category', 'mainImage'])
            ->where('id', $id)
            ->whereHas('category', function ($query) use ($category) {
                $query->where('brand', $category);
            })
            ->firstOrFail();

        $subImages = $product->subImages()->where('is_main', 0)->get();

        $user = auth()->user();

        return view('products.show', compact('product', 'subImages', 'user'));
    }
}
