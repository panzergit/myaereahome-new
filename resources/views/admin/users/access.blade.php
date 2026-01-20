
@extends('layouts.adminnew')




@section('content')
@php 
   $permission = Auth::user();
   $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(7,$permission->role_id); 
 

@endphp
  <div class="status">
    <h1>User Access - Update </h1>
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
                        <li class="activeul"><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li  ><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
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
      <form action="{{url('/opslogin/user/accesssearch')}}" method="get" role="search" class="forunit forbottom">
           
                     <div class="row asignbg mb-0">
						 <div class="col-lg-3">
                           <div class="form-group">
                           {{ Form::select('role', ['' => '--User Role--'] + $roles, $role, ['class'=>'form-control','id'=>'role']) }}

						    </div>
                        </div>
						 <!--<div class="col-lg-3">
                           <div class="form-group">
                       <input  type="text" name="unit" class="form-control" value="" id="" placeholder="Enter Unit">
                           </div>
                        </div> -->
						 <div class="col-lg-3"></div>
						 <div class="col-lg-3">
                           <div class="form-group">
						    <button type="submit" class="submit float-right">search</button>
						    </div>
                        </div>
                     </div>
                  </form>
      <form action="{{url('/opslogin/user/accessupdate')}}" method="post" class="forunit">
      {{ csrf_field() }}
            <!--div class="form-group frcheck">
               <label><input type="checkbox"  id="checkAll" value="1"> &nbsp; <span>Select all</span></label>
			     
            </div-->   
            <div class="overflowscroll">
                     <table class="table usertable1">
                     <thead>
                        <tr class="topinput">
                           <th>User</th>
                           <th>Role</th>
                           <th style="padding-right: 40px;">Unit</th> 
                           @if($modules)
                              @foreach($modules as $mk => $module)
                           
                                 <th>{{$module->name}} </th>
                              @endforeach
                           @endif
                        </tr>
                        <tr class="tbor">
                        <th colspan="3" style="    text-align: left;">
						<label class="containeruser">Select all
                                 <input type="checkbox" id="ckbCheckAll" value="1">
                                 <span class="checkmarkuser"></span>
                                 </label>
						</th>
                        @if($modules)
                              @foreach($modules as $mk => $module)
                                 <?php 
                                 $k = $mk+1; 
                                 $class = "id".$k;
                                 ?>
                                 <th><!--input type="checkbox" class="{{$class}}"-->
								     <label class="containeruser">&nbsp;
                                 <input type="checkbox" class="{{$class}}">
                                 <span class="checkmarkuser"></span>
                                 </label>
								 </th>
                              @endforeach
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        @if($users)
                           @foreach($users as $k => $user)
                              @if(isset($user->usermoreinfo->status) && $user->usermoreinfo->status ==1)
                                 <tr style="border-bottom: 1px solid #9e9e9e7a;">
                                    <td>{{isset($user->usermoreinfo->first_name)?Crypt::decryptString($user->usermoreinfo->first_name):''}} {{isset($user->usermoreinfo->last_name)?Crypt::decryptString($user->usermoreinfo->last_name):''}}</td>
                                    <td>{{isset($user->role->name)?$user->role->name:''}}</td>
                                    <td style="border-right: 1px solid #9e9e9e7a;">{{isset($user->addunitinfo->unit)?"#".Crypt::decryptString($user->addunitinfo->unit):''}}</td>
                                    @if($modules)
                                       @php 
                                       $role_access = $user_access[$user->id];
                                          $ck ='';
                                          $class ='';
                                       @endphp
                                       @foreach($modules as $ck => $module)

                                       @php
                                       $k = $ck+1; 
                                       $class = "class".$k;
                                       $view =false;
                                       $array_exist=false;
                                          if(isset($role_access[$module->id])) {
                                             $array_exist = true;
                                             if($role_access[$module->id][0] ==1)
                                             $view=1;
                                             
                                          }

                                       @endphp   
                                       <td>
                                          <div class="form-group form-check">
                                             
                                 
                                    <label class="containeruser">
                                       <input type="checkbox" class="{{$class}} checkBoxClass"  name="mod_{{$module->id}}_pid_{{$user->id}}"  value="1" {{ (isset($view) && $view ==1) ?'checked':'' }}>
                                       <span class="checkmarkuser" style="top: -10px;"></span>
                                       </label>
                                          </div>
                                       </td>
                                       @endforeach
                                    @endif
                                 </tr>
                              @endif
                           @endforeach
                        @endif  
                     </tbody>
               </table>
                                    </div>
				  <div class="row">
                        <div class="col-lg-8">
                        </div>
                        <div class="col-lg-4">
                        <input type="hidden" name="role" value="{{($role >0)?$role:''}}">
                        <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
          
                    
      {!! Form::close() !!}
      </div>



</section>


@stop

