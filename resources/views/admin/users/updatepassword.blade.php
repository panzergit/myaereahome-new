@extends('layouts.adminnew')




@section('content')

  <div class="status">
    <h1 class="text-center">Change Password</h1>
  </div>

  

<div class="">
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
                        {!! Form::open(['method' => 'POST','class'=>'forunit', 'id' => "user-form", 'url' => url('opslogin/configuration/savepassword'), 'files' => true]) !!}

                     <div class="row ">
                         <div class="col-lg-4">
                              
             </div>
                     <div class="col-lg-4 col-12 asignbg  pl-3 pr-3">
                           <div class="form-group ">
          <label>old password <span>*</span>:</label>
                              {{ Form::text('old_password', null, ['class'=>'form-control','required' => true]) }} 
                           </div>
               
                           <div class="form-group ">
          <label>new password <span>*</span>: </label>
            {{ Form::text('password', null, ['class'=>'form-control','required' => true]) }}
                              </div>
                         
               
                           <div class="form-group ">
          <label>confirm password <span>*</span>: </label>
            {{ Form::text('confirmpassword', null, ['class'=>'form-control','required' => true]) }}
                             
                           </div>
						    <button type="submit" class="submit mt-2 float-right">submit</button>
                </div>
              
              <div class="col-lg-4">
                              
             </div>
              
             
                     </div>
         {!! Form::close() !!}
               </div>
</div>
</section>


@stop