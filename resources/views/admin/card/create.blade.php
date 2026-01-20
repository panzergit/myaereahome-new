@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>add new access card </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/card')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/card/create')}}">Add new card</a></li>
                  </ul>
               </div>
               </div>

       <div class="">
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/card'), 'files' => false]) !!}

                  <div class="row asignbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-4">
                           <div class="form-group">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                
                 <div class="col-lg-4">
                           <div class="form-group ">
                              <label>card no :</label>
                                {{ Form::text('card', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Card Number']) }}
                           </div>
						   </div>
						   <div class="col-lg-4">
                           <div class="form-group">
                              <label>building :</label>
                              {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control','id'=>'build_temp','onchange'=>'getunitlists()' ]) }}
                           </div>
                           </div>
						   <div class="col-lg-4">
                           <div class="form-group">
                              <label>unit no :</label>
                              {{ Form::select('unit_no', ['' => '--Select Unit--'] + $unites, null, ['class'=>'form-control','id'=>'unit_temp' ]) }}
                           </div>
                           </div>
						   <div class="col-lg-4">
                           <div class="form-group ">
                              <label>status :</label>
                              {{ Form::select('status', ['1' => 'Active','2'=>'Inactive','3'=>'Faulty','4'=>'Loss','5'=>'Stolen'], null, ['class'=>'form-control','id'=>'status']) }}
                           </div>
                           </div>
                
                
               
                     </div>


                    
                     <div class="row">
                        <div class="col-lg-12">
                        <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop