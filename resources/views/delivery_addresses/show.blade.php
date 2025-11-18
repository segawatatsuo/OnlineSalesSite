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

        <h2>お届け先選択</h2>
        @foreach ($lists as $list)
            <div class="card mb-3">
                {{ $list->id }}
                {{ $list->company_name }} {{ $list->department_name }} {{ $list->sei }}{{ $list->mei }}
                <form
                    action="{{ route('delivery-addresses.setDefault', [
                        // **パスパラメータのみ**を残す
                        'corporateCustomerId' => $corporateCustomerId,
                        'addressId' => $list->id,
                    ]) }}"
                    method="POST">

                    @csrf

                    {{-- 🎉 ここに隠しフィールドを追加！ --}}
                    <input type="hidden" name="redirectUrl" value="{{ route('orders.create') }}">

                    <button type="submit" class="btn btn-primary">選択する</button>
                </form>
            </div>
        @endforeach

    </div>
@endsection
