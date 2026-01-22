@extends('layouts.adminnew')
@section('content')

@php 
$name =  Auth::user()->name;
$account_id = Auth::user()->account_id;
$admin_id = Auth::user()->id;

if(isset(Auth::user()->userinfo->last_name))
$name = $name." ".Auth::user()->userinfo->last_name;

$permission = Auth::user();
$reg_count = $permission->noOfReg($account_id);
$faceid_count = $permission->noOfFaceids($account_id);
$takeover_count = $permission->noOfTakeover($account_id);
$inspection_count = $permission->noOfInspection($account_id);
$defect_count = $permission->noOfDefects($account_id);
$feedback_count = $permission->noOfFeedback($account_id);
$facilitybooking_count = $permission->noOfFacilityBooking($account_id);
$fileupload_count = $permission->noOfFileupload($account_id);
$vm_count = $permission->noOfVisitors($account_id);

@endphp
<style>
   .pofix{
	    position: fixed;
    margin-top: 40px;
}
/*
.tophead thead{    position: fixed;
    width: 83%;
    display: inherit;}*/
    @media screen and (max-width: 768px) {
    .tophead thead{    position: initial;
    width: 100%;
    display: inherit;}
	 .pofix{position: initial;     margin-top: 0px;}
    }
    @media only screen and (min-width: 768px) and (max-width: 991px) {
      .tophead thead{    position: initial;
    width: 100%;
    display: inherit;}
	 .pofix{position: initial;     margin-top: 0px;}
    }
</style>

<div class="status">
                  <h1>Ops Portal</h1>
               </div>
<div class="">
<div class="row rescol">
   @php
      $user =  $permission->check_menu_permission(7,$permission->role_id,1);
      if((isset($user) && $user->view==1) ){
         $access =  $permission->check_menu_permission_level(7,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=User&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp

         <div class="col-lg-3">
            <div class="serviceBox">
                 <a href="{{url('/opslogin/user')}}" {{$popup}} data-target="#mydata" class="datafech">
                  @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification">{{$reg_count}}</span>
                  @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/admin/img/user.png')}}"></span>
                User <br> Management 
                              </div>
                           </div>
                </a>
            </div>
         </div>

   @php
      }
      $announcement =  $permission->check_menu_permission(1,$permission->role_id,1);
      if((isset($announcement) && $announcement->view==1)){
         $access =  $permission->check_menu_permission_level(1,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Announcements data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
		   <div class="col-lg-3">
           <div class="serviceBox">
             <a href="{{url('/opslogin/announcement')}}" {{$popup}} class="datafech">
                      <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Announcements.png')}}"></span>
                Announcements
                              </div>
                      </div>
              </a>
          </div>
          </div>
   @php 
      }
      $accesscard =  $permission->check_menu_permission(38,$permission->role_id,1);
      if((isset($accesscard) && $accesscard->view>=1) ){
         $access =  $permission->check_menu_permission_level(38,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Access&nbsp;Card&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
         <div class="col-lg-3">
            <div class="serviceBox">
                 <a href="{{url('/opslogin/card')}}" {{$popup}} class="datafech">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/admin/img/Card.png')}}"></span>
                 Access Card<br> Management 
                              </div>
                           </div>
                </a>
            </div>
         </div>
   @php 
      }
      $device =  $permission->check_menu_permission(48,$permission->role_id,1);
      if((isset($device) && $device->view>=1)){
         $access =  $permission->check_menu_permission_level(48,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Device&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
         <div class="col-lg-3">
            <div class="serviceBox">
                 <a href="{{url('/opslogin/device')}}" {{$popup}} class="datafech">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/admin/img/Device.png')}}"></span>
                                 Device <br> Management 
                              </div>
                           </div>
                </a>
            </div>
            </div> 
   @php 
      }
      $faceid =  $permission->check_menu_permission(50,$permission->role_id,1);
      if((isset($faceid) && $faceid->view>=1) ){
         $access =  $permission->check_menu_permission_level(50,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Face&nbsp;Ids data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
            <div class="col-lg-3">
            <div class="serviceBox">
                 <a href="{{url('opslogin/faceid')}}" {{$popup}} class="datafech">
                 @if(isset($faceid_count) && $faceid_count >0 )
                  <span class="notification">{{$faceid_count}}</span>
                  @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/admin/img/Face.png')}}"></span>
                                 Face Ids
                              </div>
                           </div>
                </a>
            </div>
            </div>  <!--face-->   
   @php 
      }
      $takeover =  $permission->check_menu_permission(2,$permission->role_id,1);
      if((isset($takeover) && $takeover->view>=1) ){
         $access =  $permission->check_menu_permission_level(2,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Key&nbsp;Collection data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
           <div class="col-lg-3">
          <div class="serviceBox">
             <a href="{{url('/opslogin/takeover_appt/lists')}}" {{$popup}} class="datafech">
            @if(isset($takeover_count) && $takeover_count >0 )
             <span class="notification">{{$takeover_count}}</span>
            @endif
                  <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Key.png')}}"></span>
                                 Key Collection
                              </div>
                  </div>
              </a>
          </div>
          </div>
         
   @php 
      }
      $defects =  $permission->check_menu_permission(3,$permission->role_id,1);
      $defect_due_count = 0;
      if((isset($defects) && $defects->view>=1) ){
         $access =  $permission->check_menu_permission_level(3,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Defects data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $defect_due_count = $permission->noOfDueDefects($account_id);
            if($defect_due_count >0)
               $popup = "data-toggle=modal data-target=#dueModal";
            else
               $popup = '';
         }
   @endphp
           <div class="col-lg-3">
          <div class="serviceBox">
             <a href="{{url('/opslogin/defects')}}" {{$popup}}  class="datafech">
             @if(isset($defect_count) && $defect_count >0 )
             <span class="notification">{{$defect_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Defects.png')}}"></span>
                                 Defects
                              </div>
                           </div>
              </a>
          </div>
          </div>
		  <div class="modal" id="dueModal">
         <div class="modal-dialog">
            <div class="modal-content modal-content3">
               <div class="modal-body deletesur">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h2>Defects Alert!</h2>
                  <p>You have {{$defect_due_count}} defects that are over 21 days that are not completed.  </p>
                  <div class="clearfix"></div>
                  <a href="{{url('/opslogin/defects#defect')}}" class="backtofull">View</a>
               </div>
            </div>
         </div>
      </div>
         
   @php 
      }
      $inspection =  $permission->check_menu_permission(4,$permission->role_id,1);
      if((isset($inspection) && $inspection->view>=1) && 1==2){
         $access =  $permission->check_menu_permission_level(4,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Defect&nbsp;Inspection data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/inspection_appt/lists')}}" {{$popup}} class="datafech">
            @if(isset($inspection_count) && $inspection_count >0 )
             <span class="notification">{{$inspection_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Defects.png')}}"></span>
                                 Defect Inspection
                              </div>
                           </div>
              </a>
          </div>
          </div>
   @php 
      }
      $facility =  $permission->check_menu_permission(5,$permission->role_id,1);
      if((isset($facility) && $facility->view>=1) ){
         $access =  $permission->check_menu_permission_level(5,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Facillties&nbsp;Booking data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/facility?view=dashboard')}}" {{$popup}} class="datafech">
            @if(isset($facilitybooking_count) && $facilitybooking_count >0 )
             <span class="notification">{{$facilitybooking_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Booking.png')}}"></span>
                 Facillties Booking
                              </div>
                           </div>
            </a>
          </div>
          </div>
   @php 
      }
      $feedback =  $permission->check_menu_permission(6,$permission->role_id,1);
      if((isset($feedback) && $feedback->view>=1) ){
         $access =  $permission->check_menu_permission_level(6,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Feedback data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/feedbacks/summary?view=dashboard')}}" {{$popup}} class="datafech">
            @if(isset($feedback_count) && $feedback_count >0 )
             <span class="notification">{{$feedback_count}}</span>
            @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto">
                                 <span><img src="{{url('assets/img/Feedback.png')}}"></span>
                 Feedback
                              </div>
                           </div>
            </a>
          </div>
          </div>
          
   @php 
      }
      $condodocs =  $permission->check_menu_permission(32,$permission->role_id,1);
      if((isset($condodocs) && $condodocs->view>=1) ){
         $access =  $permission->check_menu_permission_level(32,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Condo&nbsp;Document data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
        <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/docs-category')}}" {{$popup}} class="datafech">
                  <div class="service-content imagew">
                     <div class="service-icon imagew my-auto setimg">
                        <span><img src="{{url('assets/img/Condo.png')}}"></span>
                           Condo  <br> Document
                     </div>
                  </div>
            </a>
        </div>
        </div>
   @php 
      }                   
      $fileupload =  $permission->check_menu_permission(33,$permission->role_id,1);
      if((isset($fileupload) && $fileupload->view>=1)){
         $access =  $permission->check_menu_permission_level(33,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Resident&nbsp;File&nbsp;Upload data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
        <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/residents-uploads')}}" {{$popup}} class="datafech">
               @if(isset($fileupload_count) && $fileupload_count >0 )
               <span class="notification">{{$fileupload_count}}</span>
               @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Resident.png')}}"></span>
                                 Resident’s  <br>  File Upload
                              </div>
                           </div>
            </a>
        </div>
        </div>
   @php 
      }                   
      $eform =  $permission->check_menu_permission(47,$permission->role_id,1);
      if((isset($eform) && $eform->view>=1)){
         $access =  $permission->check_menu_permission_level(47,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=E-Form&nbsp;Submissions data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
        <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/eform/moveinout#ef')}}" {{$popup}} class="datafech">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Form.png')}}"></span>
                                 E-Form  <br>  Submissions
                              </div>
                           </div>
            </a>
        </div>
        </div>

   @php 
      }                   
      $vm =  $permission->check_menu_permission(34,$permission->role_id,1);
      if((isset($vm) && $vm->view>=1)){
         $access =  $permission->check_menu_permission_level(34,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Visitor&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
        <div class="col-lg-3">
          <div class="serviceBox">
          <a href="{{url('opslogin/visitor-summary?view=dashboard')}}" {{$popup}} class="datafech">
            @if(isset($vm_count) && $vm_count >0 )
               <span class="notification">{{$vm_count}}</span>
               @endif
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Visitor.png')}}"></span>
                                 Visitor <br> Management
                              </div>
                           </div>
            </a>
        </div>
        </div>
        @php 
      }                   
      $rm =  $permission->check_menu_permission(61,$permission->role_id,1);
      if((isset($rm) && $rm->view>=1)){
         $access =  $permission->check_menu_permission_level(61,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Resident&nbsp;Management&nbsp; data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
        <div class="col-lg-3">
          <div class="serviceBox">
          <a href="{{url('opslogin/paymentoverview#vm')}}" class="datafech">
               <!--span class="notification"></span-->
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Residents.png')}}"></span>
                                 Resident <br> Management
                              </div>
                           </div>
            </a>
        </div>
        </div>

   @php 
      }                   
      $digital =  $permission->check_menu_permission(51,$permission->role_id,1);
      if((isset($digital) && $digital->view>=1)){
         $access =  $permission->check_menu_permission_level(51,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Open&nbsp;Door&nbsp;Records data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
   <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/digitalaccess/dooropen#odr')}}" {{$popup}} class="datafech">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Door.png')}}"></span>
                                 Open Door    <br>  Records
                              </div>
                           </div>
            </a>
        </div>
        </div>
        @php 
      }                   
      $digital =  $permission->check_menu_permission(76,$permission->role_id,1);
      if((isset($digital) && $digital->view>=1)){
         $access =  $permission->check_menu_permission_level(76,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=ResiChat data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
      <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('/opslogin/resichat#odr')}}">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/chat.png')}}"></span>
                                  ResiChat 
                              </div>
                           </div>
            </a>
        </div>
        </div>
        @php 
      }                   
      $digital =  $permission->check_menu_permission(77,$permission->role_id,1);
      if((isset($digital) && $digital->view>=1)){
         $access =  $permission->check_menu_permission_level(77,$account_id);
         if(isset($access) && $access->view ==2){
            $popup = "data-id=Marketplace data-toggle=modal data-target=#mydata class=datafech";
         }else if((isset($access) && $access->view ==1) || $admin_id ==1){
            $popup = '';
         }
   @endphp
   <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('/opslogin/marketplace#odr')}}">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/admin/img/marketplace.png')}}"></span>
                                  Marketplace 
                              </div>
                           </div>
            </a>
        </div>
        </div>
		<div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('/opslogin/magazine#odr')}}">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/admin/img/magazine.png')}}"></span>
                                  Magazine 
                              </div>
                           </div>
            </a>
        </div>
        </div>
		
        @php
      }
      @endphp
		  <div class="col-lg-3">
          <div class="serviceBox">
            <a href="https://new.aereaworld.com/index.php/aerea-manager-user-guide/" target="_blank">
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/admin/img/tutorial.png')}}"></span>
                                  User Tutorial 
                              </div>
                           </div>
            </a>
        </div>
        </div>
          <div class="col-lg-3">
          <div class="serviceBox">
            <a href="{{url('opslogin/configuration/landing')}}" >
                           <div class="service-content imagew">
                              <div class="service-icon imagew my-auto setimg">
                                 <span><img src="{{url('assets/img/Settings.png')}}"></span>
                  Settings 
                              </div>
                           </div>
            </a>
        </div>
        </div>
    </div>
 </div>
@stop
