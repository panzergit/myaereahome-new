@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>door access card : acknowledgement of receipt</h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     <li><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
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
            
                  <div class="col-lg-12 asignFace">
                  <h2>official use only</h2>
               </div>
              
                  
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/eform/dooraccess/acknowledgementsave/'.$eformObj->id)]) !!}
                         <div class="row asignbg">
                            <div class="col-lg-4">
                           <div class="form-group ">
                              <label>number of access card issued: 
                              </label>
                                 {{ Form::text('number_of_access_card', isset($eformObj->ack->number_of_access_card)?$eformObj->ack->number_of_access_card:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                            
                           </div>
                           </div>
						   <div class="col-lg-4">
                           <div class="form-group ">
                              <label>serial number of access card issued : 
                              </label>
                                 {{ Form::text('serial_number_of_card', isset($eformObj->ack->serial_number_of_card)?$eformObj->ack->serial_number_of_card:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                           
                           </div>
                           </div>
						   <div class="col-lg-4">
                           <div class="form-group">
                              <label>received by resident : 
                              </label>
                                 {{ Form::text('acknowledged_by', isset($eformObj->ack->acknowledged_by)?$eformObj->ack->acknowledged_by:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                          
                           </div>
                           </div>
						   
                           @if(isset($eformObj->ack->signature))
							   <div class="col-lg-4">
                           <div class="form-group ">
                              <label>name of management issued : 
                              </label>
                                 {{ Form::text('manager_issued', isset($eformObj->ack->manager_issued)?$eformObj->ack->manager_issued:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                              
                           </div>
                           </div>
                          <div class="col-lg-4">
                           <div class="form-group ">
                              <label >signature of management : 
                              </label>
                              <div class="">
                                 <a href="#" target="_blank"><img src="data:image/png;base64, {{$eformObj->ack->signature}}" class="viewsig"/></a>
                              </div>
                           </div>
                           </div>
<div class="col-lg-4">
                           <div class="form-group ">
                              <label >date of singnature : 
                              </label>
                                 <div id="sandbox4">
                                    {{ Form::text('date_of_signature', isset($eformObj->ack->date_of_signature)?$eformObj->ack->date_of_signature:'', ['class'=>'form-control','required' => true,'id' =>'datetext2','placeholder' => '']) }}
                                 </div>
                           </div>
                           </div>
                           @endif
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                        <div class="form-group">
                        <a href="{{url("/opslogin/eform/moveinout")}}"  class="Delete ml-3 mr-0 mt-2 float-right">cancel</a>
                            
                              <input type="hidden" name="ack_id" value="{{isset($eformObj->ack->id)?$eformObj->ack->id:''}}">
                              <button type="submit" class="submit mt-2  float-right">update</button>
                             
                           </div>
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


