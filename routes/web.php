<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Registered;
use App\Admin\Controllers\MailPreviewController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\ProductJaController;
use App\Http\Controllers\ProductImageJaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminRegisterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AmazonPayController;
use App\Http\Controllers\SquarePaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\Auth\CorporateRegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Models\Order;
use App\Models\User;
use PhpParser\Node\Stmt\Return_;
use Square\Environments;
use Illuminate\Http\Request;
use App\Models\Categorization;

/*
|--------------------------------------------------------------------------
| トップページ
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('index');
})->name('top');

/*
|--------------------------------------------------------------------------
| 商品
|--------------------------------------------------------------------------
*/
Route::prefix('products')->name('products.')->group(function () {
    Route::get('{category}', [ProductJaController::class, 'category'])->name('category'); // 商品カテゴリ一覧
    Route::get('{category}/{product}', [ProductJaController::class, 'show'])->name('show'); // 商品詳細
});

// 法人会員ログイン
Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');

/*
|--------------------------------------------------------------------------
| Guest Routes (未認証ユーザー向け)
|--------------------------------------------------------------------------
*/

// 認証ルート(Laravel標準)
Auth::routes(['register' => false, 'verify' => false]);

// カスタムログアウト
Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
|  カート
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');            // カート内容表示
    Route::post('/', [CartController::class, 'store'])->name('store');           // 商品追加
    Route::put('/update', [CartController::class, 'update'])->name('update');     // 数量更新
    Route::delete('/destroy', [CartController::class, 'destroy'])->name('destroy');// 商品削除

    // 特例：決済関連（REST外アクション）
    /*
    Route::get('amazonpay/checkout', [AmazonPayController::class, 'checkout'])->name('amazonpay.checkout');
    Route::post('amazonpay/complete', [AmazonPayController::class, 'complete'])->name('amazonpay.complete');
    */
    Route::post('square/checkout', [SquarePaymentController::class, 'checkout'])->name('square.checkout');
    Route::post('square/complete', [SquarePaymentController::class, 'complete'])->name('square.complete');

});

/*
|--------------------------------------------------------------------------
|  注文
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('create', [OrderController::class, 'create'])->name('create');   // 注文フォーム
    Route::post('confirm', [OrderController::class, 'confirm'])->name('confirm'); // 確認画面
    Route::post('/', [OrderController::class, 'store'])->name('store');         // 注文確定
    Route::get('complete', [OrderController::class, 'complete'])->name('complete'); // 完了画面
});


/*
// ★★★ カスタムメール認証ルートを Auth::routes() より前に定義 ★★★
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 個人ユーザー向けのメール確認ルート
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
    ->name('verification.verify'); // 'signed' ミドルウェアも削除

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.resend');
*/


/*
|--------------------------------------------------------------------------
| メール認証(個人ユーザー)
|--------------------------------------------------------------------------
*/

Route::prefix('email')->name('verification.')->middleware('auth')->group(function () {
    Route::get('verify', function () {
        return view('auth.verify-email');
    })->name('notice');

    Route::get('verify/{id}/{hash}', [VerificationController::class, '__invoke'])
        ->withoutMiddleware('auth')
        ->name('verify');

    Route::post('resend', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('resend');
});


// 法人ユーザー向けのメール確認ルート
Route::get('/corporate/email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
    ->name('corporate.verification.verify'); // 'signed' ミドルウェアも削除


// 認証関連
// CustomRegisterControllerによるカスタム登録フローを優先するため、
// Auth::routes() で生成されるデフォルトの /register を無効にし、
// 他の認証機能（ログイン、パスワードリセット）のみを有効にします。
// ★★★ verify => false に変更してカスタム認証ルートを使用 ★★★



/*
Route::get('/register', [CustomRegisterController::class, 'showForm'])->name('register');
Route::post('/register/confirm', [CustomRegisterController::class, 'confirm'])->name('register.confirm');
Route::post('/register/store', [CustomRegisterController::class, 'store'])->name('register.store');
*/
/*
|--------------------------------------------------------------------------
| 個人ユーザー登録
|--------------------------------------------------------------------------
*/

Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [CustomRegisterController::class, 'showForm'])->name('index');
    Route::post('confirm', [CustomRegisterController::class, 'confirm'])->name('confirm');
    Route::post('store', [CustomRegisterController::class, 'store'])->name('store');
});




// カスタム(法人取引会員)登録ルート
/*
Route::get('/corporate/register', [CorporateRegisterController::class, 'showForm'])->name('corporate.register');
Route::post('/corporate/register/confirm', [CorporateRegisterController::class, 'confirm'])->name('corporate.register.confirm');
Route::post('/corporate/register', [CorporateRegisterController::class, 'store'])->name('corporate.register.store');
*/

/*
|--------------------------------------------------------------------------
| 法人ユーザー登録
|--------------------------------------------------------------------------
*/

Route::prefix('corporate')->name('corporate.')->group(function () {
    // 登録フォーム
    Route::get('register', [CorporateRegisterController::class, 'showForm'])->name('register');
    Route::post('register/confirm', [CorporateRegisterController::class, 'confirm'])->name('register.confirm');
    Route::post('register', [CorporateRegisterController::class, 'store'])->name('register.store');

    // メール認証
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
        ->name('verification.verify');

    Route::post('resend-verification', function () {
        $email = session('resent_email');

        if (!$email) {
            return redirect()
                ->route('corporate.register')
                ->withErrors(['error' => 'セッションが切れました。もう一度登録してください。']);
        }

        $user = User::where('email', $email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            event(new Registered($user));
            return back()->with('status', '認証メールを再送信しました。');
        }

        return back()->withErrors(['error' => 'メール再送信に失敗しました。']);
    })->name('verification.resend');
});


//法人取引会員登録でのメール送信しました確認画面
Route::get('/corporate/register/confirm_message', function () {
    return view('auth.confirm_message'); // 任意の Blade テンプレート
})->name('corporate.register.confirm_message');


// 顧客マイページ（認証必須、メール認証済み必須）
Route::middleware(['auth', 'verified'])->prefix('mypage')->name('mypage.')->group(function () {
    Route::get('/', [MypageController::class, 'index'])->name('index');
    Route::get('edit', [MypageController::class, 'edit'])->name('edit');
    Route::post('update', [MypageController::class, 'update'])->name('update');
    Route::get('password', [MypageController::class, 'editPassword'])->name('password.edit');
    Route::post('password', [MypageController::class, 'updatePassword'])->name('password.update');
});

/*
// 管理者ルート（ログイン必須）
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('products', AdminProductController::class);
    // 管理者登録（ポリシー使用）
    Route::get('register', [AdminRegisterController::class, 'create'])->middleware('can:admin')->name('register');
    Route::post('register', [AdminRegisterController::class, 'store'])->middleware('can:admin');
    // 商品画像削除
    Route::delete('product-images/{id}', [ProductImageJaController::class, 'destroy'])->name('product_images.destroy');
});
*/

/*
|--------------------------------------------------------------------------
| 管理者ルート(Encore Laravel-Admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        // 商品管理(RESTfulリソース)
        Route::resource('products', AdminProductController::class);

        // 商品画像削除
        Route::delete('product-images/{id}', [ProductImageJaController::class, 'destroy'])
            ->name('product_images.destroy');

        // 管理者登録
        Route::middleware('can:admin')->group(function () {
            Route::get('register', [AdminRegisterController::class, 'create'])->name('register');
            Route::post('register', [AdminRegisterController::class, 'store']);
        });
    });



// Encore Laravel-Adminのルーティング
Encore\Admin\Facades\Admin::routes();

// 注文処理が終わったら(cartが空になったら)以下のページにアクセスしてきたらトップページにリダイレクトさせるミドルウェア用のルート
Route::middleware(['cart.not.empty', 'prevent.back.history'])->group(function () {
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::get('/cart/square-payment', [CartController::class, 'square-payment'])->name('cart.square-payment');
});

/*
//お問い合わせフォーム
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
Route::get('/contact/complete', [ContactController::class, 'complete'])->name('contact.complete');
*/


/*
|--------------------------------------------------------------------------
| お問い合わせ
|--------------------------------------------------------------------------
*/

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'showForm'])->name('form');
    Route::post('/', [ContactController::class, 'submitForm'])->name('submit');
    Route::get('complete', [ContactController::class, 'complete'])->name('complete');
});



/*
|--------------------------------------------------------------------------
|  静的ページ（利用規約 / プライバシー / 特商法）
|--------------------------------------------------------------------------
*/
Route::controller(PageController::class)->group(function () {
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy.policy');
    Route::get('/rule', 'rule')->name('rule');       // 利用規約
    Route::get('/legal', 'legal')->name('legal');    // 特商法
});

/*
// ホーム画面（ログイン後のリダイレクト用）
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/thank-you', function () {
    return view('thank-you');
})->name('order.thank-you');
*/

/*
|--------------------------------------------------------------------------
| その他のページ
|--------------------------------------------------------------------------
*/
// ホーム画面（ログイン後のリダイレクト用）
Route::get('home', [HomeController::class, 'index'])->name('home');

Route::get('thank-you', function () {
    return view('thank-you');
})->name('order.thank-you');


/*
// 法人ユーザー向けのメール再送信ルート
Route::post('/corporate/resend-verification', function () {
    $email = session('resent_email');

    if (!$email) {
        return redirect()->route('corporate.register')->withErrors(['error' => 'セッションが切れました。もう一度登録してください。']);
    }

    $user = User::where('email', $email)->first();

    if ($user && !$user->hasVerifiedEmail()) {
        event(new Registered($user)); // ← ここで再送
        return back()->with('status', '認証メールを再送信しました。');
    }

    return back()->withErrors(['error' => 'メール再送信に失敗しました。']);
})->name('corporate.verification.resend');
*/




/*
|--------------------------------------------------------------------------
| Amazon Pay Routes
|--------------------------------------------------------------------------
*/

Route::prefix('amazon-pay')->name('amazon-pay.')->group(function () {
    Route::get('/payment', [AmazonPayController::class, 'showPayment'])->name('payment');
    Route::post('/create-session', [AmazonPayController::class, 'createSession'])->name('create-session');
    Route::get('/complete', [AmazonPayController::class, 'complete'])->name('complete'); //Amazon が checkoutSessionId を持った状態で/completeにリダイレクトします。

    Route::get('/cancel', [AmazonPayController::class, 'cancelPayment'])->name('cancel');
    Route::get('/error', [AmazonPayController::class, 'errorPayment'])->name('error');
    Route::get('/return', [AmazonPayController::class, 'amazonPayReturn'])->name('return');
    Route::post('/webhook', [AmazonPayController::class, 'webhook'])->name('webhook');
    /*動作確認*/
    Route::get('/captureOrder', [AmazonPayController::class, 'captureOrder'])->name('captureOrder');
});


/*
|--------------------------------------------------------------------------
| square Routes
|--------------------------------------------------------------------------
*/

Route::prefix('square')->name('square.')->group(function () {
    Route::get('/checkout', [SquarePaymentController::class, 'checkout'])->name('checkout'); // フロント画面
    Route::post('/process-payment', [SquarePaymentController::class, 'processPayment'])->name('process-payment');
    Route::post('/capture-payment/{paymentId}', [SquarePaymentController::class, 'capturePayment'])->name('capture-payment');
});


// Admin画面での商品発送メールのプレビュー
// ルートをAdmin認証が必要なグループ内に配置
Route::middleware(['admin.auth'])->group(function () {
    // 従来のビューベースプレビュー
    Route::get('admin/mail-preview/{orderId}', [MailPreviewController::class, 'preview'])
        ->name('admin.mail-preview')
        ->where('orderId', '[0-9]+'); // 数字のみ許可

    // テンプレートベースプレビュー
    Route::get('admin/mail-preview-template/{orderId}', [MailPreviewController::class, 'previewTemplate'])
        ->name('admin.mail-preview-template')
        ->where('orderId', '[0-9]+'); // 数字のみ許可
});


/*
|--------------------------------------------------------------------------
Encore Admin(管理画面)のProduct(商品)ページでcategoryに関連するmajor_classificationのプルダウンを表示するためのAPI
|--------------------------------------------------------------------------
*/
Route::get('/admin/api/major-classifications', function (Request $request) {
    $categoryId = $request->get('q');

    if (!$categoryId) {
        return response()->json([]);
    }

    return \App\Models\Categorization::where('category_id', $categoryId)
        ->whereNotNull('major_classification')
        ->distinct()
        ->pluck('major_classification')
        ->map(fn ($item) => ['id' => $item, 'text' => $item])
        ->values();
})->name('admin.api.major-classifications');

/*
|--------------------------------------------------------------------------
Encore Admin(管理画面)のProduct(商品)ページでcategory->major_classificationに関連するclassificationのプルダウンを表示するためのAPI
|--------------------------------------------------------------------------
*/
Route::get('/admin/api/classifications', function (Request $request) {
    $major = $request->get('q');
    $categoryId = $request->get('category_id'); // 必要なら一緒に渡す

    if (!$major) {
        return response()->json([]);
    }

    return \App\Models\Categorization::where('major_classification', $major)
        ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
        ->whereNotNull('classification')
        ->distinct()
        ->pluck('classification')
        ->map(fn ($item) => ['id' => $item, 'text' => $item])
        ->values();
});
