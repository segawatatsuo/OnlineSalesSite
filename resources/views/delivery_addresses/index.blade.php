@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/corporate_confirm.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
@endpush

@section('content')
    <div class="container">

        @error('message')
        @enderror

        <h2>お届け先</h2>

        {{-- 追加フォーム --}}
        <form method="POST" action="{{ route('delivery-addresses.store', $customer->id) }}">
            @csrf
            <div class="mb-2">
                <label>会社名</label>
                <input type="text" name="company_name" class="form-control">
            </div>
            <div class="mb-2">
                <label>部署名</label>
                <input type="text" name="department" class="form-control">
            </div>

            <div class="mb-2">
                <label>担当者姓</label>
                <input type="text" name="sei" class="form-control">
            </div>

            <div class="mb-2">
                <label>担当者名</label>
                <input type="text" name="mei" class="form-control">
            </div>

            <div class="mb-2">
                <label>郵便番号</label>
                <input type="text" name="zip" class="form-control">
            </div>

            <div class="mb-2">
                <label>住所</label>
                <input type="text" name="add01" class="form-control" placeholder="都道府県・市区町村">
                <input type="text" name="add02" class="form-control mt-1" placeholder="番地・建物名など">
                <input type="text" name="add03" class="form-control" placeholder="都道府県・市区町村">
            </div>

            <div class="mb-2">
                <label>電話番号</label>
                <input type="text" name="phone" class="form-control">
            </div>


            <div class="form-check mb-3">
                <input type="checkbox" name="is_default" value="1" class="form-check-input">
                <label class="form-check-label">次回もこの住所を使う</label>
            </div>

            <button type="submit" class="btn btn-primary">追加</button>
        </form>

        <hr>

        {{-- 一覧表示 --}}
        <h4>登録済みのお届け先</h4>
        <ul class="list-group">
            @foreach ($customer->deliveryAddresses as $address)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $address->company_name }}</strong><br>
                        {{ $address->zip }} {{ $address->add01 }} {{ $address->add02 }}
                        @if ($address->is_default)
                            <span class="badge bg-success ms-2">デフォルト</span>
                        @endif
                    </div>
                    <div>
                        @if (!$address->is_default)
                            <form method="POST"
                                action="{{ route('delivery-addresses.setDefault', [$customer->id, $address->id]) }}"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">デフォルトに設定</button>
                            </form>
                        @endif
                        <form method="POST"
                            action="{{ route('delivery-addresses.destroy', [$customer->id, $address->id]) }}"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">削除</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
