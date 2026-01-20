@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>View Resident’s file upload</h1>
  </div>
    <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/residents-uploads#rfu')}}">Summary</a></li>
                     <!--li><a href="{{url('/opslogin/residents-uploads/new#rfu')}}">New upload</a></li-->
                  </ul>
               </div>
               </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
      <div class="show">
  <div class="overlay"></div>
  <div class="img-show">
    <!--span>X</span-->
    <img src="">
  </div>
</div>
       <div class="">
                 {!! Form::model($submissionObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/residents-uploads/'.$submissionObj->id)]) !!}
                 <div class="row asignbg editbg">
                        <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>unit: </label>
							<h4> {{isset($submissionObj->user->userinfo->getunit->unit)?Crypt::decryptString($submissionObj->user->userinfo->getunit->unit):''}}</h4>
                           </div>
						    </div>
					 <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>Upload by: </label>
							<h4>  {{isset($submissionObj->user->name)?Crypt::decryptString($submissionObj->user->name):''}}</h4>
                           </div>
						    </div>
							 <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>Upload date : </label>
							<h4>  {{date('d/m/y',strtotime($submissionObj->created_at))}}</h4>
                           </div>
						    </div>
							 <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>category: </label>
							<h4> {{isset($submissionObj->category->docs_category)?$submissionObj->category->docs_category:''}}</h4>
                           </div>
						    </div>
							 <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>file(S): </label>
                       @php
                        if(isset($submissionObj->files))
                        foreach($submissionObj->files as $k =>$file){
                       @endphp
                       
                              <div class=" permit">
							 <a href="{{$file_path}}/{{$file->docs_file}}" target="_blank">  <img src="{{url('assets/admin/img/Condo.png')}}" class="viewimgfile2">
						<p>{{$file->docs_file_name}}</p></a>
                              </div>
                       @php
                        }
                        @endphp
                           </div>
						    </div>
						
							 <div class="col-lg-3 col-6">
                           <div class="form-group ">
					<label>message: </label>
							<h4>{{$submissionObj->notes}}</h4>
                           </div>
						    </div>
							  	 <div class="col-lg-3 col-6">
							   <div class="form-group ">
					<label>status: </label>
							 
                              {{ Form::select('status', ['a' => '--ALL--',0=>'NEW','1'=>'PROCESSING',2=>'PROCESSED'], $submissionObj->status, ['class'=>'form-control','id'=>'role']) }}
                            
                           </div>
							  </div>
							   <div class="col-lg-3 col-6">
                     <label>management remarks : </label>
                     {{ Form::textarea('remarks', null, ['class'=>'form-control','placeholder' => '','rows'=>'4']) }}

                              </div>
						   </div>
						   
							   
						 
						     <div class="row">
							
							  <div class="col-lg-12 ">
						     <button type="submit" class="submit mt-2 float-right">Update</button>
						   </div>
							   
						   </div>

                    {!! Form::close() !!}
               
               
            </div>
         </div>

</section>
@stop


