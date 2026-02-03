@extends('visitors.layouts.visitors')
@section('content')
<style>
.qrimg a {
	color: #8F7F65
}

.qrimg a:hover {
	color: #8F7F65
}

.Purpose {

	 margin-bottom: 20px;
}
.forunitvisit p {
    color: #5D5D5D;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 0px;
    margin-top: 0px;
    padding-left: 15px;
}
.qrimg img {
    float: right;
}
</style>
<div class="status">
                  <h1>REGISTRATION SUCCESS !</h1>
               </div>

               <div class="containerwidth1">
               <form method="POST" action="{{ route('visitor-save') }}" class="forunitvisit" >
 {{ csrf_field() }} 
                        <div class="row resborder asignbg">
                           <div class="col-lg-4 resnone col-10">
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
                           <div class="col-lg-4 col-10">
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
                           <div class="col-lg-4 col-10">
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
                           <div class="col-lg-4 col-10">
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
                           <div class="col-lg-4 col-10">
                              <div class="form-group row">
                                 <label class="col-sm-3 col-4 col-form-label">
                                 <label>QR valid from: </label>
                                 </label>
                                 <div class="col-sm-9 col-8">
                                    <label class=" col-form-label ">
                                    <label>{{date('d/m/y h:i a',strtotime($bookingObj->visiting_start_time))}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4 col-10">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label forleft">
                                 <label>QR valid until: </label>
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <label class="col-form-label ">
                                    <label>{{date('d/m/y h:i a',strtotime($bookingObj->visiting_end_time))}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4 col-10">
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label">
                                 <label>Unit No: </label>
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <label class=" col-form-label ">
                                    <label>#{{isset($bookingObj->getunit->unit)?Crypt::decryptString($bookingObj->getunit->unit):''}}</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4 col-10">
                              <div class="form-group row">
                                 <label class="col-sm-3 col-4 col-form-label forleft">
                                 <label>Purpose: </label>
                                 </label>
                                 <div class="col-sm-9 col-8">
                                    <label class="col-form-label ">
                                    <label>{{isset($bookingObj->visitpurpose->visiting_purpose)?$bookingObj->visitpurpose->visiting_purpose:''}} </label>
                                 </div>
                              </div>
                           </div>
                          
                          
                        </div>
                        
                        <div class="row">
                           @isset($bookingObj->visitors)
                              <?php
                                 $div_space = '<div class="col-lg-0"></div>';
                                 $div_row = '</div><div class="row">';
                              ?>
                              @foreach($bookingObj->visitors as $k => $visitor)
                                 <div class="col-lg-6 Purpose">
                                    @if($property->security_option == 2)
										 <h3>Visitor {{$k+1}} Details</h3>
                                       <div class="">
                                         
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Name :</span>
                                             <span>{{$visitor->name}}</span>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-12">
                                             <span class="icon3">Mobile :</span>
                                             <span>{{$visitor->mobile}}</span>
                                          </div>
                                          @if($visitor->vehicle_no !='')
                                          <div class="conDiv3 col-12">
                                             <span class="icon3">Vechicle No :</span>
                                             <span>{{$visitor->vehicle_no}}</span>
                                          </div>
                                          @endif
                                          @if($visitor->email !='')
                                          <div class="conDiv3 col-12">
                                             <span class="icon3">Email :</span>
                                             <span>{{$visitor->email}}</span>
                                          </div>
                                          @endif
                                          @if($visitor->id_number !='')
                                          <div class="conDiv3 col-12">
                                             <span class="icon3">ID No :</span>
                                             <span>{{$visitor->id_number}}</span>
                                          </div>
                                          @endif
                                       </div>
                                    @endif
                                    @if($property->security_option != 2)
                                    <div class="row">
                                       <div class="col-lg-12 col-8">
                                          <div class="">
                                             <h3>Visitor {{$k+1}} Details</h3>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-8">
                                             <span class="icon3">Name :</span>
                                             <span>{{$visitor->name}}</span>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-8">
                                             <span class="icon3">Mobile :</span>
                                             <span>{{$visitor->mobile}}</span>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-8">
                                             <span class="icon3">Vechicle No :</span>
                                             <span>{{$visitor->vehicle_no}}</span>
                                          </div>
                                          <div class="conDiv3 col-12 col-lg-8">
                                             <span class="icon3">Email :</span>
                                             <span>{{$visitor->email}}</span>
                                          </div>
                                          @if($visitor->id_number !='')
                                          <div class="conDiv3 col-12 col-lg-8">
                                             <span class="icon3">ID No  :</span>
                                             <span>{{$visitor->id_number}}</span>
                                          </div>
                                          @endif
                                       </div>
                                       <!--div class="col-lg-4 col-4 qrimgside">
                                          <img src="{{$qrcode_path}}{{$visitor->qrcode_file}}">
                                          <p>{{$visitor->name}}</p>
                                       </div-->
                                    </div>
                                    @endif


                                 </div>
                                 
                                 <?php
                                 
                                 if($k%2==1 && $k !=0){
                                    //echo "Even".$k%2;
                                    echo $div_row;
                                 }

                                 else{
                                    //echo "Add".$k%2;
                                    echo $div_space;
                                 }

                                 ?>
                              @endforeach

                           @endif 
                          
                           @if($bookingObj->qr_scan_type == 3)
                           <div class="row mo1">
                              <div class="col-lg-10 kindly" id="web_msg">
                                 <p>Please scan the QR code at the Facial Recognition system located beside the gate or at the security booth to gain entry into the premise.</p>
                                 <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                                 <p>Right click on the QR code to save it. </p>
                                 </p>
                              </div>
							   <div class="col-lg-2 qrimgside" id="web_msg">
                                          <img src="{{$qrcode_path}}{{$visitor->qrcode_file}}">
                                          <p>{{$visitor->name}}</p>
                                          <p>({{$visitor->visit_count}}/{{$bookingObj->qr_scan_limit}})</p>
                                       </div>
                              <div class="col-lg-10 kindly" id="mob_msg">
                                 <p>Please scan the respective QR code at the Facial Recognition system located beside the gate to gain entry into the premise.</p>
                                 <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                                 <p>Press and hold onto the QR code to save or forward it.</p>
                                 </p>
                              </div>
                              <div class="col-lg-2 qrimgside" id="mob_msg">
                                 <img src="{{$qrcode_path}}{{$visitor->qrcode_file}}">
                              <p>{{$visitor->name}}</p>
                              <p>({{$visitor->visit_count}}/{{$bookingObj->qr_scan_limit}})</p>
                              </div>
                           </div>
                           @endif

                           @if($bookingObj->qr_scan_type == 1)
                           <div class="row mo1">
                              <div class="col-lg-10 kindly" id="web_msg">
                                 <p>Please scan the respective QR code at the Facial Recognition system located beside the gate to gain entry into the premise.</p>
                                 <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                                 <p>Right click on the QR code to save it. </p>
                                 </p>
                              </div>
							   <div class="col-lg-2 qrimgside" id="web_msg">
                                          <img src="{{$qrcode_path}}{{$visitor->qrcode_file}}">
                                          <p>{{$visitor->name}}</p>
                                          <p>({{$visitor->visit_count}}/{{$bookingObj->qr_scan_limit}})</p>
                                       </div>
                              <div class="col-lg-10 kindly" id="mob_msg">
                                 <p>Please scan the respective QR code at the Facial Recognition system located beside the gate to gain entry into the premise.</p>
                                 <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                                 <p>Press and hold onto the QR code to save or forward it.</p>
                                 </p>
                              </div>
<div class="col-lg-2 qrimgside" id="mob_msg">
                                          <img src="{{$qrcode_path}}{{$visitor->qrcode_file}}">
                                          <p>{{$visitor->name}}</p>
                                          <p>({{$visitor->visit_count}}/{{$bookingObj->qr_scan_limit}})</p>
                                       </div>
                           </div>
                           @endif
                           
                           @if($bookingObj->qr_scan_type == 2 && $bookingObj->qrcode_file !='')
                           <div class="row">
                           <div class="col-lg-10 visitorr" id="web_msg">
                              <p>All visitor registered this pre-registration can use the QR code listed below while at the secuirty guardhouse to gain entry into the premise.</p>
                              <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                              <p>Right click on the QR code to save it. </p>
                           </div>
                           <div class="col-lg-10 visitorr" id="mob_msg">
                              <p>All visitor registered this pre-registration can use the QR code listed below while at the secuirty guardhouse to gain entry into the premise.</p>
                              <p>A copy of the QR code has been sent to the email addresses that was provided.</p>
                              <p>Press and hold onto the QR code to save or forward it. </p>
                           </div>
						         <div class="col-lg-2 qrimg">

                              <img src="{{$qrcode_path}}{{$bookingObj->qrcode_file}}">
                              <p>{{$bookingObj->ticket}}</p>
                              <p>({{$bookingObj->scan_count}}/{{$bookingObj->qr_scan_limit}})</p>
                              <p><a href="">Click on the QR code to save it .</a></p>
                           </div>
                        </div>
                        
                        @endif
                           
                        </div>

                  </form>
            </div>
         </div>

@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
