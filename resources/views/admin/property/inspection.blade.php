
@extends('layouts.adminnew')




@section('content')

@php
  $user = Auth::user();
  $jointinspection =  $PropertyObj->check_property_permission(57,  $PropertyObj->id,1);
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
   $permission = $permission->check_permission(57,$permission->role_id); 
@endphp

 <div class="status">
 <h1>defect inspection appoinment settings- update  </h1>
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
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/inspectionappoinment')}}">Defects Inspection  <br>Appointment Settings</a></li>
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
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

    

       <div class="">
                {!! Form::model($PropertyObj,['method' =>'PATCH','class'=>'forunit','files' => true,'url' => url('opslogin/configuration/inspectionappoinmentupdate/'.$PropertyObj->id)]) !!}

                <div class="row asignbg editbg">
                    
                    @php  
                    
                    if(isset( $jointinspection) && $jointinspection->view==1){
                    @endphp
                    <div class="col-lg-12">
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
                 <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>defects inspection calendar availability :<br><p class="pcp">(In Days) </p></label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::number('inspection_availability_start', null, ['class'=>'form-control','required' => true,'placeholder' => 'Calendar availability start']) }}</span>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>defects inspection block out dates : <p class="pcp">
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

                   <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>defects inspection booking notes : </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('inspection_notes', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Notes']) }}
                         </div>
                      </div>
                    </div>

                    <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>final inspection required? : </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::select('final_inspection_required', ['1' => 'Yes','0'=>'No'] ,$PropertyObj->final_inspection_required, ['class'=>'form-control','id'=>'final_inspection_required']) }}
                         </div>
                      </div>
                    </div>
                     <div class="col-lg-12">
                        <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">
                           <label>fomr defects submission limit (AereaHome App)? : </label>
                           </label>
                           <div class="col-sm-8">
                           {{ Form::text('defect_max_limit', null, ['class'=>'form-control']) }}
                        </div>
                     </div>
                    </div>

                    @php
                    }
                    @endphp

                  </div>
               
                       
                  @if(isset($permission) && $permission->create==1 && $admin_id !=1)

                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                  @endif
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop



