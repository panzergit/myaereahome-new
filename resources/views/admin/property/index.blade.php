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

<div class="status">
  <h1>manage property</h1>
</div>
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
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
<div class="overflowscroll2">
                 <table class="gap">
                     @if(Auth::user()->role_id ==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/configuration/property/create")}}"  class="submit ml-3 mt-2 float-left" style="width:auto"> + Add New</a>
                           </div>
                       </div>
                    </div>
                     @endif
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>logo</th>
                           <th>property</th>
                           <th>contact no</th>
                           <th>contact email</th>
                           <th>due date</th>
                           <th>action</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($properities)

                        @foreach($properities as $k => $property)
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           <td class="spacer">@if(!empty($property->company_logo))
                                       <div class="image-upload">
                                          <label for="file-input">
                                          <img src="{{$file_path}}/{{$property->company_logo}}" class="viewimg"/>
                                          </label>
                                       </div>
                                    @endif</td>
                           <td class="spacer">{{$property->company_name}}</td>
                           <td class="spacer">{{$property->company_contact}}</td>
                           <td class="spacer">{{$property->company_email}}</td>
                           <td class="spacer">
                              @php
                              if($property->due_date  !=''){
                                 $popObj = new \App\Models\v7\Property();
                                 $rec = $popObj->CheckOverDue($property->id);
                                 echo $rec;
                              }else{
                                 echo "<font color='green'>Not Added</font>";
                              }
                              @endphp
                           </td>
                           <td class="roundright">
						    <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                            <a class="dropdown-item" href='{{url("opslogin/configuration/property/$property->id/edit")}}'>Edit</a>
                           @php if(Auth::user()->role_id ==1){ @endphp
                             
                           <a class="dropdown-item" href='{{url("opslogin/configuration/property/access/$property->id")}}'>Modules Settings</a>
                           @if($property->status ==0)
                              <a class="dropdown-item" href="#"onclick="activate_record('{{url("opslogin/configuration/property/activate/$property->id")}}');">Deactive</a>
                              @else
                              <a class="dropdown-item" href="#" onclick="deactivate_record('{{url("opslogin/configuration/property/deactivate/$property->id")}}');" >Active</a>
                              @endif
                           @php } @endphp
                           @if(Auth::user()->role_id ==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/property/delete/$property->id")}}');" >Delete</a>
                           @endif
                                       </div>
                                    </div>
						
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
@endsection


