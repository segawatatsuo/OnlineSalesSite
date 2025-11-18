@extends('layouts.app')

@section('title', '住所変更')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/change_of_delivery_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/address-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
@endpush

@section('content')
    <main class="container">
        <main class="main">
            <div class="form-container">

                @if ($type == 'order')
                    <h1 class="form-title">ご注文者の修正</h1>
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="my-form"
                        action="{{ route('corporate-customers.update', ['corporateCustomerId' => Auth::user()->corporateCustomer->id]) }}"
                        method="post" class="h-adr">
                        @csrf
                        <div class="form-section">
                            <h2 class="form-section-title">会社情報</h2>
                            <div class="form-group">
                                <label for="name" class="form-label">会社名</label>
                                <input type="text" id="order_company_name" name="order_company_name" class="form-input"
                                    value="{{ old('order_company_name', Auth::user()->corporateCustomer->order_company_name ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">部署名</label>
                                <input type="text" id="order_department" name="order_department" class="form-input"
                                    value="{{ old('order_company_name', Auth::user()->corporateCustomer->order_department ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">担当者姓</label>
                                <input type="text" id="order_sei" name="order_sei" class="form-input"
                                    value="{{ old('order_sei', Auth::user()->corporateCustomer->order_sei ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">担当者名</label>
                                <input type="text" id="order_mei" name="order_mei" class="form-input"
                                    value="{{ old('order_mei', Auth::user()->corporateCustomer->order_mei ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">郵便番号</label>
                                <input type="text" id="order_zip" name="order_zip" class="form-input p-postal-code"
                                    value="{{ old('order_zip', Auth::user()->corporateCustomer->order_zip ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">住所（都道府県）</label>
                                <input type="text" id="order_add01" name="order_add01" class="form-input p-region"
                                    value="{{ old('order_add01', Auth::user()->corporateCustomer->order_add01 ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">住所（市区町村）</label>
                                <input type="text" id="order_add02" name="order_add02" class="form-input p-locality"
                                    value="{{ old('order_add02', Auth::user()->corporateCustomer->order_add02 ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">市区町村以降の住所</label>
                                <input type="text" id="order_add03" name="order_add03"
                                    class="form-input p-extended-address"
                                    value="{{ old('order_add03', Auth::user()->corporateCustomer->order_add03 ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">電話番号</label>
                                <input type="text" id="order_phone" name="order_phone" class="form-input"
                                    value="{{ old('order_phone', Auth::user()->corporateCustomer->order_phone ?? '') }}"
                                    required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <input type="hidden" name="type" value="order">
                            <input type="hidden" class="p-country-name" value="Japan">
                            <button type="submit" class="a-button" style="border: none">変更を保存</button>

                            <button type="button" class="b-button" style="border: none;"
                                onclick="location.href='{{ url('/orders/create') }}'">
                                戻る
                            </button>
                        </div>
                    </form>
                @else
                    <h1 class="form-title">お届け先の修正</h1>
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (!empty($delivery))
                        <form id="my-form"
                            action="{{ route('corporate-customers.update', ['corporateCustomerId' => $delivery->corporate_customer_id ?? null]) }}"
                            method="POST" class="h-adr">
                            @csrf
                            <div class="form-section">
                                <h2 class="form-section-title">会社情報</h2>
                                <div class="form-group">
                                    <label for="name" class="form-label">会社名</label>
                                    <input type="text" id="company_name" name="company_name" class="form-input"
                                        value="{{ old('company_name', $delivery->company_name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">部署名</label>
                                    <input type="text" id="department" name="department" class="form-input"
                                        value="{{ old('company_name', $delivery->department ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">担当者姓</label>
                                    <input type="text" id="sei" name="sei" class="form-input"
                                        value="{{ old('sei', $delivery->sei ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">担当者名</label>
                                    <input type="text" id="mei" name="mei" class="form-input"
                                        value="{{ old('mei', $delivery->mei ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">郵便番号</label>
                                    <input type="text" id="zip" name="zip" class="form-input p-postal-code"
                                        value="{{ old('zip', $delivery->zip ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">住所（都道府県）</label>
                                    <input type="text" id="add01" name="add01" class="form-input p-region"
                                        value="{{ old('add01', $delivery->add01 ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">住所（市区町村）</label>
                                    <input type="text" id="add02" name="add02" class="form-input p-locality"
                                        value="{{ old('add02', $delivery->add02 ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">市区町村以降の住所</label>
                                    <input type="text" id="add03" name="add03"
                                        class="form-input p-street-address"
                                        value="{{ old('add03', $delivery->add03 ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">電話番号</label>
                                    <input type="text" id="phone" name="phone" class="form-input"
                                        value="{{ old('phone', $delivery->phone ?? '') }}" required>
                                </div>
                            </div>

                            <div class="form-actions">
                                <input type="hidden" class="p-country-name" value="Japan">
                                <input type="hidden" name="type" value="delivery">
                                <button type="submit" class="a-button" style="border: none">変更を保存</button>

                                <button type="button" class="b-button" style="border: none;"
                                    onclick="location.href='{{ url('/orders/create') }}'">
                                    戻る
                                </button>
                            </div>
                        </form>
                    @else
                        <p>お届け先情報が設定されていません。</p>
                    @endif
                @endif
            </div>
        </main>
    </main>
@endsection
