@extends('layouts.app')

@section('title', 'ご注文情報確認')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/corporate_confirm.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
    <style>
        /* input[type=date] を select と同じように見せる */
        .form-control-date {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            display: block;
            width: 100%;
            max-width: 200px;
            /* 👈 ← PCでの横幅を制限 */
            height: calc(2.5rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control-date:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
@endpush


@section('content')
    <main class="main">
        <div class="order-container">
            <h1 class="order-title">ご注文情報確認</h1>

            <div class="order-summary-wrapper">

                {{-- ご注文者 --}}
                <div class="order-card order-billing-address">
                    <h2 class="order-card-title">ご注文者</h2>
                    <div class="order-field">
                        <span class="order-label">会社名:</span>
                        <span class="order-value">{{ $corporateCustomer->order_company_name }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">部署名:</span>
                        <span class="order-value">{{ $corporateCustomer->order_department }}</span>
                    </div>

                    <div class="order-field">
                        <span class="order-label">担当者:</span>
                        <span class="order-value">{{ $corporateCustomer->order_sei }}
                            {{ $corporateCustomer->order_mei }}</span>
                    </div>


                    <div class="order-field">
                        <span class="order-label">郵便番号:</span>
                        <span class="order-value">{{ $corporateCustomer->order_zip }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">住所:</span>
                        <span class="order-value">
                            {{ $corporateCustomer->order_add01 }}
                            {{ $corporateCustomer->order_add02 }}
                            {{ $corporateCustomer->order_add03 }}
                        </span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">電話番号:</span>
                        <span class="order-value">{{ $corporateCustomer->order_phone }}</span>
                    </div>
                    <div style="text-align: right">
                        <a
                            href="{{ route('corporate_customers.addresses.edit', ['id' => $corporateCustomer->id, 'type' => 'order']) }}">修正</a>
                    </div>
                </div>

                {{-- お届け先 --}}

                <div class="order-card order-shipping-address">
                    <h2 class="order-card-title">お届け先</h2>

                    @if ($selectedAddress)
                        <div class="order-field">
                            <span class="order-label">会社名:</span>
                            <span class="order-value">{{ $selectedAddress->company_name }}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-label">部署名:</span>
                            <span class="order-value">{{ $selectedAddress->department }}</span>
                        </div>

                        <div class="order-field">
                            <span class="order-label">担当者:</span>
                            <span class="order-value">{{ $selectedAddress->sei }}
                                {{ $selectedAddress->mei }}</span>
                        </div>

                        <div class="order-field">
                            <span class="order-label">郵便番号:</span>
                            <span class="order-value">{{ $selectedAddress->zip }}</span>
                        </div>

                        <div class="order-field">
                            <span class="order-label">住所:</span>
                            <span class="order-value">{{ $selectedAddress->add01 }} {{ $selectedAddress->add02 }}
                                {{ $selectedAddress->add03 }}</span>
                        </div>


                        <div class="order-field">
                            <span class="order-label">電話番号:</span>
                            <span class="order-value">{{ $selectedAddress->phone }}</span>
                        </div>
                    @else
                        <div class="order-field">
                            <span class="order-label">会社名:</span>
                            <span class="order-value">{{ $delivery->company_name }}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-label">部署名:</span>
                            <span class="order-value">{{ $delivery->department }}</span>
                        </div>

                        <div class="order-field">
                            <span class="order-label">担当者:</span>
                            <span class="order-value">{{ $delivery->sei }}
                                {{ $delivery->mei }}</span>
                        </div>

                        <div class="order-field">
                            <span class="order-label">郵便番号:</span>
                            <span class="order-value">{{ $delivery->zip }}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-label">住所:</span>
                            <span class="order-value">{{ $delivery->add01 }}
                                {{ $delivery->add02 }}
                                {{ $delivery->add03 }}</span>
                        </div>
                        <div class="order-field">
                            <span class="order-label">電話番号:</span>
                            <span class="order-value">{{ $delivery->phone }}</span>
                        </div>
                    @endif


                    <div style="text-align: right">
                        <a
                            href="{{ route('corporate_customers.addresses.edit', ['id' => $corporateCustomer->id, 'type' => 'delivery']) }}">修正</a>
                    </div>


                    <div style="text-align: right">
                        <a href="{{ route('delivery-addresses.index', $corporateCustomer->id) }}">お届け先を追加</a>
                    </div>

                    <div style="text-align: right">
                        <a href="{{ route('delivery-addresses.showlist', $corporateCustomer->id) }}">お届け先を変更</a>
                    </div>



                </div>
            </div>

            {{-- 以下は元のまま：配送日時、カート商品、合計 --}}
            @include('order.partials.delivery_time', ['deliveryTimes' => $deliveryTimes])
            @include('order.partials.cart_table', [
                'cart' => $cart,
                'shipping_fee' => $shipping_fee,
                'total' => $total,
            ])


            <div class="order-total-card">
                <h3>配送料：&yen;{{ number_format($shipping_fee) }}</h3>
                <h2 class="order-total-title">合計金額</h2>
                <div class="order-total-amount">&yen;{{ number_format($total ?? 0) }}</div>
            </div>



            <div class="button-area">
                <a href="{{ route('cart.index') }}" class="btn_return">戻る</a>

                <form action="{{ route('amazon-pay.create-session') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $total }}">
                    <button type="submit" class="btn_payment">AmazonPayでお支払い</button>
                </form>

                <form action="{{ route('square.checkout') }}" method="GET" class="d-inline">
                    <button type="submit" class="btn_payment">Squareでお支払い</button>
                </form>
            </div>


        </div>
    </main>

    <script>
        document.getElementById('deliverySelect')?.addEventListener('change', function() {
            const selectedId = this.value;
            const form = document.getElementById('addressSelectForm');
            form.action = form.action.replace(/0$/, selectedId);
            form.submit();
        });
    </script>
@endsection
