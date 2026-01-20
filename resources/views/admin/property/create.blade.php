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
   $permission = $permission->check_permission(28,$permission->role_id); 
@endphp

<style>
  input::file-selector-button {
    border: thin solid grey;
    border-radius: 20px!important;
}
</style>
<!-- Content Header (Page header) -->

  <div class="status">
    <h1> property - add </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if( isset($property->view) && $property->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
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
                        <li><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                     @endif
                     
                     @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
                     @endif
                  </ul>
               </div>
               </div>

       <div class="">
                {!! Form::open(['method' => 'POST','class'=>'forunit', 'url' => url('opslogin/configuration/property'), 'files' => true]) !!}

                  <div class="row asignbg">
                    <div class="col-lg-4 col-12">
                          <label>property name  <span>*</span>: </label>
                    
                          {{ Form::text('company_name', null, ['class'=>'form-control','required' => true]) }}
                      
                    </div>
                  <div class="col-lg-4 col-12">
                          <label>short code  <span>*</span>: </label>
                    
                          {{ Form::text('short_code', null, ['class'=>'form-control','required' => true]) }}
                      
                    </div>
                   <div class="col-lg-4 col-12">
                          <label>account due date  <span>*</span>: </label>
                      
                          {{ Form::text('due_date', null, ['class'=>'form-control datetext9','required' => true]) }}
                       
                    </div>
                    <div class="col-lg-4 col-12">
                          <label>person-in-charge contact  <span>*</span>: </label>
                   
                          {{ Form::text('company_contact', null, ['class'=>'form-control','required' => true]) }}

                    </div>
                    <div class="col-lg-4 col-12">
                          <label>person-in-charge email  <span>*</span>: </label>
                  
                          {{ Form::text('company_email', null, ['class'=>'form-control','required' => true]) }}
                    
                    </div>

                    <div class="col-lg-4 col-12">
                          <label>property address  <span>*</span>: </label>
                       
                          {{ Form::textarea('company_address', null, ['class'=>'form-control','rows' => 3,'required' => true]) }}
                     
                    </div>

                    

                  <div class="col-lg-4 col-12">
                    
                          <label>management company name  <span>*</span>: </label>
              
                          {{ Form::text('management_company_name', null, ['class'=>'form-control','required' => true]) }}
                   
                    </div>

                    <div class="col-lg-4 col-12">
                    
                          <label>management company address  <span>*</span>: </label>
                      
                          {{ Form::textarea('management_company_addr', null, ['class'=>'form-control','rows' => 3,'required' => true]) }}
                    
                    </div>

                  

                   <div class="col-lg-4 col-12">
                     
                          <label>mcst code  <span>*</span>: </label>
                      
                          {{ Form::text('mcst_code', null, ['class'=>'form-control','required' => true]) }}
                      
                    </div>
                    <!--<div class="col-lg-8">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>DEVICE PROVIDER *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::select('third_party_option', ['0' => '--NA--'] + $services, null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>-->
                    @if(1 ==2)
                    <div class="col-lg-4 col-12">
                          <label>invoice notes  <span>*</span>: </label>
                      
                          {{ Form::textarea('invoice_notes', null, ['class'=>'form-control','rows' => 3,'required' => true]) }}
                    
                    </div>
                    <div class="col-lg-4 col-12">
                    
                          <label>account enquiry email  <span>*</span>: </label>
                      
                          {{ Form::text('enquiry_email', null, ['class'=>'form-control','required' => true]) }}
                       
                    </div>

                  <div class="col-lg-4 col-12">
                     
                          <label>account enquiry  contact  <span>*</span>: </label>
                      
                          {{ Form::text('enquiry_contact', null, ['class'=>'form-control','required' => true]) }}
                      
                    </div>
                    

                   
                   <div class="col-lg-4 col-12">
                   
                          <label>paynow method  <span>*</span>: </label>
                
                        {{ Form::select('qrcode_option', ['1' => 'Dynamic QR Code','2'=>'Manual Upload QR Code'] ,'Null', ['class'=>'form-control','id'=>'qrcode_option']) }}
				       
                    </div>
                    <div class="col-lg-4 col-12">
                     
                          <label>OPN Merchant Id  <span>*</span>: </label>
                      
                          {{ Form::text('opn_secret_key', null, ['class'=>'form-control']) }}
                      
                    </div>
                    @endif
                    <!--<div class="col-lg-4 col-12">
                     
                          <label>OPN password  <span></span>: </label>
                      
                          {{ Form::text('opn_password', null, ['class'=>'form-control']) }}
                      
                    </div>-->
                    <div class="col-lg-4 col-12">
                   
                          <label>otp option (Home App)<span>*</span>: </label>
                     
                        {{ Form::select('otp_option', ['1' => 'Email','2'=>'SMS'] ,'Null', ['class'=>'form-control','onchange'=>'getsmsnewfields()','id'=>'otp_option']) }}
				    
                    </div>
                     <div class="col-lg-4 col-12">
                   
                          <label>otp option (OPS / Manager App)<span>*</span>: </label>
                     
                        {{ Form::select('manager_otp_option', ['1' => 'Email','2'=>'SMS'] ,'Null', ['class'=>'form-control','onchange'=>'getsmsnewfields()','id'=>'manager_otp_option']) }}
				    
                    </div>

                    <div class="col-lg-4 col-12" id="sms_field" style="display:none">
                    
                          <label>SMS Provider username: </label>
                  
                        {{ Form::text('sms_username', null, ['class'=>'form-control','id'=>'sms_username']) }}				                          
                                     
                
                          <label>SMS Provider password : </label>
                      
                        {{ Form::text('sms_password', null, ['class'=>'form-control','id'=>'sms_password']) }}				                          
                     
                    </div>

                        <div class="col-lg-4 col-12">
                          <label>security option  <span>*</span>: </label>
                   
                        {{ Form::select('security_option', ['1' => 'Facial Recognition','2'=>'Manual Check','3'=>'Both'] ,'Null', ['class'=>'form-control']) }}
				         
                    </div>


                     <div class="col-lg-4 col-12">
                          <label>logo  <span>*</span>: </label>
                     <div class="clearfix"></div>
                        {{ Form::file('company_logo', null, ['class'=>'form-control','required' => false]) }}
                          
                    </div>
                    <div class="col-lg-4 col-12">
                          <label>Open For Registration  <span>*</span>: </label>
                     <div class="clearfix"></div>
                        {{ Form::select('open_for_registration', ['1' => 'Yes','0'=>'No'] ,'Null', ['class'=>'form-control','id'=>'open_for_registration']) }}
                          
                    </div>
                    
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt0-2 float-right">submit</button>
                        </div>
                 
                  </div>
                    
                    
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">      
function getsmsnewfields(){
   var option =  $("#otp_option").val();
   var manager_option =  $("#manager_otp_option").val();
//alert(option);
   if(option==2 || manager_option ==2){
      $('#sms_field').show();
   }else{
      $('#sms_field').hide();
      $('#sms_username').val('');
      $('#sms_password').val('');
   }
}
</script>
@stop

