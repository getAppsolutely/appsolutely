<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>404 - Page Not Found</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset_url(site_favicon()) }}">
    <link rel="apple-touch-icon" href="{{ asset_url(site_favicon()) }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+Thai:wght@100..900&display=swap"
        rel="stylesheet">

    @vite([themed_path() . '/sass/app.scss', themed_path() . '/js/app.ts'], themed_build_path())
</head>

<body class="error-page">
    <div class="error-page__container">
        <!-- Background Image -->
        <div class="error-page__background">
            <img src="{{ asset_url('assets/images/models/aion-v_test_drive.webp') }}" alt="Background"
                class="error-page__background-image">
            <div class="error-page__overlay"></div>
        </div>

        <!-- Content - Right Aligned -->
        <div class="error-page__content">
            <div class="error-page__text">
                <h1 class="error-page__title">404</h1>
                <p class="error-page__subtitle">Page Not Found</p>
                <p class="error-page__description">
                    Sorry, the page you're looking for doesn't exist or has been moved.
                </p>

                <div class="error-page__actions">
                    <button type="button" class="btn error-page__btn error-page__btn-reload"
                        onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Reload Page</span>
                    </button>
                    <a href="{{ url('/') }}" class="btn error-page__btn error-page__btn-home">
                        <i class="bi bi-house-door"></i>
                        <span>Go to Homepage</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
