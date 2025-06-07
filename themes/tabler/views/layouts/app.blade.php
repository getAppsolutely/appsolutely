<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Tabler Theme' }}</title>
    @livewireStyles
    <!-- Add your CSS links here -->
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    @livewireScripts
    <!-- Add your JS scripts here -->
</body>
</html>
