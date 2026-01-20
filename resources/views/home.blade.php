@extends('layouts.front')



@section('content')

@php 
$name =  Auth::user()->name;

if(isset(Auth::user()->userinfo->last_name))
$name = $name." ".Auth::user()->userinfo->last_name;

$mytime = Carbon\Carbon::now();
  $permission = Auth::user();

  $annoucement_notification = $permission->noOfAnnouncement($permission->id);
@endphp
<div class="status">
                  <h1>Dashboard</h1>
               </div>
               <div class="col-lg-12 pl-0">
<div class="row">
          @php
          $announcement =  $permission->check_user_permission(1,$permission->id,1);
             if(isset( $announcement) && $announcement->view==1){
          @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('/admin/announcement')}}">
          @if($annoucement_notification >0) 
          <span class="notification">{{$annoucement_notification}}</span>
           @endif
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/megaphone.png')}}"></span>
                <p> Announcements</p>
                              </div>
                           </div>
                         </a>
                        </div>
          </div>
          
          @php 
          }
          $takeover =  $permission->check_user_permission(2,$permission->id,1);
          if(isset($takeover) && $takeover->view==1){
          @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('admin/book_appt')}}">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/calendar.png')}}"></span>
                <p> Appointment For<br>Unit Take Over</p>
                              </div>
                           </div>
                         </a>
                        </div>
          </div>
          
          @php 
          }
          $defects =  $permission->check_user_permission(3,$permission->id,1);
          if(isset( $defects) && $defects->view==1){
          @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="#">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/house.png')}}"></span>
                 <p>Defects list</p>
                              </div>
                           </div>
                         </a>
                        </div>
          </div>
     
         @php 
                    }
                      $inspection =  $permission->check_user_permission(4,$permission->id,1);
                      if(isset( $inspection) && $inspection->view==1){
                    @endphp

                    <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('admin/book_inspection')}}">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/Appointment.png')}}"></span>
                 <p>Appointment For<br>Joint Inspection</p>
                              </div>
                           </div>
                           </a>
                        </div>
          </div>
       

           @php 
                    }
                     $facility =  $permission->check_user_permission(5,$permission->id,1);
                     if(isset( $facility) && $facility->view==1){
                  @endphp
                  <div class="col-lg-3">
          <div class="serviceBox">
            <a href="#">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/booking.png')}}"></span>
                 <p>Facillties Booking</p>
                              </div>
                           </div>
                         </a>
                        </div>
          </div>


           @php 
                    }
                   
                    $feedback =  $permission->check_user_permission(6,$permission->id,1);
                    if(isset( $feedback) && $feedback->view==1){
                  @endphp
                  <div class="col-lg-3">
          <div class="serviceBox">
            <a href="#">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/feedback.png')}}"></span>
                 <p>Feedback</p>
                              </div>
                           </div>
                         </a>
                        </div>
          </div>
     
          @php 
                    }
                   
                    $feedback =  $permission->check_user_permission(9,$permission->id,1);
                    if(isset( $feedback) && $feedback->view==1){
                  @endphp
          
                  <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('admin/user/settings')}}">
                           <div class="service-content imagew">
                              <div class="service-icon  my-auto">
                                 <span><img src="{{url('assets/img/setting.png')}}"></span>
                 <p>Settings</p>
                              </div>
                           </div>
                           </a>
                        </div>
          </div>
          </div>
           @php 
                     } 
          @endphp
               </div>
   
@stop
