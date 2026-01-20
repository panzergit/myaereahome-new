@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>renovation application - update </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
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
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/eform/renovation/'.$eformObj->id)]) !!}

                     <div class="row asignbg editbg">
					   <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>ticket : 
                              </label>
                              <h4>  {{$eformObj->ticket}}  </h4> 
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group">
                             
                              <label>submitted date : 
                              </label>
                            <h4>    {{date('d/m/y',strtotime($eformObj->created_at))}}  </h4> 
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>name of resident : 
                              </label>
						 <h4> 	  {{isset($eformObj->user->name)?Crypt::decryptString($eformObj->user->name):''}}  </h4> 
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>unit no : 
                              </label>
                            <h4>    {{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}}  </h4> 
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group ">
                             <label>contact no : 
                             </label>
                          <h4>     {{$eformObj->contact_no}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group">
                             <label>email : 
                             </label>
                            <h4>   {{$eformObj->email}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">
                             <label>name of contractor company : 
                             </label>
                         <h4>      {{$eformObj->reno_comp}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group">
                             <label>person in-charge : 
                             </label>
                          <h4>     {{$eformObj->in_charge_name}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">
                             <label>address of contractor company : 
                             </label>
                            <h4>   {{$eformObj->comp_address}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>contact number: 
                             </label>
                             <h4>  {{$eformObj->comp_contact_no}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group">    
                             <label >work start & end :
                             </label>
                           <h4>    {{date("d/m/y",strtotime($eformObj->reno_start))}} - {{date('d/m/y',strtotime($eformObj->reno_end))}}  </h4> 
                          </div>
                          </div>
						   <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>hacking work start & end : 
                             </label>
                             
                            <h4>   {{date('d/m/y',strtotime($eformObj->hacking_work_start))}} -  {{date('d/m/y',strtotime($eformObj->hacking_work_end))}}  </h4> 
                        
                          </div>
                          </div>
                         
                          @if(isset($eformObj->owner_signature) && $eformObj->owner_signature !='')
							   <div class="col-lg-3 col-6">
                             <div class="form-group ">
                                 <label>signature of owner : 
                                 </label>
                                 <div class="">
                                    <img src="data:image/png;base64, {{$eformObj->owner_signature}}" class="viewsig"/>
                                 </div>
                              </div>
                              </div>
                              @endif   
                              @if(isset($eformObj->nominee_signature) && $eformObj->nominee_signature !='')  
 <div class="col-lg-3 col-6">								  
                              <div class="form-group ">
                                 <label >signature of nominee : 
                                 </label>
                                 <div class="">
                                    <img src="data:image/png;base64, {{$eformObj->nominee_signature}}" class="viewsig"/>
                                 </div>
                              </div>
                              </div>
                           @endif
                           @if(isset($eformObj->nominee_contact_no) && $eformObj->nominee_contact_no !='')    
 <div class="col-lg-3 col-6">							   
                              <div class="form-group ">
                                 <label>nominee contact no : 
                                 </label>
                                <h4> {{$eformObj->nominee_contact_no}}  </h4> 
                                 
                              </div>
                              </div>
                           @endif

                           @if(isset($eformObj->letter_of_authorization) && $eformObj->letter_of_authorization !='') 
 <div class="col-lg-3 col-6">							   
                              <div class="form-group ">
                                 <label>letter of authorization : 
                                 </label>
                                 <div class="">
                                    <a href="{{$file_path}}/{{$eformObj->letter_of_authorization}}" target="_blank">  
                                          <img src="{{url('assets/admin/img/fileshw.png')}}" class="" style="width:50px; display: block;">
                                     </a>
                                 </div>
                              </div>
                              </div>
                           @endif

                      <div class="col-lg-3 col-6">
					  <div class="form-group ">
					  <label>works</label>
                                 <h4>
                                 
                                 @if($eformObj->details)
                                    @foreach($eformObj->details as $k => $detail)
                                     
                                          {{$k+1}}{{". ".$detail->detail}}
                                      
                                    @endforeach
                                 @endif
</h4>
                        </div>
                        </div>
                         			
                     </div>
                  <div class="">
				  <div class="overflowscroll2">
                  <table class="gap">
                           <thead>
                              <tr>
                                 <th>s/n</th>
                                 <th>workmen / sub-contractor</th>
                                 <th>id type</th>
                                 <th>id number</th>
                                 <th>expiry date of work permit</th>
                              </tr>
                           </thead>
                           <tbody id="tbody">
                           @if($eformObj->sub_con)

                              @foreach($eformObj->sub_con as $k => $contractor)

                              <tr>
                                 <td class="roundleft">{{$k+1}}</td>
                                 <td class="spacer">
                                 {{isset($contractor->workman)?$contractor->workman:''}}
                                 </td>
                                 <td class="spacer">
                                 @php
                                    if($contractor->id_type ==1)
                                       echo "Passport";
                                    else if($contractor->id_type ==2)
                                       echo "NRIC";
                                    else
                                       echo "Work Permit";

                                 @endphp
                                 </td>
                                 <td class="spacer">
                                 {{isset($contractor->id_number)?$contractor->id_number:''}}
                                 </td>
                                 <td class="roundright">
                                 {{isset($contractor->permit_expiry)?date('d/m/y',strtotime($contractor->permit_expiry)):''}}
                                 </td>
                                
                              </tr>

                              @endforeach

                      @endif 
                             
                           </tbody>
                        </table>
               
                     </div>
                     </div>
					 <div class="col-lg-12 asignFace">
                  <h2>management update</h2>
               </div>
                     <div class="row asignbg editbg">
                     <div class="col-lg-4 ">
                           
                        <div class="form-group  ">
                                 <label class=""> status 
                                 </label>
                                 {{ Form::select('status', ['a' => '--ALL--',0=>'NEW','3'=>'APPROVED',2=>'IN PROGRESS',1=>"CANCELLED","4"=>"REJECTED","5"=>"PAYMENT PENDING","6"=>"REFUNDED"], null, ['class'=>'form-control','id'=>'status', 'onchange'=>'getfields()']) }}
                              
                              </div>   
                        </div>
						<div class="col-lg-8 ">
                        <div class="form-group  ">
                                 <label class=""> remarks 
                                 </label>
                              <textarea class="form-control" name="remarks" rows="6">{{$eformObj->remarks}}
                              </textarea>
                        </div>
                        </div>
                     
                     <div class="col-lg-8 "></div>
                     <div class="col-lg-4">
                     <div class="form-group  ">
                     <button type="submit" class="submit mt-2 ml-2 float-left">update</button>
                         
                     <a href="{{url("/opslogin/eform/renovation")}}"  class="Delete mt-2 float-right">cancel</a>
                         
                                 </div>
                                 </div>
                                 </div>


                    {!! Form::close() !!}
               
               
            </div>
         </div>


         <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/book_inspection/updatecancelstatus'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Booking - Cancel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>REASON:</label>
                {{ Form::textarea('reason', null, ['class'=>'form-control','rows'=>4]) }}
              </div>
              <div class="modal-body">
                 <input type="hidden" name="return_url" value="list">
               <input type="hidden" name="bookId" id="bookId" value="">
               <input type="hidden" name="status"value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
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


