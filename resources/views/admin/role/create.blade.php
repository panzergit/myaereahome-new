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
    <h1>manage role - add new </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

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
                {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/configuration/role'), 'files' => false]) !!}

                  <div class="row asignbg">
                  @if(@Auth::user()->role_id ==1 && 1==2)
                  <div class="col-lg-4">
                                <label>
            <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>

                @endif
                 <div class="col-lg-4">
                           <div class="form-group ">
                              <label >role title :</label>
                                {{ Form::text('name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Role']) }}
                            
                           </div>
                </div>
                </div>
              
                @if($properties)
					<div class="">
					<div class="col-lg-12 asignFace">
                  <h2>also avilable on the following property</h2>
               </div>
                <div class="overflowscroll">
                          <table class="table usertable1">
                              <thead>
                                <tr>
                                  <th> <span style="text-align: left;     padding-left: 0px;" class="form-group form-check">property </span></th>
                                  <th> <div class="">
								  <label class="containeruser1">   all property
                                    <input type="checkbox" id="checkAllView" name="check_all" value="1" class ='id1'>
                                    <span class="checkmarkuser1"></span>
                                    </label>
								 
                                  </div></th>
                                </tr>
                              </thead>
                              <tbody>
                            
                              @foreach($properties as $property)
                                <tr style="background-color:#a9f1f9">
                                  <td>{{$property->company_name}}</td>
                                  <td>
								  <label class="containeruser1">
               <input type="checkbox" name="props[]"  value="{{$property->id}}" class ='viewCheckBox class1' >yes
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
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop

