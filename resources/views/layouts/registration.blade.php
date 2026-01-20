<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="{{ asset('assets/img/favicon.png') }} " rel="icon">
         <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/style2.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-select.css') }}">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link rel="stylesheet" href="{{ asset('assets/admin/css/Lato.css') }}">
	  <link rel='stylesheet' href="{{ asset('assets/admin/css/select2.css') }}">


</head>
<body>
      <section>
        
           
            @yield('content')
         
      </section>
	    <script src=" {{ asset('assets/admin/js/jquery.min.js') }}"></script>
		<script src=" {{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
		<script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>
		 <script src="{{ asset('assets/admin/js/bootstrap-select.js') }}"></script>
	  <script>
         $(function() {
            $("#pro_select").change(function() {
               if ($("#pro_select").val() =='') {
               $("#pro_form").hide();
               } else {
               $("#pro_form").show();
               }
            }).trigger('change');
            $("#im_select").change(function() {
               if ($("#im_select").val() ==29) {
                  $("#im_form").show();
                  $("#file-contract").prop('required',true);
               }else{
                  $("#im_form").hide();
                  $("#file-contract").prop('required',false);
               }
            }).trigger('change');
         });
      </script>
      
   </body>
</html>