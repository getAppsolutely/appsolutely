<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Builder - {{ $page->title }}</title>

    @vite(['resources/page-builder/assets/main.ts', 'resources/page-builder/assets/page-builder.css'], 'build/page-builder')
</head>
<body class="bg-gray-50">
    <div id="page-builder-app"
         data-page-id="{{ $pageId }}"
         data-page-title="{{ $page->title }}"
         data-api-base="{{ admin_url('api') }}">
        <!-- Vue.js app will mount here -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading Page Builder...</p>
            </div>
        </div>
    </div>
</body>
</html>
