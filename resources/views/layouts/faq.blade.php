<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta name="format-detection" content="telephone=no">
   <title>Aerea Home</title>
   <!-- Scripts -->
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="{{ asset('assets/img/favicon.png') }} " rel="icon">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="{{ asset('assets/faq/css/faq.css') }}"  media="all" />
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <style>
      @media (min-width: 1200px) {
         .container {
            width: 100%;
         }
      }
   </style>
</head>
<section>
   <div class="row">
      <div class="col-lg-2 col3 top-nav">
         <div>
            <a href="{{url('/')}}"> <img src="{{asset('assets/faq/img/Aerea-l.png')}}" class="logo"></a>
         </div>
         <input id="menu-toggle" type="checkbox" />
            <label class='menu-button-container' for="menu-toggle">
               <div class='menu-button'></div>
            </label>
            <ul class="annonce menu">
               <li>
                  <p>
                     <h3>FAQ</h3>
                  </p>
               </li>
               <li>
                  <p><a href="{{url('/faq/profile')}}" {{ (Request::is('faq/profile') ? 'class=actives' : '') }} >Profile</a></p>
               </li>
               <li>
                  <p><a href="{{url('/faq/unit-takeover')}}" {{ (Request::is('faq/unit-takeover') ? 'class=actives' : '') }} >Appointment for<br> Unit Take Over</a></p>
               </li>
               <li>
                  <p><a href="{{url('/faq/defects')}}" {{ (Request::is('faq/defects/*') ? 'class="actives"' : '') }} >Defects List</a></p>
               </li>
               <li>
                  <p><a href="{{url('/faq/joint-inspection')}}" {{ (Request::is('faq/joint-inspection') ? 'class=actives' : '') }} >Appointment for<br> Joint Inspection</a></p>
               </li>
               <li>
                  <p><a href="{{url('/faq/feedback')}}" {{ (Request::is('faq/feedback') ? 'class=actives' : '') }} >Feedback</a></p>
               </li>
               <li>
                  <p><a href="{{url('/faq/facilities')}}" {{ (Request::is('faq/facilities') ? 'class=actives' : '') }} >Facilities Booking</a></p>
               </li>
               <li>
                  <p><a href="{{url('/contact-us')}}" {{ (Request::is('contact-us') ? 'class=actives' : '') }} >Contact Us</a></p>
               </li>
            </ul>
         </div>
         <div class="col-lg-10 col9">
            @yield('content')
            <!-- content-wrapper ends -->
         </div>
      </div>
   </section>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>