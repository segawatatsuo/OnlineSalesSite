@extends('layouts.app')

@section('title', 'ショッピングカート')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
    <style>
        /* === 共通 === */
        .update-btn,
        .delete-btn,
        .checkout-btn,
        .continue-shopping {
            border: none;
            border-radius: 4px;
            /*padding: 6px 14px;*/
            padding: 16px 32px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
        }

        .update-btn {
            background: #0d6efd;
            color: #fff;
        }
        .update-btn:hover {
            background: #0b5ed7;
        }

        .delete-btn {
            background: #dc3545;
            color: #fff;
        }
        .delete-btn:hover {
            background: #bb2d3b;
        }

        .continue-shopping {
            background: #6c757d;
            color: #fff;
            margin-right: 10px;
        }
        .continue-shopping:hover {
            background: #5a6268;
        }

        .checkout-btn {
            /*background: #198754;
            color: #fff;*/
        }
        .checkout-btn:hover {
            /*background: #157347;*/
        }

        /* === PC版：テーブル === */
        .cart-table {
            margin-bottom: 20px;
        }
        .cart-table table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #e0e0e0;
        }
        .cart-table th,
        .cart-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        .cart-table thead {
            background: #f9f9f9;
        }
        .cart-table tbody tr:hover {
            background: #fdfdfd;
        }

        /* === スマホ版：カード === */
        .cart-mobile {
            display: none; /* PCでは非表示 */
        }

        @media (max-width: 768px) {
            .cart-table {
                display: none; /* スマホではテーブルを非表示 */
            }
            .cart-mobile {
                display: block;
            }
            .cart-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 12px;
            }
            .cart-card h4 {
                font-size: 15px;
                margin-bottom: 8px;
                color: #333;
            }
            .cart-card .price,
            .cart-card .subtotal {
                font-size: 14px;
                color: #555;
                margin-bottom: 6px;
            }
            .cart-card .quantity-controls {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
            }
            .cart-card .actions {
                display: flex;
                gap: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <main class="container">
        <div class="cart-header">
            <h2>ショッピングカート</h2>
        </div>

        {{-- PC版：テーブル --}}
        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>金額</th>
                        <th>数量</th>
                        <th>小計</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>&yen;{{ number_format($item['price']) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update') }}" class="quantity-controls">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="quantity-input">
                                    <button type="submit" class="update-btn">更新</button>
                                </form>
                            </td>
                            <td>&yen;{{ number_format($item['subtotal']) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                    <button type="submit" class="delete-btn">削除</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

{{-- スマホ版：カード --}}
<div class="cart-mobile">
    @foreach ($cart as $item)
        <div class="cart-card">
            <h4>{{ $item['name'] }}</h4>

            {{-- 上段：単価・数量・更新 --}}
            <div class="top-row">
                <div class="price">単価: &yen;{{ number_format($item['price']) }}</div>
                <form method="POST" action="{{ route('cart.update') }}" class="quantity-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="quantity-input">
                    <button type="submit" class="update-btn">更新</button>
                </form>
            </div>

            {{-- 下段：小計・削除 --}}
            <div class="bottom-row">
                <div class="subtotal">小計: &yen;{{ number_format($item['subtotal']) }}</div>
                <form method="POST" action="{{ route('cart.remove') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                    <button type="submit" class="delete-btn">削除</button>
                </form>
            </div>
        </div>
    @endforeach
</div>

        <div class="cart-total">
            <h4>お買い物カゴの合計</h4>
            <div class="total-amount">&yen;{{ number_format($total) }}</div>
        </div>

        <div class="cart-actions">
            <button class="continue-shopping"
                onclick="window.location.href='{{ ($category ?? null) ? asset('product/' . $category) : url('/') }}'">
                買い物を続ける
            </button>
            <button class="checkout-btn" onclick="window.location.href='{{ route('order.create') }}'">購入手続きに進む</button>
        </div>
    </main>
@endsection
