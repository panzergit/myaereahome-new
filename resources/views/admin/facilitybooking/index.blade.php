@extends('layouts.adminnew')
<style>
.sortby {
    color: #5D5D5D;
    margin-top: 10px;
    font: normal normal bold 18px/24px Helvetica;
}</style>

@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(5,$permission->role_id); 
   //print_r($permission);
@endphp
@if(request()->has('view') && request()->view=='dashboard')
 <style>
.view_detail{
    cursor:pointer ;
}
    
    .monthbg{    background: #fff;
         margin-top: 20px;
         border-left: 15px solid #e9e9ea;     border-right: 15px solid #e9e9ea;}
         .monthbg h2 {     font-weight: 600;
         font-size: 16px;
         margin-top: 16px;
         text-align: center;
     }
     .countp #chartContainer01{}
     .countp #chartContainer02{}
     .Contractindex .col-lg-2 {
         flex: 0 0 19.666667%;
         max-width: 19.666667%;
         padding-right: 0px;
         padding-left: 10px;
     }
     .Contractbox {
         border: 1px solid #999999;
         border-radius: 10px;
         padding: 20px 6px;
         padding-top: 6px;
         margin-top: 20px;    
         background: #fff;
     }
     .Contractbox h5 {
         font-family: 'Lato';
         font-size: 12px;
         font-weight: 500;     text-align: center;
     }
     .Contractbox p {
         font-family: 'Lato';
         font-size: 18px;
         font-weight: 700;
         margin-bottom: 0px;
         text-align: center;
     }
    .mt0-4 {
        margin-top: 25px;
    }
        
   .table1 thead th {
        vertical-align: text-top;
    }
    .usertable td span{
        color: rgb(255 255 255)!important;
        -webkit-text-fill-color: rgb(255 255 255)!important;
    }
	.Contractindex a{    color: #212529;}
</style>
@endif
<div class="status">
  <h1>facility booking</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li @if(request()->has('view') && request()->view=='dashboard') class="activeul" @endif><a href="{{url('/opslogin/facility?view=dashboard')}}">Dashboard</a></li>
                     <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/facility?view=summary')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/facility/new')}}">New facility bookings</a></li>
                  </ul>
               </div>
               </div>
  <div class="">
@if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

@if(request()->has('view') && request()->view=='dashboard')
<form action="{{url('/opslogin/facility')}}" method="get" role="search" class="forunit">
           <input type="hidden" name="view" value="dashboard" />
                     <div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group">
                               <label class="">Year</label>
                                <select class="form-control" name="year">
                                    <option value="">--select--</option>
                                    @for($i=date('Y');$i>=(date('Y')-10);$i--)
                                    <option @if((request()->has('year') && request()->year==$i) || (!request()->has('year') && $i==date('Y'))) selected @endif value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                           </div>
                           </div>
                                 
						    <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">facility :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('category', ['' => 'ALL'] + $types, (request()->has('category') ? request()->category : ''), ['class'=>'form-control','id'=>'category']) }}
				                           </div>
										
                           </div>
                           </div>
               
							 <div class="col-lg-3">
                     <div class="form-group">
                              <label class="">unit : 
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="{{(request()->has('unit') ? request()->unit : '')}}" id="unit_list">
                           </div>
                           </div>
                           
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">status :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('status', ['' => 'All','0' => 'New','2'=>'Confirmed','1'=>'Cancelled'], (request()->has('status') ? request()->status : ''), ['class'=>'form-control','id'=>'status' ]) }}
                            
                              </div>
                           </div>
                           </div>
						    <div class="col-lg-8">
							</div>
						    <div class="col-lg-4 col-12 mt-2">
						   <div class="form-group ">
                     <a href="{{url('opslogin/facility?view=dashboard')}}"  class="submit ml-2 mr-2 float-right ">clear</a>
                     <button type="submit" class="submit  float-right">search</button>	
                  </div>
							</div>
						    
                     </div>
                      
                  </form>
                  
                  <div class="row Contractindex">
   <div class="col-lg-3 col-3">
     <a href='{{url("/opslogin/facility/search?view=summary")}}'>
      <div class="Contractbox">
         <h5>Total Bookings</h5>
         <p>{{$totalBookings}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href='{{url("/opslogin/facility/search?view=summary&status=0")}}'>
      <div class="Contractbox">
         <h5>Total Bookings (New)</h5>
         <p>{{$totalBookingsNew}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href="{{url("/opslogin/facility/search?view=summary&status=3")}}">
      <div class="Contractbox">
         <h5>Total Bookings (Confirmed)</h5>
         <p>{{$totalBookingsConfirmed}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href="{{url("/opslogin/facility/search?view=summary&status=1")}}">
      <div class="Contractbox">
         <h5>Total Bookings (Cancelled)</h5>
         <p>{{$totalBookingsCancelled}}</p>
      </div>
	  </a>
   </div>
</div>

<div class="row">
   <div class="col-lg-6 countp monthbg pb-3">
      <h2 class="text-center">Booking by Month</h2>
      <div id="chartContainer01" style="height: 370px; width: 100%;">
      </div>
      <!-- <div class="removewatermark01"></div> -->
   </div>
   <div class="col-lg-6 monthbg pb-3">
      <h2 class="text-center">Booking by Facility</h2>
      <div id="chartContainer02" style="height: 370px; width: 100%;">
      </div>
      <div class="removewatermark"></div>
   </div>
</div>

@endif

@if(request()->has('view') && request()->view=='summary')
<form action="{{url('/opslogin/facility/search')}}" method="get" role="search" class="forunit">
           <input name="view" type="hidden" value="summary">
                     <div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group">
                                 <label class=""> start date : 
                              </label>
                                 <div id="sandbox2">
                                    <input id="fromdate" name="fromdate" type="text" class="form-control" value="<?php echo(isset($fromdate)?$fromdate:'');?>">
                              </div>
                           </div>
                           </div>
                                 <div class="col-lg-3">                      
                           <div class="form-group ">
                                 <label class=""> end date :
                                 </label>
                                 <div id="sandbox">
                                    <input id="todate" name="todate" type="text" class="form-control" value="<?php echo(isset($todate)?$todate:'');?>">
                              </div>
                           </div>
                           </div>
						    
<div class="col-lg-3">
                     <div class="form-group">
                              <label class="">Block : 
                              </label>
                     {{ Form::select('building', ['' => '--Block--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
                           </div>
                           </div>
							 <div class="col-lg-3">
                     <div class="form-group">
                              <label class="">unit : 
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
                           </div>
<div class="col-lg-3">
                           <div class="form-group">
                              <label class="">facility :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('category', ['' => ''] + $types, $category, ['class'=>'form-control','id'=>'category']) }}
				                           </div>
										
                           </div>
                           </div>
                           @if(1==2)
							    <div class="col-lg-3">
						         <div class="form-group ">
                              <label class="">select month :
                              </label>
                                 <div id="sandbox2">
						<input id="datepickermonth" type="text" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
										
                              </div>
                           </div>
                           </div>
                           @endif
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">status :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('status', ['' => 'All','0' => 'New','2'=>'Confirmed','1'=>'Cancelled'], $status, ['class'=>'form-control','id'=>'status' ]) }}
                            
                              </div>
                           </div>
                           </div>
						        <div class="col-lg-12 asignFace1">
                  <h2 class="sortby">Sort By :</h2>
               </div>
			   <div class="col-lg-12">
                           <div class="form-group row">
                              <div  class="col-sm-2 filerl  col-5">
                                 <label class="containerbut">earliest date
                                 <input type="radio" name="filter" value="created_at" checked="">
                                 <span class="checkmarkbut"></span>
                                 </label>
                                                      </div>
                                                         <div  class="col-sm-1 filerl  col-3">
                                          <label class="containerbut">facility
                                 <input type="radio" name="filter" value="type_id" {{($filter=='type_id')?'checked':''}}>
                                 <span class="checkmarkbut"></span>
                                 </label>
                                    
                                                            </div>
															  <div  class="col-sm-2 filerl  col-3 ml-5">
                                          <label class="containerbut">date of event
                                 <input type="radio" name="filter" value="booking_date" {{($filter=='booking_date')?'checked':''}}>
                                 <span class="checkmarkbut"></span>
                                 </label>
                                    
                                                            </div>
                                                      <div  class="col-sm-2 filerl  col-4">
                                                      <label class="containerbut"> status
                                 <input type="radio" name="filter" value="status" {{($filter=='status')?'checked':''}}>
                                 <span class="checkmarkbut"></span>
                                 </label>
                              </div>
                           </div>
					 </div>
						    <div class="col-lg-12 col-12 ">
						   <div class="form-group ">
							
                   
                     <a href="{{ url('/opslogin/exportfacility?option='.$option.'&fromdate='.$fromdate.'&todate='.$todate.'&ticket='.$ticket.'&unit='.$unit.'&month='.$month.'&status='.$status.'&filter='.$filter) }}"  class="submit float-right">print</a>
                     <a href="{{url('opslogin/facility?view=summary')}}"  class="submit ml-2 mr-2 float-right ">clear</a>
                     <button type="submit" class="submit  float-right">search</button>	
                  </div>
							</div>
						    
                     </div>
                   
					 
                  </form>

                  <div>
				  <div class="overflowscroll2">
                  <table class="gap">
                     <thead>
                        <tr>
                        <th>facility</th>
                           <th>booked by</th>
                           
                          <th style="width:100px;">block</th>
                           <th style="width:100px;">unit</th>
                           <th>booking date</th>
                           <th>booking time</th>
                           <th>Booking status</th>
                           <th>Booking Fee</th>
                           <th>Deosit Fee</th>

                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($bookings)

                        @foreach($bookings as $k => $booking)
                        <tr>
                           <td class="roundleft">{{isset($booking->gettype->facility_type)?$booking->gettype->facility_type:''}}</td>
                           <td class="spacer">{{isset($booking->getname->name)?Crypt::decryptString($booking->getname->name):''}}</td>
                           <td class="spacer">{{isset($booking->getunit->buildinginfo->building)?$booking->getunit->buildinginfo->building:''}}</td>

                           <td class="spacer">{{isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):''}}</td>

                           <td class="spacer">{{date('d/m/y',strtotime($booking->booking_date))}}</td>
                           <td class="spacer">{{$booking->booking_time}}</td>
                           <td class="spacer">
                           @php
                            if(isset($booking->status)){
                              if($booking->status==0)
                                echo "New";
                              else if($booking->status==1)
                                echo "Cancelled";
                              else
                                echo "Confirmed";
                            }
                           @endphp
                            </td>
                           <td class="spacer">
                           <?php 
                              if($booking->payment_status==2)
                                 echo "Received";
                              else if($booking->payment_status==3)
                                 echo 'Refunded';
                              else
                                 echo "Not received";
                              ?>
                           </td>
                           <td class="spacer">
                           <?php 
                              if($booking->deposit_payment_status==2)
                                 echo "Received";
                              else if($booking->deposit_payment_status==3)
                                 echo 'Refunded';
                              else
                                 echo "Not received";
                              ?>
                           </td>
                           <td class="roundright">
						   
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
									 @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/facility/$booking->id/edit")}}">Edit Booking</a>
                           @if($booking->deposit_payment_status ==2 && $booking->refund_status ==0 && $booking->payment_required==1  && $booking->booking_date <  date('Y-m-d'))
                              <a class="dropdown-item buttonpopup datafech" onclick="facilityrefundpopup({{$booking->id}})" >Refund Deposit</a>
                           @endif
                           @if($booking->booking_date > date('Y-m-d'))
                           <span data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$booking->id}}" class="open-dialog">
                              <a class="dropdown-item" href="#">
                                 Cancel Booking
                              </a>
                           </span>
                              <!--<a class="dropdown-item" href="#" onclick="cancel_booking('{{url("opslogin/facility/cancellationrefund/$booking->id")}}');">Cancel Booking </a> -->
                           @endif
                           @php
                           $deposit_amt = $booking->deposit_fee;
                           @endphp
                           <input type="hidden" id="deposit_amt_id_{{$booking->id}}"  value="{{$deposit_amt}}">
                           <!--a class="dropdown-item buttonpopup datafech" data-id="{{isset($booking->gettype->facility_type)?$booking->gettype->facility_type:''}}<br> {{isset($booking->getname->name)?$booking->getname->name:''}}<br> 
						   {{isset($booking->getunit->unit)?$booking->getunit->unit:''}}" >Refund Deposit</a-->
                           @endif
								
                                    </div>
                                 </div>
                          </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
				    
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($bookings->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($bookings->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $bookings->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($bookings->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $bookings->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($bookings->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $bookings->lastPage()) as $i)
									@if($i >= $bookings->currentPage() - 2 && $i <= $bookings->currentPage() + 2)
										@if ($i == $bookings->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $bookings->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($bookings->currentPage() < $bookings->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($bookings->currentPage() < $bookings->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $bookings->appends($_GET)->url($bookings->lastPage()) }}">{{ $bookings->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($bookings->hasMorePages())
									<li><a href="{{ $bookings->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
                </div>

                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/facility/cancellationrefund'), 'files' => false]) !!}
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
                {{ Form::textarea('reason', null, ['class'=>'form-control', 'required' => true,'rows'=>4]) }}
              </div>
              <div class="modal-body">
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

				<section class="popup" id="my_form" >
   <div class="popup__content">
      <div class="close">
         <span></span>
         <span></span>
      </div>
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
			<!--p id="fechdata"></p-->
			
               <!--form action="" class="refunddes" method="post"-->
             
               {!! Form::open(['method' => 'POST','class'=>'refunddes', 'url' => url('opslogin/facility/refunddeposit'), 'files' => false]) !!}
               {{ csrf_field() }}
               <!--div class="refunddes"-->
                  <div class="col-lg-12">
                     <h2>Deposit Refund </h2>
                  </div>
                  <div class="col-lg-12">
                     <div class="form-group">
                        <label class="">Deposit Collected 
                        </label>
                        <input type="text" name="dcollection" class="form-control" value="" id="Depositid" readonly>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <div class="form-group">
                        <label class="">Fees to charge
                        </label>
                        <input type="number" name="charge_amount" class="form-control" value="" id="Feesid" onblur="refundCalcAmount()">
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <div class="form-group">
                        <label class="">Refund Amount
                        </label>
                        <input type="text" name="ramount" class="form-control" value="" id="Refund" readonly>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <div class="form-group">
                        <label class="">Notes
                        </label>
                        {{ Form::textarea('reason', null, ['class'=>'form-control', 'required' => true,'rows'=>4]) }}
                        </div>
                  </div>
                  <div class="col-lg-12">
                     <input type="hidden" name="refund_booking" id="refund_booking_id" value="">
                     <input type="submit"  value="Submit" name="submit" class="submitfull mt-4">
                     <!--<input type="button" value="Submit" name="submit" class="submitfull mt-4"   onclick="refundFacilityDeposit()">
					 	<input type="submit" class="row-dialog-btn btn btn-success" value="Yes, Accept" onclick="Submit();" /-->
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="popup2" id="thank_you" style="display:none">
   <div class="popup__content">
      <a href="{{url('opslogin/facility')}}"> <div class="close">
         <span></span>
         <span></span>
      </div></a>
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
               <div class="row h-100">
                  <div class="col-lg-12 col-12">
                     <div class="form-group  my-auto refunddes">
                        <h2 class="text-center mt-5">Deposit has been refunded successfully</h2>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<style>
.refunddes{}
.refunddes p{font-weight: 600;}
.refunddes h2{    text-align: left; color:#8F7F65; font-size: 18px; font-weight:600;    margin-bottom: 20px;}
.refunddes label{}
.refunddes input{}
.refunddes input {
    height: 34px;
    line-height: 34px;
    margin-bottom: 0px;
    background: #D0D0D0 0% 0% no-repeat padding-box;
    border-radius: 34px;
    border: none;
    font: normal normal bold 14px/20px Helvetica;
    letter-spacing: 0.5px;
}
.refunddes label {
	    text-align: left;
  font-weight: 600;
    font-size: 13px;
    color: #5D5D5D;
    padding-right: 0px;
    margin-bottom: 3px!important;
    padding-top: 2px;
    text-transform: capitalize;
    letter-spacing: 0px;
}
.refunddes .col-lg-12{padding: 0px;}
.submitfull:hover {
    border: none;
    /* color: #8F7F65; */
    color: #5D5D5D;
    background-color: #DFCFB5;
    text-decoration: none;
    outline: none;
}
.submitfull {
    text-align: center;
    margin: 0 auto;
    display: block;
    background: #8F7F65!important;
    width: 100%;
    height: 34px;
    line-height: 22px;
    color: #fff!important;
    font-weight: 600;
    padding: 6px 20px;
    border: none;
    text-transform: capitalize;
    box-shadow: 0px 2px 4px #00000029;
    border-radius: 6px;
    font: normal normal 900 14px/0px Lato;
    letter-spacing: 0.5px;
    line-height: 24px!important;
}
.nextbtn{position: absolute;
    bottom: 30px;
    right: 10px; background: #dfcfb5; color: #000;
    font-size: 14px; border: 2px solid #dfcfb5;     border-radius: 0.375rem;
       padding: 4px 25px!important;
    text-transform: uppercase;}
.popup {
  width: 100%;
  height: 100%;
  background: transparent;
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}
.popup .popup__content {
    width: 25%;
    height: auto;
    overflow: auto;
    padding: 15px 0px;
	    padding-bottom: 40px;
    background: white;
    color: black;
    position: relative;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-sizing: border-box;

    border-radius: 16px;
	box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
}
.popup .popup__content .close {
  position: absolute;
  right: 5px;
  top: 14px;
  width: 20px;     z-index: 99;
  display: block;
}
.popup .popup__content .close span {
  cursor: pointer;
  position: fixed;
  width: 14px;
  height: 3px;
  background: #495057;
}
.popup .popup__content .close span:nth-child(1) {
  transform: rotate(45deg);
}
.popup .popup__content .close span:nth-child(2) {
  transform: rotate(135deg);
}

.buttonpopup {
  cursor: pointer;
}
.close {
    opacity: 1;
}
.popup2 {
  width: 100%;
  height: 100%;
  background: transparent;
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}
.popup2 .popup__content {
   width: 25%;
    height: auto;
    overflow: auto;
    padding: 15px 0px;
	    padding-bottom: 40px;
    background: white;
    color: black;
    position: relative;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-sizing: border-box;

    border-radius: 16px;
	box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
}
.popup2 .popup__content .close {
  position: absolute;
  right: 20px;
  top: 16px;
  width: 20px;
  display: block; z-index:99
}
.popup2 .popup__content .close span {
  cursor: pointer;
  position: fixed;
  width: 20px;
  height: 3px;
  background: #495057;
}
.popup2 .popup__content .close span:nth-child(1) {
  transform: rotate(45deg);
}
.popup2 .popup__content .close span:nth-child(2) {
  transform: rotate(135deg);
}
</style>
  @endif
@endsection

@section('customJS')
<script>
    
    
    @if(request()->has('view') && request()->view=='dashboard')
    let ydates = [];
    let defLocs = [];
@foreach($dates as $d)
    ydates.push({
        x : new Date("{{ explode('-',$d['date'])[0] }}", "{{ explode('-',$d['date'])[1] }}", "{{ explode('-',$d['date'])[2] }}"),
        y : parseInt("{{ $d['defects'] }}"),
        indexLabel : "{{ date('M Y',strtotime($d['date'])) }}"
    });
@endforeach

@foreach($defactsByFacility as $d)
    defLocs.push({
        exploded: true,
        y : parseInt("{{ $d['defects'] }}"),
        name : "{{ $d['name'] }}"
    });
@endforeach

window.onload = function () {
    CanvasJS.addColorSet("greenShades",
                         [//colorSet Array
         
                         "#1A2E67",            
                         ]);
         var chart01 = new CanvasJS.Chart("chartContainer01", {
         	animationEnabled: true,
         	colorSet: "greenShades",
            axisX: {
                interval: 1,
                intervalType: "year"
            },
         	axisY: {
                gridThickness: 1
            },
            axisY2: {
                suffix: "%",
                gridThickness: 0,
            },	
         	toolTip: {
         		shared: true
         	},
         	legend: {
         		cursor:"pointer",
         		itemclick: toggleDataSeries
         	},
         	data: [
                {
                    type: "line",
                    name: "Total ticket",
                    showInLegend: true,
                    dataPoints: ydates
                }
            ]
         });
         chart01.render();
         
         function toggleDataSeries(e) {
         	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
         		e.dataSeries.visible = false;
         	}else {
         		e.dataSeries.visible = true;
         	}
         	chart01.render();
         }
         
         CanvasJS.addColorSet("greenShades",
                         [//colorSet Array
                        "#f2cfee",
                         "#caeefb",              
                         "#c2f1c8",              
                         "#fbe3d6",              
                         "#c1e5f5",              
                         "#dceaf7",             
                         "#e59edd",             
                         "#f6c6ad",             
                         "#d5d5b8",             
                         "#d9f2d0",             
                         ]);
         var chart = new CanvasJS.Chart("chartContainer02", {
         theme: "light2", // "light1", "light2", "dark1", "dark2"
         	colorSet: "greenShades",
         	//exportEnabled: true,
         	animationEnabled: true,
         	title:{
         		//text: "State Operating Funds"
         	},
         	<!-- legend:{ -->
         		<!-- cursor: "pointer", -->
         		<!-- itemclick: explodePie -->
         	<!-- }, -->
         	data: [{
         		type: "pie",
         		showInLegend: true,
         		toolTipContent: "{name}: <strong>{y}</strong>",
         		indexLabel: "{name} - {y}",
         		dataPoints: defLocs
         	}]
         });
         chart.render();
         
         function explodePie (e) {
         	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
         		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
         	} else {
         		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
         	}
         	e.chart.render();
         
         }
          }
    @endif
</script>
@endsection
