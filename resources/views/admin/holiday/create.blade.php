@extends('layouts.adminnew')

@section('content')


<!-- Content Header (Page header) -->

  <div class="status">
    <h1> PROPERTY - ADD  </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="containerwidth">
                {!! Form::open(['method' => 'POST','class'=>'forunit', 'url' => url('opslogin/configuration/property'), 'files' => true]) !!}

                  <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>PROPERY NAME *: </label>
                        </label>
                        <div class="col-sm-8">
                          {{ Form::text('company_name', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>SHORT CODE *: </label>
                        </label>
                        <div class="col-sm-8">
                          {{ Form::text('short_code', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>CONTACT NUMBER *: </label>
                        </label>
                        <div class="col-sm-8">
                          {{ Form::text('company_contact', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>EMAIL *: </label>
                        </label>
                        <div class="col-sm-8">
                          {{ Form::text('company_email', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>OTP OPTION *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::select('otp_option', ['1' => 'Email','2'=>'SMS'] ,'Null', ['class'=>'form-control']) }}
				                          
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>SECURITY OPTION *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::select('security_option', ['1' => 'Facial Recognition','2'=>'Manual Check'] ,'Null', ['class'=>'form-control']) }}
				                          
                        </div>
                      </div>
                    </div>


                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>LOGO *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('company_logo', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>DEFAULT BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('default_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>FAQ BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('faq_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>ANNOUNCEMNT BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('announcement_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>UNIT TAKE OVER BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('takeover_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>DEFECT BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('defect_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>JOINT INSPECTION BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('inspection_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>FEEDBACK BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('feedback_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>FACILITIES BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('facilities_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>CONDO DOCUMENT UPLOAD BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('condodocs_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>RESIDENT FILE UPLOAD BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('resident_fileupload_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>VISITOR MANAGEMENT BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('visitor_management_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>FACIAL RECOGNITION BACKGROUND *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::file('facial_reg_bg', null, ['class'=>'form-control','required' => false]) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>UNIT TAKE OVER TIMING *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('takeover_timing', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Address']) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>UNIT TAKE BLOCK OUT DATES *: <br>
                          (yyyy-mm-dd)</label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('takeover_blockout_days', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>JOINT INSPECTION TIMING *: </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('inspection_timing', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Address']) }}
                             
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>JOINT INSPECTION BLOCK OUT DATES *: <br>
                          (yyyy-mm-dd)</label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('inspection_blockout_days', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
                             
                        </div>
                      </div>
                    </div>

                  </div>
                    
                     <div class="row">
                        <div class="col-lg-11">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop

