@extends('layouts.adminnew')



@section('content')

@php 
$name =  Auth::user()->name;

if(isset(Auth::user()->userinfo->last_name))
$name = $name." ".Auth::user()->userinfo->last_name;

  $permission = Auth::user();

$takeover_count = $permission->noOfTakeover();
$inspection_count = $permission->noOfInspection();
@endphp
<div class="status">
                  <h1>Ops Portal</h1>
               </div>
<div class="">
<div class="row">
<div class="col-lg-4">
            <div class="serviceBox">
        
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/admin/img/user.png')}}"></span>
                 <a href="{{url('/admin/user')}}">User Management</a>
                              </div>
                           </div>
                        </div>
                        </div>

          @php
          $announcement =  $permission->check_menu_permission(1,$permission->role_id,1);
             if(isset( $announcement) && $announcement->view==1){
          @endphp
          <div class="col-lg-4">
           <div class="serviceBox">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/megaphone.png')}}"></span>
                 <a href="{{url('/admin/announcement')}}">Announcements</a>
                              </div>
                           </div>
                        </div>
                        </div>
         
          @php 
          }
          $takeover =  $permission->check_menu_permission(2,$permission->role_id,1);
          if(isset($takeover) && $takeover->view==1){
          @endphp
          <div class="col-lg-4">
          <div class="serviceBox">
            @if(isset($takeover_count) && $takeover_count >0 )
             <span class="notification">{{$takeover_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/calendar.png')}}"></span>
                 <a href="{{url('takeover_appt/lists')}}">Appointment For<br>Unit Take Over</a>
                              </div>
                           </div>
                        </div>
                        </div>
          @php 
          }
          $defects =  $permission->check_menu_permission(1,$permission->role_id,1);
          if($defects->view==1){
          @endphp
          <div class="col-lg-4">
          <div class="serviceBox">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/house.png')}}"></span>
                 <a href="#">Defects list</a>
                              </div>
                           </div>
                        </div>
                        </div>
         @php 
                    }
                      $inspection =  $permission->check_menu_permission(4,$permission->role_id,1);
                      if($inspection->view==1){
                    @endphp

                    <div class="col-lg-4">
          <div class="serviceBox">
            @if(isset($inspection_count) && $inspection_count >0 )
             <span class="notification">{{$inspection_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/Appointment.png')}}"></span>
                 <a href="{{url('admin/book_inspection')}}">Appointment For<br>Joint Inspection</a>
                              </div>
                           </div>
                        </div>
                        </div>

           @php 
                    }
                     $facility =  $permission->check_menu_permission(5,$permission->role_id,1);
                    if($facility->view==1){
                  @endphp
                  <div class="col-lg-4">
          <div class="serviceBox">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/booking.png')}}"></span>
                 <a href="#">Facillties Booking</a>
                              </div>
                           </div>
                        </div>
                        </div>

           @php 
                    }
                   
                    $feedback =  $permission->check_menu_permission(6,$permission->role_id,1);
                    if($feedback->view==1){
                  @endphp
                  <div class="col-lg-4">
          <div class="serviceBox">
          <span class="notification">1</span>
                           <div class="service-content imagew">
                              <div class="service-icon ">
                                 <span><img src="{{url('assets/img/feedback.png')}}"></span>
                 <a href="#">Feedback</a>
                              </div>
                           </div>
                        </div>
                        </div>
           @php 
                     } 
          @endphp
          <div class="col-lg-4">
          <div class="serviceBox">
                           <div class="service-content imagew">
                              <div class="service-icon ">
                                 <span><img src="{{url('assets/img/setting.png')}}"></span>
                 <a href="settings.html">Settings</a>
                              </div>
                           </div>
                        </div>
                        </div>
    </div>
 </div>
   
@stop
