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
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/landing/css/bootstrap.min.css') }}"  media="all" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/landing/css/landing.css') }}"  media="all" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
        <style>
            body, html {
                height: 100%;
            }
            * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
            }
            .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
        --bs-gutter-x: 0px;
    }
    .landimg2{margin-bottom: 0px;
        margin-top: 40px;     text-align: center;padding-left:0px;}
    .landimg2 li {
        display: inline;
        margin-right: 15px;
        margin-left: 15px;
    }
    .landimg2 li a {
        color: #5D5D5D; font-size:10px;
    }
            .login100-more {
            width: 50%;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            position: relative;
            z-index: 1;
            }
            .login100-form {
            width: 50%;
            min-height: 100vh;
            display: block;
            background-color: #f7f7f7;
            padding: 15px;
            }
            .wrap-login100 {
            width: 100%;
            background: #fff;
            overflow: hidden;
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-box;
            display: -ms-flexbox;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            flex-direction: row-reverse;
            }
            .container-login100 {
            width: 100%;
            min-height: 100vh;
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-box;
            display: -ms-flexbox;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            background: #f2f2f2;
            }
            .limiter {
            width: 100%;
            margin: 0 auto;
            }
            .login100-more::before {
            content: "";
            display: block;
            position: absolute;
            z-index: -1;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.1);
            }
            .item {
         border: 2px solid rgb(95 97 110);
         border-radius: 0.5em;
         padding: 20px;
         width: 10em;
         }
         .h-100 {
         height: 100% !important;
         }
         .my-auto {
         margin-top: auto !important;
         margin-bottom: auto !important;
         }
         .landingection{}
         .landingection .llogo {
         width: 200px;
         margin: 0 auto;
         display: block;
         }
         .landingection h2 {
         color: #8F7F65;
         text-align: center;
         font-size: 22px;
         }
         .landingection h3 {
         text-align: center;
         font-size: 22px;
         font-weight: 600;
         color: #5D5D5D;
         margin-top: 40px;
         margin-bottom: 40px;
         }
         .landingection h4 {
         text-align: center;
         font-size: 20px;
         font-weight: 400;
         color: #5D5D5D;
         line-height: 28px;
         }
         .landingection h5{text-align: center;
         font-size: 22px;
         font-weight: 600;
         color: #5D5D5D;}
         .landingection .ignup {
         background: #8F7F65;
         color: #fff;
         text-decoration: none;
         padding: 12px;
         border-radius: 10px;
         font-size: 12px;
         font-weight: 600;
         margin-left: 20px;
         margin-right: 20px;
         }
         .landingection .ignup img {
         width: 35px;
         margin-right: 6px;
         margin-top: -5px;
         }
         .qrimg {    margin: 0 auto;
         display: block;
         width: 100%;
         text-align: center; margin-top: 20px;
         margin-bottom: 15px;}
         .qrimg img {
         width: 20%;     margin-left: 20px;
         margin-right: 20px;
         }
         .sqr {
         width: 10% !important;
         }
         .appimg {    margin: 0 auto;
         display: block;
         width: 100%;
         text-align: center;}
         .appimg img{ width: 20%;}
         .landingection{}
         .iconimg {
         margin: 0 auto;
         display: block;
         width: 100%;
         text-align: center;
         margin-top: 40px;
         margin-bottom: 40px;
         }
         .qrfooter{}
         .vertop{    width: 10%!important;
         vertical-align: top!important;}
         .vertop2{    width: 10%!important;
         margin-left: 16%!important;
         }
      </style>
</head>
<div class="container-scroller">
         <div class="container-fluid page-body-wrapper full-page-wrapper">
         
            @yield('content')
        
            <!-- content-wrapper ends -->
         </div>
         <!-- page-body-wrapper ends -->
      </div>
</body>
</html>
