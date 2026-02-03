@extends('visitors.layouts.visitors')
@section('content')
<div class="status">
                  <h1>VISITOR PRE-REGISTRATION</h1>
               </div>
               <div class="col-lg-12 kindlymobile">
                  <p>Kindly fill in the details below prior to your visit and you will be provided with a QR code for a seamless access experience on your day of visit.</p>
                  <h2>Property Details</h2>
               </div>
               <div class="containerwidth1">
               <form method="POST" action="{{ route('visitor-save') }}"  class="forunitvisit" id="reg_form" >
 {{ csrf_field() }} 
                <div class="">
                        <div class="row resborder asignbg">
                           <div class="col-lg-4 resnone">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-6 col-form-label">
                                 <label>Booking ID:</label>
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    <label class="col-form-label ">
                                    <label>{{$bookingObj->ticket}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label class="col-sm-3 col-4 col-form-label">
                                 <label>Property: </label>
                                 </label>
                                 <div class="col-sm-9 col-8">
                                    <label class="col-form-label ">
                                    <label>{{$bookingObj->propertyinfo->company_name}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label forleft">
                                 <label>Invited By: </label>
                                 </label>
                                 <div class="col-sm-8 col-8">
                                    <label class="col-form-label ">
                                    <label>{{isset($bookingObj->user->name)?Crypt::decryptString($bookingObj->user->name):''}} {{isset($bookingObj->user->userinfo->last_name)?Crypt::decryptString($bookingObj->user->userinfo->last_name):''}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label">
                                 <label>Day Of Visit: </label>
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <label class=" col-form-label ">
                                    <label>{{date('d/m/y',strtotime($bookingObj->visiting_date))}} </label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label class="col-sm-3 col-4 col-form-label">
                                 <label>Unit No: </label>
                                 </label>
                                 <div class="col-sm-9 col-8">
                                    <label class=" col-form-label ">
                                    <label>#{{isset($bookingObj->getunit->unit)?Crypt::decryptString($bookingObj->getunit->unit):''}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label forleft">
                                 <label>Purpose: </label>
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <label class="col-form-label ">
                                    <label>{{isset($bookingObj->visitpurpose->visiting_purpose)?$bookingObj->visitpurpose->visiting_purpose:''}} </label>
                                 </div>
                              </div>
                           </div>
                           
                        </div>
                        <div class="col-lg-12 kindly">
                              <p>Kindly fill in the details below prior to your visit and you will be provided with a QR code for a seamless access experience on your day of visit.</p>
                           </div>
                              
                        @for($i=1;$i<=$slot_available;$i++)
                        @php
                           if($i ==1)
                             { 
                                $display_style = "";

                             }
                           else
                           {
                              $display_style = "display:none";
                           }
                        @endphp
                        <div class="row" id="add_field{{$i}}" style="{{$display_style}}">
                           <div class="col-lg-12 " id="tbody">
                             
							  
                           <div class="alert alett">
                              <h3 class="vhead">Visitor {{$i}} Details</h3>
                              <div class="clbord clfet" >
                                 @if($i !=1)
                                 <button type="button" class="close precon alertclik" data-dismiss="alert" onclick="hidevisitor({{$i}})">&times;</button>
                                 @endif
                                 <div class="form-group row ">
                                    <div class="col-lg-4">
                                       <div class="form-group ">
                                      
                                          <div class="col-sm-12 col-12 pado paname">
										  <label>Name{{($i ==1)?"* ":""}}: </label>
                                             <label class="col-form-label">
                                                <input type="text" name="name_{{$i}}" id="name_{{$i}}" class="form-control" placeholder="" >
												</label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-lg-4">
                                    <div class="form-group ">
                                
                                    <div class="col-sm-12 col-12 pado ">
									<label>Mobile{{($i ==1)?"* ":""}} :</label>
                                    <label class="col-form-label">
                                    <input type="text" name ="mobile_{{$i}}" id="mobile_{{$i}}" class="form-control" placeholder="" >
									</label>
                                    </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-4">
                                    <div class="form-group ">
                                   
                                    <div class="col-sm-12 col-12 pado">
									<label>Vechicle No : </label>
                                    <label class="col-form-label">
                                    <input type="text" name ="vehicle_no_{{$i}}" id="vehicle_no_{{$i}}" class="form-control" placeholder="">
									</label>
                                    </div>
                                    </div>
                                    </div> 
                                    <div class="col-lg-4">
                                    <div class="form-group ">
                                
                                    <div class="col-sm-12 col-12 pado paname">
									<label>Email : </label>
                                    <label class="col-form-label">
                                    <input type="text" name ="email_{{$i}}" id="email_{{$i}}"  class="form-control" placeholder="">
									 
                                  </label>
								   <!--p>(If entered, QR code will be sent via email<br>
                                    to visitor for their use on the day of visit)</p-->
									 <p>(QR code will be sent to this email)</p>
									
                                    </div>
                                    </div>
                                    </div>
                                   
                                    @if(isset($bookingObj->visitpurpose) && $bookingObj->visitpurpose->id_required ==1)
                                    <div class="col-lg-4">
                                    <div class="form-group ">
                                   
                                    <div class="col-sm-12 col-12 pado ">
									<label>ID No* :</label>
                                    <label class="col-form-label">
                                    <input type="text" name ="id_number_{{$i}}" id="id_number_{{$i}}" class="form-control" placeholder="" {{($i ==1)?"required ":""}}>
									         </label>
                                    </div>
                                    </div>
                                    </div>
                                    @else
                                       <input type="hidden" id="id_number_{{$i}}" class="form-control" placeholder="">
                                    
                                    @endif
                                 </div>
                              </div>
                              </div>
                           </div>
						    <div class="">
						    <div class="container">
							<div class="col-lg-12">
							
							  <!--p>(If entered, QR code will be sent via email<br>
                                    to visitor for their use on the day of visit)</p-->
									
						  
                     
                        </div>  
                        </div>  
                        </div>  
                        </div>  
                           
                           @endfor
                           


                        <div class="row">
                           @if($slot_available>1)
                           <div class="col-lg-12 p-0 rescol" id="buttonSection">
                              <a class="addrow"
                                 id="addBtn03" type="button" onclick="showmore()">
                              Add Visitor
                              </a>
                           </div>
                           @endif
                           <input type="hidden" id="rowcount" value="1">
                           <input type="hidden" id="maxcount" value="{{$slot_available}}">
                        </div>
                     </div>
                     <!--<div class="row">
                        <div class="col-lg-12 p-0 rescol">
                        <input type="hidden" name="BookId" value="{{$bookingObj->id}}">
                           <button type="submit" class="submit2 mt-3 float-center">SUBMIT</button>
                        </div>
                     </div> -->
                     <div class="row">
                        <div class="col-lg-12 p-0 rescol">
                        <input type="hidden" name="BookId" value="{{$bookingObj->id}}">
                        <input type="submit" class="submit2 mt-3 float-center" value="Submit">
                        </div>
                     </div>

                     
                  </form>

                
               </div>
            </div>
         </div>

@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>