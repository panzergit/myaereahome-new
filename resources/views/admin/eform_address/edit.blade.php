@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>Changing Mailing Address application - update </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     <li><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/changeaddress#ef')}}"> Mailing Address </a></li>
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
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/configuration/econcierge/'.$eformObj->id)]) !!}

                     <div class="row asignbg editbg">
					   <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label >ticket : 
                              </label>
                           <h4> {{$eformObj->ticket}} </h4> 
                           </div>
						       </div>
							    <div class="col-lg-3 col-6">
                           <div class="form-group ">
                             
                              <label>submitted date : 
                              </label>
                              <h4>  {{date('d/m/y',strtotime($eformObj->created_at))}} </h4> 
                           </div>
                           </div>
                            <div class="col-lg-3 col-6">
                          <div class="form-group ">
                             <label >declared by : 
                             </label>
                              <h4> {{$eformObj->declared_by}} </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group">
                              <label>unit no : 
                              </label>
                            <h4>    {{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}} </h4> 
                             
                           </div>
                           </div>
                          <div class="col-lg-3 col-6">
                          <div class="form-group ">
                             <label>address : 
                             </label>
                             <h4>  {{$eformObj->address}} </h4> 
                          </div>
                          </div>
                          
                          <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>contact number: 
                             </label>
                             <h4>  {{$eformObj->contact_no}} </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>email: 
                             </label>
                             <h4>  {{$eformObj->email}} </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>Singnature of owner: 
                             </label>
                            <h4> 
                              @if(isset($eformObj->owner_signature))
                                 <img src="data:image/png;base64, {{$eformObj->owner_signature}}" width="100px"/>
                              @endif
                           </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label >Signature of nominee: 
                             </label>
                              <h4> 
                             @if(isset($eformObj->nominee_signature))
                             <img src="data:image/png;base64, {{$eformObj->nominee_signature}}" width="100px"/>
                              @endif
                             
                              </h4> 
                          </div>
                          </div>
                          </div>
                          
                           
						    
                    <div class="col-lg-12 asignFace">
                  <h2>management update</h2>
               </div>
			    <div class="row asignbg editbg">
                         <div class="col-lg-4">
                        <div class="form-group  ">
                                 <label > status 
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
                     <div class="form-group">
                     <button type="submit" class="submit mt-2  float-left">update</button>
                         <a href="{{url("/opslogin/eform/changeaddress")}}"  class="Delete  mt-2 float-right">cancel</a>
                 
                  
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


