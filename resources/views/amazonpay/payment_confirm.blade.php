@extends('layouts.app')

@section('title', 'Amazon Pay 決済確認')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/amazon_payment_confirm.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">

    <style>
        .section-title {
            text-align: center;
            font-size: 2.2em;
            /* タイトルを少し大きく */
            margin-bottom: 40px;
            /*color: #4a546e;*/
            /* タイトル色を落ち着いたネイビーグレーに */
            color: #50391a;
            position: relative;
            padding-bottom: 10px;
            font-weight: 700;
            /* より強調 */
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            /* 下線を少し長く */
            height: 3px;
            /*background-color: #6c7b8b;*/
            /* 下線の色を落ち着いた青グレーに */
            background-color: #50391a;
            margin: 10px auto 0;
        }
    </style>
@endpush

@section('content')

    <main class="main">

        <div class="container">

            <h1 class="section-title">Amazon Pay 決済確認</h1>

            @if (session('error'))
                <div class="error">
                    {{ session('error') }}
                </div>
            @endif

            <h2>お支払い金額</h2>
            <div class="amount-display">
                <div class="amount-text">¥{{ number_format($amount) }}</div>
                <div style="font-size: 14px; color: #666; margin-top: 5px;">
                    （税込み）
                </div>
            </div>
            <p>上記の金額でAmazon Payにて決済を行います。</p>
            <p>下のボタンをクリックして決済を続行してください。</p>


            <div id="AmazonPayButton"></div>

        </div>
    </main>

    <script src="{{ config('amazonpay.checkout_js_url') }}"></script>
    <script type="text/javascript">
        amazon.Pay.renderButton('#AmazonPayButton', {
            merchantId: '{{ $merchantId }}',
            ledgerCurrency: 'JPY',
            sandbox: {{ $sandbox ? 'true' : 'false' }},
            checkoutLanguage: 'ja_JP',
            productType: 'PayOnly',
            placement: 'Cart',
            buttonColor: 'Gold',
            buttonSize: 'small', // または 'medium', 'large'
            createCheckoutSessionConfig: {
                payloadJSON: '{!! $payloadJson !!}',
                signature: '{{ $signature }}',
                publicKeyId: '{{ $publicKeyId }}'
            }
        });
    </script>
@endsection
