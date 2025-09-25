<?php

namespace App\Http\Controllers;

use App\Models\ProductJa;
use App\Models\TopPage;

use Illuminate\Support\Facades\Auth;

class ProductJaController extends Controller
{
    /*
    public function index()
    {
        $products = ProductJa::with('category')->get();
        if ($products->isEmpty()) {
            abort(404);
        }
        $user = auth()->user();
        return view('products.index', compact('products', 'user'));
    }
    */

    /*
    public function category($category)
    {
        //空のコレクション（Collectionオブジェクト）を作る
        $premiumSilk = collect();
        $diamondLegs = collect();

        if ($category === 'airstocking') {
            $user = Auth::user();

            // セッションに今のカテゴリーを保存
            session()->put('category', $category);

            $topPageItem = TopPage::where('category', 1)->first(); //トップページのairstockingのHeroイメージや説明コピーなどを取得

            // 商品の基本クエリ
            $baseQuery = ProductJa::with(['category', 'mainImage'])
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('brand', $category);
                })
                ->where('not_display', '=', 0);

            // ユーザータイプに応じてwholesale(法人商品)条件を分岐
            if ($user && $user->user_type === 'corporate') {
                // 法人会員 → wholesale = 1 のみ
                $baseQuery = $baseQuery->where('wholesale', 1);
            } else {
                // 一般ユーザー or 未ログイン → wholesale is null or 0
                $baseQuery = $baseQuery->where(function ($query) {
                    $query->whereNull('wholesale')
                        ->orWhere('wholesale', 0);
                });
            }

            // 各分類でクローンして商品取得
            $premiumSilk = (clone $baseQuery)
                ->where('classification', 'Premium Silk')
                ->get();

            $diamondLegs = (clone $baseQuery)
                ->where('classification', 'Diamond Legs')
                ->get();

            return view('products.category', [
                'category' => $category,
                'premiumSilk' => $premiumSilk,
                'diamondLegs' => $diamondLegs,
                'topPageItem' => $topPageItem
            ]);
        } else {

            return view('products.category', [
                'category' => $category,
                //'premiumSilk' => $premiumSilk,
                //'diamondLegs' => $diamondLegs,
                //'topPageItem' => $topPageItem
            ]);
        }
    }
    */

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
