@extends('layouts.adminnew')




@section('content')
@php 
   $permission = Auth::user();
   $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(7,$permission->role_id); 
 

@endphp
<div class="status">
  <h1>manage user lists</h1>
</div>

  <div class="">
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
                        <li  ><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li class="activeul"><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
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
              

               <div class="">
               {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/user/importcsv'), 'files' => true]) !!}

                  <div class="row">
                 
                <div class="col-lg-7">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-form-label">
          <label>UPLOAD CSV FILE:</label>
                </label>
                              <div class="col-sm-7 pl-0">{{ Form::file('csv_file', null, ['class'=>'form-control','required' => false]) }}
</div>
                           </div>
                </div>
</div>
                <div class="row">
              <div class="col-lg-7">
              <div class="row" id="add_field">
              <div class="col-lg-3">
</div>
                        <div class="col-lg-7 pl-0">
                           <button type="submit" class="submit mt-2 ml-1 float-left">SUBMIT</button>
                        </div>
</div></div>
              </div>
 
                <!--<div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">BUILDING NUMBER:<br>
                              Only supports numbers</label>
                              <div class="col-sm-5">
                                {{ Form::text('building_no', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Building Number']) }}
                              </div>
                           </div>
               
                </div> -->
               
                     </div>

          
                    
                     <!--div class="row">
                        <div class="col-lg-6">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div-->
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop