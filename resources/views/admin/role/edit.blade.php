
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
   $sharesetting =  $permission->check_menu_permission(63,$permission->role_id,1);
   $permission = $permission->check_permission(23,$permission->role_id); 
@endphp
 <div class="status">
    <h1>manage role - update  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                    @if(isset($role->view) && $role->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/role#rolesettings')}}">Manage Role </a></li>
                     @endif

                     @if(isset($building->view) && $building->view==1 )
                        <li><a href="{{url('/opslogin/configuration/building#buildingsettings')}}">Manage Block </a></li>
                     @endif

                     @if(isset($unit->view) && $unit->view==1 )
                        <li><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
                     @endif

                     @if(isset($sharesetting->view) && $sharesetting->view==1 )
                        <li><a href="{{url('/opslogin/configuration/sharesettings#unitsettings')}}">Manage Mgmt/Sinking Fund </a></li>
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
                {!! Form::model($roleObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/configuration/role/'.$roleObj->id)]) !!}

                  <div class="row asignbg editbg">
                  @if(@Auth::user()->role_id ==1 && !in_array($roleObj->id,$user_app_roles))
                <div class="col-lg-4">
                           <div class="form-group ">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                 <div class="col-lg-4">
                           <div class="form-group">
                              <label>role title :</label>
                                {{ Form::text('name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Role']) }}
                             
                           </div>
                </div>
                 </div>

                @if(!empty($devices) && $device_display ==1)
                     	<div class="col-lg-12 asignFace">
                  <h2>available devices</h2>
               </div>
                 
                    <div class="overflowscroll">
					 <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>Device Name </th>
                           <th>Serial No. </th>
                           <th>Location</th>
                           <th>
						     <label class="containeruser1">  Bluetooth Door Open 
                                   <input type="checkbox" class="id1" name="device_13" value="1">
                                    <span class="checkmarkuser1"></span>
                                    </label></th>
                           <th>
						     <label class="containeruser1">  Remote Door Open
                                   <input type="checkbox" class="id2" name="device_13" value="1"> 
                                    <span class="checkmarkuser1"></span>
                                    </label> </th>
                        </tr>
                     </thead>
                     <tbody>
                     @if(isset($devices))
                        @foreach($devices as $device)
                            @php
                                if($roleObj->id ==3){
                                    $addition = 1;
                                   $building_name =  isset($device->buildinginfo->building)?$device->buildinginfo->building:'';
                                    $add_info = $device->propertyinfo->company_name ." - ". $building_name;
                                }                                
                                else{
                                    $addition = 0;
                                    $add_info = '';
                                }
                                    
                            @endphp
                            <tr>
                              <td> {{$device->device_name}} @if($addition ==1)<br /><i>{{$add_info}}</i> @endif</td>
                              <td>{{$device->device_serial_no}}</td>
                              <td>{{isset($device->buildinginfo->building)?$device->buildinginfo->building:''}}</td>
                              <td>
							   <label class="containeruser1" style="    margin-top: -10px;"> 
                                  <input type="checkbox" class="class1"  name="device_{{$device->id}}"  value="1" class ='viewCheckBox' {{(in_array($device->id,$device_access)) ?'checked':'' }}>
                                    <span class="checkmarkuser1"></span>
                                    </label> 
							 </td>
                              <td>
							   <label class="containeruser1" style="    margin-top: -10px;"> 
                                   <input type="checkbox" class="class2"  name="device_remote_{{$device->id}}"  value="1" class ='viewCheckBox' {{(in_array($device->id,$device_remote_access)) ?'checked':'' }}>
                                    <span class="checkmarkuser1"></span>
                                    </label> 
							</td>
                            </tr>
                        @endforeach
                      @endif
					
                     </tbody>
                  </table>	
					
                    </div>
               @endif

                   
                    @if(!in_array($roleObj->id,$user_app_roles))
        	<div class="col-lg-12 asignFace">
                  <h2>system access</h2>
               </div>
                     <div class="">
                 
			   <div class="overflowscroll">
                            <table class="table usertable1 ">
                              <thead>
                                <tr>    
                                  <th>Module
  <label class="containeruser1">   Check All
                                  <input type="checkbox" id="ckbCheckAll" name="check_all" value="1" class ='form-check-input'>
                                    <span class="checkmarkuser1"></span>
                                    </label></th>
                                  <th> 
								    <label class="containeruser1">  View
                                 <input type="checkbox" id="checkAllView" name="check_all" value="1" class ='id3 form-check-input'>
                                    <span class="checkmarkuser1"></span>
                                    </label>
								  </th>
                                  <th>
								   <label class="containeruser1">  Add / Create
                                 <input type="checkbox" id="checkAllAdd" name="check_all" value="1" class ='id4 form-check-input'>
                                    <span class="checkmarkuser1"></span>
                                    </label></th>
                                  <th>
								  <label class="containeruser1"> Edit
                                 <input type="checkbox" id="checkAllEdit" name="check_all" value="1" class ='id5 form-check-input'>
                                    <span class="checkmarkuser1"></span>
                                    </label></th>
                                  <th>
								   <label class="containeruser1"> Delete
                                <input type="checkbox" id="checkAllDelete" name="check_all" value="1" class ='id6 form-check-input'>
                                    <span class="checkmarkuser1"></span>
                                    </label></th>
                                </tr>
                              </thead>

                              <tbody>
                            @if($modules)

                            @foreach($modules as $module)
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
                                if($role_access[$module->id][1] ==1)
                                  $create=1;
                                if($role_access[$module->id][2] ==1)
                                  $edit=1;
                                if($role_access[$module->id][3] ==1)
                                  $delete=1;
                              }
                            @endphp   
                            <tr >
                              
                              <td>{{$module->name}}</td>
                              <td>
							    <label class="containeruser1 mt15"> 
                                <input type="checkbox" name="mod_view_{{$module->id}}"  value="1" class ='class3 checkBoxClass viewCheckBox' {{ ($view ==1) ?'checked':'' }} >
                                    <span class="checkmarkuser1"></span>
                                    </label>
							  </td>
                              <td>
							   <label class="containeruser1 mt15"> 
                                 <input type="checkbox" name="mod_add_{{$module->id}}" value="1" class ='class4 checkBoxClass addCheckBox' {{ ($create ==1) ?'checked':'' }} >
                                    <span class="checkmarkuser1"></span>
                                    </label>
							</td>
                              <td>
							   <label class="containeruser1 mt15"> 
                                <input type="checkbox" name="mod_edit_{{$module->id}}" value="1" class ='class5 checkBoxClass editCheckBox' {{ ($edit ==1) ?'checked':'' }} >
                                    <span class="checkmarkuser1"></span>
                                    </label>
							 </td>
                              @if($module->id !=28)
                              <td>
						    <label class="containeruser1 mt15"> 
                                <input type="checkbox" name="mod_delete_{{$module->id}}" value="1" class ='class6 checkBoxClass deleteCheckBox' {{ ($delete ==1) ?'checked':'' }} >
                                    <span class="checkmarkuser1"></span>
                                    </label>
						 </td>
                              @else
                              <td></td>
                              @endif
                              
                            </tr>

                            @endforeach

                            @endif

                            

                          </tbody>

                        </table>
                            </div>         
                      </div>
                    @endif
                     <div class="row">
                        <div class="col-lg-12 ">
                           <button type="submit" class="submit  mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop



