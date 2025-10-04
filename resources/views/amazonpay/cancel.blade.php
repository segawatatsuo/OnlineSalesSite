@extends('layouts.app')

@section('title', 'Amazon Payの利用をキャンセル - CCMedico Shop')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')


    <div class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <div class="text-center">
                <!-- エラーアイコン -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </div>

                <!-- タイトル -->
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Amazon Payの利用をキャンセル</h1>

                <!-- エラーメッセージ -->
                <div class="mb-6">
                    <p class="text-sm text-gray-500">
                        しばらく時間をおいて再度お試しいただくか、<br>
                        お困りの場合はお客様サポートまでお問い合わせください。
                    </p>
                </div>


                <!-- アクションボタン -->
                <div class="space-y-3">
                    <a href="{{ route('top') }}"
                        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        ホームに戻る
                    </a>
                </div>


            </div>
        </div>
    </div>

@endsection
