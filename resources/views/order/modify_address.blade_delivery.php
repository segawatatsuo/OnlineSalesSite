@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/change_of_delivery_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/address-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <main class="container">
        <main class="main">
            <div class="form-container">


                <h1 class="form-title">お届け先の変更</h1>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form id="my-form"
                    action="{{ route('corporate-customers.update', ['corporateCustomerId' => Auth::user()->corporateCustomer->id]) }}"
                    method="POST">
                    @csrf
                    <div class="form-section">
                        <h2 class="form-section-title">会社情報</h2>
                        <div class="form-group">
                            <label for="name" class="form-label">会社名</label>
                            <input type="text" id="delivery_company_name" name="delivery_company_name" class="form-input"
                                value="{{ old('delivery_company_name', Auth::user()->corporateCustomer->delivery_company_name ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">部署名</label>
                            <input type="text" id="delivery_department" name="delivery_department" class="form-input"
                                value="{{ old('delivery_department', Auth::user()->corporateCustomer->delivery_department ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">担当者姓</label>
                            <input type="text" id="delivery_sei" name="delivery_sei" class="form-input"
                                value="{{ old('delivery_sei', Auth::user()->corporateCustomer->delivery_sei ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">担当者名</label>
                            <input type="text" id="delivery_mei" name="delivery_mei" class="form-input"
                                value="{{ old('delivery_mei', Auth::user()->corporateCustomer->delivery_mei ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">郵便番号</label>
                            <input type="text" id="delivery_zip" name="delivery_zip" class="form-input"
                                value="{{ old('delivery_zip', Auth::user()->corporateCustomer->delivery_zip ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">住所（都道府県）</label>
                            <input type="text" id="delivery_add01" name="delivery_add01" class="form-input"
                                value="{{ old('delivery_add01', Auth::user()->corporateCustomer->delivery_add01 ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">住所（市区町村）</label>
                            <input type="text" id="delivery_add02" name="delivery_add02" class="form-input"
                                value="{{ old('delivery_add02', Auth::user()->corporateCustomer->delivery_add02 ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">市区町村以降の住所</label>
                            <input type="text" id="delivery_add03" name="delivery_add03" class="form-input"
                                value="{{ old('delivery_add03', Auth::user()->corporateCustomer->delivery_add03 ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">電話番号</label>
                            <input type="text" id="delivery_phone" name="delivery_phone" class="form-input"
                                value="{{ old('delivery_phone', Auth::user()->corporateCustomer->delivery_phone ?? '') }}"
                                required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="a-button" style="border: none">変更を保存</button>

                        <button type="button" class="b-button" style="border: none;"
                            onclick="location.href='{{ url('/orders/create') }}'">
                            戻る
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </main>
@endsection
