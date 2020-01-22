<!doctype html>
<html class="no-js" lang="en">
    <head>
        <base href="/" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ env('APP_NAME', 'Laravel') }} | @yield('pageTitle')</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
        <meta property="og:image" content="{{ asset('image/logo.png') }}">
        <meta property="og:title" content="{{ env('APP_NAME', 'Laravel') }}">
        <meta property="og:description" content="{{ env('APP_NAME', 'Laravel') }}">
        <meta property="og:url" content="">
        <meta property="og:site_name" content="{{ env('APP_NAME', 'Laravel') }}">
        <meta property="og:type" content="website">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- AdminLTE -->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('adminlte/dist/css/AdminLTE.min.css') }}">

        <!-- Styles -->
        @if(env('APP_ENV') == 'Production')
        <link rel="stylesheet" href="{{ asset('css/build/styles.min.css') }}">        
        @else
        <link rel="stylesheet" href="{{ asset('css/helper.css') }}">
        <link rel="stylesheet" href="{{ asset('css/general.css') }}">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        @endif        

        <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>
    <body class="hold-transition lockscreen">

        @yield('section')

    </body>
</html>