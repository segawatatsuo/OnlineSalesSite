<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin name
    |--------------------------------------------------------------------------
    |
    | This value is the name of laravel-admin, This setting is displayed on the
    | login page.
    | この値はlaravel-adminの名前です。この設定はログインページに表示されます。
    |
    */
    'name' => 'CCM-admin',

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'.
    | すべての管理ページのロゴです。`img` タグ（例: '<img src="http://logo-url" alt="Admin logo">'）を使用して画像として設定することもできます。
    |
    */
    'logo' => '<b>CCM</b> admin',

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin mini logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages when the sidebar menu is collapsed. You can
    | also set it as an image by using a `img` tag, eg
    | '<img src="http://logo-url" alt="Admin logo">'.
    | サイドバーメニューが折りたたまれているときの、すべての管理ページのロゴです。
    | `img` タグ（例：'<img src="http://logo-url" alt="Admin logo">'）を使用して画像として設定することもできます。
    |
    */
    'logo-mini' => '<b>La</b>',

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin bootstrap setting
    |--------------------------------------------------------------------------
    |
    | This value is the path of laravel-admin bootstrap file.
    | この値は、laravel-admin ブートストラップ ファイルのパスです。
    */
    'bootstrap' => app_path('Admin/bootstrap.php'),

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    | 管理ページのルーティング設定（パスプレフィックス、コントローラーの名前空間、デフォルトのミドルウェアを含む）。
    | ルートパス経由でアクセスする場合は、プレフィックスを空文字列に設定してください。
    |
    */
    'route' => [

        //'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),
        'prefix' => 'online-shop-ccm/admin',  // ←ここを修正

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],

        'path' => base_path('routes/admin.php'), // ← これが必要
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    | 管理ページのコントローラーとルーティング設定ファイルのインストールディレクトリ。
    |デフォルトは `app/Admin` ですが、設定を有効にするには `artisan admin::install` を実行する前に設定する必要があります。
    */
    'directory' => app_path('Admin'),

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin html title
    |--------------------------------------------------------------------------
    |
    | Html title for all pages.
    | すべてのページの HTML タイトル。
    */
    'title' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    | ページに https 経由でアクセスする場合は、これを `true` に設定します。
    */
    'https' => env('ADMIN_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    | すべての管理ページの認証設定。認証ガードと認証ドライバーのユーザープロバイダー設定が含まれます。
    |`login` `logout` などの認証ルートのコントローラーを指定できます。
    |
    */
    'auth' => [

        'controller' => App\Admin\Controllers\AuthController::class,

        'guard' => 'admin',

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,

        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => 'auth/login',

        // The URIs that should be excluded from authorization.
        'excepts' => [
            'auth/login',
            'auth/logout',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin upload setting
    |--------------------------------------------------------------------------
    |
    | File system configuration for form upload files and images, including
    | disk and upload path.
    | ディスクとアップロード パスを含む、フォーム アップロード ファイルと画像のファイル システム構成。
    |
    */
    'upload' => [

        // Disk in `config/filesystem.php`.disk が "admin" になっている場合は、config/filesystems.php を確認：
        'disk' => 'admin',
        //'disk' => 'uploads', // ← ここを変更
        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],

        // 👇 ここを追加または修正（APP_URL を使うのが安全）
        'host' => env('APP_URL') . '/uploads',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel-admin builtin model & tables.
    | 以下は、laravel-admin 組み込みモデルとテーブルのデータベース設定です。
    */
    'database' => [

        // Database connection for following tables.次のテーブルへのデータベース接続。
        'connection' => '',

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // Pivot table for table above.　上記の表のピボット テーブル
        'operation_log_table'    => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
    ],

    /*
    |--------------------------------------------------------------------------
    | User operation log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close operation log in laravel-admin.
    |
    */
    'operation_log' => [

        'enable' => true,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs.
         */
        'except' => [
            env('ADMIN_ROUTE_PREFIX', 'admin') . '/auth/logs*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Indicates whether to check route permission.
    |--------------------------------------------------------------------------
    */
    'check_route_permission' => true,

    /*
    |--------------------------------------------------------------------------
    | Indicates whether to check menu roles.
    |--------------------------------------------------------------------------
    */
    'check_menu_roles'       => true,

    /*
    |--------------------------------------------------------------------------
    | User default avatar
    |--------------------------------------------------------------------------
    |
    | Set a default avatar for newly created users.
    |
    */
    'default_avatar' => '/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg',

    /*
    |--------------------------------------------------------------------------
    | Admin map field provider
    |--------------------------------------------------------------------------
    |
    | Supported: "tencent", "google", "yandex".
    |
    */
    'map_provider' => 'google',

    /*
    |--------------------------------------------------------------------------
    | Application Skin
    |--------------------------------------------------------------------------
    |
    | This value is the skin of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported:
    |    "skin-blue", "skin-blue-light", "skin-yellow", "skin-yellow-light",
    |    "skin-green", "skin-green-light", "skin-purple", "skin-purple-light",
    |    "skin-red", "skin-red-light", "skin-black", "skin-black-light".
    |
    */
    'skin' => env('ADMIN_SKIN', 'skin-blue-light'),

    /*
    |--------------------------------------------------------------------------
    | Application layout
    |--------------------------------------------------------------------------
    |
    | This value is the layout of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported: "fixed", "layout-boxed", "layout-top-nav", "sidebar-collapse",
    | "sidebar-mini".
    |
    */
    'layout' => ['sidebar-mini', 'sidebar-collapse'],
    'layout' => ['sidebar-mini', 'fixed'], //サイドバーをデフォルトはopen状態に変更
    /*
    |--------------------------------------------------------------------------
    | Login page background image
    |--------------------------------------------------------------------------
    |
    | This value is used to set the background image of login page.
    |
    */
    'login_background_image' => '',

    /*
    |--------------------------------------------------------------------------
    | Show version at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the version number of laravel-admin at the footer of
    | each page
    |
    */
    'show_version' => true,

    /*
    |--------------------------------------------------------------------------
    | Show environment at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the environment at the footer of each page
    | 各ページのフッターに環境を表示するかどうか
    |
    */
    'show_environment' => true,

    /*
    |--------------------------------------------------------------------------
    | Menu bind to permission
    |--------------------------------------------------------------------------
    |
    | whether enable menu bind to a permission
    | メニューを権限にバインドするかどうか
    */
    'menu_bind_permission' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
    | すべてのページ コンテンツに対してデフォルトのパンくずリストを有効にするかどうか。
    */
    'enable_default_breadcrumb' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable assets minify
    |--------------------------------------------------------------------------
    */
    'minify_assets' => [

        // Assets will not be minified.
        'excepts' => [],

    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable sidebar menu search
    |--------------------------------------------------------------------------
    */
    'enable_menu_search' => true,

    /*
    |--------------------------------------------------------------------------
    | Exclude route from generate menu command
    |--------------------------------------------------------------------------
    */
    'menu_exclude' => [
        '_handle_selectable_',
        '_handle_renderable_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert message that will displayed on top of the page.
    |--------------------------------------------------------------------------
    */
    'top_alert' => '',

    /*
    |--------------------------------------------------------------------------
    | The global Grid action display class.
    |--------------------------------------------------------------------------
    */
    'grid_action_class' => \Encore\Admin\Grid\Displayers\DropdownActions::class,

    /*
    |--------------------------------------------------------------------------
    | Extension Directory
    |--------------------------------------------------------------------------
    |
    | When you use command `php artisan admin:extend` to generate extensions,
    | the extension files will be generated in this directory.
    */
    'extension_dir' => app_path('Admin/Extensions'),

    /*
    |--------------------------------------------------------------------------
    | Settings for extensions.
    |--------------------------------------------------------------------------
    |
    | You can find all available extensions here
    | https://github.com/laravel-admin-extensions.
    |
    */
    'extensions' => [
        'summernote' => [
            'enable' => true,
        ],
    ],


    'menu' => [
        [
            'title' => 'ダッシュボード',
            'icon'  => 'fa-dashboard',
            'uri'   => '/',
        ],
        [
            'title' => '商品管理',
            'icon'  => 'fa-box',
            'uri'   => 'product-ja', // このURLが /admin/product-ja に対応している必要があります
        ],
    ],
];
