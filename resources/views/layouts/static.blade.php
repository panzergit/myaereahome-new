<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="format-detection" content="telephone=no">
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>{{ config('app.name', 'Laravel') }}</title>
   <!-- Scripts -->
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="{{ asset('assets/img/favicon.png') }} " rel="icon">
   <link rel="stylesheet" href="{{ asset('assets/static/css/bootstrap.min.css')}}" type="text/css">
   <link rel="stylesheet" href="{{ asset('assets/static/css/style.css')}}" type="text/css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
</head>
   <body>
      <section>
         <div class="row">
            <div class="col-lg-2 col3">
               <a href="{{url('/')}}"> <img src="{{ asset('img/aerea-logo.png')}}" class="logo sticky"></a>
            </div>
            @yield('content')
         </div>
      </section>
   </body>
</html>