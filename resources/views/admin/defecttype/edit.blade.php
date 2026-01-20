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
   $permission = $permission->check_permission(27,$permission->role_id); 
@endphp
<!-- Content Header (Page header) -->

  <div class="status">
    <h1> defect location - update </h1>
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
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/defect#defectsettings')}}">Defects  <br>Location</a></li>
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
       {!! Form::model($defectObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/configuration/defect/'.$defectObj->id)]) !!}

                  <div class="row asignbg editbg">
                    <div class="col-lg-4">
                              <div class="form-group ">
                                  <label >defect location :</label>
                                    {{ Form::text('defect_location', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Defect Option']) }}
                              </div>
                    </div>
               
                     </div>
   <div class="col-lg-12 asignFace">
                  <h2>defect types</h2>
               </div>

                     <div class="row asignbg editbg">
                 

                  @for($i=1;$i<=10;$i++)
                              @php
                                if($i ==1){
                                  $display_style = "";
                                  //$rowcount = 1;
                                  }
                                else if(isset($defect_types[$i]['key']) && $defect_types[$i]['key'] ==$i)
                                  {
                                    $display_style = "";
                                    $rowcount = $i;
                                  }
                                else
                                  {
                                    $display_style = "display:none";
                                    //$rowcount = 1;
                                  }
                              @endphp
                               

                              <div class="col-lg-4 col-4" id="add_field{{$i}}" style="{{$display_style}}">
                                <div class="form-group ">
                                    <label>type {{$i}}:</label>
                                        <input type="hidden" name="type_id_{{$i}}" value="{{isset($defect_types[$i]['id'])?$defect_types[$i]['id']:''}}">
                                          {{ Form::text("defect_type_$i", isset($defect_types[$i]['defect_type'])?$defect_types[$i]['defect_type']:'', ['class'=>'form-control','placeholder' => 'Enter defect type']) }}
                                       
                                  </div>
                              </div>
                              @endfor
							   </div>
                              <div class="row">
                              <div class="col-lg-12">
                              <a class="addrow  float-right mt-2 mb-4" style="color:#000"
                           id="addBtn" type="button" onclick="showmore()">
                        <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
<br>				
                        Add Type
                        </a>
                                </div>
                                </div>
                        <input type="hidden" id="rowcount" value="{{isset($rowcount)?$rowcount:1}}">
						  <div class="row">
						  <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right ">update</button>
                        </div>
                        </div>
             


                    
                       
            
                   
                    {!! Form::close() !!}
              
               
            </div>
         </div>
      </section>


@stop