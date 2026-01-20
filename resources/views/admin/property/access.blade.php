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
    <h1> Property Modules - Update 
  </h1>
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
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


        <div class="">
                 {!! Form::model($PropertyObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/configuration/property/accessupdate/'.$PropertyObj->id),'class'=>'forunit']) !!}

               


                     <div class="row">
					 
                        <div class="col-lg-12 asignFace">
						<div class="form-group mt0-3">
                              <label> property : <span class="comspan">  {{$PropertyObj->company_name}}</span></label>
                           
                           </div>
                  <h2>modules</h2>
               </div>
              <table class="table usertable1  ">
			 
                <thead>
                  <tr> 
                    <th colspan="2">group name</th>   
                    <!--th> <span style="text-align: left;     padding-left: 0px;" class="form-group form-check">MODULE &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<input type="checkbox" id="ckbCheckAll" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">CHECK All</span></th-->
                    <th>
<label class="containeruser1">  full access
                                 <input type="checkbox" id="checkAllView" name="check_all" value="1" class =' id1'>
                                    <span class="checkmarkuser1"></span>
                                    </label>
					</th>
                    <th> 
					<label class="containeruser1"> view access
                      <input type="checkbox" id="checkAllViewOnly" name="check_view_all" value="2" class =' id2'>
                                    <span class="checkmarkuser1"></span>
                                    </label>
					</th>
                    <th> 
					<label class="containeruser1">  hide
					<input type="checkbox" id="checkAllHideOnly" name="check_hide_all" value="0" class =' id3'>
                                    <span class="checkmarkuser1"></span>
                                    </label>
					</th>
                    
                  </tr>
                </thead>

                <tbody>
              @if($groups)
                @foreach($groups as $group)
                  <tr >
                    <th colspan="5" style="background-color:#DFCFB5"><h3 class="mb-0">{{$group->name}}</h3></th>
                  </tr>
                  @if($group->modules)
                    @foreach($group->modules as $module)
                      @php
                      $view =false;
                      $create=false;
                      $edit =false;
                      $delete =false;
                      $array_exist=false;
                        if(isset($role_access[$module->id])) {
                          $array_exist = true;
                          if($role_access[$module->id][0] ==1)
                            $view=1;
                          else if($role_access[$module->id][0] ==2)
                            $view=2;
                          else
                            $view=0;

                          if($role_access[$module->id][1] ==1)
                            $create=1;
                          if($role_access[$module->id][2] ==1)
                            $edit=1;
                          if($role_access[$module->id][3] ==1)
                            $delete=1;
                        }
                      @endphp   
                      <tr style="background-color:#a9f1f9" class="unitstate">
                       
                        <td colspan="2">{{$module->name}}</td>
                        <td><input type="radio" name="mod_view_{{$module->id}}"  value="1" class ='viewCheckBox class1 checkBoxClass' {{ ($view ==1) ?'checked':'' }} style="width: 17px;height: 17px; vertical-align: sub;">&nbsp; Full 
                        </td> 
                        <td>
                        <input type="radio" name="mod_view_{{$module->id}}"  value="2" class ='viewCheckBox class2 checkBoxClass' {{ ($view ==2) ?'checked':'' }} style="width: 17px;height: 17px; vertical-align: sub;">&nbsp; View</td> 
                        <td>
                        <input type="radio" name="mod_view_{{$module->id}}"  value="0" class ='viewCheckBox class3 checkBoxClass' {{ ($view ==0) ?'checked':'' }} style="width: 17px;height: 17px; vertical-align: sub;">&nbsp; Hide</td>                                 
                      </tr>
                      @endforeach
                  @endif
                @endforeach   
              @endif
            </tbody>

        </table>
            
                        <div class="col-lg-12">
                           <button type="submit" class="submit ml-3 mt-2 float-right">submit</button>
                        </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


