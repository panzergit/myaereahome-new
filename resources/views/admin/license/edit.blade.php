@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
    $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(32,$permission->role_id); 
@endphp

 <div class="status">
    <h1>license plate - update </h1>
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
                 {!! Form::model($LicenseObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/licenseplate/'.$LicenseObj->id)]) !!}

                  <div class="row asignbg editbg">
                 
                 
                  <div class="col-lg-3">
                           <div class="form-group ">
                              <label >License Plate :</label>
                                {{ Form::text('license_plate', null, ['class'=>'form-control','placeholder' => 'Enter License Plate']) }}
                             
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


