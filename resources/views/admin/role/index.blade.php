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
   $sharesetting =  $permission->check_menu_permission(73,$permission->role_id,1);
   $permission = $permission->check_permission(23,$permission->role_id); 
@endphp

<div class="status">
  <h1>manage roles</h1>
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
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
  <div class="overflowscroll2">
                 <table class="gap">
                  @if(isset($permission) && $permission->create==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/configuration/role/create")}}"  class="submit mt-4 ml-3 float-left" style="width:auto"> + Add New</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>role</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                           @endif
                           
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($roles)

                        @foreach($roles as $k => $dept)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           <td class="spacer">{{$dept->name}}</td>
                           @if(Auth::user()->role_id ==1)
                              <td  class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:'All Property'}}</td>
                           @endif
                           
                           <td  class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                     @if((isset($permission) && $permission->edit==1)|| Auth::user()->role_id ==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/role/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if((isset($permission) && $permission->delete==1) || Auth::user()->role_id ==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/role/delete/$dept->id")}}');">Delete</a>
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


