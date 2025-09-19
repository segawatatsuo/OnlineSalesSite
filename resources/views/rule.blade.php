@extends('layouts.app')

@section('title', '利用規約')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">

@endpush


@section('content')
    <div class="container py-4">
        <h1 class="mb-4">利用規約</h1>
        <div>
            {!! nl2br(e($ruleContent)) !!}
        </div>
    </div>
@endsection