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
@endphp

<!-- Content Header (Page header) -->
 <style>
			    .box{
        display: none;
    }
			   </style>
  <div class="status">
    <h1> home banner - add  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if( isset($property->view) && $property->view==1 )
                        <li><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
                     @endif

                     @if(Auth::user()->role_id ==1)
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/banner#settings')}}">Home Banner  <br>Management</a></li>
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
                {!! Form::open(['method' => 'POST','class'=>'forunit', 'url' => url('opslogin/configuration/banner'), 'files' => true]) !!}

                  <div class="row asignbg">
                    <div class="col-lg-4">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>banner title <span>*</span>: </label>
                        </label>
                        <div class="col-sm-8">
                          {{ Form::text('banner_title', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-lg-4">
                        <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">
                              <label>banner <span>*</span>: </label>
                           </label>
                           <div class="col-sm-8">
                              {{ Form::file('banner_image', null, ['class'=>'form-control','required' => false]) }}   
                           </div>
                        </div>
                     </div>
					  <div class="col-lg-4">
                     </div>
                     <div class="col-lg-8">
                        <div class="form-group row">
                           <label  class="col-sm-2 col-form-label">
                              <label>Link <span>*</span>: </label>
                           </label>
                           <div class="col-sm-4">
                              {{ Form::select('banner_url_type', ['0' => 'No Link','1'=>'External Link','2'=>'Internal Link'] ,'Null', ['class'=>'form-control selectlink']) }}  
                           </div>
                           <div class="col-sm-6 box 1">
                              {{ Form::text('banner_url', null, ['class'=>'form-control']) }}                             
                           </div>
                           <div class="col-sm-3 box 2 pr-0"> 
                           {{ Form::select('module', ['announcement'=>'Announcement','chat'=>'Chat','defects'=>'Defects','facility'=>'Facility Booking','feedback'=>'Feedback','magazine' => 'Magazine','marketplace'=>'Marketplace','resident management'=>'Resident Management','vistor management'=>'Vistor Management'] ,'Null', ['class'=>'form-control']) }}  
                           </div>
						     <label  class="col-sm-1 col-form-label  box 2">
                              <label>Ref. Id: </label>
                           </label>
                           <div class="col-sm-2 box 2 pr-0">
                              {{ Form::text('ref_id', null, ['class'=>'form-control']) }}                             
                           </div>
                        </div>
                     </div>
                  </div>
                  @if(@Auth::user()->role_id ==1)
					  <div class="col-lg-12 asignFace">
                  <h2>assign property</h2>
               </div>
                  <div class="">
			  
                 <div class="overflowscroll">
					 <table class="table usertable1">
 
                     <thead>
                        <tr>
                           <th>Property Name</th>
                           <th>
						   
						      <label class="containeruser1">  Assign
                                <input type="checkbox" class="id1" name="property_13" value="1"> 
                                    <span class="checkmarkuser1"></span>
                                    </label>
						   </th>
                        </tr>
                     </thead>
                     <tbody>
                     @foreach($agent_properties as $property)
                        <tr>
                           <td> {{$property->company_name}}</td>
                          <td>
						     <label class="containeruser1" style=" margin-top: -10px">  
                             <input type="checkbox" class="class1"  name="property_{{$property->id}}"  value="1" class ='viewCheckBox' {{(in_array($property->id,$assigned_property)) ?'checked':'' }}>
                                    <span class="checkmarkuser1"></span>
                                    </label>
						  </td>
                        </tr>
                     @endforeach
					
                     </tbody>
                  </table>	
					
                    </div>
                    </div>
               @endif
                    
                  <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">Submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop

