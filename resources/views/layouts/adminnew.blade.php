<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="-1">
      <title>Aerea Home | Ops Portal</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="{{ asset('assets/img/favicon.png') }} " rel="icon">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/style2.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-select.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/tree.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.scrolling-tabs.min.css') }}">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link rel='stylesheet' href="{{ asset('assets/admin/css/bootstrap-datetimepicker.css') }}">
      <meta name="csrf-token" content="{{ csrf_token() }}">
	   <link rel="stylesheet" href="{{ asset('assets/admin/css/all.css') }}">
      <link rel='stylesheet' href="{{ asset('assets/admin/css/select2.css') }}">
      <meta name="csrf-token" content="{{ csrf_token() }}">
	   <style>
	  .dropdown1 a {
         color: #495057;
         font-size: 14px;
         font-weight: 600;
      }
      .dropdown1 p {
         
         margin-bottom: 0px;
      }
      .dropdown1 a:hover {
         color: #495057;
         font-size: 14px;
         font-weight: 600;
      }
      .dropdown-container1:focus {
      outline: none;
      }

.dropdown-container1:focus .dropdown1 {
  opacity: 1;
  z-index: 100;
  max-height: 100vh;
  transition: opacity 0.2s, z-index 0.2s, max-height: 0.2s;
}

.dropdown-container1:focus-within .dropdown1 {display:block;}
.dropdown1 {display:none;}

      </style>
   </head>
    <body>
      @php 
      $loggedInUser = request()->user();
      $admin_id = $loggedInUser->id;
      $account_id = $loggedInUser->account_id;
      $takeover_count = $loggedInUser->noOfTakeover($account_id);
      $inspection_count = $loggedInUser->noOfInspection($account_id);
      $faceid_count = $loggedInUser->noOfFaceids($account_id);
      $defect_count = $loggedInUser->noOfDefects($account_id);
      $feedback_count = $loggedInUser->noOfFeedback($account_id);
      $facilitybooking_count = $loggedInUser->noOfFacilityBooking($account_id);
      $fileupload_count = $loggedInUser->noOfFileupload($account_id);
      $vm_count = $loggedInUser->noOfVisitors($account_id);
      $moveinout_count = $loggedInUser->noOfMovinginout($account_id);
      $renovation_count = $loggedInUser->noOfRenovation($account_id);
      $regvehicle_count = $loggedInUser->noOfRegvehicle($account_id);
      $dooraccess_count = $loggedInUser->noOfDooraccess($account_id);
      $mailling_count = $loggedInUser->noOfMailing($account_id);
      $particular_count = $loggedInUser->noOfparticulars($account_id);
      $verification_count = $loggedInUser->noOfPendingVerificationPayment($account_id);
      $eform_total = $moveinout_count + $renovation_count + $regvehicle_count + $dooraccess_count + $mailling_count + $particular_count;
      $img_full_path = env('APP_URL') . "/storage/app/";
      
         $logo_path = ($loggedInUser->propertyinfo && trim($loggedInUser->propertyinfo->company_logo)!="") 
            ? Storage::disk('s3')->url($loggedInUser->propertyinfo->company_logo) : null;

      @endphp
      <section class="headersec">
         <div class="container">
            <div class="row">
               <div class="col-lg-9 col-6">
                  <div class=" top-nav">
                     <input id="menu-toggle" type="checkbox" />
                     <label class='menu-button-container' for="menu-toggle">
                        <div class='menu-button'></div>
                     </label>
                     <nav id="sidebar" class="sidebar-wrapper menu">
                        <div class="sidebar-content">
                           <div class="sidebar-menu">
                              <ul>
                                 @php
                                 $user =  $loggedInUser->check_menu_permission(7,$loggedInUser->role_id,1);
                                 if((isset($user) && $user->view==1) || Auth::user()->id ==1){
                                    $access =  $loggedInUser->check_menu_permission_level(7,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=User&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 
                                 @endphp
                                    <li> <a href="{{url('/opslogin/user')}}"><span class="sideimg"><img src="{{url('assets/admin/img/user.png')}}"></span> User Management</a></li>
                                 @php 
                                 }
                                 $announcement =  $loggedInUser->check_menu_permission(1,$loggedInUser->role_id,1);
                                 if(isset( $announcement) && $announcement->view==1){
                                    $module_permission = $loggedInUser->check_permission(1,$loggedInUser->role_id); 
                                    $access =  $loggedInUser->check_menu_permission_level(1,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Announcement data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/announcement')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Announcements.png')}}"></span> Announcements</a></li>
                                 @php 
                                 }
                                 $cards =  $loggedInUser->check_menu_permission(38,$loggedInUser->role_id,1);
                                 if(isset( $cards) && $cards->view==1){
                                    $module_permission = $loggedInUser->check_permission(38,$loggedInUser->role_id); 
                                    $access =  $loggedInUser->check_menu_permission_level(38,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Access&nbsp;Card&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp

                                    <li> <a href="{{url('/opslogin/card')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Card.png')}}"></span> Access Card Management</a></li>
                                 @php 
                                 }
                                 $device =  $loggedInUser->check_menu_permission(48,$loggedInUser->role_id,1);
                                 if(isset( $device) && $device->view==1){
                                    $module_permission = $loggedInUser->check_permission(48,$loggedInUser->role_id); 
                                    $access =  $loggedInUser->check_menu_permission_level(48,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Device&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/device')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Device.png')}}"></span> Device Management</a></li>
                                 @php 
                                 }
                                 $faceids =  $loggedInUser->check_menu_permission(50,$loggedInUser->role_id,1);
                                 if(isset($faceids) && $faceids->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(50,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Face&nbsp;Ids data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/faceid#fi')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Face.png')}}"></span> Face Ids</a></li>
                                 @php 
                                 }
                                 $takeover =  $loggedInUser->check_menu_permission(2,$loggedInUser->role_id,1);
                                 if(isset( $takeover) && $takeover->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(2,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Key&nbsp;Collection data-toggle=modal data-target=#mydata class=datafech";
                                    }
                                    else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/takeover_appt/lists#kc')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Key.png')}}"></span> Key Collection</a></li>
                                 @php 
                                 }
                                 $defects =  $loggedInUser->check_menu_permission(3,$loggedInUser->role_id,1);
                                 if(isset($defects) && $defects->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(3,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Defects data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/defects#defect')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Defects.png')}}"></span> Defects</a></li>
                                 @php 
                                 }  
                                 $facility =  $loggedInUser->check_menu_permission(5,$loggedInUser->role_id,1);
                                 if(isset($facility) &&   $facility->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(5,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Facility&nbsp;Booking data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/facility#fb')}}"><span class="sideimg"><img src="{{url('assets/admin/img/booking.png')}}"></span> Facilities Booking</a></li>
                                 @php 
                                 } 
                                 $feedback =  $loggedInUser->check_menu_permission(6,$loggedInUser->role_id,1);
                                 if(isset($feedback) &&  $feedback->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(6,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Feedbacks data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/feedbacks/summary#fb')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Feedback.png')}}"></span> Feedback</a></li>
                                 @php 
                                 }
                                 $condodocs =  $loggedInUser->check_menu_permission(32,$loggedInUser->role_id,1);
                                 if(isset($condodocs) &&  $condodocs->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(32,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Condo&nbsp;Document data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/docs-category#cd')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Condo.png')}}"></span> Condo Document</a></li>
                                 @php 
                                 }
                                 $fileupload =  $loggedInUser->check_menu_permission(33,$loggedInUser->role_id,1);
                                 if(isset($fileupload) &&  $fileupload->view >=1){
                                    $access =  $loggedInUser->check_menu_permission_level(33,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Resident’s&nbsp;File&nbsp;Upload data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/residents-uploads#rfu')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Resident.png')}}"></span> Residents File Upload</a></li>
                                 @php 
                                 }
                                 $e_movein =  $loggedInUser->check_menu_permission(40,$loggedInUser->role_id,1);
                                 $e_renovation=  $loggedInUser->check_menu_permission(41,$loggedInUser->role_id,1);
                                 $e_card =  $loggedInUser->check_menu_permission(42,$loggedInUser->role_id,1);
                                 $e_vehicle =  $loggedInUser->check_menu_permission(43,$loggedInUser->role_id,1);
                                 $e_addr =  $loggedInUser->check_menu_permission(44,$loggedInUser->role_id,1);
                                 $e_info =  $loggedInUser->check_menu_permission(45,$loggedInUser->role_id,1);
                                 if((isset($e_movein) && $e_movein->view>=1) || (isset($e_renovation->view) && $e_renovation->view ==1) || (isset($e_card->view) && $e_card->view ==1)|| (isset($e_vehicle->view) && $e_vehicle->view ==1)|| (isset($e_addr->view) && $e_addr->view ==1) || (isset($e_info->view) && $e_info->view ==1)){
                                    if(isset($e_movein->view) && $e_movein->view ==1)
                                       $eform_url = url('/opslogin/eform/moveinout#ef');
                                    else if(isset($e_renovation->view) && $e_renovation->view ==1)
                                       $eform_url = url('/opslogin/eform/renovation#ef');
                                    else if(isset($e_card->view) && $e_card->view ==1)
                                       $eform_url = url('/opslogin/eform/dooraccess#ef');
                                    else if(isset($e_vehicle->view) && $e_vehicle->view ==1)
                                       $eform_url = url('/opslogin/eform/regvehicle#ef');
                                    else if(isset($e_addr->view) && $e_addr->view ==1)
                                       $eform_url = url('/opslogin/eform/changeaddress#ef');
                                    else if(isset($e_info->view) && $e_info->view ==1)
                                       $eform_url = url('/opslogin/eform/particular#ef');
                                    else 
                                       $eform_url = '#';

                                    $access1 =  $loggedInUser->check_menu_permission_level(40,$account_id);
                                    $access2 =  $loggedInUser->check_menu_permission_level(41,$account_id);
                                    $access3 =  $loggedInUser->check_menu_permission_level(42,$account_id);
                                    $access4 =  $loggedInUser->check_menu_permission_level(43,$account_id);
                                    $access5 =  $loggedInUser->check_menu_permission_level(44,$account_id);
                                    $access6 =  $loggedInUser->check_menu_permission_level(45,$account_id);

                                    if((isset($access1) && $access1->view ==2) && (isset($access2) && $access2->view ==2) && (isset($access3) && $access3->view ==2) && (isset($access4) && $access4->view ==2) && (isset($access5) && $access5->view ==2) && (isset($access6) && $access6->view ==2)){
                                       $popup = "data-id=E-Form&nbsp;Submissions data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                              @endphp  
                                    <li> <a href="{{$eform_url}}"><span class="sideimg"><img src="{{url('assets/admin/img/Form.png')}}"></span> E-Form Submissions</a></li>
                                 @php 
                                 }
                                 $vm =  $loggedInUser->check_menu_permission(34,$loggedInUser->role_id,1);
                                 if(isset($vm) &&  $vm->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(34,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Visitor&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/visitor-summary#vm')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Visitor.png')}}"></span> Visitor Management</a></li>
                                 @php 
                                 }
                                 $rm =  $loggedInUser->check_menu_permission(60,$loggedInUser->role_id,1);
                                 $rm1 =  $loggedInUser->check_menu_permission(61,$loggedInUser->role_id,1);
                                 $rm2 =  $loggedInUser->check_menu_permission(62,$loggedInUser->role_id,1);
                                 $rm3 =  $loggedInUser->check_menu_permission(71,$loggedInUser->role_id,1);
                                 $rm4 =  $loggedInUser->check_menu_permission(72,$loggedInUser->role_id,1);
                                 if((isset($rm) &&  $rm->view==1) || (isset($rm1) &&  $rm1->view==1) || (isset($rm2) &&  $rm2->view==1) || (isset($rm3) &&  $rm3->view==1) || (isset($rm4) &&  $rm4->view==1)){
                                    $access1 =  $loggedInUser->check_menu_permission_level(61,$account_id);
                                    $access2 =  $loggedInUser->check_menu_permission_level(62,$account_id);
                                    $access3 =  $loggedInUser->check_menu_permission_level(63,$account_id);
                                    $access4 =  $loggedInUser->check_menu_permission_level(71,$account_id);
                                    $access5 =  $loggedInUser->check_menu_permission_level(72,$account_id);

                                    if((isset($access1) && $access1->view ==2) && (isset($access2) && $access2->view ==2) && (isset($access3) && $access3->view ==2) && (isset($access4) && $access4->view ==2) && (isset($access5) && $access5->view ==2)){
                                       $popup = "data-id=Residence&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                    $batch_count = $loggedInUser->check_importcsv_permission($account_id);
                                 @endphp
                                    <li> <a href="{{url('/opslogin/paymentoverview#vm')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Residents.png')}}"></span> Resident Management</a></li>
                                 @php 
                                 }
                                 $supplier =  $loggedInUser->check_menu_permission(79,$loggedInUser->role_id,1);
                                 if((isset($supplier) &&  $supplier->view==1) ){
                                    $supplier =  $loggedInUser->check_menu_permission_level(79,$account_id);
                                    if((isset($supplier) && $supplier->view ==2)){
                                       $popup = "data-id=Supplier&nbsp;Management data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($supplier) && $supplier->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                   
                                 @endphp
                                    <li> <a href="{{url('/opslogin/supplier#vm')}}"><span class="sideimg"><img src="{{url('assets/admin/img/supplier.png')}}"></span> Supplier Management</a></li>
                                 @php 
                                 }
                                 $dooropen =  $loggedInUser->check_menu_permission(56,$loggedInUser->role_id,1);
                                 $bluetooth =  $loggedInUser->check_menu_permission(69,$loggedInUser->role_id,1);
                                 $callunit =  $loggedInUser->check_menu_permission(67,$loggedInUser->role_id,1);
                                 $doorfailed =  $loggedInUser->check_menu_permission(66,$loggedInUser->role_id,1);
                                 $qrcode =  $loggedInUser->check_menu_permission(68,$loggedInUser->role_id,1);

                                 //$digital =  $loggedInUser->check_menu_permission(51,$loggedInUser->role_id,1);
                                 if((isset($dooropen) && $dooropen->view>=1) || (isset($bluetooth) && $bluetooth->view>=1) || (isset($callunit) && $callunit->view>=1) ||(isset($doorfailed) && $doorfailed->view>=1) || (isset($qrcode) && $qrcode->view>=1)){

                                    if(isset($dooropen->view) && $dooropen->view ==1)
                                       $digital_url = url('/opslogin/digitalaccess/dooropen#odr');
                                    else if(isset($bluetooth->view) && $bluetooth->view ==1)
                                       $digital_url = url('/opslogin/digitalaccess/bluetoothdooropen#odr');
                                    else if(isset($callunit->view) && $callunit->view ==1)
                                       $digital_url = url('/opslogin/digitalaccess/callunit#odr');
                                    else if(isset($doorfailed->view) && $doorfailed->view ==1)
                                       $digital_url = url('/opslogin/digitalaccess/dooropenfailed#odr');
                                    else if(isset($qrcode->view) && $qrcode->view ==1)
                                       $digital_url = url('/opslogin/digitalaccess/qropenrecords#odr');
                                    else 
                                       $digital_url = '#';

                                    $access1 =  $loggedInUser->check_menu_permission_level(56,$account_id);
                                    $access2 =  $loggedInUser->check_menu_permission_level(69,$account_id);
                                    $access3 =  $loggedInUser->check_menu_permission_level(67,$account_id);
                                    $access4 =  $loggedInUser->check_menu_permission_level(66,$account_id);
                                    $access5 =  $loggedInUser->check_menu_permission_level(68,$account_id);

                                    if((isset($access1) && $access1->view ==2) || (isset($access2) && $access2->view ==2) || (isset($access3) && $access3->view ==2) || (isset($access4) && $access4->view ==2) || (isset($access5) && $access5->view ==2)){
                                       $popup = "data-id=Open&nbsp;Door&nbsp;Records data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/digitalaccess/dooropen#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Door.png')}}"></span> Open Door Records</a></li>
                                 @php 
                                 }

                                 $digital =  $loggedInUser->check_menu_permission(76,$loggedInUser->role_id,1);
                                 if(isset($digital) &&  $digital->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(76,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Chatter&nbsp;Box data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/resichat#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/chat.png')}}"></span> ResiChat</a></li>
                                 @php 
                                 }
                                

                                 $digital =  $loggedInUser->check_menu_permission(77,$loggedInUser->role_id,1);
                                 if(isset($digital) &&  $digital->view==1){
                                    $access =  $loggedInUser->check_menu_permission_level(76,$account_id);
                                    if(isset($access) && $access->view ==2){
                                       $popup = "data-id=Chatter&nbsp;Box data-toggle=modal data-target=#mydata class=datafech";
                                    }else if((isset($access) && $access->view ==1) || $admin_id ==1){
                                       $popup = '';
                                    }
                                 @endphp
                                    <li> <a href="{{url('/opslogin/marketplace#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/marketplace.png')}}"></span> Marketplace</a></li>
                                 @php 
                                 }
                                 
                                 if($admin_id ==1){
                                 @endphp
                                    <li> <a href="{{url('/opslogin/magazine#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/magazine.png')}}"></span> Magazine</a></li>
                                    <li> <a href="{{url('/opslogin/userguide#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/tutorial.png')}}"></span> User Guide</a></li>
                                    <li> <a href="{{url('/opslogin/resichat#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/chat.png')}}"></span> ResiChat</a></li>
                                    <li> <a href="{{url('/opslogin/marketplace#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/marketplace.png')}}"></span> Marketplace</a></li>
                                    <li> <a href="{{url('/opslogin/loghistory#odr')}}"><span class="sideimg"><img src="{{url('assets/admin/img/history.png')}}"></span> Activity Log</a></li>
                                 @php 
                                 }
                                 
                                 $settings =  $loggedInUser->check_menu_permission(9,$loggedInUser->role_id,1);
                                 $module =  $loggedInUser->check_menu_permission(22,$loggedInUser->role_id,1);
                                 $role =  $loggedInUser->check_menu_permission(23,$loggedInUser->role_id,1);
                                 $building =  $loggedInUser->check_menu_permission(49,$loggedInUser->role_id,1);
                                 $unit =  $loggedInUser->check_menu_permission(24,$loggedInUser->role_id,1);
                                 $menu =  $loggedInUser->check_menu_permission(25,$loggedInUser->role_id,1);
                                 $feedback =  $loggedInUser->check_menu_permission(26,$loggedInUser->role_id,1);
                                 $defect =  $loggedInUser->check_menu_permission(27,$loggedInUser->role_id,1);
                                 $property =  $loggedInUser->check_menu_permission(28,$loggedInUser->role_id,1);
                                 $facility =  $loggedInUser->check_menu_permission(29,$loggedInUser->role_id,1);
                                 $vm =  $loggedInUser->check_menu_permission(37,$loggedInUser->role_id,1);
                                 $eforms =  $loggedInUser->check_menu_permission(39,$loggedInUser->role_id,1);
                                 $payment =  $loggedInUser->check_menu_permission(46,$loggedInUser->role_id,1);
                                 $holiday =  $loggedInUser->check_menu_permission(53,$loggedInUser->role_id,1);
                                 $building =  $loggedInUser->check_menu_permission(49,$loggedInUser->role_id,1);
                                 $dashmenu =  $loggedInUser->check_menu_permission(55,$loggedInUser->role_id,1);
                                 $key_setting =  $loggedInUser->check_menu_permission(9,$loggedInUser->role_id,1);
                                 $inspection_setting =  $loggedInUser->check_menu_permission(57,$loggedInUser->role_id,1);
                                 $sharesetting =  $loggedInUser->check_menu_permission(63,$loggedInUser->role_id,1);

                                 if(isset($settings->view) && $settings->view ==1 || isset($module->view) && $module->view ==1 ||  isset($role->view) && $role->view ==1 || isset($unit->view) && $unit->view ==1 || isset($menu->view) &&  $menu->view ==1 || isset($property->view) &&  $property->view ==1 || isset($defect->view) &&  $defect->view ==1 || isset($feedback->view) &&  $feedback->view ==1 || isset($facility->view) &&  $facility->view ==1 || isset($vm->view) &&  $vm->view ==1 || isset($eforms->view) &&  $eforms->view ==1 || isset($payment->view) && $payment->view ==1 ||  isset($building->view) &&  $building->view ==1 ||  isset($key_setting->view) &&  $key_setting->view >=1||  (isset($inspection_setting->view) &&  $inspection_setting->view >=1)){
                                 @endphp
                                    <li> <a href="{{url('/opslogin/configuration/landing')}}"><span class="sideimg"><img src="{{url('assets/admin/img/Settings.png')}}"></span> Settings</a></li>
                                 @php 
                                 }
                                 @endphp
                                 <div id=""></div>
                              </ul>
                           </div>
                        </div>
                     </nav>
                  </div>
                  <a href="{{url('opslogin/home')}}"><img src="{{url('assets/admin/img/ærea.png')}}" class="araimg"></a>
               </div>
               <div class="col-lg-3 col-6">
			    <div class="dropdown-container1" tabindex="-1">
                  <div class="conDiv">
                     <span>
                        <p><b>{{ isset(Auth::user()->name)?Crypt::decryptString(Auth::user()->name):'' }}</b> <br> {{ isset(Auth::user()->role->name)?Auth::user()->role->name:'' }}</p><i class="fa fa-caret-down" aria-hidden="true"></i>
                     </span>
                     <span class="icon"><img src="{{url('assets/admin/img/userimg.jpg')}}"></span>
                  </div>
				  <div class="dropdown1">
				      <a href="{{url('opslogin/user/profile')}}"> Profile </a> <br>
    <a href="{{url('logout')}}"> Logout </a>
    </div>
    </div>
				    
               </div>
            </div>
         </div>
      </section>

      <section class="bgsec1">
         <div class="container">
               @if(Auth::user()->id !=1)
                  <form action="{{url('/opslogin/user/switchproperty')}}" id="switchform" method="get" >
			  <div class="row subheder" >
                     <div class="col-lg-7 col-4">
                        <div class="status">
                           @if(!empty($logo_path))
                              <img src="{{$logo_path}}" class="logo">
                           @else
                              <h1>{{$loggedInUser->propertyinfo->company_name}}</h1>
                           @endif
                        </div>
                     </div>
                     <div class="col-lg-2 col-3">
                        <div class="Propertysel">
                           <p>Property:</p>
                        </div>
                     </div>
                     <div class="col-lg-3 col-5">
                        <div class="Propertysel">
                       <!-- opslogin-->
                           <select name="ag_prop" id="ag_prop" class="form-control selectimg">
                              @php
                                 $ag_properties = $loggedInUser->propdropdown(Auth::user()->id);
                              @endphp
                              @foreach($ag_properties as $ag_property){
                                 <option value="{{$ag_property->id}}" @if($account_id==$ag_property->id) selected="selected" @endif data-src="{{$img_full_path.$ag_property->company_logo}}" 
                                    class="optimg">{{$ag_property->company_name}}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
					 
            </div>
                  </form>
               @endif

            @yield('content')
         </div>
      </section>
      <div class="modal" id="mydata">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="voting1"></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <p><b id="voting"> </b>&nbsp;is currently not available in your subscription. Please contact us to find out more on how to unlock this function for your property.</p>
               </div>
            </div>
         </div>
      </div>


   <!--<script src=" {{ asset('assets/admin/js/jquery.min.js') }}"></script>-->
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   <script src="{{asset('js/jquery-ui.min.js')}}" type="text/javascript"></script>
   <script src=" {{ asset('assets/admin/js/popper.min.js') }}"></script>
   <script src=" {{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
   <script src="{{ asset('assets/admin/js/bootstrap-datetimepicker.js') }}"></script>
   <script src="{{ asset('assets/admin/js/bootstrap-select.js') }}"></script>
   <script src="{{ asset('assets/admin/js/tree.js') }}"></script>
   <script src=" {{ asset('assets/admin/js/jquery.rollNumber.js') }}" src="assets/js/jquery.rollNumber.js"></script>
   <script src=" {{ asset('assets/admin/js/canvasjs.min.js') }}" src="assets/js/canvasjs.min.js"></script>
   <script src="{{ asset('assets/admin/js/jquery.scrolling-tabs.min.js') }}"></script>
   <!-- <script src="{{ asset('assets/admin/js/select2.min.js') }}"></script> -->
   <!--script src="https://code.jquery.com/jquery-3.5.1.min.js"></script-->
	  <script>
$(document).ready(function(){
    $(".selectlink").change(function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if(optionValue){
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".box").hide();
            }
        });
    }).change();
});
</script>
  <script>
       $(function() {
         $("#device_type").change(function() {
            if ($("#device_type").val() ==2) {
               $("#locations").show();
               $("#location").hide();
               $("#location").removeAttr('required');
			   $("#locations").prop('required',true);
            } else {
               $("#locations").hide();
			    $("#locations").removeAttr('required');
				 $("#location").prop('required',true);
                $("#location").show();
			   
            }
  }).trigger('change');
});
      </script>
   <script>
      var hidWidth;
      var scrollBarWidths = 40;

      var widthOfList = function(){
      var itemsWidth = 0;
      $('.list a').each(function(){
         var itemWidth = $(this).outerWidth();
         itemsWidth+=itemWidth;
      });
      return itemsWidth;
      };

      var widthOfHidden = function(){
         var ww = 0 - $('.wrapper').outerWidth();
         var hw = (($('.wrapper').outerWidth())-widthOfList()-getLeftPosi())-scrollBarWidths;
         var rp = $(document).width() - ($('.nav-item.nav-link').last().offset().left + $('.nav-item.nav-link').last().outerWidth());
         
         if (ww>hw) {
            //return ww;
            return (rp>ww?rp:ww);
         }
         else {
            //return hw;
            return (rp>hw?rp:hw);
         }
      };

      var getLeftPosi = function(){
         
         var ww = 0 - $('.wrapper').outerWidth();
         var lp = $('.list').length ? $('.list').position().left : 0;
         
         if (ww>lp) {
            return ww;
         }
         else {
            return lp;
         }
      };

      var reAdjust = function(){
          
        let $lastNav = $('.nav-item.nav-link').last();
        let rp = 0;
        if ($lastNav.length) {
             // check right pos of last nav item
          rp = $(document).width() - ($lastNav.offset().left + $lastNav.outerWidth());
        }
      
      if (($('.wrapper').outerWidth()) < widthOfList() && (rp<0)) {
         $('.scroller-right').show().css('display', 'flex');
      }
      else {
         $('.scroller-right').hide();
      }
      
      if (getLeftPosi()<0) {
         $('.scroller-left').show().css('display', 'flex');
      }
      else {
         $('.item').animate({left:"-="+getLeftPosi()+"px"},'slow');
         $('.scroller-left').hide();
      }
      }

      reAdjust();

      $(window).on('resize',function(e){  
         reAdjust();
      });

      $('.scroller-right').click(function() {
      
      $('.scroller-left').fadeIn('slow');
      $('.scroller-right').fadeOut('slow');
      
      $('.list').animate({left:"+="+widthOfHidden()+"px"},'slow',function(){
         reAdjust();
      });
      });

      $('.scroller-left').click(function() {
      
         $('.scroller-right').fadeIn('slow');
         $('.scroller-left').fadeOut('slow');
      
         $('.list').animate({left:"-="+getLeftPosi()+"px"},'slow',function(){
            reAdjust();
         });
      });    
   </script>

    <script>
      $(document).ready(function(){
         $(".datafech").on("click", function(){
            var dataId = $(this).attr("data-id");
         document.getElementById("voting").innerHTML = "" + dataId ;
         document.getElementById("voting1").innerHTML = "" + dataId ;
            
         });
      });
   </script>
   <script>
      $('#terms1').click(function(){
      //If the checkbox is checked.
         if($(this).is(':checked')){
            //Enable the submit button.
            $('#checkid1').attr("disabled", false);
         } else{
            //If it is not checked, disable the button.
            $('#checkid1').attr("disabled", true);
         }
      });
      $('#terms2').click(function(){
         //If the checkbox is checked.
         if($(this).is(':checked')){
            //Enable the submit button.
            <!-- $('#checkid2').attr("disabled", false); -->
            $('#checkid2,#checkid3,#checkid4,#checkid5,#checkid6,#checkid7').attr("disabled", false);
         } else{
            //If it is not checked, disable the button.
            <!-- $('#checkid2').attr("disabled", true); -->
            $('#checkid2,#checkid3,#checkid4,#checkid5,#checkid6,#checkid7').attr("disabled", true);
         }
      });
      $('#terms3').click(function(){
         //If the checkbox is checked.
         if($(this).is(':checked')){
            //Enable the submit button.
            $('#checkid8').attr("disabled", false);
         } else{
            //If it is not checked, disable the button.
            $('#checkid8').attr("disabled", true);
         }
      });
   </script>
   <script>
      $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
      });
   </script>
   <script>
         $(function () {
         $('.selectpicker').selectpicker();
      });
   </script>
   <script>
     
   @if(isset($create_invoice) && $create_invoice ==1)
      (function(seconds) {
         var refresh,       
            intvrefresh = function() {
                  clearInterval(refresh);
                  refresh = setTimeout(function() {
                     location.href = location.href;
                  }, seconds * 1000);
            };

         $(document).on('keypress click', function() { intvrefresh() });
         intvrefresh();

      }(120));
   @endif
</script>
<script>
$(document).ready(function() {

var $divs = $(".menu-open").show(),
    current = 0;
$divs.eq(0).show();

});

$( "#quote" ).click(function( event ) {
	 $(".top-nav").animate({
    scrollTop: 0
  }, 1000)
  
});

$("#ag_prop").change(function() {
   if (confirm('Do you want to switch property?')) {
      $('#switchform').submit();
   }
});
</script>


<script>
         jQuery(function ($) {
         
          $(".sidebar-dropdown > a").click(function() {
         $(".sidebar-submenu").slideUp(200);
         if (
          $(this)
            .parent()
            .hasClass("active")
         ) {
          $(".sidebar-dropdown").removeClass("active");
          $(this)
            .parent()
            .removeClass("active");
         } else {
          $(".sidebar-dropdown").removeClass("active");
          $(this)
            .next(".sidebar-submenu")
            .slideDown(200);
          $(this)
            .parent()
            .addClass("active");
         }
         });
         
         $("#close-sidebar").click(function() {
         $(".page-wrapper").removeClass("toggled");
         });
         $("#show-sidebar").click(function() {
         $(".page-wrapper").addClass("toggled");
         });   
         
         });


         $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
         });
   $("#ckbCheckAll").click(function () {
                 $(".checkBoxClass").prop('checked', $(this).prop('checked'));
             });

$(document).ready(function () {
	 $(".aid1").click(function () {
                 $(".aclass1").prop('checked', $(this).prop('checked'));
             });
			  $(".aid2").click(function () {
                 $(".aclass2").prop('checked', $(this).prop('checked'));
             });
			  $(".aid3").click(function () {
                 $(".aclass3").prop('checked', $(this).prop('checked'));
             });
			  $(".aid4").click(function () {
                 $(".aclass4").prop('checked', $(this).prop('checked'));
             });
			  $(".aid5").click(function () {
                 $(".aclass5").prop('checked', $(this).prop('checked'));
             });
			 
    $(".id1").click(function () {
        $(".class1").prop('checked', $(this).prop('checked'));
    });
    
    $(".class1").change(function(){
        if (!$(this).prop("checked")){
            $(".id1").prop("checked",false);
        }
    });
	<!-- 2 -->
	   $(".id2").click(function () {
        $(".class2").prop('checked', $(this).prop('checked'));
    });
    
    $(".class2").change(function(){
        if (!$(this).prop("checked")){
            $(".id2").prop("checked",false);
        }
    });
		<!-- 3 -->
	   $(".id3").click(function () {
        $(".class3").prop('checked', $(this).prop('checked'));
    });
    
    $(".class3").change(function(){
        if (!$(this).prop("checked")){
            $(".id3").prop("checked",false);
        }
    });
		<!-- 4 -->
	   $(".id4").click(function () {
        $(".class4").prop('checked', $(this).prop('checked'));
    });
    
    $(".class4").change(function(){
        if (!$(this).prop("checked")){
            $(".id4").prop("checked",false);
        }
    });
		<!-- 5 -->
	   $(".id5").click(function () {
        $(".class5").prop('checked', $(this).prop('checked'));
    });
    
    $(".class5").change(function(){
        if (!$(this).prop("checked")){
            $(".id5").prop("checked",false);
        }
    });
		<!-- 6 -->
	   $(".id6").click(function () {
        $(".class6").prop('checked', $(this).prop('checked'));
    });
    
    $(".class6").change(function(){
        if (!$(this).prop("checked")){
            $(".id6").prop("checked",false);
        }
    });
		<!-- 7 -->
	   $(".id7").click(function () {
        $(".class7").prop('checked', $(this).prop('checked'));
    });
    
    $(".class2").change(function(){
        if (!$(this).prop("checked")){
            $(".id2").prop("checked",false);
        }
    });
		<!-- 8 -->
	   $(".id8").click(function () {
        $(".class8").prop('checked', $(this).prop('checked'));
    });
    
    $(".class8").change(function(){
        if (!$(this).prop("checked")){
            $(".id8").prop("checked",false);
        }
    });
		<!-- 9 -->
	   $(".id9").click(function () {
        $(".class9").prop('checked', $(this).prop('checked'));
    });
    
    $(".class9").change(function(){
        if (!$(this).prop("checked")){
            $(".id9").prop("checked",false);
        }
    });
		<!-- 10 -->
	   $(".id10").click(function () {
        $(".class10").prop('checked', $(this).prop('checked'));
    });
    
    $(".class10").change(function(){
        if (!$(this).prop("checked")){
            $(".id10").prop("checked",false);
        }
    });
		<!-- 11 -->
	   $(".id11").click(function () {
        $(".class11").prop('checked', $(this).prop('checked'));
    });
    
    $(".class11").change(function(){
        if (!$(this).prop("checked")){
            $(".id11").prop("checked",false);
        }
    });
			<!-- 12 -->
	   $(".id12").click(function () {
        $(".class12").prop('checked', $(this).prop('checked'));
    });
    
    $(".class12").change(function(){
        if (!$(this).prop("checked")){
            $(".id12").prop("checked",false);
        }
    });
	<!-- 13 -->
	$(".id13").click(function () {
        $(".class13").prop('checked', $(this).prop('checked'));
    });
    
    $(".class13").change(function(){
        if (!$(this).prop("checked")){
            $(".id13").prop("checked",false);
        }
    });
	<!-- 14 -->
	$(".id14").click(function () {
        $(".class14").prop('checked', $(this).prop('checked'));
    });
    
    $(".class14").change(function(){
        if (!$(this).prop("checked")){
            $(".id14").prop("checked",false);
        }
    });
	<!-- 15 -->
	$(".id15").click(function () {
        $(".class15").prop('checked', $(this).prop('checked'));
    });
    
    $(".class15").change(function(){
        if (!$(this).prop("checked")){
            $(".id15").prop("checked",false);
        }
    });
	<!-- 16 -->
	$(".id16").click(function () {
        $(".class16").prop('checked', $(this).prop('checked'));
    });
    
    $(".class16").change(function(){
        if (!$(this).prop("checked")){
            $(".id16").prop("checked",false);
        }
    });
	<!-- 17 -->
	$(".id17").click(function () {
        $(".class17").prop('checked', $(this).prop('checked'));
    });
    
    $(".class17").change(function(){
        if (!$(this).prop("checked")){
            $(".id17").prop("checked",false);
        }
    });
	<!-- 18 -->
	$(".id18").click(function () {
        $(".class18").prop('checked', $(this).prop('checked'));
    });
    
    $(".class18").change(function(){
        if (!$(this).prop("checked")){
            $(".id18").prop("checked",false);
        }
    });
	
	 $(".uid1").click(function(){$(".uclass1").prop('checked', $(this).prop('checked'));});
                    $(".uid2").click(function(){$(".uclass2").prop('checked', $(this).prop('checked'));});
                    $(".uid3").click(function(){$(".uclass3").prop('checked', $(this).prop('checked'));});
                    $(".uid4").click(function(){$(".uclass4").prop('checked', $(this).prop('checked'));});
                    $(".uid5").click(function(){$(".uclass5").prop('checked', $(this).prop('checked'));});
                    $(".uid6").click(function(){$(".uclass6").prop('checked', $(this).prop('checked'));});
                    $(".uid7").click(function(){$(".uclass7").prop('checked', $(this).prop('checked'));});
                    $(".uid8").click(function(){$(".uclass8").prop('checked', $(this).prop('checked'));});
                    $(".uid9").click(function(){$(".uclass9").prop('checked', $(this).prop('checked'));});
                    $(".uid10").click(function(){$(".uclass10").prop('checked', $(this).prop('checked'));});
                    $(".uid11").click(function(){$(".uclass11").prop('checked', $(this).prop('checked'));});
                    $(".uid12").click(function(){$(".uclass12").prop('checked', $(this).prop('checked'));});
					$(".uid13").click(function(){$(".uclass13").prop('checked', $(this).prop('checked'));});
                    $(".uid14").click(function(){$(".uclass14").prop('checked', $(this).prop('checked'));});
                    $(".uid15").click(function(){$(".uclass15").prop('checked', $(this).prop('checked'));});
                    $(".uid16").click(function(){$(".uclass16").prop('checked', $(this).prop('checked'));});
                    $(".uid17").click(function(){$(".uclass17").prop('checked', $(this).prop('checked'));});
                    $(".uid18").click(function(){$(".uclass18").prop('checked', $(this).prop('checked'));});

});
<!-- check box select  end -->

   $("#reg_form").submit(function(){
      
      var row = $("#rowcount").val();
      
      var invalid = false;
      for(var i=1; i<=row; i++){
         var name = "#name_"+i;
         var mobile = "#mobile_"+i;
         if(i ==1 && $(name).val() ==''){
            alert("Vistor "+i+" name should not be empty.")
            invalid = true;
            return false;
         }
         if(i ==1 && $(mobile).val() ==''){
            alert("Vistor "+i+" mobile number should not be empty.")
            invalid = true;
            return false;
         }
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
    <script>


function hiderow(rowname,fieldrow){
   var row = $("#rowcount").val();
   var max_row = $("#maxcount").val();

   var new_row = Number(row)- 1;
   $("#rowcount").val(new_row);

   //alert($("#reference"+fieldrow).val())
   $("#type"+fieldrow).val('');
   $("#reference"+fieldrow).val('');
   $("#description"+fieldrow).val('');
   $("#amount"+fieldrow).val('');
  
   $("#"+rowname).hide();
   if(new_row < max_row){
      $("#buttonsection").show();
   }
   
}
function showmore(){
            var row = $("#rowcount").val();
            var max_row = $("#maxcount").val();

            var new_row = Number(row)+ 1;
            
            $("#add_field"+new_row).show();
            $("#rowcount").val(new_row);

            if(new_row == max_row)
               $("#buttonsection").hide();
         }
  


var date = new Date();
var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

//text
var optSimple3 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox3',
  
};
$( '#datetext1').datepicker( optSimple3 );
var optSimple4 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox4',
  
};
$( '#datetext2').datepicker( optSimple4 );

var optSimple5 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox5',
  
};
$( '#datetext5').datepicker( optSimple5 );
var optSimple6 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox6',
  
};
$( '#datetext6').datepicker( optSimple6 );
var optSimple7 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox7',
  
};
$( '#datetext7').datepicker( optSimple7 );
var optSimple8 = {
   format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '.sandbox8',
  
};
$( '.datetext8').datepicker( optSimple8 );

$(".datetext9").datepicker( {
   format: 'yyyy-mm-dd',
   todayHighlight: true,
   //startDate:  '-30d',
   orientation: 'bottom left',
  autoclose: true,
	
});
//text

var optSimple = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox',
  
};


$( '#fromdate').datepicker( optSimple );

$( '#todate').datepicker( optSimple );

$( '#visiting_date' ).datepicker( optSimple );
$( '#visiting_date' ).datepicker( 'setDate', today );

var optSimple11 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox11',
  
};
$( '#inspection_date' ).datepicker( optSimple11 );
var optSimple12 = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox12',
  
};
$( '#inspection_date12' ).datepicker( optSimple12 );
$( '#booking_date' ).datepicker( optSimple );

var processDate = {
  format: 'yyyy-mm-dd',
  todayHighlight: true,
  orientation: 'bottom left',
  autoclose: true,
  container: '#sandbox',
  startDate: today
};

$('#progress_date').datepicker(processDate);

$("#datepickermonth").datepicker( {
    format: "yyyy-mm-dd",
    viewMode: "months", 
    minViewMode: "months"
	
});
  //$( '#datepickermonth' ).datepicker( 'setDate', today );


//$( '#inspection_date' ).datepicker( 'setDate', today );

$('#inspection_date').datepicker()
    .on('changeDate', function(e) {
       getFacilityTimeslots(); 
});

$('#booking_date').datepicker()
    .on('changeDate', function(e) {
      getFacilityTimeslots(); 
});


$( '#appt_date' ).datepicker( optSimple );


//$( '#inspection_date' ).datepicker( 'setDate', today );

$('#appt_date').datepicker()
    .on('changeDate', function(e) {
       getTakeoverTimeslots(); 
});
 

function getpurpose(){
   var purpose =  $("#purpose").val();
   var current_id =  $("#current_id").val();
   $("#type_"+purpose).show();
   $("#type_"+current_id).hide();

   $("#current_id").val(purpose);

   check_availability()


}

function check_availability(){
   var visiting_date =  $("#visiting_date").val();
   var visiting_purpose =  $("#purpose").val();
   var rowcount =  $("#rowcount").val();

   if(visiting_date !='' && visiting_purpose !=''){
      $.ajax({
         url : "{!!URL:: route('availability_check')!!}",
            dataType : "json",
            data:{
                  date:visiting_date,
                  purpose:visiting_purpose
            },
            success:function(data)
            {
               $("#maxcount").val(data.slot_available);


               if(data.id_required==0)
                  $(".idclass").hide();
               else
                  $(".idclass").show();
               
               if(data.slot_available==0)
                  $("#submit_btn_div").hide();
               else
                  $("#submit_btn_div").show();

               if(rowcount > data.slot_available)
                  $("#buttonsection").hide();
               else
                  $("#buttonsection").show();

               if(data.limit ==1){
                  $("#limit").show();
                  alert(data.slot_available+ " slot(s) available on this selected date.");
                  }
               else{
                  $("#limit").hide();
                  //alert("Slot(s) available!");
               }

              
               
            }    
      });
                   
   }
}
function getroles(){
 
 var property =  $("#property").val();
  if(property)
              {
                 $.ajax({
                    url : "{!!URL:: route('getroles')!!}",
                    dataType : "json",
                    data:{
                      property:property
                    },
                    success:function(data)
                    {
                       //console.log(data);
                       $('#role').empty();
                       $("#role").append('<option value="">Select role</option>')
                       $.each(data, function(id,rec){
                        // const json =rec;
                         //const obj = JSON.parse(rec);
                         console.log(id);
                         console.log(rec);
                          $("#role").append('<option value="'+ id +'">'+ rec +'</option>')
                          
                          

                       });
                    }
                 });
              }
              else
              {
                 $('select[name="role[]"]').empty();
              }

}


getunits();

function getothers(){
   var option =  $("#option").val();
//alert(option);
   if(option==8){
      $('#otherdiv').show();
   }else{
      $('#otherdiv').hide();
      $('#otherdiv').val('');
   }
}

function getsmsfields(){
   var option =  $("#otp_option").val();
//alert(option);
   if(option==2){
      $('#sms_field').show();
   }else{
      $('#sms_field').hide();
      $('#sms_username').val('');
      $('#sms_password').val('');
   }
}
function getintfields(){
   var option =  $("#int_option").val();
//alert(option);
   if(option==2){
      $('#int_field').show();
   }else{
      $('#int_field').hide();
      $('#int_percentage').val('');
      $('#due_period_value').val('');
      $('#due_period_type').val('');
   }
}
function gettypes(){
      var location =  $("#location").val();
      var type =  $("#temp_type").val();

      $.ajax({
         url : "{!!URL:: route('getlocationtypes')!!}",
         dataType : "json",
         data:{
            location:location,
         },
         success:function(data)
            {
               $('#types').empty();
               $.each(data, function(id,rec){
                  if(temp_type == id)
                     $("#types").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                  else
                     $("#types").append('<option value="'+ id +'">'+ rec +'</option>')
               });
            }
      });
}


function getunits(){
 
 var property =  $("#property").val();
 var building =  $("#building").val();
 var role =  $("#role").val();
 var unit =  $("#unit_temp").val();
 if( role ==2){
      $('#primary_div').show();
      $('#faceid_access_div').hide();

   }
   else{
      $('#primary_div').hide();
      $('#faceid_access_div').show();
      $('#primary').prop('checked', false); 
   }
   if( role ==2 ||role ==7  || role ==28 || role ==29 || role ==31|| role ==46)
   {
                  $('#pwd_div').hide();
                  $('#faceid_access_div').hide();
                  $('#password').empty();
                  $('#unit_div').show();
                  //console.log(role);
                  $.ajax({
                     url : "{!!URL:: route('getunits')!!}",
                     dataType : "json",
                     data:{
                        property:property,
                        building:building
                     },
                     success:function(data)
                     {
                        $('#unit_div').show();
                        $('#unit').empty();
                        console.log(data);
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                           if(unit == id)
                              $("#unit").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#unit").append('<option value="'+ id +'">'+ rec +'</option>')
                           
                           

                        });
                     }
                  });
              }
              else
              {
                  $('#faceid_access_div').show();
                  $('#pwd_div').show();
                  $('#unit').empty();
                  $('#unit_div').hide();
                //$("#unit").html('');
              }

}

function getblockunits(){
 
 var property =  $("#property").val();
 var building =  $("#building").val();
 var info_id =  $("#user_more_info_id").val();

 var unit =  $("#unit_temp").val();
 
                  //console.log(role);
                  $.ajax({
                     url : "{!!URL:: route('getblockunits')!!}",
                     dataType : "json",
                     data:{
                        property:property,
                        building:building,
                        info_id:info_id
                     },
                     success:function(data)
                     {
                        $('#unit_div').show();
                        $('#unit').empty();
                        console.log(data);
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                           if(unit == id)
                              $("#unit").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#unit").append('<option value="'+ id +'">'+ rec +'</option>')
                           
                           

                        });
                     }
                  });
}

function checkinpopup()
        {
            var code = $("#access_code").val();
            var id = $("#Id").val();
            console.log(id);
            
            $.ajax({
               url : "{!!URL:: route('accessfaceid')!!}",
               dataType : "json",
               data:{
                  code:code,
                  id:id
                },
                success:function(data)
                {
                  console.log(data);
                  if(data.status ==1){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     });
                    // alert("hi");
	                  var image = $('<img></img>');
                     image.attr('src', "data:image/png;base64, "+data['64img']);
                     $('#image-popup').append(image);
                  }
                  else if(data.status ==2){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Login!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else if(data.status ==3){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Access Code!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else {
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b>No Record Found!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                    
               },
               error: function (textStatus, errorThrown) {
                  //alert("NRIC in blacklist!")
               }
            });
            
         
        }
function checkinpopup2()
        {
            var code = $("#access_code").val();
            var id = $("#Id").val();
            console.log(id);
            
            $.ajax({
               url : "{!!URL:: route('accessfaceid')!!}",
               dataType : "json",
               data:{
                  code:code,
                  id:id
                },
                success:function(data)
                {
                  console.log(data);
                  if(data.status ==1){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     });
                    // alert("hi");
	                  var image = $('<img></img>');
                     image.attr('src', "data:image/png;base64, "+data['64img']);
                     $('#image-popup').append(image);
                  }
                  else if(data.status ==2){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Login!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else if(data.status ==3){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Access Code!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else {
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b>No Record Found!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                    
               },
               error: function (textStatus, errorThrown) {
                  //alert("NRIC in blacklist!")
               }
            });
            
         
        }
function getunitlists(){
 
 var building =  $("#build_temp").val();
 //console.log(building);
  if( building !='')
              {
                 $.ajax({
                    url : "{!!URL:: route('getbuildingunitlists')!!}",
                    dataType : "json",
                    data:{
                      building:building
                    },
                    success:function(data)
                    {
                        //console.log(data);
                        $('#unit_temp').empty();
                        $('#user').empty();
                        $("#unit_temp").append('<option value="">Select Unit</option>')
                        $.each(data, function(id,rec){
                           if(building == id)
                              $("#unit_temp").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#unit_temp").append('<option value="'+ id +'">'+ rec +'</option>')
                          
                       });
                    }
                 });
              }
              else
              {
                  $('#unit_temp').empty();
                  $('#user').empty();
              }

}

function getunituserlists(){
 
 var unit =  $("#unit_temp").val();
 console.log(unit);
  if( unit !='')
              {
              
                console.log(unit);
                 $.ajax({
                    url : "{!!URL:: route('getunituserlists')!!}",
                    dataType : "json",
                    data:{
                      unit:unit
                    },
                    success:function(data)
                    {
                      
                       $('#user').empty();
                       //$('select[name="user[]"]').append('<option value="a">All User</option>');
                       $("#user").append('<option value="">Select User </option>')
                       $.each(data, function(id,rec){
                          if(unit == id)
                            $("#user").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                          else
                            $("#user").append('<option value="'+ id +'">'+ rec +'</option>')
                          
                          

                       });
                    }
                 });
              }
              else
              {
                $('#user').empty();
              }

}

function getunitusernewlists(){
 
   var role =  $("#role").val();
   var unit =  $("#unit_temp").val();
  
  console.log(unit);
               if( unit !='')
               {
                  console.log(unit);
                  console.log(role);
                  $.ajax({
                    url : "{!!URL:: route('getunitusernewlists')!!}",
                    dataType : "json",
                    data:{
                      unit:unit,
                      role:role
                    },
                    success:function(data)
                     {
                        $('#user').empty();
                        $.each(data, function(id,rec){
                        console.log(rec['name']);
                           $("#user").append('<option value="'+ rec['id'] +'">'+ rec['name'] +'</option>')
                        });
                     }
                  });
               }
               else
               {
                  $('#user').empty();
               }

}

function getmanagerlists(){
   var role =  $("#role").val();
   var userroles =  $("#userroles").val();
   var user_array = userroles.split(",");
  
   if(jQuery.inArray(role, user_array) !== -1){
      $("#buildingfld").show();
      $('#unitfld').show();
      $('#user').empty();
   }
   else if(role !='') {
      $("#buildingfld").hide();
      $('#unitfld').hide();
      $("#option").val(1);
      $.ajax({
         url : "{!!URL:: route('getmanagerlists')!!}",
         dataType : "json",
         data:{
            role:role
            },
            success:function(data)
               {
                  //console.log(data);
                  $('#user').empty();
                  $.each(data, function(id,rec){
                        //console.log(rec['name']);
                        $("#user").append('<option value="'+ rec['id'] +'">'+ rec['name'] +'</option>')
                  });
               }
      });
   }
   

}

function getinvoicetypeamount(typefield,amtield){
 var type =  $("#"+typefield).val();
 var account =  $("#accountid").val();
 if(type>0)
              {
              
                console.log(type);
                 $.ajax({
                    url : "{!!URL:: route('getinvoicetypeamount')!!}",
                    dataType : "json",
                    data:{
                     type:type,
                     account_id:account
                    },
                    success:function(data)
                    {
                     $("#"+amtield).val(data);
                    }
                 });
              }
              else
              {
               
               $("#"+amtield).val('');
              }
  
}

function displaycat(){
   var catdropdown = $("#catdropdown").val();
   if(catdropdown ==1)
      $('#categoryid').show();
   else
      $('#categoryid').hide();
}

function getcards(){
 
 var unit =  $("#unit").val();
 var role =  $("#role").val();
 var card =  $("#card_temp").val().split(",");
 if( role ==2 ||role ==7  || role ==28 || role ==29 || role ==31|| role ==46)
 {
               
                console.log(unit);
                 $.ajax({
                    url : "{!!URL:: route('getcards')!!}",
                    dataType : "json",
                    data:{
                      unit:unit
                    },
                    success:function(data)
                    {
                     console.log(data);

                       $('#card').empty();
                       //$('select[name="user[]"]').append('<option value="a">All User</option>');
                       $.each(data, function(id,rec){
                           if ($.inArray(id, card) > -1)
                              $("#card").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#card").append('<option value="'+ id +'">'+ rec +'</option>')
                          
                          

                       });
                    }
                 });
              }
              else
              {
                $('#card').empty();
                //$("#unit").html('');
              }

}

getcards();
function getcards(){
 
 var unit =  $("#unit").val();
 var role =  $("#role").val();
 if( role ==2 ||role ==7  || role ==28 || role ==29 || role ==31|| role ==46)
 {
               
                console.log(unit);
                 $.ajax({
                    url : "{!!URL:: route('getcards')!!}",
                    dataType : "json",
                    data:{
                      unit:unit
                    },
                    success:function(data)
                    {
                     console.log(data);

                       $('#card').empty();
                       //$('select[name="user[]"]').append('<option value="a">All User</option>');
                       $.each(data, function(id,rec){
                           if ($.inArray(id, card) > -1)
                              $("#card").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#card").append('<option value="'+ id +'">'+ rec +'</option>')
                          
                          

                       });
                    }
                 });
              }
              else
              {
                $('#card').empty();
                //$("#unit").html('');
              }

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
   var propertyid =  "{{Auth::user()->account_id}}";

  var selectedDate =  $("#inspection_date").val();
   if(selectedDate)
               {
                  $.ajax({
                     url : "{!!URL:: route('getinspectiontimeslots')!!}",
                     dataType : "json",
                     data:{
                      date:selectedDate,
                      property:propertyid
                     },
                     success:function(data)
                     {
                        //console.log(property);
                        $('#timeslotstables').empty();
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                         // const json =rec;
                          //const obj = JSON.parse(rec);
                          //console.log(rec.time);
                         
                          if(rec.count <=0){
                           $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value,\'appt_time\')" value="'+ rec.time +'"><span class="checkmark">'+ rec.time  +'</span></label>');
                         }
                          else{
                            $('#timeslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value,\'appt_time\')" value="'+ rec.time +'" checked="checked" disabled="disabled"><span class="checkmark taken">'+ rec.time +'</span></label>');
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


function getFacilityTimeslots(){
  //console.log("hai");
 var type = $("#type").val();
 var selectedDate =  $("#booking_date").val();
  if(selectedDate)
              {
                 $.ajax({
                    url : "{!!URL:: route('getfacilitytimeslots')!!}",
                    dataType : "json",
                    data:{
                     date:selectedDate,
                     type:type
                    },
                    success:function(data)
                    {
                       //console.log(data);
                       $('#facilityslotstables').empty();
                       //$('select[name="user[]"]').append('<option value="a">All User</option>');
                       $.each(data, function(id,rec){
                        // const json =rec;
                         //const obj = JSON.parse(rec);
                         console.log(rec.time);
                        
                         if(rec.count <=0){
                          $('#facilityslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'"><span class="checkmark">'+ rec.time  +'</span></label>');
                        }
                         else{
                           $('#facilityslotstables').append('<label class="containers " ><input type="radio" name="timeslot" onclick="gettime(this.value)" value="'+ rec.time +'" checked="checked" disabled="disabled"><span class="checkmark taken">'+ rec.time +'</span></label>');
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
</script>
<!--script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script-->


<!--script src="{{asset('js/jquery-ui.min.js')}}" type="text/javascript"></script>
<script src="{{asset('bower_components/validator/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('bower_components/validator/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{asset('bower_components/jquery-steps/jquery.steps.js')}}" type="text/javascript"></script-->

<!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/locales.js"></script-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script> -->
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script-->

@yield('customJS')

<script >
$(".rotate").click(function(){
 $(this).toggleClass("down")  ; 
});

$('.buttonsub1').on('click', function(e){

$(".sub_Box_Div1").toggle();
$(this).toggleClass('class1')
});
$('.buttonsub2').on('click', function(e){

$(".sub_Box_Div2").toggle();
$(this).toggleClass('class1')
});
$('.buttonsub3').on('click', function(e){

$(".sub_Box_Div3").toggle();
$(this).toggleClass('class1')
});
$(document).ready(function() {

  

   // Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementById("close");

// When the user clicks the button, open the modal 
/*btn.onclick = function() {
    $('#action_status').val();
    modal.style.display = "block";
}*/


// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
    modal.style.display = "none";
}*/

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
        }); 

function opendialog(status) {
  var modal = document.getElementById('myModal');
  var span = document.getElementById("close");
    $('#action_status').val(status);
    modal.style.display = "block";
}

function adjustment(status) {
  
    $('#type').val(status);
    $("#adjustment").submit();
}



function countChar(val) {
   var len = val.value.length;
   if (len >= 1250) {
    val.value = val.value.substring(0, 1250);
    $('#charNum').text(' you have reached the limit');
  } else {
   var char = 1250 - len;
   $('#charNum').text(char + ' characters left');
  }
 }
</script>

<script>
 
 //open dialog for takeover
 

  $(document).on("click", ".open-dialog", function () {
     var bookId = $(this).data('id');
     $(".modal-body #bookId").val( bookId );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});

$(document).on("click", ".open-dialog-access", function () {
     var Id = $(this).data('id');
     $(".modal-body #Id").val( Id );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});

  $(document).on("click", ".open-dialog-confirm", function () {
     var Id = $(this).data('id');
     $(".modal-body #Id").val( Id );
     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});


if($('#unit_list').length){

    $("#unit_list").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('getunitlist')!!}",
                dataType: "json",
                data: {
                    term : $("#unit_list").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 3,
        delay: 300
    });
}

 function deleteUserInfo(uid,infoid,fieldname,img){
  if (confirm('Do you want to remove?')) {
     var token = '{{csrf_token()}}';
     console.log(infoid+ " , "+ fieldname + " , "+ img+ ' , '+token)
      $.ajax({
        url: "{{URL::TO('admin/delete_user_file')}}",
        type: 'post',
        data: {
          id: infoid,
          field_name:fieldname,
          file_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
            <?php if($loggedInUser->role->name =="Employee"){ ?>
            var name = <?php echo $loggedInUser->name ?>;
            window.location.href ='../editmyprofile/'+ name;
          <?php } else { ?>
            window.location.href ='../'+uid+'/edit';
          <?php } ?>
        },

        error: function (response) {
          console.log(response);
        }
      })
    }

    }


function deleteMediaPicture(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_media_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteCondoFile(fieldid){
  if (confirm('Do you want to remove file?')) {
      var token = '{{csrf_token()}}';
      console.log(fieldid);
      $.ajax({
         url : "{!!URL:: route('deleteCondoFile')!!}",
         dataType : "json",
         data:{
            fid:fieldid,
         },
         success:function(data)
         {
			 location.reload();
            //$("#fileDiv").html('');
         }
      });    
  }

}
function deleteUserguideFile(fieldid){
  if (confirm('Do you want to remove file?')) {
      var token = '{{csrf_token()}}';
      console.log(fieldid);
      $.ajax({
         url : "{!!URL:: route('deleteUserguideFile')!!}",
         dataType : "json",
         data:{
            fid:fieldid,
         },
         success:function(data)
         {
			 location.reload();
            //$("#fileDiv").html('');
         }
      });    
  }

}
function deleteMagazineFile(fieldid){
  if (confirm('Do you want to remove file?')) {
      var token = '{{csrf_token()}}';
      console.log(fieldid);
      $.ajax({
         url : "{!!URL:: route('deleteMagazineFile')!!}",
         dataType : "json",
         data:{
            fid:fieldid,
         },
         success:function(data)
         {
			 location.reload();
            //$("#fileDiv").html('');
         }
      });    
  }

}

function deleteRecruitmentInfo(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/recruitment_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteCompanyLogo(mid,img){
  if (confirm('Do you want to remove logo?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_company_logo')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           //window.location.href ='../'+mid+'/edit';
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteSharefileInfo(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/sharedoc_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
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

if($('#SearchEmpName').length){
    
    $("#SearchEmpName").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocomplete')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchEmpName").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 3,
        delay: 300
    });
}

if($('#SearchEmpId').length){
    $("#SearchEmpId").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocompleteid')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchEmpId").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 3,
        delay: 300
    });
}

if($('#SearchByFileNo').length){
    $("#SearchByFileNo").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocompletefileno')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchByFileNo").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 1,
        delay: 300
    });
}

function payrollAdjustment(status){

  var note = $("#note").val();
  var amount = $("#amount").val();
  //var type = $("#type").val();
  var type = status;
  var id = $("#payroll_id").val();
  console.log(note+" "+amount+" "+type);
  
  if(note !='' && amount !='' && type !=''){
    
  
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/add_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: id,
          note: note,
          amount:amount,
          type:type,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          console.log(response);
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }
  else {
    if(note =='')
      alert('Please enter your note!');
    else if(amount =='')
      alert('Please enter your amount!');
    else if(type =='')
      alert('Please select type!');
  }

}    


function deleteAdditional(mid){
  if (confirm('Do you want to delete this record?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: mid,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}
function refundCalcAmount(){
      var deposit_val = parseFloat($("#Depositid").val());
      var charge_val = parseFloat($("#Feesid").val());
      var refund_amount = deposit_val - charge_val;
      var float_number = Number(refund_amount).toFixed(2);   
      $("#Refund").val(float_number);

}

function refundFacilityDeposit(){
   if (confirm('Do you want to continue?')) {
      var booking_id = $("#refund_booking_id").val();
      var deposit_val = $("#Depositid").val();
      var charge_val = $("#Feesid").val();
      var refund_amount = $("#Refund").val();

      /*console.log(" booking id value"+booking_id);
      console.log(" deposit_id id value"+deposit_val);
      console.log(" fees_id id value"+charge_val);
      console.log(" refund_id id value"+refund_amount);*/
      var token = '{{csrf_token()}}';
      //console.log(" token value"+token);
      $.ajax({
        url: "{{URL::TO('refundfacility')}}",
        dataType : "json",
        data: {
         id: booking_id,
         deposit_amount: deposit_val,
         charge_amount: charge_val,
         refund_amount: refund_amount,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
         success: function (response) {
            console.log(response);
            $("#my_form").hide();
            $(".popup2").show();
            $(".close").click(function() 
            {
				
                  $(".popup2").fadeOut(200);
            });
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

/*function refundFacilityDeposit(){
   if (confirm('Do you want to continue?')) {
      var booking_id = $("#refund_booking_id").val();
      var deposit_id = $("#Depositid").val();
      var fees_id = $("#Feesid").val();
      var refund_id = $("#Refund").val();

      console.log(" booking id value"+booking_id);
      console.log(" deposit_id id value"+deposit_id);
      console.log(" fees_id id value"+fees_id);
      console.log(" refund_id id value"+refund_id);
      
      $("#my_form").hide();
      $(".popup2").show();
      $(".close").click(function() 
      {
            $(".popup2").fadeOut(5000);
      });
      /*var token = '{{csrf_token()}}';
      var note = $("#booking_id").val();
      var amount = $("#charge_amount").val();
      $.ajax({
        url: "{{URL::TO('admin/refundfacility')}}",
        type: 'post',
        data: {
          id: mid,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
         success: function (response) {
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  /*}

} */

function addClaimAddition(mid){
     var payroll = $("#payroll_id").val();
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/claim_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: mid,
          payroll: payroll,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      }) 
}

   
function delete_record(url)
{ 
 doyou = confirm("Do you want to delete this record? All related data will be deleted (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}
function cancel_booking(url)
{ 
 doyou = confirm("Do you want to cancel this booking? (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

function bulk_delete(url)
{ 
   doyou = confirm("Do you want to delete all selected record?(OK = Yes   Cancel = No)");
   if (doyou == true)
      {
         $("#list_form").submit();
   }
}

function bulk_print(url)
{ 
   //alert(url);
   var values = $(".gap input:checkbox:checked").map(function(){
      if($(this).val() !=1)
         return $(this).val();
   }).get();
   var unit_id = $('#unitno').val();
   var full_url = url+"?unit_id="+unit_id+"&invoice_ids="+values;
   window.open(full_url, '_blank');
   //window.location.href= 
}


function resend_notification(url)
{ 
 doyou = confirm("A notification has been sent previously. Do you want to send another notification?(OK = Resend   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

function activate_record(url)
{ 
 doyou = confirm("Do you want to activate?(OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

function deactivate_record(url)
{ 
 doyou = confirm("Do you want to de-activate? (OK = Yes   Cancel = No)");
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

$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
$("#checkAllView").click(function(){
    $('.viewCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllAdd").click(function(){
    $('.addCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllEdit").click(function(){
    $('.editCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllDelete").click(function(){
    $('.deleteCheckBox').not(this).prop('checked', this.checked);
});

//Confirmation Due Date  Start //

function confirmationDuebyProbation(){


       var date = new Date($("#hire_date").val()),
           days = parseInt($("#probation_month").val(), 10);
        
        if(!isNaN(date.getTime())){
            date.setMonth(date.getMonth() + days);
            
            $("#confirmation_due").val(date.toInputFormat());
        } else {
            alert("Invalid Date");  
        }


}
  ;(function($, window, document, undefined){
  
    
    
    //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
    };
})(jQuery, this, document);

function confirmationDuebyDate(){


       var date = new Date($("#hire_date").val()),
           days = parseInt($("#probation_month").val(), 10);
        
        if(!isNaN(date.getTime()) && days !=''){
            date.setMonth(date.getMonth() + days);
            
            $("#confirmation_due").val(date.toInputFormat());
        } else {
            alert("Invalid Date");  
        }


}
  

  ;(function($, window, document, undefined){
  
    
    
    //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
    };
})(jQuery, this, document);



//Confirmation Due Date  End //
  
</script>
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script>
<script>
	  $(function () {
    "use strict";
    
    $(".popup img").click(function () {
        var $src = $(this).attr("src");
        $(".show").fadeIn();
        $(".img-show img").attr("src", $src);
    });
    
    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });
    
});
	   $(".perquessthiden").hide();
	   $(".requestshow").click(function() {
		     if($(this).is(":checked")) {
        $(".perquessthiden").show();
    } else {
        $(".perquessthiden").hide();
    }
		   });
	  $(".perquessthide").show();
$(".requestshow").click(function() {
    if($(this).is(":checked")) {
        $(".perquessthide").show();
    } else {
        $(".perquessthide").hide();
    }
});
      </script>
      <script>
         $diy = $('.number-diy .data');
         if($diy.length){
             $diy.rollNumber({
               number: $diy[0].dataset.number, 
               speed: 500, 
               interval: 200,
               //rooms: 9,
               space: 100,
               <!-- symbol: ',',  -->
               fontStyle: {
                 'font-size': 22,
               }
             });
         }
         
         $diy2 = $('.number-diy2 .data2');
         if($diy2.length){
             $diy2.rollNumber({
               number: $diy2[0].dataset.number, 
               speed: 500, 
               interval: 200,
               //rooms: 9,
               space: 100,
               <!-- symbol: ',',  -->
               fontStyle: {
                 'font-size': 22,
               }
             });
         }
         
         $diy3 = $('.number-diy3 .data3');
         if($diy3.length){
             $diy3.rollNumber({
               number: $diy3[0].dataset.number, 
               speed: 500, 
               interval: 200,
               //rooms: 9,
               space: 100,
               <!-- symbol: ',',  -->
               fontStyle: {
                 'font-size': 22,
               }
             });
         }
        
   function facilityrefundpopup(booking_id)
	{
			
      $(".datafech").on("click", function(){
         var dataId = $(this).attr("data-id");
	      document.getElementById("fechdata").innerHTML = "" + dataId ;
	   });
      var deposit_id = "#deposit_amt_id_"+booking_id;
      var deposit_amount = $(deposit_id).val();
      var deposit_decimal_val = Number(deposit_amount).toFixed(2);
      //alert(booking_id);
	  
      $("#refund_booking_id").val(booking_id);
		$("#Depositid").val(deposit_decimal_val);
		$("#Feesid").val("0");
		$("#Refund").val(deposit_decimal_val);
		$(".popup").show();
		$(".close").click(function() {
         $(".popup").fadeOut(500);
         });  
	}

</script>
    <script type="text/javascript">
$(document).ready(function(){
    $('.checkAll1').on('click',function(){
        if(this.checked){
            $('.check').each(function(){
                this.checked = true;
            });
        }else{
             $('.check').each(function(){
                this.checked = false;
            });
        }
    });
    
    $('.check').on('click',function(){
        if($('.check:checked').length == $('.check').length){
            $('.checkAll1').prop('checked',true);
        }else{
            $('.checkAll1').prop('checked',false);
        }
    });
});
</script>
	   <script>
         $('.checknew').click(function(){
         
             if(!$(this).is(':checked')){
              $('#btncheck').attr("disabled","disabled");   
         }
         else
             $('#btncheck').removeAttr('disabled');
         });
         	  
      </script>

      <script>
	  $( ".check" ).on( "click", function() {
  if($( ".check:checked" ).length > 0)
  {
  	$('#btncheck').prop('disabled', false);
  }
  else
  {
  	$('#btncheck').prop('disabled', true);
  }  
});

      </script>
	  <script>
$(document).ready(function($) {
  $(document).on('.submit',  function(event) {
    event.preventDefault();
   // alert('reload');
   location.reload();
  });
});
</script>
  <script>
         $('.checknew').click(function(){
         
             if(!$(this).is(':checked')){
              $('#btncheck2').attr("disabled","disabled");   
         }
         else
             $('#btncheck2').removeAttr('disabled');
         });
         	  
      </script>

      <script>
	  $( ".check" ).on( "click", function() {
  if($( ".check:checked" ).length > 0)
  {
  	$('#btncheck2').prop('disabled', false);
  }
  else
  {
  	$('#btncheck2').prop('disabled', true);
  }  
});

      </script>
      <script>
	  	   var optSimple = {
            format: 'yyyy-mm-dd',
        endDate: '+0d',
           todayHighlight: true,
           orientation: 'bottom left',
           autoclose: true,
           container: '#sandbox02'
         };
         
         $( '#datepicker02').datepicker( optSimple );
      </script>
	     <script>
	 $('#area').hide();

function checkval() {

    if ($('#myCheck').is(':checked')) {
       // alert('is checked');
        $('#area').show();
    } else {
        $('#area').hide();
    }

}

$(function () {
    checkval(); // this is launched on load
    $('#myCheck').click(function () {
        checkval(); // this is launched on checkbox click
    });

});
      </script>
      <script>
	   function facidPopup()
 {
	 $("#facidmodal2").show();
	 $("#exampleModalCenter").hide();
 }
      </script>
      <script>
$(".select2").click(function() {
  var is_open = $(this).hasClass("open");
  if (is_open) {
    $(this).removeClass("open");
  } else {
    $(this).addClass("open");
  }
});

$(".select2 li").click(function() {

  var selected_value = $(this).html();
  var first_li = $(".select2 li:first-child").html();

  $(".select2 li:first-child").html(selected_value);
  $(this).html(first_li);

});

$(document).mouseup(function(event) {

  var target = event.target;
  var select = $(".select2");

  if (!select.is(target) && select.has(target).length === 0) {
    select.removeClass("open");
  }

});

      </script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>
