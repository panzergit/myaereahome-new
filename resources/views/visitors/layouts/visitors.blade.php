<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aerea Home</title>

    <!-- Scripts -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="{{ asset('assets/img/visitors/favicon.png') }} " rel="icon">
      <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/css/visitors_style.css')}}">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
      <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
      <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<section>
   <div class="row">  
     
      <div class="col-lg-12 bgsec1">
       
      <div class="row">
         <div class=" col-lg-4">&nbsp;</div>
         <div class=" col-lg-4"><br><img src="{{asset('assets/img/aereabl.png')}}" class="araimg"></div>
         <div class=" col-lg-4" >&nbsp</div>
        </div>   
            @yield('content')  
      </div>
   </div>
</section>
<script src=" {{ asset('assets/js/jquery.min.js') }}"></script>
<script src=" {{ asset('assets/js/bootstrap.min.js') }}"></script>
<script>
   function showmore(){
      var row = $("#rowcount").val();
      var max_row = $("#maxcount").val();
      var new_row = Number(row)+ 1;
            
      $("#add_field"+new_row).show();
      $("#rowcount").val(new_row);

      if(new_row == max_row)
         $("#buttonSection").hide();
   }
   function hidevisitor(divid){         
      $("#add_field"+divid).hide();
      
      $("#name_"+divid).val("");
      $("#mobile_"+divid).val("");
      $("#vehicle_no_"+divid).val("");
      $("#email_"+divid).val("");
      $("#id_number_"+divid).val("");

      
   }

   $("#reg_form").submit(function(){
      
      var row = $("#rowcount").val();
      
      var invalid = false;
      for(var i=1; i<=row; i++){
         var name = "#name_"+i;
         var mobile = "#mobile_"+i;
         if($(name).val() !='' && $(mobile).val() ==''){
            alert("Vistor "+i+" mobile number should not be empty.")
            invalid = true;
            return false;

         }
         if($(name).val() =='' && $(mobile).val() !=''){
            alert("Vistor "+i+" name should not be empty.")
            invalid = true;
            return false;
         }

      }
      return true;
  
   });

   
</script>

</body>
</html>
