@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/change_of_delivery_address.css') }}">
    <link rel="stylesheet" href="{{ asset('css/address-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush



@section('content')

    <main class="container">
        <main class="main">
            <div class="form-container">
                <h3>{{ $type === 'order' ? '注文会社情報一覧' : 'お届け先会社情報一覧' }}</h3>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($addresses->isEmpty())
                    <div class="alert alert-info">
                        登録されている住所がありません。(index.blade.php)
                    </div>
                @else
                    <p>index</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>会社名</th>
                                <th>部署名</th>
                                <th>担当者</th>
                                <th>住所</th>
                                <th>電話</th>
                                <th>メイン設定</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addresses as $address)
                                <tr>
                                    <td>{{ $address->company_name }}</td>
                                    <td>{{ $address->department }}</td>
                                    <td>{{ $address->sei }} {{ $address->mei }}</td>
                                    <td>
                                        〒{{ $address->zip }}<br>
                                        {{ $address->add01 }} {{ $address->add02 }} {{ $address->add03 }}
                                    </td>
                                    <td>
                                        TEL: {{ $address->tel }}<br>
                                        @if ($address->fax)
                                            FAX: {{ $address->fax }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($address->is_main)
                                            <span class="badge bg-success">メイン</span>
                                        @else
                                            <form
                                                action="{{ route('corporate_customers.addresses.selectMain', ['id' => $customer->id, 'addressId' => $address->id]) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary">メインにする</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-3">
                    <a href="{{ route('corporate_customers.addresses.create', [$customer->id, $type]) }}"
                        class="btn btn-success">新しい住所を登録</a>

                    <a href="{{ route('orders.create') }}" class="btn btn-secondary">注文画面に戻る</a>
                </div>
            </div>
        </main>
    </main>
@endsection
