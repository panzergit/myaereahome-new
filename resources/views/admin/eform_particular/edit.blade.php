@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>update particulars application - update </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     <li><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
                     <li><a href="{{url('/opslogin/eform/changeaddress#ef')}}"> Mailing Address </a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/particular#ef')}}">Particulars </a></li>
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
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/eform/particular/'.$eformObj->id)]) !!}

                     <div class="row asignbg editbg">
                           <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                    <label >ticket : 
                                    </label>
                                   <h4> {{$eformObj->ticket}} </h4> 
                                 </div>
                                 </div>
								  <div class="col-lg-3 col-6">
                                 <div class="form-group ">
                                 
                                    <label>submitted date : 
                                    </label>
                                  <h4>   {{date('d/m/y',strtotime($eformObj->created_at))}} </h4> 
                                 </div>
                                 </div>
                              <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                    <label>unit no : 
                                    </label>
                                  <h4>   {{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}} </h4> 
                                 </div> 
                                 </div>
								  <div class="col-lg-3 col-6">
                                 <div class="form-group ">
                             <label>address : 
                             </label>
                            <h4>  {{$eformObj->address}}
                          </div>
                          </div>
                           <div class="col-lg-3 col-6">
                          <div class="form-group ">    
                             <label>email: 
                             </label>
                            <h4>  {{$eformObj->email}} </h4> 
                          </div>
                          </div>
                          </div>
						  
                           <div class="">
						    <div class="col-lg-12 asignFace">
                  <h2>section a: particluars of owner</h2>
               </div>
                       <div class="overflowscroll">
                  <table class="table usertable1">
                           <thead>
                              <tr>
                                 <th>S/N</th>
                                 <th>name of owner</th>
                                 <th>passport / nric </th>
                                 <th>contact no</th>
                                 <th>vehicle no</th>
                              </tr>
                           </thead>
                           <tbody id="tbody">
                           @if($eformObj->owners)

                              @foreach($eformObj->owners as $k => $owner)

                              <tr>
                                 <td>{{$k+1}}</td>
                                 <td>
                                 {{isset($owner->owner_name)?$owner->owner_name:''}}
                                 </td>
                                 <td>
                                 {{isset($owner->owner_nric)?$owner->owner_nric:''}}
                                 </td>
                                 <td>
                                 {{isset($owner->owner_contact_no)?$owner->owner_contact_no:''}}  
                                 </td>
                                 <td>
                                 {{isset($owner->owner_vehicle_no)?$owner->owner_vehicle_no:''}}  
                                 </td>
                                
                              </tr>

                              @endforeach

                      @endif 
                             
                           </tbody>
                        </table>
               
                     </div>
                     </div>


					 <div class="col-lg-12 asignFace">
                  <h2>section b: particluars of tenants</h2>
               </div>
                         <div class="overflowscroll">
                  <table class="table usertable1">
                           <thead>
                              <tr>
                                 <th>S/N</th>
                                 <th>name of tenant</th>
                                 <th>passport / nric </th>
                                 <th>contact no</th>
                                 <th>vehicle no</th>
                              </tr>
                           </thead>
                           <tbody id="tbody">
                           @if($eformObj->tenants)

                              @foreach($eformObj->tenants as $k => $tenant)

                              <tr>
                                 <td>{{$k+1}}</td>
                                 <td>
                                 {{isset($tenant->tenant_name)?$tenant->tenant_name:''}}
                                 </td>
                                 <td>
                                 {{isset($tenant->tenant_nric)?$tenant->tenant_nric:''}}
                                 </td>
                                 <td>
                                 {{isset($tenant->tenant_contact_no)?$tenant->tenant_contact_no:''}}  
                                 </td>
                                 <td>
                                 {{isset($tenant->tenant_vehicle_no)?$tenant->tenant_vehicle_no:''}}  
                                 </td>
                                
                              </tr>

                              @endforeach

                      @endif 
                             
                           </tbody>
                        </table>
               
                     </div>
					   <div class="row asignbg editbg">
                  <div class="col-lg-4 ">
                          
                         
                          
                          <div class="form-group ">    
                             <label >singnature of owner: 
                             </label>
                             <label class="">
                              @if(isset($eformObj->owner_signature))
                                 <img src="data:image/png;base64, {{$eformObj->owner_signature}}" width="200px"/>
                              @endif
                             </label>
                          </div>
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
                                 {{ Form::select('status', ['a' => '--ALL--',0=>'NEW','3'=>'APPROVED',2=>'IN PROGRESS',1=>"CANCELLED","4"=>"REJECTED"], null, ['class'=>'form-control','id'=>'status', 'onchange'=>'getfields()']) }}
                              
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
                     <div class="col-lg-8">
                     </div>
					   <div class="col-lg-4">
					   <button type="submit" class="submit mt-2 float-left">update</button>
                        <a href="{{url("/opslogin/eform/particular")}}"  class="Delete mt-2 float-right">cancel</a>
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


