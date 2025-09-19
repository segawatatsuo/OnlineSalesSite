@extends('layouts.app')

@section('title', 'カード決済')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Square Web Payments SDK --}}
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
@endsection

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')

<main class="main">
    <div class="container">
        <h2>カード決済フォーム</h2>
        <p>お支払い金額：<span id="display-amount">{{ number_format($totalAmount) }}</span>円</p>

        {{-- Squareカード入力フォームが入る場所 --}}
        <div id="card-container"></div>

        <button id="card-button" disabled>支払う</button>

        <div id="messages" style="margin-top: 20px;"></div>
    </div>
</main>

<script type="text/javascript">
    const SQUARE_APP_ID     = "{{ config('square.application_id') }}";
    const SQUARE_LOCATION_ID = "{{ config('square.location_id') }}";

    async function main() {
        if (!window.Square) {
            alert("Square SDKの読み込みに失敗しました");
            return;
        }

        const payments = Square.payments(SQUARE_APP_ID, SQUARE_LOCATION_ID);
        const card = await payments.card();
        await card.attach('#card-container');

        const cardButton = document.getElementById('card-button');
        cardButton.disabled = false;

        cardButton.addEventListener('click', async function (event) {
            event.preventDefault();

            const result = await card.tokenize();
            if (result.status === 'OK') {
                const token = result.token;
                const purchaseAmount = parseInt(document.getElementById('display-amount').innerText.replace(/,/g, ''));

                //Route::post('/process-payment', [SquarePaymentController::class, 'processPayment'])->name('process-payment');
                const response = await fetch("{{ route('square.process-payment') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: token,
                        amount: purchaseAmount,
                    }),
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message || "与信が成功しました！");
                    window.location.href = "{{ url('/order/complete') }}";
                } else {
                    alert(data.errors ? JSON.stringify(data.errors) : data.message);
                }
            } else {
                alert("カード情報に誤りがあります。");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", main);
</script>
@endsection
