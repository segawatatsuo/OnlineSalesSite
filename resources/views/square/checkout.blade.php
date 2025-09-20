@extends('layouts.app')

@section('title', 'squareカード決済')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Square Web Payments SDK --}}
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
@endsection

@push('styles')
        <link rel="stylesheet" href="{{ asset('css/square.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">

@endpush

@section('content')


<main class="main">
    <div class="container">
        <h2 class="section-title">お支払いフォーム</h2>
        <p>お支払い金額：<span id="display-amount">{{ number_format($totalAmount) }}</span>円</p>
        
        <!-- Squareのカード入力欄 -->
        <div id="card-container"></div>
        
        <!-- 支払いボタン -->
        <button id="card-button" class="pay-button">支払う</button>
        
        <!-- カードブランドアイコン -->
        <div class="card-brands">
            <img src="{{ asset('/images/card/visa.png') }}" alt="Visa">
            <img src="{{ asset('/images/card/master.png') }}" alt="Mastercard">
            <img src="{{ asset('/images/card/amex.png') }}" alt="Amex">
            <img src="{{ asset('/images/card/jcb.png') }}" alt="JCB">
        </div>

        <div style="margin-top: 10px">
            <a href="https://squareup.com/jp/ja" target="_blank">
                当店はSquareオンライン決済を利用しています
            </a>
        </div>
    </div>
</main>


<!-- オーバーレイ -->
<div id="loading-overlay"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(255,255,255,0.9); justify-content:center; align-items:center;
            z-index:1000; flex-direction:column;">  
  <div class="spinner"
       style="border:6px solid #f3f3f3; border-top:6px solid #3498db;
              border-radius:50%; width:60px; height:60px;
              animation: spin 1s linear infinite;">
  </div>
  <p style="margin-top:15px; font-size:18px; color:#333;">お支払い処理中です…</p>
</div>

<style>
@keyframes spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>


<div id="messages" style="margin-top: 20px;"></div>



<script>
    document.addEventListener("DOMContentLoaded", async () => {
    const SQUARE_APP_ID = "{{ config('square.application_id') }}";
    const SQUARE_LOCATION_ID = "{{ config('square.location_id') }}";

    const payButton = document.getElementById('card-button');
    const loadingOverlay = document.getElementById('loading-overlay');

    if (!window.Square) {
        alert("Square SDKの読み込みに失敗しました");
        return;
    }

    const payments = Square.payments(SQUARE_APP_ID, SQUARE_LOCATION_ID);
    const card = await payments.card();
    await card.attach('#card-container');

    payButton.addEventListener('click', async (event) => {
        event.preventDefault();
        loadingOverlay.style.display = 'flex'; // ✅ 表示
        payButton.disabled = true;

        try {
            const result = await card.tokenize();
            if (result.status === 'OK') {
                const token = result.token;
                const purchaseAmount = parseInt(
                    document.getElementById('display-amount').innerText.replace(/,/g, '')
                );

                const response = await fetch("{{ route('square.process-payment') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ token, amount: purchaseAmount }),
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message || "支払い成功！");
                    window.location.href = "{{ url('/order/complete') }}";
                } else {
                    alert(data.message || "支払い失敗しました");
                }
            } else {
                alert("カード情報に誤りがあります。");
            }
        } catch (error) {
            console.error(error);
            alert("通信エラーが発生しました。");
        } finally {
            loadingOverlay.style.display = 'none'; // ✅ 非表示
            payButton.disabled = false;
        }
    });
});

</script>


@endsection
