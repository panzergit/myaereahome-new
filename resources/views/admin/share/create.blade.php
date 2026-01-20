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

<!-- Content Header (Page header) -->

  <div class="status">
    <h1>Manage Mgmt/Sinking Fund - add</h1>
  </div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if(isset($role->view) && $role->view==1 )
                        <li ><a href="{{url('/opslogin/configuration/role#rolesettings')}}">Manage Role </a></li>
                     @endif

                     @if(isset($building->view) && $building->view==1 )
                        <li><a href="{{url('/opslogin/configuration/building#buildingsettings')}}">Manage Block </a></li>
                     @endif

                     @if(isset($unit->view) && $unit->view==1 )
                        <li><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
                     @endif

                     @if(isset($sharesetting->view) && $sharesetting->view==1 )
                        <li   class="activeul"><a href="{{url('/opslogin/configuration/sharesettings#unitsettings')}}">Manage Mgmt/Sinking Fund </a></li>
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
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/configuration/sharesettings'), 'files' => true]) !!}

                  <div class="row asignbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-4">
                           <div class="form-group ">
                              <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property']) }}
                           </div>
                </div>
                @endif
                
                 <div class="col-lg-4">
                           <div class="form-group ">
                              <label>management fund amt (s$) <span>*</span>:</label>
                                {{ Form::number('management_fund_share', null, ['class'=>'form-control','required' => true,'step'=>'any']) }}
                            
                           </div>
                           </div>
                           <div class="col-lg-4">
                           <div class="form-group ">
                              <label>sinking fund amt (s$) <span>*</span>:</label>
                                {{ Form::number('sinking_fund_share', null, ['class'=>'form-control','required' => true,'step'=>'any']) }}
                        
                           </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group ">
                                 <label>Share value <span>*</span>:</label>
                                 {{ Form::select('share_amount', ['1' => '1'] ,null, ['class'=>'form-control','required' => true]) }}
                             
                              </div>
                              </div>
                              <div class="col-lg-4">
                              <div class="form-group ">
                                 <label>no of billing month(s)<span>*</span>:</label>
                                 {{ Form::number('no_of_billing_month', 1, ['class'=>'form-control','required' => true,"min"=>"1","max"=>"12"]) }}
                              
                                 </div>
                              </div>
                              <div class="col-lg-4">
                                 <div class="form-group ">
                                    <label>interest <span>*</span>: </label>
                                    {{ Form::select('interest', ['1' => 'Not Applicable','2'=>'Applicable'] ,'Null', ['class'=>'form-control','onchange'=>'getintfields()','id'=>'int_option']) }}     
                                   
                                 </div>
                              </div>
                           <div class="col-lg-4" id="int_field" style="display:none">
                              <div class="form-group ">
                                 <label>per annum interest rate (%) <span>*</span>:</label>
                                 {{ Form::number('int_percentage', 1, ['class'=>'form-control',"step"=>"any"]) }}
                               
                              </div>
                           </div>

                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label  class="col-sm-12">due date <span>*</span>:</label>
                                 <div class="col-sm-4">
                                 {{ Form::number('due_period_value', 1, ['class'=>'form-control',"min"=>"1"]) }}
                                 </div>
                                 <div class="col-sm-8">
                                 {{ Form::select('due_period_type', [''=>'--Select--','1' => 'Day(s)','2'=>'Month(s)'] ,null, ['class'=>'form-control','required' => true]) }}
                                 </div>
                              </div>
                              </div>

                              <div class="col-lg-4">
                                 <div class="form-group ">
                                    <label>tax <span>*</span>: </label>
                                    {{ Form::select('tax', ['1' => 'Not Applicable','2'=>'Applicable'] ,'Null', ['class'=>'form-control','onchange'=>'getsmsfields()','id'=>'otp_option']) }}
                                               
                                 </div>
                              </div>
                              <div class="col-lg-4" id="sms_field" style="display:none">
                                 <div class="form-group ">
                                    <label>tax percentage: </label>
                                    {{ Form::text('tax_percentage', null, ['class'=>'form-control','id'=>'tax_percentage']) }}			
                                 </div>                   
                              </div>
                              @if($property_info->qrcode_option ==2)
                                 <div class="col-lg-4" id="sms_field" >
                                    <div class="form-group ">
                                       <label>upload qr code<span> *</span>: </label>
                                       {{ Form::file('qrcode_file', null, ['class'=>'form-control','required' => true]) }}
                                    
                                    </div>                   
                                 </div>
                              @endif
                        </div>
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop