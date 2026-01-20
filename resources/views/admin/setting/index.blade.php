@extends('layouts.adminnew')



@section('content')

@php 
$permission = Auth::user();
@endphp


<!-- Content Header (Page header) -->

   <div class="status">
        <h1>GENERAL INFORMATION</h1>
    </div>

 @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif


      <div class="containerwidth">
                 {!! Form::model($configObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/configuration/setting/'.$configObj->id)]) !!}
                     <div class="row">
                        <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">COMPANY NAME :</label>
                              <div class="col-sm-5">
                                 {{ Form::text('company_name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter File Title']) }}
                              </div>
                           </div>
                </div>
               <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">REG. NO. :</label>
                              <div class="col-sm-8">
                                 <div id="sandbox">
                                    {{ Form::text('company_reg_no', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Company Registartion Number']) }}
                                   </div>
                              </div>
                           </div>
                           </div>
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">CONTACT NO :</label>
                              <div class="col-sm-8">
                                 <div id="sandbox">
                                   {{ Form::text('company_contact', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Amount']) }}
                                   </div>
                              </div>
                           </div>
                           </div>
                <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">EMAIL:</label>
                              <div class="col-sm-8">
                                 <div id="sandbox">
                                   {{ Form::text('company_email', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Amount']) }}
                                   </div>
                              </div>
                           </div>
                           </div>
              


                        <div class="col-lg-11 mt-4">
                           <div class="form-group">
                             {{ Form::textarea('company_address', null, ['class'=>'form-control','required' => false,'placeholder' => 'Enter Addition Notes']) }}
                             
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-11">
                           <button type="submit" href="#" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>

      
          <!-- /.box -->

    </section>  

@stop
