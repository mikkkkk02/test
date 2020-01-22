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

        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.0/css/ionicons.min.css">

        <!-- AdminLTE -->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('adminlte/dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminlte/dist/css/skins/skin-black-light.min.css') }}">
        <!-- AdminLTE: DataTables -->
        <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables/dataTables.bootstrap.css') }}">

        @yield('styles')

        <!-- Styles -->
        @if(env('APP_ENV') == 'Production')
        <link rel="stylesheet" href="{{ mix('css/build/styles.min.css') }}">        
        @else
        <link rel="stylesheet" href="{{ asset('css/helper.css') }}">
        <link rel="stylesheet" href="{{ asset('css/general.css') }}">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        @endif


        <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
    </head>
    <body>

        <div id="main" class="wrapper">
                    
            @yield('content')

            @yield('modal')

            @include('includes.modals.alert')

        </div>

        <!-- Vars -->
        <script type="text/javascript">
            var pageID = '{{ $route }}',

                @yield('data')

                baseHref = '{{ url("/") }}',
                urlHref = '{{ \Request::url() }}';
        </script>

        <!-- App -->
        <script type="text/javascript" src="{{ mix('js/manifest.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/vendor.js') }}"></script>        
        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
        
        <!-- AdminLTE -->
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{ asset('adminlte/dist/js/app.min.js') }}"></script>      

        <!-- jQuery Validate -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
        <!-- Slimscroll -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
        <!-- Fastclick -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js" async></script>

        @yield('js')

        <!-- Script -->
        @if(env('APP_ENV') == 'Production')
        <script type="text/javascript" src="{{ mix('js/build/script.min.js') }}"></script>        
        @else
        <script type="text/javascript" src="{{ asset('js/script.js') }}"></script>
        @endif        

    </body>
</html>