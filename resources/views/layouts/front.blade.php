<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">
  <title>{{ config('app.name', 'Jui Residence | Portal') }}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="{{ asset('assets/img/favicon.png') }} " rel="icon">
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" type="text/css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
      
      <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel='stylesheet' href="{{ asset('assets/css/bootstrap-datetimepicker.css') }}">

 
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
    <body>
     

@php 
$name =  Auth::user()->name;

if(isset(Auth::user()->userinfo->last_name))
$name = $name." ".Auth::user()->userinfo->last_name;

$mytime = Carbon\Carbon::now();
  $permission = Auth::user();

  $annoucement_notification = $permission->noOfAnnouncement($permission->id);
 
   $img_full_path = env('APP_URL') . "/storage/app/";

@endphp

      <section>
         <div class="row">
            <div class="col-lg-2 col3">
               <a href="{{url('/admin/home')}}"> <img src="{{url('assets/img/logo.png')}}" class="logo"></a>
               <ul class="annonce">
                 <li {{ (Request::is('admin/home*') ? 'class=active' : '') }} >
                     <a href="{{url('/admin/home')}}" class="anone notification2">Home
                    </a>
                  </li>

                  @php
                    $announcement =  $permission->check_user_permission(1,$permission->id,1);
                    if(isset( $announcement) && $announcement->view==1){
                  @endphp
                  <li {{ (Request::is('admin/announcement*') ? 'class=active' : '') }} >
                     <a href="{{url('/admin/announcement')}}" class="anone notification2">Announcements
                      @if($annoucement_notification >0) 
                        <span class="">{{$annoucement_notification}}</span>
                      @endif
                    </a>
                  </li>
                   @php 
                    }
                    $user =  $permission->check_user_permission(7,$permission->id,1);
                    if(isset($user) && $user->view==1){
                  @endphp

                   <li {{ (Request::is('admin/user*') ? 'class=active' : '') }}>
                     <a href="{{url('admin/user')}}" class="anone">User Management</a>
                  </li>

                   @php 
                      }
                      $takeover =  $permission->check_user_permission(2,$permission->id,1);
                      if(isset($takeover) && $takeover->view==1){
                    @endphp

                  <li >
                     <p><a href="{{url('admin/book_appt')}}" {{ (Request::is('admin/book_appt') ? 'class=actives' : '') }}>Appointment For<br />
                        Unit Take Over
                      </a>
                     </p>
                     <a href="{{url('admin/book_appt/message')}}" {{ (Request::is('admin/book_appt/message') ? 'class=actives' : '') }}>- Check Appointment</a><br />
                     
                  </li>
                   @php 
                     }
                    $defects =  $permission->check_user_permission(3,$permission->id,1);
                    if(isset($defects) && $defects->view==1){
                  @endphp
                 <li  {{ (Request::is('admin/defects*') ? 'class=active' : '') }}>
                     <p><a href="{{url('admin/defect/submit')}}" {{ (Request::is('admin/defect/submit') ? 'class=actives' : '') }}>Defects list</a></p>
                     <a href="{{url('admin/defect/lists')}}" {{ (Request::is('admin/defect/lists') ? 'class=actives' : '') }}>- Submitted Lists</a>
                  </li>
                   @php 
                    }
                      $inspection =  $permission->check_user_permission(4,$permission->id,1);
                      if(isset($inspection) && $inspection->view==1){
                    @endphp

                  <li>
                     <p><a href="{{url('admin/book_inspection')}}" {{ (Request::is('admin/book_inspection') ? 'class=actives' : '') }}>Appointment For<br />
                        Joint Inspection
                      </a>
                     </p>
                     <a href="{{url('admin/book_inspection/message')}}"  {{ (Request::is('admin/book_inspection/message') ? 'class=actives' : '') }}>- Check Appointment</a><br />
                     
                  </li>
                   @php 
                    }
                    $feedback =  $permission->check_user_permission(6,$permission->id,1);
                    if(isset($feedback) && $feedback->view==1){
                  @endphp
                 <li>
                     <p><a href="{{url('admin/feedback/submit')}}" {{ (Request::is('admin/feedback/submit') ? 'class=actives' : '' ) }}>Feedback</a></p>
                     <a href="{{url('admin/feedback/lists')}}" {{ (Request::is('admin/feedback/lists') ? 'class=actives' : '' ) }}>- Feedback Status</a>
                  </li>
                   @php 
                    }
                    $facility =  $permission->check_user_permission(5,$permission->id,1);
                    if(isset($facility) && $facility->view==1){
                  @endphp
                  
                  <li>
                     <p>Facillties Booking</p>
                     <a href="#">- Check Booking</a>
                  </li>
                   @php 
                    }
                    $settings =  $permission->check_user_permission(9,$permission->id,1);
                    if(isset($facility) && $facility->view==1){
                  @endphp
                  
                  <li {{ (Request::is('admin/user/settings*') ? 'class=active' : '') }}>
                     <p> <a href="{{url('admin/user/settings')}}">Settings</a></p>
                  </li>
                   @php 
                     } 
                     
                    @endphp
                  
                  <li class="userlog">
                    @php if(isset(Auth::user()->userinfo->profile_picture) && Auth::user()->userinfo->profile_picture !='') {@endphp 
                          <img src="{{$img_full_path}}{{Auth::user()->userinfo->profile_picture}}">
              
                      @php } else {@endphp
                     <img src="{{url('assets/img/user.png')}}">
                      @php } @endphp

                     <h3>{{$name}}</h3>
                     <h4>{{isset( Auth::user()->userinfo->getunit->unit)? '#'.Auth::user()->userinfo->getunit->unit:''}}</h4>

                     

                      <a href="{{url('logout')}}" class="logicon">Logout <img src="{{url('assets/img/logout.png')}}"></a>
                    
                  </li>
               </ul>
            </div>
            <div class="col-lg-10 col9">
               <table class="table tablefbor tophead">
                  <thead>
                     <tr>
                        <th scope="col">Hi {{$name}}</th>

                        <th scope="col"></th>
                        <th scope="col" >{{date('d M Y')}}</th>
                     </tr>
                  </thead>
               </table>
               <div class="">
                  <div class="">
                    @yield('content')
                  </div>
               </div>
            </div>
         </div>
      </section>
  <!-- /.content-wrapper -->

  

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.js') }}"></script>
<script>
var date = new Date();
var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

var optSimple = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox'

 
};

$( '#appt_date' ).datepicker( optSimple );


$( '#appt_date' ).datepicker( 'setDate', today );

$('#appt_date').datepicker()
    .on('changeDate', function(e) {
       getTakeoverTimeslots(); 
});


$( '#inspection_date' ).datepicker( optSimple );


$( '#inspection_date' ).datepicker( 'setDate', today );

$('#inspection_date').datepicker()
    .on('changeDate', function(e) {
       getInspectionTimeslots(); 
});


</script>
      <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>

 



@yield('customJS')



<script>
 
 function showmore(){
            var row = $("#rowcount").val();
            var new_row = Number(row)+ 1;
            
            $("#add_field"+new_row).show();
            $("#rowcount").val(new_row);
         }
  


function getTakeoverTimeslots(){
 
  var selectedDate =  $("#appt_date").val();
   if(selectedDate)
               {
                  $.ajax({
                     url : "{!!URL:: route('gettakeovertimeslots')!!}",
                     dataType : "json",
                     data:{
                      date:selectedDate
                     },
                     success:function(data)
                     {
                        //console.log(data);
                        $('#timeslotstables').empty();
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                         if(rec.count <=0){
                           $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'"><span class="checkmark">'+ rec.time +'</span></label>');
                         }
                          else{
                            $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'"><span class="checkmark taken">'+ rec.time +'</span></label>');
                          }
                        });
                     }
                  });
               }
               else
               {
                  $('select[name="user[]"]').empty();
               }

}


function getInspectionTimeslots(){
 
  var selectedDate =  $("#inspection_date").val();
   if(selectedDate)
               {
                  $.ajax({
                     url : "{!!URL:: route('getinspectiontimeslots')!!}",
                     dataType : "json",
                     data:{
                      date:selectedDate
                     },
                     success:function(data)
                     {
                        //console.log(data);
                        $('#timeslotstables').empty();
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                         // const json =rec;
                          //const obj = JSON.parse(rec);
                          //console.log(rec.time);
                         
                          if(rec.count <=0){
                           $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'"><span class="checkmark">'+ rec.time +'</span></label>');
                         }
                          else{
                            $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'"><span class="checkmark taken">'+ rec.time +'</span></label>');
                          }
                           

                        });
                     }
                  });
               }
               else
               {
                  $('select[name="user[]"]').empty();
               }

}

function getuserslist(){

 var roleId = $("#role").val();
              
               if(roleId)
               {
                  $.ajax({
                     url : "{!!URL:: route('getuserlist')!!}",
                     dataType : "json",
                     data:{
                      role:roleId
                     },
                     success:function(data)
                     {
                        console.log(data);
                        $('select[name="user[]"]').empty();
                        $('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,value){
                           $('select[name="user[]"]').append('<option value="'+ id +'">'+ value +'</option>');
                        });
                     }
                  });
               }
               else
               {
                  $('select[name="user[]"]').empty();
               }
}


   
function delete_record(url)
{ 
 doyou = confirm("Do you want to delete this record? All related data will be deleted (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

function cancel_record(url)
{ 
 doyou = confirm("Do you want to cancel this claim? you can't revert (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}





//Confirmation Due Date  End //
  
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>
