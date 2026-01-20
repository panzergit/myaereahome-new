@extends('layouts.adminnew')




@section('content')
@php 
   $permission = Auth::user();
   $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(7,$permission->role_id); 
 

@endphp
<style>
.f14{
    font-size: 12px!important;
}
.face14{    margin-top: -6px;}

.checkmarkuser1 {
    position: absolute;
    top: 5px;
    left: 0px;
    height: 19px;
    width: 19px;
    background-color: #D0D0D0;
}
.containeruser1 .checkmarkuser1:after {
    left: 7px;
    top: 3px;
    width: 6px;
    height: 12px;
    border: solid #8F7F65;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
.containeruser1 {
    padding-left: 28px;
}
</style>
  <div class="status">
    <h1>User Management - Add New User</h1>
  </div>

  @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li  ><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li class="activeul" ><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li ><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/registrations')}}">Registrations  @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a> </li>
                     @endif
                  </ul>
               </div>
               </div>
<div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Building and Unit should be created before bulk upload of User.<br/><br />Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/user/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
<div class="">
                        {!! Form::open(['method' => 'POST','class'=>'forunit', 'id' => "user-form", 'url' => url('opslogin/user'), 'files' => true]) !!}

                     <div class=" tworow asignbg p-3">
                     @if(@Auth::user()->role_id ==1)
                <div class="col-lg-3 pl-0">
                           <div class="form-group">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, old('account_id'), ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                            
                           </div>
                </div>
                @else
                <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                @endif
                <div id="primary_div" class="row" style="display:none">
                  <div class="col-lg-3" id="role_priority" >
                     <div class="form-group">
                      <label class="containeruser1 f14">
               <input type="checkbox" class="id1" id="primary" name="primary_contact" value="1"> primary contact 
               <span class="checkmarkuser1"></span>
               </label>
            
                     </div>
                  </div>
                  </div>
				  <div class="row">
                     <div class="col-lg-3">
                           <div class="form-group">
          <label>assign role:</label>
            <select class="form-control wauto" id="role" onchange="getunits()" name="role_id">
            @foreach($roles as $role)
               <option value="{{$role->id}}">{{($role->type ==1)?"AH":"AM"}} - {{$role->name}}</option>
            @endforeach
            </select>
                          
                           </div>
                </div>
               
                
                        <div class="col-lg-3">
                           <div class="form-group ">
          <label>first name <span>*</span>: </label>
            {{ Form::text('name', old('name'), ['class'=>'form-control','required' => true]) }}
                         
                           </div>
                </div>
               <div class="col-lg-3">
                           <div class="form-group ">
          <label>last name <span>*</span>: </label>
            {{ Form::text('last_name', null, ['class'=>'form-control','required' => true]) }}
                           
                           </div>
                </div>
               <div class="col-lg-3">
                           <div class="form-group ">
          <label>contact <span>*</span>: </label>
             {{ Form::text('phone', null, ['class'=>'form-control','required' => true]) }}
                           
                           </div>
                </div>
               <div class="col-lg-3">
                           <div class="form-group ">
          <label>email <span>*</span>: </label>
             {{ Form::text('email', null, ['class'=>'form-control','required' => true]) }}
                            
                           </div>
                </div>
				<div class="col-lg-3">
                <div id="pwd_div" style="display:block">
                  
                     <div class="form-group ">
                        <label>password : </label>
                        {{ Form::input('password', 'password','', ['class'=>'form-control','placeholder' => 'Enter Password']) }}
                     </div>
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="" id="faceid_access_div" style="display:none">
                     <div class="form-group face14">
                        <label class="containeruser1 f14">
                              <input type="checkbox" name="faceid_access_permission" id="myCheck" >Allow Face ID Access 
                              <span class="checkmarkuser1"></span>
                        </label>
                     </div>
                     <div class="form-group hide" id="area">
                     {{ Form::text('faceid_access_code', '', ['class'=>'form-control','placeholder' => 'Access Code']) }}
                     </div>
                  </div>
               </div>
            </div>
				 
               @php
               $unit_div_display = "display:block";
               if(old('role_id') !=''){ 
                  $unit_array = explode(",",env('USER_APP_ROLE'));
                  if(in_array(old('role_id'),$unit_array)){
                     $unit_div_display = "display:none";
                  }
               }
               @endphp
              
                  <div id="unit_div" style="{{$unit_div_display}}">
				   <div class="row">
                     <div class="col-lg-3" >
                        <div class="form-group ">
                              <label>building <span>*</span>: </label>
                         
                           {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control','id'=>'building','onchange'=>'getunits()' ]) }}
                         
                        </div>
                     </div>
                     <div class="col-lg-3" >
                        <div class="form-group ">
                              <label>unit no <span>*</span>: {{old('unit_no')}}</label>
                          
                              {{ Form::select('unit_no', ['' => '--Select Unit--'], null, ['class'=>'form-control','id'=>'unit','onchange'=>'getcards()' ]) }}
                          
                        </div>
                     </div>
                     <div class="col-lg-3" >
                        <div class="form-group">
                              <label>card no : </label>
                              {{ Form::select('card_nos[]', ['' => '--Select Card--'], null, ['class'=>'form-control','id'=>'card','multiple'=>'multiple','rows'=>4, 'style'=>'height:100px !important']) }}
                           
                        </div>
                     </div>
               </div>
               </div>
  <div class="row">
               <div class="col-lg-3">
                           <div class="form-group ">
          <label>company: </label>
            {{ Form::text('company_name', null, ['class'=>'form-control']) }}
                            
                           </div>
                </div>
            
               <div class="col-lg-3">
                           <div class="form-group ">
          <label>mailing add <span>*</span>: </label>
                       {{ Form::textarea('mailing_address', null, ['class'=>'form-control','required' => true,'rows'=>4]) }}

                           </div>
                </div>
                <div class="col-lg-3">
                           <div class="form-group ">
          <label>country: </label>
                              {{ Form::select('country', $countries, old('country'), ['class'=>'form-control ']) }}
                             
                           </div>
                </div>
                <div class="col-lg-3">
                           <div class="form-group">
          <label>postal code: </label>
            {{ Form::text('postal_code', null, ['class'=>'form-control']) }}
                           </div>
                </div>
            
              
             
                     </div>
                 
                </div>
                </div>
                <div class="row tworow">
                 
                
                 <div class="col-lg-12">
              <div class="form-group">
                               <button type="submit" class="submit mt-3 ml-3 float-right ">submit</button>
                            
                            </div>
                      
    
             </div>
             </div>

  
        </div>
         {!! Form::close() !!}
               </div>
</div>
</section>


@stop