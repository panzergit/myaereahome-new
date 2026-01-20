@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>MANAGE DEFECT LOCATION - ADD </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="containerwidth">
                 {!! Form::open(['method' => 'POST', 'url' => url('opslogin/defects'), 'files' => false]) !!}

                  <div class="row">
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">DEFECT LOCATION :</label>
                              <div class="col-sm-5">
                                {{ Form::text('defect_location', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Defect Option']) }}
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
      </section>


@stop