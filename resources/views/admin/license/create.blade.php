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
<style>
.mr-20{margin-left:20px;}
.mr6 {
    -ms-flex: 0 0 49%;
    flex: 0 0 49%;
    max-width: 49%;
}
</style>
  <div class="status">
    <h1>add new License plate </h1>
  </div>
<div class="row">
                <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   ><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li class="activeul"><a href="#">Registrations @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a></li>
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
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/licenseplate/save'), 'files' => false]) !!}

                  <div class="row ">
                  <div class="col-lg-6 asignbg row mr6">
                 
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label>unit no :</label>
                                  <select class="form-control wauto"  name="unit1">
                                    @foreach($units_data as $data)
                                       <option value="{{$data['id']}}">{{$data['block']}} - {{$data['unit']}}</option>
                                    @endforeach
                                    </select>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <div class="form-group ">
                              <label>License Plate :</label>
                                {{ Form::text('license_plate1', null, ['class'=>'form-control','placeholder' => 'Enter License Plate']) }}
                           
                           </div>
                           </div>
                     </div>
                     <div class="col-lg-6 asignbg row mr-20 mr6">
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label>unit no :</label>

                               <select class="form-control wauto"  name="unit2">
                                    @foreach($units_data as $data)
                                       <option value="{{$data['id']}}">{{$data['block']}} - {{$data['unit']}}</option>
                                    @endforeach
                                    </select>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <div class="form-group ">
                              <label>License Plate :</label>
                                {{ Form::text('license_plate2', null, ['class'=>'form-control','placeholder' => 'Enter License Plate']) }}
                           
                           </div>
                           </div>
                     </div>
                     </div>

                    
                     <div class="row">
                        <div class="col-lg-12 pr-0">
                           <input type="hidden" name="user_info_id" value="{{$moreInfoObj->id}}">
                           <input type="hidden" name="user_id" value="{{$moreInfoObj->user_id}}">

                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop