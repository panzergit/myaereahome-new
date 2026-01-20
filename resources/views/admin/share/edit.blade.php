@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>MANAGE UNIT - UPDATE </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="containerwidth">
                 {!! Form::model($unitObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/configuration/unit/'.$unitObj->id)]) !!}

                  <div class="row">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4  col-form-label">
          <label>Property:</label>
                </label>
                              <div class="col-sm-5">
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                              </div>
                           </div>
                </div>
                @endif
                <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">
          <label>Building:</label>
                </label>
                              <div class="col-sm-5">
             {{ Form::select('building_id', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                              </div>
                           </div>
                </div>
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-6 col-form-label">UNIT NO. :</label>
                              <div class="col-sm-5 col-6">
                                {{ Form::text('unit', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Unit']) }}
                              </div>
                           </div>

                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">CODE :</label>
                              <div class="col-sm-5">
                                {{ Form::text('code', null, ['class'=>'form-control','placeholder' => 'Enter unit code','readonly'=>'true']) }}
                              </div>
                           </div> 
               
                           <div class="form-group row">
                              <label  class="col-sm-4 col-6 col-form-label">SIZE :</label>
                              <div class="col-sm-5 col-6">
                                {{ Form::text('size', null, ['class'=>'form-control','placeholder' => 'Enter unit size']) }}
                              </div>
                           </div>
                
                           <div class="form-group row">
                              <label  class="col-sm-4 col-6 col-form-label">SHARE VALUE :</label>
                              <div class="col-sm-5 col-6">
                                {{ Form::text('share_amount', null, ['class'=>'form-control','placeholder' => 'Enter unit share amount']) }}
                              </div>
                           </div>
                </div>
               
                     </div>


                     
                       
            
                     <div class="row">
                        <div class="col-lg-6">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


