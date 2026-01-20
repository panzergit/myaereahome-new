@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>vehicle iu registration application - update </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
                     <li><a href="{{url('/opslogin/eform/changeaddress#ef')}}"> Mailing Address </a></li>
                     <li><a href="{{url('/opslogin/eform/particular#ef')}}">Particulars </a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
      <!--div class="show">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div-->
       <div class="">
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/eform/regvehicle/'.$eformObj->id)]) !!}

                     <div class="row asignbg editbg">
					   <div class="col-lg-4 col-6">
                           <div class="form-group ">
                              <label>ticket : 
                              </label>
                           <h4> {{$eformObj->ticket}} </h4>
                           </div>
						    </div>
							<div class="col-lg-4 col-6">
                           <div class="form-group ">
                             
                              <label>submitted date : 
                              </label>
                               <h4>{{date('d/m/y',strtotime($eformObj->created_at))}} </h4>
                           </div>
                           </div>
						   <div class="col-lg-4 col-6">
                           <div class="form-group ">
                              <label>name of owner : 
                              </label>
                              <h4> {{isset($eformObj->user->name)?Crypt::decryptString($eformObj->user->name):''}} </h4>
                           </div>
                           </div>
						   <div class="col-lg-4 col-6">
                           <div class="form-group">
                              <label>unit no : 
                              </label>
                              <h4> {{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}} </h4>
                           </div>
                           </div>
						   <div class="col-lg-4 col-6">
                           <div class="form-group ">
                             <label>contact no : 
                             </label>
                             <h4> {{$eformObj->contact_no}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group">
                             <label>email : 
                             </label>
                            <h4>  {{$eformObj->email}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group">
                             <label>registered owner of vehicle: 
                             </label>
                            <h4>  {{$eformObj->owner_of_vehicle}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group ">
                             <label>vehicle licence plate no : 
                             </label>
                            <h4>  {{$eformObj->licence_no}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group">
                             <label>iu label no : 
                             </label>
                            <h4>  {{$eformObj->iu_number}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group ">
                             <label>declared by : 
                             </label>
                           <h4>   {{$eformObj->declared_by}} </h4>
                          </div>
                          </div>
                         <div class="col-lg-4 col-6">
                           <div class="form-group ">
                                 <label>relationship : 
                                 </label>
                                <h4>  {{($eformObj->relationship ==1)?"Family":"Tenant"}} </h4>
                              </div>
                              </div>
							  <div class="col-lg-4 col-6">
                              <div class="form-group">
                                 <label>name of nominee : 
                                 </label>
                               <h4>   {{$eformObj->in_charge_name}} </h4>
                              </div>
                              </div>
							  <div class="col-lg-4 col-6">
                              <div class="form-group ">
                                 <label>passport / nric no: 
                                 </label>
                                <h4>  {{$eformObj->passport_no}} </h4>
                              </div>
                              </div>
                              <div class="col-lg-4 col-6">
                              <div class="form-group ">    
                                 <label>Contact number: 
                                 </label>
                               <h4>   {{$eformObj->nominee_contact_no}} </h4>
                              </div>
                              </div>
							  <div class="col-lg-4 col-6">
                              <div class="form-group ">    
                                 <label>email.: 
                                 </label>
                                <h4>  {{$eformObj->nominee_email}} </h4>
                              </div>
                              </div>
                           @if($eformObj->relationship ==2)
							   <div class="col-lg-4 col-6">
                              <div class="form-group ">    
                                 <label>tenancy period: 
                                 </label>
                                <h4>  {{date('d/m/y',strtotime($eformObj->tenancy_start))}} - {{date('d/m/y',strtotime($eformObj->tenancy_end))}} </h4>
                              
                              </div>
                              </div>
                           @endif
						   <div class="col-lg-4 col-6">
                          <div class="form-group ">
                             <label>Registered owner of vehicle: 
                             </label>
                             <h4> {{$eformObj->owner_of_nominee_vehicle}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group ">
                             <label>vehicle licence plate no : 
                             </label>
                            <h4>  {{$eformObj->nominee_vehicle_licence_no}} </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group ">
                             <label>iu label no : 
                             </label>
                           <h4>   {{$eformObj->nominee_vehicle_iu_number}}  </h4>
                          </div>
                          </div>
						  <div class="col-lg-4 col-6">
                          <div class="form-group">    
                             <label>singnature of owner: 
                             </label>
                             <h4 class="">
                              @if(isset($eformObj->owner_signature))
                                 <img src="data:image/png;base64, {{$eformObj->owner_signature}}" width="200px"/>
                              @endif
                             </h4>
                          </div>
                          </div>
                          @if(isset($eformObj->owner_signature) && $eformObj->owner_signature !='')
<div class="col-lg-4 col-6">
                           <div class="form-group ">    
                              <label>signature of nominee: 
                              </label>
                              <h4 class="">
                              @if(isset($eformObj->nominee_signature))
                              <img src="data:image/png;base64, {{$eformObj->nominee_signature}}" width="200px"/>
                                 @endif
                              
                              </h4>
                           </div>
                           </div>
                           @endif   
<div class="col-lg-4 col-6">
                           <div class="form-group ">
					<label>file(S): </label>
                    
                       @php
                        if(isset($eformObj->documents))
                        foreach($eformObj->documents as $k =>$document){
                           if($document->file_original !=''){
                              @endphp
                           
                                 <div class="col-lg-3 col-form-label permit">
                                    <a href="{{$file_path}}/{{$document->file_original}}" target="_blank">  
                                       <img src="{{url('assets/admin/img/fileshw.png')}}" class="" style="width:50px; display: block;">
                                    <label>{{$document->category_name->category}}</label></a>
                                 </div>     
                              @php
                           }
                        }
                        @endphp
                           </div> 
                           </div> 
                           </div> 
						    
                    <div class="col-lg-12 asignFace">
                  <h2>management update</h2>
               </div>
                       
                        <div class="row asignbg editbg">
						<div class="col-lg-4">
                        <div class="form-group ">
                                 <label class=""> status 
                                 </label>
                                 {{ Form::select('status', ['a' => '--ALL--',0=>'NEW','3'=>'APPROVED',2=>'IN PROGRESS',1=>"CANCELLED","4"=>"REJECTED"], null, ['class'=>'form-control','id'=>'status', 'onchange'=>'getfields()']) }}
                              
                              </div>   
                              </div>   
                        <div class="col-lg-8">
                              <div class="form-group  ">
                                 <label class=""> remarks 
                                 </label>
                              <textarea class="form-control" name="remarks" rows="6">{{$eformObj->remarks}}
                              </textarea>
                        </div>
                        </div>
                    <div class="col-lg-8"></div>
                     <div class="col-lg-4">
                     <div class="form-group  ">
                     <button type="submit" class="submit mt-2  float-left">update</button>
					  <a href="{{url("/opslogin/eform/regvehicle")}}"  class="Delete mt-2 float-right">cancel</a>
                        
                  
                                 </div>
                                 </div>
                     </div>
                     






                    {!! Form::close() !!}
               
               
            </div>
         </div>

</section>
 <script type="text/javascript">

      window.onload = function() {
         getInspectionTimeslots();
      };

      function gettime($time){
        $("#appt_time").val($time);

      }

      function getfields(){
         if($("#status").val() ==4){
            $("#reminderflds").show(); 
         }else{
            $("#reminderflds").hide(); 
         }
      }
    </script>
@stop


