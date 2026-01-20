@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>BLOCK BULK UPLOAD </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="containerwidth fileuplod">
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/configuration/building/importcsv'), 'files' => true]) !!}

                  <div class="row">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-7">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-form-label">
          <label>Property:</label>
                </label>
                              <div class="col-sm-7 pl-0">
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                              </div>
                           </div>
                </div>
                @endif
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
                              <label  class="col-sm-4 col-form-label">BLOCK NUMBER:<br>
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