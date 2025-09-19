@extends('layouts.app')

@section('title', '商品詳細 | '.$product->product_code)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/detail-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush


@section('content')

    <!-- メインコンテンツ -->
    <main class="main-content">
        <div class="product-detail fade-in">
            <!-- 左側 - 画像セクション -->
            <div class="product-images">

                <div class="main-image" onclick="openModal(this)">
                    <img src="{{ url('uploads/' . $product->mainImage->image_path) }}" alt="メインイメージ">
                </div>

                <div class="thumbnail-grid">
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/1.jpg') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/2.jpg') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/3.jpg') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/4.gif') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/5.gif') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/6.gif') }}" alt="">
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="{{ asset('/images/other/point/7.jpg') }}" alt="">
                    </div>
                </div>
            </div>

            <!-- 右側 - 商品情報セクション -->
            <div class="product-info">
                <h2 class="product-title">{{ optional($product)->name }}</h2>

                <div class="product-image-cut">
                    <img src="{{ asset('images/other/AirStocking_POINT123.jpg') }}" alt="商品イメージ">
                </div>

                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="purchase-section">
                        <p>{{ optional($product)->product_code}} {{ optional($product)->name }}</p>
                        <p class="product-price">&yen;{{ number_format(optional($product)->price) }}</p>
                        <div class="quantity-section">
                            <label class="quantity-label">数量:</label>
                            <input type="number" class="quantity-input" value="1" min="1" name="quantity" >
                        </div>
                        <button class="add-to-cart">カートに入れる</button>
                    </div>
                </form>

                <div class="product-description">

                    <div class="description-section">
                        <h3 class="description-title">{{ optional($product)->description_1_heading }}</h3>
                        <p class="description-text">
                            {!! optional($product)->description_1 !!}
                        </p>
                    </div>

                    <div class="description-section">
                        <h3 class="description-title">{{ optional($product)->description_2_heading }}</h3>
                        <p class="description-text">
                            {!! optional($product)->description_2 !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- モーダル -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img class="modal-image" id="modalImage" src="" alt="">
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(element) {
            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("modalImage");
            const img = element.querySelector("img"); // サムネ or メイン画像
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }

        // モーダル背景クリックでも閉じられるように
        window.onclick = function(event) {
            const modal = document.getElementById("imageModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
@endpush

@endsection
