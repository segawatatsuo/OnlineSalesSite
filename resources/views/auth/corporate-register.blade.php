@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/address-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush



@push('scripts')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckbox = document.getElementById('same_as_orderer');
            const deliverySection = document.getElementById('delivery_section');
            // delivery_section 内のすべての入力要素（input, select, textarea）を取得
            const deliveryInputs = deliverySection.querySelectorAll('input, select, textarea');

            function toggleDeliverySection() {
                if (toggleCheckbox.checked) {
                    deliverySection.style.display = 'none';
                    // チェックされた場合、入力フィールドを無効化 (disabled)
                    deliveryInputs.forEach(input => {
                        input.setAttribute('disabled', 'disabled');
                    });
                } else {
                    deliverySection.style.display = 'block';
                    // チェックが外れた場合、入力フィールドを有効化
                    deliveryInputs.forEach(input => {
                        input.removeAttribute('disabled');
                    });
                }
            }

            // チェックボックスの状態が変更されたときに実行
            toggleCheckbox.addEventListener('change', toggleDeliverySection);

            // ページロード時の初期状態を設定
            toggleDeliverySection();
        });
    </script>

    <script>
        document.getElementById('my-form').addEventListener('keydown', function(event) {
            // Enterキーを押したとき
            if (event.key === 'Enter') {
                // フォームのsubmitを阻止（ただし textarea 内では許可）
                if (event.target.tagName !== 'TEXTAREA') {
                    event.preventDefault();
                    return false;
                }
            }
        });
    </script>
@endpush

@section('content')

    <main class="container">

        <form method="POST" action="{{ route('corporate.register.confirm') }}" class="post-content" id="my-form">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="main">
                <h1>法人取引会員情報入力</h1>

                <div class="h-adr">
                    <span class="p-country-name" style="display:none;">Japan</span>

                    <dl class="post-table flex-between">
                        <dt>会社名</dt>
                        <dd><input type="text" name="order_company_name" class="form-control" placeholder="会社名"
                                value="{{ old('order_company_name') }}" /></dd>
                    </dl>




                    <dl class="post-table flex-between">
                        <dt>部署名</dt>
                        <dd><input type="text" name="order_department" class="form-control" placeholder="部署名"
                                value="{{ old('order_department') }}" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>ご担当者姓</dt>
                        <dd><input type="text" name="order_sei" class="form-control" placeholder="姓"
                                value="{{ old('order_sei') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>ご担当者名</dt>
                        <dd><input type="text" name="order_mei" class="form-control" placeholder="名"
                                value="{{ old('order_mei') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>電話番号</dt>
                        <dd><input type="text" name="order_phone" class="form-control" placeholder="03-000-0000"
                                value="{{ old('order_phone') }}" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>ホームページURL</dt>
                        <dd><input type="text" name="homepage" class="form-control" placeholder="http://example.com"
                                value="{{ old('homepage') }}" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>メールアドレス</dt>
                        <dd><input type="text" name="email" class="form-control" placeholder="example@mail.com"
                                value="{{ old('email') }}" /></dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>パスワード</dt>
                        <dd><input type="password" name="password" class="form-control" placeholder="パスワード"
                                value="" />
                        </dd>
                    </dl>

                    <dl class="post-table flex-between">
                        <dt>パスワード確認</dt>
                        <dd><input type="password" name="password_confirmation" class="form-control" placeholder="パスワード確認"
                                value="" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>郵便番号</dt>
                        <dd>
                            <input type="text" name="order_zip" class="p-postal-code form-control input-half"
                                placeholder="123-4567" value="{{ old('order_zip') }}" />
                            <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small" target="_blank">郵便番号検索</a>
                        </dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（都道府県）</dt>
                        <dd><input type="text" name="order_add01" class="p-region form-control" placeholder="○○県"
                                value="{{ old('order_add01') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>住所（市区町村）</dt>
                        <dd><input type="text" name="order_add02" class="p-locality p-street-address form-control"
                                placeholder="△△市□□町" value="{{ old('order_add02') }}" /></dd>
                    </dl>
                    <dl class="post-table flex-between">
                        <dt>市区町村以降の住所</dt>
                        <dd><input type="text" name="order_add03" class="p-extended-address form-control"
                                placeholder="マンション名など" value="{{ old('order_add03') }}" /></dd>
                    </dl>



                </div>

                <dl class="post-table flex-between same-address-block"
                    style="padding: 1rem; background-color: #f0f8ff; border: 2px solid #007bff; border-radius: 8px; margin: 20px auto;">
                    <dt style="font-weight: bold; font-size: 1.1em;">
                        お届け先は上記の住所と同じですか？
                    </dt>
                    <dd>
                        <label style="font-size: 1.1em;">
                            <input type="hidden" name="same_as_orderer"
                                value="0"><!-- チェックをはずすと空欄になるのでその場合は0を送る -->
                            <input type="checkbox" id="same_as_orderer" name="same_as_orderer" value="1"
                                {{ old('same_as_orderer', '1') == '1' ? 'checked' : '' }}><!-- チェックした場合はこっちで上書きされる -->
                            はい（チェックを外すと別の住所を入力できます）
                        </label>
                    </dd>
                </dl>



                <div id="delivery_section">
                    <h1>お届け先入力</h1>
                    <div class="h-adr">
                        <span class="p-country-name" style="display:none;">Japan</span>


                        <dl class="post-table flex-between">
                            <dt>会社名</dt>
                            <dd><input type="text" name="delivery_company_name" class="form-control"
                                    placeholder="会社名" value="{{ old('delivery_company_name') }}" /></dd>
                        </dl>

                        <dl class="post-table flex-between">
                            <dt>部署名</dt>
                            <dd><input type="text" name="delivery_department" class="form-control" placeholder="部署名"
                                    value="{{ old('delivery_department') }}" /></dd>
                        </dl>


                        <dl class="post-table flex-between">
                            <dt>ご担当者姓</dt>
                            <dd><input type="text" name="delivery_sei" class="form-control" placeholder="姓"
                                    value="{{ old('delivery_sei') }}" />
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>ご担当者名</dt>
                            <dd><input type="text" name="delivery_mei" class="form-control" placeholder="名"
                                    value="{{ old('delivery_mei') }}" />
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>電話番号</dt>
                            <dd><input type="text" name="delivery_phone" class="form-control"
                                    placeholder="090-999-0000" value="{{ old('delivery_phone') }}" /></dd>
                        </dl>

                        <dl class="post-table flex-between">
                            <dt>郵便番号</dt>
                            <dd>
                                <input type="text" name="delivery_zip" class="p-postal-code form-control input-half"
                                    placeholder="123-4567" value="{{ old('delivery_zip') }}" />
                                <a href="https://www.post.japanpost.jp/zipcode/" class="btn-01 small"
                                    target="_blank">郵便番号検索</a>
                            </dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>住所（都道府県）</dt>
                            <dd><input type="text" name="delivery_add01" class="p-region form-control"
                                    placeholder="○○県" value="{{ old('delivery_add01') }}" /></dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>住所（市区町村）</dt>
                            <dd><input type="text" name="delivery_add02"
                                    class="p-locality p-street-address form-control" placeholder="△△市□□町"
                                    value="{{ old('delivery_add02') }}" /></dd>
                        </dl>
                        <dl class="post-table flex-between">
                            <dt>市区町村以降の住所</dt>
                            <dd><input type="text" name="delivery_add03" class="p-extended-address form-control"
                                    placeholder="マンション名など" value="{{ old('delivery_add03') }}" /></dd>
                        </dl>
                    </div>
                </div>
                <input type="hidden" class="p-country-name" value="Japan">


                <div style="margin-top: 20px;background-color:#ffffff;">
                    <button type="submit" class="a-button" style="border: none">
                        登録内容確認
                    </button>

                </div>

            </main>
        </form>
    </main>
@endsection
