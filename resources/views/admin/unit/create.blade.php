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
    <h1>add new unit </h1>
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
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
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
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/configuration/unit'), 'files' => false]) !!}

                  <div class="row asignbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-3">
                           <div class="form-group ">
          <label>property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                <div class="col-lg-3">
                           <div class="form-group ">
          <label>building:</label>
             {{ Form::select('building_id', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                 <div class="col-lg-3">
                           <div class="form-group">
                              <label>unit no :</label>
                                {{ Form::text('unit', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Unit']) }}
                           </div>
 </div>
                          <!-- <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">CODE :</label>
                              <div class="col-sm-5">
                                {{ Form::text('code', null, ['class'=>'form-control','placeholder' => 'Enter unit code']) }}
                              </div>
                           </div> -->
                  <div class="col-lg-3">
                           <div class="form-group ">
                              <label>size :</label>
                                {{ Form::text('size', null, ['class'=>'form-control','placeholder' => 'Enter unit size']) }}
                           
                           </div>
                           </div>
               
                 <div class="col-lg-3">
                           <div class="form-group ">
                              <label>share value :</label>
                                {{ Form::text('share_amount', null, ['class'=>'form-control','placeholder' => 'Enter unit share amount']) }}
                          
                           </div>
                           </div>
               
               
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