<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Auth')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('head')
    <style>
        body { font-family: Arial, sans-serif; background:#fff; }
        .auth-container { min-height:100vh; display:flex; align-items:center; justify-content:center; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div style="width:100%; max-width:980px;">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
