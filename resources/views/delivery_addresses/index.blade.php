@extends('layouts.app')

@section('title', 'お届け先の追加')

@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
@endpush

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


                <h1 class="form-title">お届け先の追加</h1>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form id="my-form" method="POST" action="{{ route('delivery-addresses.store', $customer->id) }}"
                    class="h-adr">

                    @csrf
                    <div class="form-section">
                        <h2 class="form-section-title">会社情報</h2>
                        <div class="form-group">
                            <label for="name" class="form-label">会社名</label>
                            <input type="text" id="company_name" name="company_name" class="form-input"
                                value="{{ old('company_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">部署名</label>
                            <input type="text" id="department" name="department" class="form-input"
                                value="{{ old('department') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">担当者姓</label>
                            <input type="text" id="sei" name="sei" class="form-input"
                                value="{{ old('sei') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">担当者名</label>
                            <input type="text" id="mei" name="mei" class="form-input"
                                value="{{ old('mei') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">郵便番号</label>
                            <input type="text" id="zip" name="zip" class="form-input p-postal-code"
                                value="{{ old('zip') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">住所（都道府県）</label>
                            <input type="text" id="add01" name="add01" class="form-input p-region"
                                value="{{ old('add01') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">住所（市区町村）</label>
                            <input type="text" id="add02" name="add02" class="form-input p-locality"
                                value="{{ old('add02') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">市区町村以降の住所</label>
                            <input type="text" id="add03" name="add03" class="form-input p-street-address"
                                value="{{ old('add03') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">電話番号</label>
                            <input type="text" id="phone" name="phone" class="form-input"
                                value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_default" value="1" class="form-check-input">
                        <label class="form-check-label">次回もこの住所を使う</label>
                    </div>


                    <div class="form-actions">
                        <input type="hidden" name="type" value="order">
                        <input type="hidden" class="p-country-name" value="Japan">
                        <button type="submit" class="a-button" style="border: none">保存</button>

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
