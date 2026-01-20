
@extends('layouts.adminnew')




@section('content')

  <div class="status">
    <h1>Facility Booking - update</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                      <li><a href="{{url('/opslogin/facility?view=dashboard')}}">Dashboard</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/facility?view=summary')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/facility/new')}}">New facility bookings</a></li>
                  </ul>
               </div>
               </div>
<div class="">
 <div class="">
                   {!! Form::model($bookingObj,['method' =>'PATCH','url' => url('opslogin/facility/'.$bookingObj->id),'class'=>'forunit']) !!}
				   <div class="col-lg-12 asignFace">
                  <h2>Status Update</h2>
               </div>
                     <div class="row asignbg editbg">
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>facility:</label>
                              <h4>  {{isset($bookingObj->gettype->facility_type)?$bookingObj->gettype->facility_type:''}}</h4>
                                <input id="type" type="hidden" value="{{$bookingObj->type_id}}">
                             
                           </div>
						     </div>
							  <div class="col-lg-3">
                           <div class="form-group">
                              <label>booked by:</label>
                               <h4>  {{isset($bookingObj->getname->name)?Crypt::decryptString($bookingObj->getname->name):''}}</h4>
                              </div>
                              </div>
							   <div class="col-lg-3">
                           <div class="form-group">
                              <label>date booked:</label>
                                  <h4>  {{date('d/m/y',strtotime($bookingObj->booking_date))}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group">
                              <label>time booked:</label>
                              <h4>  {{$bookingObj->booking_time}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group">
                              <label >status:</label>
                              @if($bookingObj->status ==1)
                              <label class="col-form-label">Cancelled</label>
                              @else 
                                   {{ Form::select('status', ['0' => 'New','2'=>'Confirmed'], null, ['class'=>'form-control','id'=>'status' ]) }}
                              @endif
                           </div>
                           </div>
                           @if($bookingObj->reason !='')
							    <div class="col-lg-3">
                           <div class="form-group">
                              <label>reason:</label>
                                 <h4>@php
                                       echo str_replace("\n", '<br />',  $bookingObj->reason);
                                       @endphp
                                 </h4>
                             
                           </div>
                           </div>
                           @endif
                           
                      <div class="col-lg-3"></div>
						    <div class="col-lg-6  mt0-4">
                        @if($bookingObj->status !=1)
                           <div class="caneclbook mt-1">
                              <a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$bookingObj->id}}" class="open-dialog float-left"><span>cancel booking</span></a>
                           </div>
                         
                           
                           @if($bookingObj->reason !='')
                           <!--div class="form-group ">
                             
                           </div-->
                           @endif
                           <button type="submit" class="submit float-right">update</button>
                           @endif
                        </div>
                        </div>
               @if($bookingObj->payment_required==1)
                        <div class="col-lg-12 asignFace">
                  <h2>Payment Info</h2>
               </div>
                     <div class="row asignbg editbg">
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>booking fee:</label>
                               <h4>  @php echo number_format(($bookingObj->booking_fee),2) @endphp</h4>
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>booking fee payment status:</label>
                              <h4><?php 
                              if($bookingObj->payment_status==2)
                                 echo "Received";
                              else if($bookingObj->payment_status==3)
                                 echo 'Refunded';
                              else
                                 echo "Not received";
                              ?></h4>
                           </div>
						      </div>
                        @if($bookingObj->refund_status ==1)
                           <div class="col-lg-3">
                              <div class="form-group">
                                 <label>cancellation charge:</label>
                                 <h4>  {{$bookingObj->capture_amount}}</h4>
                              </div>
                           </div>
                           <div class="col-lg-3">
                              <div class="form-group">
                                 <label>refunded amount:</label>
                                 <h4>{{$bookingObj->refund_amount}}</h4>
                              </div>
                           </div>
                        @endif
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>deposit fee:</label>
                               <h4>@php echo number_format(($bookingObj->deposit_fee),2) @endphp</h4>
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>Deposit fee payment status:</label>
                              <h4>  <?php 
                              if($bookingObj->deposit_payment_status==2)
                                 echo "Received";
                              else if($bookingObj->deposit_payment_status==3)
                                 echo 'Refunded';
                              else
                                 echo "Not received";
                              ?></h4>
                           </div>
						      </div>
							   <div class="col-lg-3">
                           <div class="form-group">
                              <label>Damage claim:</label>
                                  <h4> {{$bookingObj->claim_amount}}</h4>
                           </div>
                           </div>
						      <div class="col-lg-3">
                           <div class="form-group">
                              <label>refunded amount:</label>
                              <h4>  {{$bookingObj->deposit_refund_amount}}</h4>
                           </div>
                        </div>
						    
                           <div class="col-lg-3">
                           <div class="form-group">
                              <label>Notes:</label>
                              <h4>  {{$bookingObj->refund_reason}}</h4>
                           </div>
                        </div>
						   
                        </div>
                  @endif
						  <div class="col-lg-12 asignFace">
                  <h2>edit date / time slot</h2>
               </div>
						 <div class="row asignbg editbg">
                        @if($bookingObj->status !=1)
                        <div class="col-lg-6">
                           <div class="form-group row">
                              <label class="col-sm-5 col-form-label"> new date :</label>
							  <div class="col-sm-7">
                                 <div id="sandbox">
                                   {{ Form::text('booking_date', null, ['class'=>'form-control','required' => true,'id'=>'booking_date']) }}
                                    
                              </div>
                              </div>
                           </div>
                           <div class="form-group row mt20">
                              <label class="col-sm-5 col-form-label"> new tim slot :</label>
							  <div class="col-sm-7">
                                 {{ Form::text('booking_time', null, ['class'=>'form-control','required' => true,'id'=>'appt_time','readonly'=>'readonly']) }}
                           
                           </div>
                           </div>
                           <button type="submit" class="submit mt-1 float-right">update</button>
                        </div>

                        <div class="col-lg-6" >
                           <div id="facilityslotstables" class="containerstime">
                      
                           @foreach($slots as $time)
                           <label class="containers ">
                           <input type="radio" name="timeslot" onclick="gettime(this.value)" value="{{$time}}" checked="checked">
                           <span class="checkmark">{{$time}}</span>
                           </label>
						 
                           @endforeach
                          </div>
                          <div class="clearfix"></div>
                           <!--div class="taskselct containers3">
                              <label class="container3">Available
                              <input type="checkbox">
                              <span class="checkmark3"></span>
                              </label>
                              <label class="container4">Taken
                              <input type="checkbox">
                              <span class="checkmark4"></span>
                              </label>
                           </div-->
</div>
                        @else
                           <div class="col-lg-6">
                              <div class="form-group row">
                                 <label class="col-sm-5 col-form-label"> Not available for cancelled booking</label>
                              </div>
                           </div>
                        @endif
                     </div>
                  </form>
               </div>

                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/facility/updatecancelstatus'), 'files' => false]) !!}
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
         getFacilityTimeslots();
      };

      function gettime($time){
        $("#appt_time").val($time);

      }
    </script>

@stop

