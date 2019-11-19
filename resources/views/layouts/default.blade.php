<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/materialize.min.css') }}"  media="screen,projection">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="grey darken-2">
    <div class="container">
        @yield('main')
    </div>

<script type="text/javascript" src="{{ asset('js/materialize.min.js') }}"></script>
</body>
</html>
