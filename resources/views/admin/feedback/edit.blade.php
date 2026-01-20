@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1> feedback - edit </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                    <li ><a href="{{url('/opslogin/feedbacks/summary?view=dashboard')}}">Dashboard</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/feedbacks/summary?view=summary')}}">Summary</a></li>
                  </ul>
               </div>
               </div>
       <div class="">
                 {!! Form::model($feedbackObj,['method' =>'PATCH' ,'class'=>"forunit",'class'=>"forunit",'url' => url('opslogin/feedbacks/'.$feedbackObj->id)]) !!}
<div class="col-lg-12 asignFace">
                  <h2>feedback - update</h2>
               </div>
                 <div class="row asignbg editbg">
				 <div class="col-lg-3 col-6">
                           <div class="form-group">
					<label>ticket: </label>
						<h4>{{$feedbackObj->ticket}}</h4>
                           </div>
						    </div>
                        <div class="col-lg-3 col-6">
                           <div class="form-group">
					<label>submission date: </label>
						<h4>{{date('d/m/y',strtotime($feedbackObj->created_at))}}</h4>
                           </div>
						    </div>
					  <div class="col-lg-3 col-6">
                           <div class="form-group">
					<label>unit no: </label><h4>{{isset($feedbackObj->getunit->unit)?Crypt::decryptString($feedbackObj->getunit->unit):''}}</h4>
                             
                           </div>
						    </div>
							
					  <div class="col-lg-3 col-6">
                           <div class="form-group">
					<label >submitted by: </label>
						<h4>{{Crypt::decryptString($feedbackObj->user->name)}}</h4>
                           </div>
						    </div>
							     <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>category: </label>
						<h4>{{$feedbackObj->getoption->feedback_option}}</h4>
                           
                           </div>
						    </div>
						
					  <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>status: </label>
							  </label>
						<h4>@php
                            if(isset($feedbackObj->status)){
                            if($feedbackObj->status==0)
							   echo "OPEN";
							else if($feedbackObj->status==1)
                               echo "CLOSED";
                            else
                               echo "IN PROGRESS";
                            }
                            @endphp</h4>
                             
                           </div>
						    </div>
							<div class="col-lg-3 col-6">
                           <div class="form-group">
					<label>subject: </label>
					<h4>	{{$feedbackObj->subject}}</h4>
                            
                           </div>
						    </div>
<div class="col-lg-3 col-6">
<div class="form-group">
					<textarea class="form-control" rows="4">{{$feedbackObj->notes}}</textarea>
							  </div>
							  </div>
							  <div class="col-lg-3 col-6">
                           <div class="form-group row">
						   @if(!empty($feedbackObj->upload_1))
									<div class="col-sm-2">
									<a href="{{$file_path}}/{{$feedbackObj->upload_1}}" target="_blank"><img src="{{$file_path}}/{{$feedbackObj->upload_1}}" class="viewimg"></a>
									</div>
                                @endif

								@if(!empty($feedbackObj->upload_2))
									<div class="col-sm-2">
									<a href="{{$file_path}}/{{$feedbackObj->upload_2}}" target="_blank"><img src="{{$file_path}}/{{$feedbackObj->upload_2}}" class="viewimg"></a>
									</div>
                                @endif
                           </div>
						    </div>
						   </div>
						   
						   
						   

						   <div class="col-lg-12 asignFace">
                  <h2>Management Update</h2>
               </div>



						     <div class="row asignbg editbg">
						
							  <div class="col-sm-4">
							   <div class="form-group">
					<label>status: </label>
							  {{ Form::select('status', ['a' => '--ALL--',0=>'OPEN',2=>'IN PROGRESS',1=>"CLOSED"], null, ['class'=>'form-control','id'=>'role']) }}
                              </div>
							  </div>
							    <div class="col-sm-8">
								 <div class="form-group">
								<label> comment</label>
					<textarea class="form-control" name="remarks" rows="4">{{$feedbackObj->remarks}}</textarea>
                              </div>
                              </div>
							 
							   
						   </div>
						    <div class="row">
						    <div class="col-lg-12">
						   <button type="submit" class="submit float-right">Update</button>
						 
						   </div>
						   </div>
                    {!! Form::close() !!}
               
               
            </div>
         </div>

</section>
@stop


