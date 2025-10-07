@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>
            @if ($type === 'order')
                注文会社情報の編集
            @else
                お届け先会社情報の編集
            @endif
        </h2>

        <form action="{{ route('corporate_customers.addresses.store', ['id' => $corporateCustomer->id]) }}" method="POST">
            @csrf

            <input type="hidden" name="type" value="{{ $type }}">

            <div class="mb-3">
                <label for="company_name" class="form-label">会社名</label>
                <input type="text" name="company_name" class="form-control"
                    value="{{ old('company_name', $address->company_name ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="postal_code" class="form-label">郵便番号</label>
                <input type="text" name="postal_code" class="form-control"
                    value="{{ old('postal_code', $address->postal_code ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="address1" class="form-label">住所</label>
                <input type="text" name="address1" class="form-control"
                    value="{{ old('address1', $address->address1 ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="tel" class="form-label">電話番号</label>
                <input type="text" name="tel" class="form-control" value="{{ old('tel', $address->tel ?? '') }}">
            </div>

            <button type="submit" class="btn btn-primary">保存</button>
        </form>

        @if ($otherAddresses->isNotEmpty())
            <h3 class="mt-4">登録済み住所</h3>
            <ul>
                @foreach ($otherAddresses as $addr)
                    <li>
                        {{ $addr->company_name }}（{{ $addr->address1 }}）
                        @if (!$addr->is_main)
                            <form
                                action="{{ route('corporate_customers.addresses.selectMain', ['id' => $corporateCustomer->id, 'addressId' => $addr->id]) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary">メインにする</button>
                            </form>
                        @else
                            <span class="badge bg-success">メイン</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
