@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
   $menu =  $permission->check_menu_permission(25,$permission->role_id,1);
   $feedback =  $permission->check_menu_permission(26,$permission->role_id,1);
   $defect =  $permission->check_menu_permission(27,$permission->role_id,1);
   $property =  $permission->check_menu_permission(28,$permission->role_id,1);
   $facility =  $permission->check_menu_permission(29,$permission->role_id,1);
   $vm =  $permission->check_menu_permission(37,$permission->role_id,1);
   $eforms =  $permission->check_menu_permission(39,$permission->role_id,1);
   $payment =  $permission->check_menu_permission(46,$permission->role_id,1);
   $holiday =  $permission->check_menu_permission(53,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
   $dashmenu =  $permission->check_menu_permission(55,$permission->role_id,1);
   $key_setting =  $permission->check_menu_permission(9,$permission->role_id,1);
   $inspection_setting =  $permission->check_menu_permission(57,$permission->role_id,1);
   $sharesetting =  $permission->check_menu_permission(63,$permission->role_id,1);
   $permission = $permission->check_permission(29,$permission->role_id); 
@endphp

<!-- Content Header (Page header) -->
<style>
.payreq{        margin-bottom: 0px; color: #5D5D5D;
    padding-right: 0px;
    padding-top: 2px;
    text-transform: capitalize;
    font: normal normal bold 12px/20px Helvetica;}
</style>
  <div class="status">
    <h1>facility type - add </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if( isset($property->view) && $property->view==1 )
                        <li><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
                     @endif

                     @if(Auth::user()->role_id ==1)
                        <li  ><a href="{{url('/opslogin/configuration/banner#settings')}}">Home Banner  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/ads#settings')}}">Ads  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/econcierge#settings')}}">E-Cconcierge  <br>Management</a></li>
                     @endif

                     @if((isset($key_setting) && $key_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/collectionappoinment')}}">Key Collection  <br>Appointment Settings</a></li>
                     @endif

                     @if((isset($inspection_setting) && $inspection_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/inspectionappoinment')}}">Defects Inspection  <br>Appointment Settings</a></li>
                     @endif

                     @if(2 ==1)
                        <li><a href="{{url('/opslogin/configuration/dashboard#settings')}}">Mobile App  <br>Dashboard Settings</a></li>
                     @endif

                     @if(isset($menu->view) && $menu->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/menu#menusettings')}}">Menu  <br>Management</a></li>
                     @endif

                     @if(isset($feedback->view) && $feedback->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/feedback#feedbacksettings')}}">Feedback  <br>Options</a></li>
                     @endif
                     
                     @if(isset($defect->view) && $defect->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/defect#defectsettings')}}">Defects  <br>Location</a></li>
                     @endif
                     
                     @if(isset($facility->view) && $facility->view==1 && $admin_id !=1)
                        <li class="activeul"><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                     @endif
                     
                     @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
                     @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
       {!! Form::open(['method' => 'POST','class'=>"forunit", 'files' => true, 'url' => url('opslogin/configuration/facility')]) !!}

<div class="row asignbg">
 <div class="col-lg-6">
      <div class="form-group">
        <label>facility name :</label>
          {{ Form::text('facility_type', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Facility Name']) }}
      </div>
  </div>

  <div class="col-lg-6">
      <div class="form-group">
          <label>facility image *: </label>
        {{ Form::file('facility_image', null, ['class'=>'form-control','required' => false]) }}
             
      </div>
    </div>

 <div class="col-lg-6">
      <div class="form-group ">
        <label>calendar availability :<br>(In Days)</label>
          {{ Form::number('calendar_availability_start', null, ['class'=>'form-control','required' => true,'placeholder' => 'Calendar availability start']) }}
      </div>
  </div>
 <div class="col-lg-6">
      <div class="form-group">
        <label>show slot availability :<br>(In Days)</label>
          {{ Form::number('allowed_booking_for', null, ['class'=>'form-control','required' => true,'placeholder' => 'Show slot availability']) }}
      </div>
  </div>
  
  <div class="col-lg-6">
      
      <div class="form-group row">
        <label class="col-sm-12">next booking allowed :</label>
          <div class="col-sm-6">
          {{ Form::select('next_booking_allowed', ['1' => 'None','2'=>'Month','3'=>"Days"], null, ['class'=>'form-control', 'required' => true, 'onchange'=>'showday()','id'=>"booking_option"]) }}
          </div>
          <div class="col-sm-6" id="days">
              {{ Form::number('next_booking_allowed_days', null, ['class'=>'form-control']) }}
            
          </div>
      </div>
@if($propertyObj->opn_secret_key !='')
<div class="row">
  <div class="col-sm-12">
    <p class="payreq">Payment Required?</p>
    <label class="containeruser1 conttext">
      <input type="checkbox" name="payment_required" value="1" class="viewCheckBox requestshow" checked="checked">
      <span class="checkmarkuser1 checksty"></span>
    </label>
  </div>
</div>
<div class="clearfix"></div>
<br>
<div class="form-group row">
  <div class="col-sm-6 perquessthide">
    <label>fee  :</label>
      {{ Form::text('booking_fee', null, ['class'=>'form-control','placeholder' => 'Enter Fee']) }}
    </div>
  <div class="col-sm-6 perquessthide">
    <label>deposit   :</label>
      {{ Form::text('booking_deposit', null, ['class'=>'form-control','placeholder' => 'Enter Deposit']) }}
  </div>
</div>

@endif
</div>
@if($propertyObj->opn_secret_key !='')

<div class="col-lg-6">
<div class="form-group row">

<div class="col-sm-4 ">
    <label>cut off day(s)  :</label>
      {{ Form::text('cut_of_days', null, ['class'=>'form-control','placeholder' => 'Enter cut off day(s) to cancel']) }}
    </div>
  <div class="col-sm-8 ">
    <label>cancellation charge (%)
deducted from booking fee :</label>
      {{ Form::text('cut_of_amount_percentage', null, ['class'=>'form-control','placeholder' => 'Enter cancellation charge']) }}
  </div>
</div>
</div>
@endif
   <div class="col-lg-6">
      <div class="form-group ">
        <label>booking timing slot:<br>(Separate time slot by comma,<br>
example:10:00AM,11:00AM,<br>
or 10:00AM-12:00PM.)</label>
        {{ Form::textarea('timing', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Available booking slots']) }}
        
      </div>
  </div>

   <div class="col-lg-6">
      <div class="form-group">
          <label>block out dates : <p class="pcp">
          (YYYY-MM-DD) <br>
Separate the dates with comma, <br>
example 2021-10-01,2021-10-31</p></label>
        {{ Form::textarea('blockout_days', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
        <span class="resp">  (YYYY-MM-DD) Separate the dates with comma, example 2021-10-01,2021-10-31 </span>
    
      </div>
    </div>

  <div class="col-lg-6">
      <div class="form-group">
        <label >notes:</label>
        {{ Form::textarea('notes', null, ['class'=>'form-control','rows'=>9,'required' => false,'placeholder' => 'Enter notes']) }}
        
      </div>
  </div>

</div>

     <div class="row">
        <div class="col-lg-12">
           <button type="submit" class="submit mt-2 float-right">submit</button>
        </div>
     </div>
    {!! Form::close() !!}
              
               
            </div>
         </div>
      </section>


@stop

<script>
window.onload = function() {
showday();
};
function  showday(){
  var option_val = $("#booking_option").val();
  if(option_val ==3){
    $('#days').show();
  }
  else{
    $('#days').hide();
  }
  //alert(option_val);
}
</script>