{{--
@extends('layouts.app')
<style>
.main {
    display: flex;
    flex-direction: column;  /* ← これを追加 */
    justify-content: center;
    align-items: center;
    min-height: 300px;
    text-align: center;
}
</style>
@section('content')
    <main class="main">
        <h1>個人情報保護について</h1>
        <img src="{{ asset('images/junbi_icon.png') }}" alt="">
    </main>
@endsection
--}}

{{-- resources/views/privacy-policy.blade.php --}}

@extends('layouts.app')

@section('title', '個人情報保護方針')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush


@section('content')
    <div class="container py-4">
        <h1 class="mb-4">個人情報保護方針</h1>
        <div>
            {!! nl2br(e($privacyContent)) !!}
        </div>
    </div>
@endsection