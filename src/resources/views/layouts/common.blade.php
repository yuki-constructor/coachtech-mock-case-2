<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'デフォルトタイトル')</title>
    <!-- 共通のCSS -->
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <!-- ページごとのCSS -->
    @stack('styles')
</head>

<body>
    <!-- 共通のヘッダー -->
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <img src="{{ asset('storage/photos/logo_images/logo.svg') }}" alt="COACHTECH ロゴ" class="logo" />
            </div>
            <div class="header-center">
            </div>
            <div class="header-right">
            </div>
        </div>
    </header>
    <main>
        <!-- ページごとのコンテンツ -->
        @yield('content')
    </main>
</body>

</html>
