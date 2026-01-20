
@extends('layouts.adminnew')




@section('content')

  <div class="status">
    <h1>Visitor Management - update</h1>
  </div>
    <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                      <li ><a href="{{url('/opslogin/visitor-summary?view=dashboard')}}">Dashboard</a></li>
                     <li ><a href="{{url('/opslogin/visitor-summary#vm')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/visitor-summary/create')}}">Add New Walk In</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/visitor-summary/new#vm')}}">New Visitors</a></li> 
                  </ul>
               </div>
               </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif

 <div class="">
                   {!! Form::model($bookingObj,['method' =>'PATCH','url' => url('opslogin/visitor-summary/'.$bookingObj->id),'class'=>'forunitvisit']) !!}
                   <div class="">
                        <div class="row asignbg forunit ">
                           <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                 <label>booking id:  </label>
                                
                                   <h4> {{$bookingObj->ticket}} </h4>
                                 </div>
                              </div>
                          
                           <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                 <label>Property: </label>
                                     <h4>{{$bookingObj->propertyinfo->company_name}} </h4>
                                
                              </div>
                           </div>
                           <div class="col-lg-3 col-6">
                              <div class="form-group">
                                 <label>Invited By: </label>
                                       @if($bookingObj->booking_type==1)
                                    <h4> {{isset($bookingObj->user->userinfo->first_name)?Crypt::decryptString($bookingObj->user->userinfo->first_name):''}} {{isset($bookingObj->user->userinfo->last_name)?Crypt::decryptString($bookingObj->user->userinfo->last_name):''}}
                                    </h4>
                                    @else
                                    <label> Walk-In</label>
                                    @endif
                                 </div>
                              </div>
                           <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                 <label>Day Of Visit: </label>
                                   <h4> {{date('d/m/y',strtotime($bookingObj->visiting_date))}}  </h4>
                                 </div>
                              </div>
                           
                        <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                 <label>Unit No: </label>
                                    <h4>#{{isset($bookingObj->getunit->unit)?Crypt::decryptString($bookingObj->getunit->unit):''}}</h4>
                                 </div>
                              </div>
                           
                           <div class="col-lg-3 col-6">
                              <div class="form-group ">
                                 <label>Purpose: </label>
                                    <h4>{{isset($bookingObj->visitpurpose->visiting_purpose)?$bookingObj->visitpurpose->visiting_purpose:''}}
                            </h4>
                              </div>
                           </div>

                             <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                    <label>Start Time:</label>
                                       <h4>{{date('h:i a',strtotime($bookingObj->visiting_start_time))}}
                              </h4>
                                 </div>
                              </div>
                              
                             <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                    <label>End Time:</label>
                                       <h4>{{date('h:i a',strtotime($bookingObj->visiting_end_time))}}
                              </h4>
                                 </div>
                              </div>
                             

                              @if(isset($bookingObj->comp_info))
                             <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                    <label>Company:</label>
                                       <h4>{{isset($bookingObj->comp_info)?$bookingObj->comp_info:''}}
                              </h4>
                                 </div>
                              </div>
                              @endif

                              @if(isset($bookingObj->sub_cat))
                             <div class="col-lg-3 col-6">
                                 <div class="form-group ">
                                    <label>{{isset($bookingObj->visitpurpose->sub_category)?$bookingObj->visitpurpose->sub_category:''}}</label>
                                       <h4>{{isset($bookingObj->visitreason->sub_category)?$bookingObj->visitreason->sub_category:''}}
                              </h4>
                                 </div>
                              </div>
                              @endif
                            

                        </div>
                    
                        <!--div class="row">
                           <div class="col-lg-6 col-6"></div>
                              <div class="col-lg-6 col-6 Entering">
                                 <h2>Entering premise</h2>
                              </div>
                           </div-->
                      
                           
                           @if($bookingObj->visitors)

                              @foreach($bookingObj->visitors as $k => $visitor)
                              <div class="row">
                                 <div class="col-lg-6 col-10 ">
                                    <div class="row asignbg  Purpose2">
                                       <div class="col-lg-12">
                                             <h3>Visitor {{$k+1}} Details</h3>
                                       </div>
                                       <div class="col-lg-8 col-8">
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Name :</span>
                                             <span>{{$visitor->name}}</span>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Mobile :</span>
                                             <span>{{$visitor->mobile}}</span>
                                          </div>
                                          
                                          @if($visitor->email !='')
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Email :</span>
                                             <span>{{$visitor->email}}</span>
                                          </div>
                                          @endif
                                         
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Vechicle No :</span>
                                             <span><input type="text" name="vehicle_no_{{$visitor->id}}" class="form-control" value="{{$visitor->vehicle_no}}"></span>
                                          </div>
                                         
                                          @if(isset($bookingObj->visitpurpose) && $bookingObj->visitpurpose->id_required ==1)
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">ID No :</span>
                                             <span><input type="text" name="id_number_{{$visitor->id}}" class="form-control" value="{{$visitor->id_number}} "></span>
                                          </div>
                                          @endif
                                       </div>
                                       <div class="col-lg-4 col-4 qrimgside qrimgside2">
                                          @if($visitor->visit_status ==1)
                                          <div class="conDiv4 col-12 col-lg-12">
                                             <span class="icon4">Entry Date :</span>
                                             <span>{{date('d/m/y',strtotime($visitor->entry_date))}}</span>
                                          </div>
                                          <div class="conDiv4 col-12 col-lg-12">
                                             <span class="icon4">Entry Time :</span>
                                             <span>{{date('H:i',strtotime($visitor->entry_date))}}</span>
                                          </div>
										   <div class="conDiv4 col-12 col-lg-12">
                                           <label class="containeruser1 ">Entering premise
                                          <input type="checkbox" name="visitor_ids[]" value="{{$visitor->id}}" {{($visitor->visit_status ==1)?"checked":''}}>
                                          
                                          <div class="checkmarkuser1"></div>
                                          </label>
                                          </div>
                                          @endif

                                       </div>
                                    </div>
                                 </div>
                            
                              </div>
                              @endforeach

                           @endif   

							   
                          

                     
                   <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit float-right">update</button>

                        </div>
                     </div>
                     </div>
                  </form>
               </div>

               
</section>


 <script type="text/javascript">

      window.onload = function() {
         getFacilityTimeslots();
      };

      function gettime($time){
        $("#appt_time").val($time);

      }
    </script>

@stop

