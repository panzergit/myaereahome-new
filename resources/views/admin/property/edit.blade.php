
@extends('layouts.adminnew')




@section('content')

@php
  $user = Auth::user();
  $takeover =  $PropertyObj->check_property_permission(2,  $PropertyObj->id,1);

  $jointinspection =  $PropertyObj->check_property_permission(4,  $PropertyObj->id,1);
@endphp

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
   $resident =  $permission->check_menu_permission_level(61,$PropertyObj->id);

   $permission = $permission->check_permission(28,$permission->role_id); 


   //print_r( $resident);
@endphp
 <div class="status">
    <h1>{{($user->role_id==1)?"property":"appoinment settings"}}- update  </h1>
  </div>
<div class="row">
 <div class="row"> 
               <div class="col-lg-12">
                  <ul class="summarytab">
                    @if(Auth::user()->role_id ==1)
                        <li class="activeul"><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
                        <li  ><a href="{{url('/opslogin/configuration/banner#settings')}}">Home Banner  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/ads#settings')}}">Ads  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/econcierge#settings')}}">E-Cconcierge  <br>Management</a></li>
                    @else
                      @if(isset($property->view) && $property->view==1 )
                          <li class="activeul"><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
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
                          <li ><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                      @endif
                      
                      @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                          <li><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
                      @endif
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
                {!! Form::model($PropertyObj,['method' =>'PATCH','class'=>'forunit','files' => true,'url' => url('opslogin/configuration/property/'.$PropertyObj->id)]) !!}

                <div class="row asignbg editbg">
                    
                    @php  if($user->role_id ==1){ @endphp
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                          <label>property name : </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('company_name', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>short code *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('short_code', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <!--<div class="col-lg-9">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                            <label>DEVICE PROVIDER *: </label>
                          </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::select('third_party_option', ['0' => '--NA--'] + $services, null, ['class'=>'form-control','required' => true]) }}                        
                        </div>
                      </div>
                    </div> -->

                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                            <label>account due date *: </label>
                          </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('due_date', null, ['class'=>'form-control datetext9','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    @php } @endphp
                   
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                          <label>person-in-charge contact : </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('company_contact', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                          <label>person-in-charge email : </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('company_email', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>property address *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::textarea('company_address', null, ['class'=>'form-control','rows' => 3,'required' => true]) }}
                        </div>
                      </div>
                    </div>

                    

                    <div class="col-lg-6">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>management company name *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('management_company_name', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>management company address *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::textarea('management_company_addr', null, ['class'=>'form-control','rows' => 3,'required' => true]) }}
                        </div>
                      </div>
                    </div>
                    @if(isset($resident->view) && $resident->view==1 )
                      <div class="col-lg-6">
                        <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                            <label>invoice notes *: </label>
                          </label>
                          <div class="col-sm-8 col-6">
                            {{ Form::textarea('invoice_notes', null, ['class'=>'form-control','rows' => 6,'required' => true]) }}
                          </div>
                        </div>
                      </div>
                    @endif
                    <div class="col-lg-6">
                      <div class="form-group row">
                      <label  class="col-sm-4 col-6 col-form-label">
                          <label>mcst code *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                          {{ Form::text('mcst_code', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>
                    
                    @if(isset($resident->view) && $resident->view==1 )
                      <div class="col-lg-6">
                        <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                            <label>account enquiry email *: </label>
                          </label>
                          <div class="col-sm-8 col-6">
                            {{ Form::text('enquiry_email', null, ['class'=>'form-control','required' => true]) }}
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group row">
                        <label  class="col-sm-4 col-6 col-form-label">
                            <label>account enquiry contact *: </label>
                          </label>
                          <div class="col-sm-8 col-6">
                            {{ Form::text('enquiry_contact', null, ['class'=>'form-control','required' => true]) }}
                          </div>
                        </div>
                      </div>
                    @endif

                    @php  if($user->role_id ==1){ @endphp

                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>otp option (Home App)*: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::select('otp_option', ['1' => 'Email','2'=>'SMS'] ,null, ['class'=>'form-control','onchange'=>'getsmsnewfields()','id'=>'otp_option']) }}				                          
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>otp option (OPS / Manager App)*: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::select('manager_otp_option', ['1' => 'Email','2'=>'SMS'] ,null, ['class'=>'form-control','onchange'=>'getsmsnewfields()','id'=>'manager_otp_option']) }}				                          
                        </div>
                      </div>
                    </div>
                    @php
                    if($PropertyObj->otp_option ==2)
                      $display = "block";
                    else
                      $display = "none";
                    @endphp

                    @if($user->role_id ==1 && (isset($resident->view) && $resident->view==1 ))
                <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>paynow method *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::select('qrcode_option', ['1' => 'Dynamic QR Code','2'=>'Manual Upload QR Code'] ,null, ['class'=>'form-control']) }}
				               </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>OPN Merchant Id : </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::text('opn_secret_key', null, ['class'=>'form-control']) }}
				               </div>
                      </div>
                    </div>
                    <!--<div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>OPN password : </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::text('opn_password', null, ['class'=>'form-control']) }}
				               </div>
                      </div>
                    </div> -->
                    @endif
                    <div class="col-lg-6" id="sms_field" style="display:{{$display}}">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>SMS Provider username: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::text('sms_username', null, ['class'=>'form-control','id'=>'sms_username']) }}				                          
                        </div>
                      </div>                   
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>SMS Provider password : </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::text('sms_password', null, ['class'=>'form-control','id'=>'sms_password']) }}				                          
                        </div>
                      </div>
                    </div>

                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>security option *: </label>
                        </label>
                        <div class="col-sm-8 col-6">
                        {{ Form::select('security_option', ['1' => 'Facial Recognition','2'=>'Manual Check','3'=>'Both'] ,null, ['class'=>'form-control']) }}
				                          
                        </div>
                      </div>
                    </div>

                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>logo (100px X 150px):</label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                           @if(!empty($PropertyObj->company_logo))
                             <div class="col-sm-4">
                                 <label for="file-input">
                                    <img src="{{$file_path}}/{{$PropertyObj->company_logo}}" class="viewimg"/>
                                 </label>
                              </div>
                           @endif
                              <div class="col-sm-6">
                                 {{ Form::file('company_logo', null, ['class'=>'form-control','required' => false]) }}
                              </div>
                           </div>
                        </div>
                      </div>
                    </div>

                  @if(1==2)
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>default background <br>(1360px X 600px):</label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                           @if(!empty($PropertyObj->default_bg))
                             <div class="col-sm-4">
                                 <label for="file-input">
                                    <img src="{{$file_path}}/{{$PropertyObj->default_bg}}" class="viewimg"/>
                                 </label>
                              </div>
                           @endif
                              <div class="col-sm-6">
                                 {{ Form::file('default_bg', null, ['class'=>'form-control','required' => false]) }}
                              </div>
                           </div>
                             
                        </div>
                      </div>
                    </div>

                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>faq background <br>(1360px X 600px):</label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                           @if(!empty($PropertyObj->faq_bg))
                             <div class="col-sm-4">
                                 <label for="file-input">
                                    <img src="{{$file_path}}/{{$PropertyObj->faq_bg}}" class="viewimg"/>
                                 </label>
                              </div>
                           @endif
                              <div class="col-sm-6">
                                 {{ Form::file('faq_bg', null, ['class'=>'form-control','required' => false]) }}
                              </div>
                           </div>
                             
                        </div>
                      </div>
                    </div>
                    @php
                    
                    $announcement =  $PropertyObj->check_property_permission(1,  $PropertyObj->id,1);
                    if(isset( $announcement) && $announcement->view==1){
                    @endphp
                 <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>announcemnt background<br> (1360px X 600px):</label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->announcement_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->announcement_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('announcement_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    if(isset( $takeover) && $takeover->view==1){
                    @endphp
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>key collection background <br>(1360px X 600px):</label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->takeover_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->takeover_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('takeover_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    $defects =  $PropertyObj->check_property_permission(3,  $PropertyObj->id,1);
                    if(isset( $defects) && $defects->view==1){
                    @endphp
                   
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>defect background<br> (1360px X 600px): </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->defect_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->defect_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('defect_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                        </div>
                      </div>
                    </div>
                    @php 

                    
                    }
                    
                    
                   
                    if(isset( $jointinspection) && $jointinspection->view==1){
                    @endphp
                   
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>defects inspection background <br>(1360px X 600px): </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->inspection_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->inspection_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('inspection_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    
                    
                    $feedback =  $PropertyObj->check_property_permission(6,  $PropertyObj->id,1);
                    if(isset( $feedback) && $feedback->view==1){
                    @endphp
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>feedback background <br>(1360px X 600px): </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->feedback_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->feedback_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('feedback_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    $facility =  $PropertyObj->check_property_permission(5,  $PropertyObj->id,1);
                    if(isset( $facility) && $facility->view==1){
                    @endphp
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>facilities background <br>(1360px X 600px): </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->facilities_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->facilities_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('facilities_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                            
                        </div>
                      </div>
                    </div>

                    @php 
                    }
                    
                    $condodocs =  $PropertyObj->check_property_permission(32,  $PropertyObj->id,1);
                    if(isset($condodocs) && $condodocs->view==1){
                    @endphp
                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>condo document upload background *: </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->condodocs_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->condodocs_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('condodocs_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                            
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    $residentupload =  $PropertyObj->check_property_permission(33,  $PropertyObj->id,1);
                    if(isset( $residentupload) && $residentupload->view==1){
                    @endphp
                  <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>resident file upload background *: </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->resident_fileupload_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->resident_fileupload_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('resident_fileupload_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                            
                        </div>
                      </div>
                    </div>
                    @php 
                    }
                    $visitor =  $PropertyObj->check_property_permission(34,  $PropertyObj->id,1);
                    if(isset( $visitor) && $visitor->view==1){
                    @endphp
                    <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>visitor management background *: </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->visitor_management_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->visitor_management_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('visitor_management_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                            
                        </div>
                      </div>
                    </div>

                   <div class="col-lg-6">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>facial recognition background *: </label>
                        </label>
                        <div class="col-sm-8">
                           <div class="form-group row">
                              @if(!empty($PropertyObj->facial_reg_bg))
                              <div class="col-sm-4">
                                    <label for="file-input">
                                       <img src="{{$file_path}}/{{$PropertyObj->facial_reg_bg}}" class="viewimg"/>
                                    </label>
                                 </div>
                              @endif
                                 <div class="col-sm-6">
                                    {{ Form::file('facial_reg_bg', null, ['class'=>'form-control','required' => false]) }}
                                 </div>
                           </div>
                            
                        </div>
                      </div>
                    </div>
                    @php
                    }
                    @endphp
                    @endif
                    @php
                    }

                    if( 1==2) {
                      if(isset( $takeover) && $takeover->view==1){
                      @endphp
                     <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>key collection timing :<br><p class="pcp">(Separate time slot by comma,<br> example:10:00AM,11:00AM,<br> or 10:00AM-12:00PM.) </p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('takeover_timing', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Time Slot']) }}
                          <span class="resp"> (Separate time slot by comma, example:10:00AM,11:00AM, or 10:00AM-12:00PM.) </span>
                          </div>
                        </div>
                      </div>

<div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>key collection calendar availability :<br><p class="pcp">(In Days) </p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::number('takeover_availability_start', null, ['class'=>'form-control','required' => true,'placeholder' => 'Calendar availability start']) }}</span>
                          </div>
                        </div>
                      </div>

                     <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>key collection block out dates :<p class="pcp"> 
                            (YYYY-MM-DD) <br>
  Separate the dates with comma, <br>
  example 2021-10-01,2021-10-31</p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('takeover_blockout_days', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
                          <span class="resp">  (YYYY-MM-DD) Separate the dates with comma, example 2021-10-01,2021-10-31</span>
                          </div>
                        </div>
                      </div>

                   <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>key collection<br> booking notes : </label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('takeover_notes', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Notes']) }}
                          </div>
                        </div>
                      </div>

                      @php 
                      }
                      
                      if(isset( $jointinspection) && $jointinspection->view==1){
                      @endphp
                     <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>defects inspection timing :<p class="pcp">(Separate time slot by comma,<br>example:10:00AM,11:00AM,<br> or 10:00AM-12:00PM.)</p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('inspection_timing', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Time Slot']) }}
                          <span class="resp">(Separate time slot by comma, example:10:00AM,11:00AM, or 10:00AM-12:00PM.)</span>  
                          </div>
                        </div>
                      </div>
                     <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>defects inspection calendar availability :<br><p class="pcp">(In Days) </p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::number('inspection_availability_start', null, ['class'=>'form-control','required' => true,'placeholder' => 'Calendar availability start']) }}</span>
                          </div>
                        </div>
                      </div>

                     <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>defects inspection<br> block out dates : <p class="pcp">
                            (YYYY-MM-DD) <br>
  Separate the dates with comma, <br>
  example 2021-10-01,2021-10-31</p></label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('inspection_blockout_days', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
                          <span class="resp">  (YYYY-MM-DD) Separate the dates with comma, example 2021-10-01,2021-10-31 </span>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>defects inspection<br> booking notes : </label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::textarea('inspection_notes', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Notes']) }}
                          </div>
                        </div>
                      </div>

                      

                      @php
                      }
                    }
                      @endphp
                      <div class="col-lg-6">
                        <div class="form-group row">
                          <label  class="col-sm-4 col-form-label">
                            <label>Open For Registration : </label>
                          </label>
                          <div class="col-sm-8">
                          {{ Form::select('open_for_registration', ['1' => 'Yes','0'=>'No'] ,$PropertyObj->open_for_registration, ['class'=>'form-control','id'=>'open_for_registration']) }}
                          </div>
                        </div>
                      </div>

                    
                  </div>
              
                       
                  @if((isset($property) && $property->edit>=1) || $admin_id ==1)
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt0-2 float-right">submit</button>
                        </div>
                     </div>
                  @endif
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



