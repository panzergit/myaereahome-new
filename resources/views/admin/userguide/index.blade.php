@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(32,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>User Guide</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/userguide#cd')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/userguide/create#cd')}}">Upload new Guide</a></li>
                  </ul>
               </div>
               </div>
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<div class="">
   {!! Form::model($fileObj,['method' =>'PATCH','class'=>"forunit",'files' => true,'url' => url('opslogin/userguide/'.$fileObj->id)]) !!}
                <div class="row asignbg editbg">
                    <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>URL :</label>
                        </label>
                        <div class="col-sm-8">
                           {{ Form::textarea('url_link', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Time Slot']) }}
                        </div>
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
@endsection

