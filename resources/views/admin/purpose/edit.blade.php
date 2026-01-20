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
   $permission = $permission->check_permission(37,$permission->role_id); 
@endphp

 <div class="status">
    <h1>visitor management - edit purpose</h1>
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
                        <li><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                     @endif
                     
                     @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                        <li class="activeul"><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
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
         {!! Form::model($Obj,['method' =>'PATCH','class'=>"forunit fonew",'url' => url('opslogin/configuration/purpose/'.$Obj->id)]) !!}
            
               @if(@Auth::user()->role_id ==1) 
                  <div class="row">
                     <div class="col-lg-8">
                        <div class="form-group row">
                           <label  class="col-sm-4  col-form-label">
                              <label>Property:</label>
                           </label>
                        <div class="col-sm-5">
                           {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                        </div>
                     </div>
                  </div>
                @endif

                <div class="row">
                           <div class="col-lg-12 tbodynew" id="tbody">
                             
                              <div class="clbord clfett row editbg">
                                    <div class="col-lg-6">
                                       <div class="form-group row">
                                          <label class="col-sm-7 col-4 col-form-label">
                                          <label>visit  purpose: </label>
                                          </label>
                                          <div class="col-sm-5 col-8  ">
                                             {{ Form::text('visiting_purpose', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Unit']) }}
                                          </div>
                                       </div>
                                    </div>
                                   
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-4 col-form-label">
                                    <label>include visitor limit</label>
                                    </label>
                                    <div class="col-sm-5 col-8  ">
                                       {{ Form::select('limit_set', ['0' => 'No','1'=>'Yes'] ,null, ['class'=>'form-control']) }}

                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-12 col-form-label">
                                    <label>Start Date / End Date option is required?</label>
                                    </label>
                                    <div class="col-sm-5 col-12  ">
                                       {{ Form::select('end_date_required', ['0' => 'No','1'=>'Yes'] ,null, ['class'=>'form-control']) }}

                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-12 col-form-label">
                                    <label>QR scan limit</label>
                                    </label>
                                    <div class="col-sm-5 col-12  ">
                                    {{ Form::number('qr_scan_limit', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter QR Scan Limit','min'=>1]) }}

                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-4 col-form-label">
                                    <label>id requirement</label>
                                    </label>
                                    <div class="col-sm-5 col-8  ">
                                       {{ Form::select('id_required', ['0' => 'No','1'=>'Yes'] ,null, ['class'=>'form-control']) }}

                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-4 col-form-label">
                                    <label>company info</label>
                                    </label>
                                    <div class="col-sm-5 col-8  ">
                                       {{ Form::select('compinfo_required', ['0' => 'No','1'=>'Yes'] ,null, ['class'=>'form-control']) }}

                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-4 col-form-label">
                                    <label>add sub category dropdown box</label>
                                    </label>
                                    <div class="col-sm-5 col-8  ">
                                       {{ Form::select('cat_dropdown', ['0' => 'No','1'=>'Yes'] ,null, ['class'=>'form-control','onchange'=>'displaycat()','id'=>'catdropdown']) }}

                                    </div>
                                    </div>
                                    </div>
                                    @php
                                       if($Obj->cat_dropdown ==1)
                                          $display_style = "";
                                       else
                                          $display_style = "display:none";
                                    @endphp
                                    <span class="row" id="categoryid" style="{{$display_style}}">
                               
                                    <div class="col-lg-6">
                                    <div class="form-group row">
                                    <label class="col-sm-7 col-4 col-form-label">
                                    <label>name of sub category</label>
                                    </label>
                                    <div class="col-sm-5 col-8  ">
                                       {{ Form::text('sub_category', null, ['class'=>'form-control','placeholder' => 'Enter Sub Category Name']) }}

                                    </div>
                                    </div>
                                    </div>
                                    @for($i=1;$i<=10;$i++)
                                    @php
                                   // print_r($subcategories[1][]);
                                       if($i ==1)
                                          $display_style = "";
                                       else if(isset($subcategories[$i]['sub_category']) &&  $subcategories[$i]['sub_category']!='')
                                          $display_style = "";
                                       else
                                          $display_style = "display:none";
                                          
                                    @endphp
                                    <div class="col-lg-6" id="add_field{{$i}}" style="{{$display_style}}">
                                       <div class="form-group row">
                                       <label class="col-sm-7 col-4 col-form-label">
                                       <label>sub category list {{$i}}</label>
                                       </label>
                                       <div class="col-sm-5 col-8  ">
                                          <input type="text" class="form-control" value="{{isset($subcategories[$i]['sub_category'])?$subcategories[$i]['sub_category']:''}}" name="sub_category_list_{{$i}}" placeholder="">   
                                        
                                       </div>
                                       </div>
                                    </div>
                                 @endfor
                                       <div class="col-lg-12" id="buttonsection">
                                          <a class="addrow mt-2 mb-2" id="addBtn03" type="button" onclick="showmore()">
      	  Add List
                                          </a>
                                       </div>
                                       <input type="hidden" id="rowcount" value="1">
                                       <input type="hidden" id="maxcount" value="10">

                              </span>
 </div>
								  <div class="row">
                        <div class="col-lg-12 ">
                        <button type="submit" class="submit mt-4 mt-0 float-right">submit</button>
                        </div>
                     </div>
                              </div>
                           </div>
                        </div>


                 
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


