@extends('layouts.adminnew')



@section('content')

@php
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
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
   $sharesetting =  $permission->check_menu_permission(73,$permission->role_id,1);
@endphp


<!-- Content Header (Page header) -->

   <div class="status">
        <h1>Operation Portal - Settings</h1>
    </div>

 @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif


    <div class="row">
               <div class="col-lg-12">
                  @php
                  if(isset($settings->view) && $settings->view ==1 || isset($module->view) && $module->view ==1 ||  isset($role->view) && $role->view ==1 || isset($unit->view) && $unit->view ==1 || isset($menu->view) &&  $menu->view ==1 ||  isset($building->view) &&  $building->view ==1 ||  isset($sharesetting->view) &&  $sharesetting->view >=1||  (isset($inspection_setting->view) &&  $inspection_setting->view >=1)){
                     if(isset($role->view) && $role->view ==1)
                        $setting_url = url('opslogin/configuration/role#rolesettings');
                     else if(isset($unit->view) && $unit->view ==1)
                        $setting_url = url('opslogin/configuration/unit#unitsettings');
                     else if(isset($building->view) && $building->view ==1)
                        $setting_url = url('opslogin/configuration/building#buildingsettings');
                     else if(isset($sharesetting->view) && $sharesetting->view ==1)
                        $setting_url = url('opslogin/configuration/sharesettings#unitsettings');
                     else
                        $setting_url = '#';

                  @endphp
                  <div class="row rescol">
                     <div class="col-lg-3">
                        <div class="serviceBox">
                           <a href="{{$setting_url}}" class="datafech">
                              <div class="service-content imagew">
                                 <div class="service-icon imagew my-auto setimg">
                                    <span><img src="{{url('assets/img/Propertymanagement.png')}}"></span>
                                    Property <br>Management
                                 </div>
                              </div>
                           </a>
                        </div>
                     </div>
                     @php 
                     }
                     if(isset($eforms->view) &&  $eforms->view >=1|| isset($payment->view) &&  $payment->view >=1||  (isset($holiday->view) &&  $holiday->view >=1)){
                        if(isset($eforms->view) && $eforms->view ==1)
                           $eform_setting_url =  url('opslogin/configuration/eform_setting#eformsettings');
                        else if(isset($payment->view) && $payment->view ==1)
                           $eform_setting_url = url('opslogin/configuration/payment_setting#paymentsettings');
                        else if(isset($holiday->view) && $holiday->view ==1)
                           $eform_setting_url = url('opslogin/configuration/holiday_setting#holidayssettings');
                     @endphp
                     <div class="col-lg-3">
                        <div class="serviceBox">
                           <a href="{{$eform_setting_url}}" class="datafech">
                              <div class="service-content imagew">
                                 <div class="service-icon imagew my-auto setimg">
                                    <span><img src="{{url('assets/img/e-formsettings.png')}}"></span>
                                    E-Forms
                                 </div>
                              </div>
                           </a>
                        </div>
                     </div>
                     @php 
                     }
                     if((isset($key_setting->view) &&  $key_setting->view >=1 )||  (isset($inspection_setting->view) &&  $inspection_setting->view >=1) || (isset($feedback->view) &&  $feedback->view >=1 ) || (isset($defect->view) &&  $defect->view >=1 ) || (isset($facility->view) &&  $facility->view >=1 ) || (isset($facility->view) &&  $facility->view >=1 )|| (isset($facility->view) &&  $facility->view >=1 )|| (isset($vm->view) &&  $vm->view >=1 )|| (isset($dashmenu->view) &&  $dashmenu->view >=1 )){
                        if(isset($property->view) && $property->view==1)
                           $other_setting_url =  url('/opslogin/configuration/property#settings');
                        else if((isset($key_setting) && $key_setting->view>=1) && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/collectionappoinment');
                        else if((isset($inspection_setting) && $inspection_setting->view>=1) && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/inspectionappoinment');
                        else if(isset($menu->view) && $menu->view==1 && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/menu#menusettings');
                        else if(isset($feedback->view) && $feedback->view==1 && $admin_id !=1)
                           $other_setting_url =  url('/opslogin/configuration/feedback#feedbacksettings');
                        else if(isset($defect->view) && $defect->view==1 && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/defect#defectsettings');
                        else if(isset($facility->view) && $facility->view==1 && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/facility#facilitysettings');
                        else if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                           $other_setting_url = url('/opslogin/configuration/purpose#visitingsettings');
                        else
                        $other_setting_url = '#';
                     @endphp
                     <div class="col-lg-3">
                        <div class="serviceBox">
                           <a href="{{$other_setting_url}}" class="datafech">
                              <div class="service-content imagew">
                                 <div class="service-icon imagew my-auto setimg">
                                    <span><img src="{{url('assets/img/Settings.png')}}"></span>
                                    Others
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
                           <a href="{{url('opslogin/configuration/updatepassword')}}" class="datafech">
                              <div class="service-content imagew">
                                 <div class="service-icon imagew my-auto setimg">
                                    <span><img src="{{url('assets/img/Changepassword.png')}}"></span>
                                    Change Password
                                 </div>
                              </div>
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

      
          <!-- /.box -->

    </section>  

@stop
