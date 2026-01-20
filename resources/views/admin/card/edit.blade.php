@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>manage access card - update </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/card')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/card/create')}}">Add new card</a></li>
                  </ul>
               </div>
               </div>

       <div class="">
                 {!! Form::model($cardObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/card/'.$cardObj->id)]) !!}

                  <div class="row asignbg editbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-8">
                           <div class="form-group">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                 <div class="col-lg-3">
                           <div class="form-group ">
                              <label>unit no :</label>
                                {{ Form::text('card', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Unit']) }}
                          
                           </div>
						   </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label>building :</label>
                              {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control','id'=>'build_temp','onchange'=>'getunitlists()' ]) }}
                           
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label>unit no :</label>
                              {{ Form::select('unit_no', ['' => '--Select Unit--'] + $unites, null, ['class'=>'form-control','id'=>'unit_temp' ]) }}
                             
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label>status:</label>
                              {{ Form::select('status', ['1' => 'Active','2'=>'Inactive','3'=>'Faulty','4'=>'Loss','5'=>'Stolen'], null, ['class'=>'form-control','id'=>'status']) }}
                             
                           </div>
                           </div>
                
                <div class="col-lg-3">
                           <div class="form-group ">
                              <label>remarks :</label>
                              {{ Form::textarea('remarks', null, ['class'=>'form-control','rows'=>4]) }}
                            
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

</section>
@stop


