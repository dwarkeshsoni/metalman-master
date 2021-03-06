<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{asset('img/ico/favicon.png')}}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

     <!-- Bootstrap core CSS -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/bootstrap-reset.css')}}" rel="stylesheet">


    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="{{asset('css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('/fonts/feather/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/fonts/font-awesome/css/fontawesome-all.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/slider.css') }}" rel="stylesheet">
    <link href="{{ asset('css/swiper.min.css') }}" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="{{asset('/css/perfect-scrollbar.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/css/prism.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/css/pickadate.css')}}">

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="overlay"> <img src="{{asset('/img/preloader.gif')}}" alt=""> </div>
    
    @yield('content')
        <!-- Scripts -->
    <script src="{{ asset('js/core/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/core/popper.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{asset('/js/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/prism.min.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/jquery.matchHeight-min.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/pace.min.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/picker.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/picker.date.js')}}" type="text/javascript"></script>

    <script src="{{ asset('js/app.js') }}" defer></script>
   
    <script src="{{asset('/js/customizer.js')}}" type="text/javascript"></script> 
    <script src="{{asset('/js/components-modal.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('/js/pick-a-datetime.js')}}" type="text/javascript"></script>

    <script>
      setTimeout(function() {
        $('#overlay').fadeOut();
        $('#overlay').delay(150).fadeOut('slow');
      }, 400);

    </script>

    @yield('script')
</body>
</html>
