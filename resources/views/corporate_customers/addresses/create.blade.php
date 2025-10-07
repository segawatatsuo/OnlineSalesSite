@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
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


                <h3>{{ $type === 'order' ? '注文会社情報の登録' : 'お届け先会社情報の登録' }}</h3>

                <h1>create.blade.php</h1>

                <form action="{{ route('corporate_customers.addresses.store', $customer->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="h-adr">
                        <span class="p-country-name" style="display:none;">Japan</span>
                        <div class="mb-3">
                            <label class="form-label">会社名</label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">部署名</label>
                            <input type="text" name="department" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ご担当者姓</label>
                            <input type="text" name="sei" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ご担当者名</label>
                            <input type="text" name="mei" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">電話番号</label>
                            <input type="text" name="tel" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">メールアドレス</label>
                            <input type="text" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">郵便番号</label>
                            <input type="text" name="order_zip" class="p-postal-code form-control input-half"
                                placeholder="123-4567" value="{{ old('order_zip') }}" required />
                        </div>


                        <div class="mb-3">
                            <label class="form-label">住所（都道府県）</label>
                            <input type="text" name="order_add01" class="p-region form-control" placeholder="○○県"
                                value="{{ old('order_add01') }}" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">住所（市区町村）</label>
                            <input type="text" name="order_add02" class="p-locality p-street-address form-control"
                                placeholder="△△市□□町" value="{{ old('order_add02') }}" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">市区町村以降の住所</label>
                            <input type="text" name="order_add03" class="p-extended-address form-control"
                                placeholder="マンション名など" value="{{ old('order_add03') }}" />
                        </div>


                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_main" value="1" class="form-check-input"
                                    id="is_main">
                                <label class="form-check-label" for="is_main">
                                    {{ $type === 'order' ? 'この住所を会社住所にする' : 'この住所をお届け先にする' }}
                                </label>
                            </div>
                        </div>


                        <button type="submit" class="a-button" style="border: none">登録</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">戻る</a>
                    </div>
                </form>
            </div>



        </main>
    </main>

@endsection
