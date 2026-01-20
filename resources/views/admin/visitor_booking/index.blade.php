@extends('layouts.adminnew')


@section('content')
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
@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(34,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>visitor management</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                <ul class="summarytab">
                    <li @if(request()->has('view') && request()->view=='dashboard') class="activeul" @endif><a href="{{url('/opslogin/visitor-summary?view=dashboard')}}">Dashboard</a></li>
                    <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/visitor-summary?view=summary')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/visitor-summary/create')}}">Add New Walk In</a></li>
                     @endif
                     <li><a href="{{url('/opslogin/visitor-summary/new#vm')}}">New Visitors</a></li>
                  </ul>
               </div>
               </div>
  <div >
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

@if(request()->has('view') && request()->view=='dashboard')
<form action="{{url('/opslogin/visitor-summary')}}" method="get" role="search" class="forunit">
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
                              <label class="">Purpose :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('category', ['' => 'ALL'] +$types , (request()->has('category') ? request()->category : ''), ['class'=>'form-control','id'=>'category']) }}
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
                              <label class="">Booking Type :
                              </label>
                                 <div id="sandbox2">
                                {{ Form::select('booking_type', ['1' => 'Pre Registration','2'=>'Walk-In'], (request()->has('booking_type') ? request()->booking_type : ''), ['class'=>'form-control']) }}
                              </div>
                           </div>
                           </div>
						    <div class="col-lg-8">
							</div>
						    <div class="col-lg-4 col-12 mt0-4">
						   <div class="form-group ">
                     <a href="{{url('opslogin/visitor-summary?view=dashboard')}}"  class="submit ml-2 mr-2 float-right ">clear</a>
                     <button type="submit" class="submit  float-right">search</button>	
                  </div>
							</div>
						    
                     </div>
                      
                  </form>
                  
                  <div class="row Contractindex">
   <div class="col-lg-2 col-3">
     <a href='{{url("/opslogin/visitor-summary?view=summary")}}'>
      <div class="Contractbox">
         <h5>Total Bookings</h5>
         <p>{{$totalBookings ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href='{{url("/opslogin/visitor-summary/search?view=summary&booking_type=1")}}'>
      <div class="Contractbox">
         <h5>Total Bookings (Pre - Registration)</h5>
         <p>{{$totalBookingsPre ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/visitor-summary/search?view=summary&booking_type=2")}}">
      <div class="Contractbox">
         <h5>Total Bookings (Walk-In)</h5>
         <p>{{$totalBookingsWalkIn ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/visitor-summary/search?view=summary&status=1&filter=feedback_submissions.created_at")}}">
      <div class="Contractbox">
         <h5>Total Bookings (Success)</h5>
         <p>{{$totalBookingsSuccess ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/visitor-summary/search?view=summary&status=1&filter=feedback_submissions.created_at")}}">
      <div class="Contractbox">
         <h5>Total Bookings (Cancelled)</h5>
         <p>{{$totalBookingsCancelled ?? 0}}</p>
      </div>
	  </a>
   </div>
</div>

<div class="row">
   <div class="col-lg-6 countp monthbg pb-3">
      <h2 class="text-center">Bookings by Month</h2>
      <div id="chartContainer01" style="height: 370px; width: 100%;">
      </div>
      <!-- <div class="removewatermark01"></div> -->
   </div>
   <div class="col-lg-6 monthbg pb-3">
      <h2 class="text-center">Bookings by Purpose</h2>
      <div id="chartContainer02" style="height: 370px; width: 100%;">
      </div>
      <div class="removewatermark"></div>
   </div>
</div>
@endif

@if(request()->has('view') && request()->view=='summary')
<form action="{{url('/opslogin/visitor-summary/search')}}" method="get" role="search" class="forunit" autocomplete="off">
                        <input name="view" type="hidden" value="summary">
<div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">booking id: 
                              </label>
                                 <input  type="text" name="bookingid" class="form-control" value="<?php echo(isset($bookingid)?$bookingid:'');?>" >
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
                              <label class="">unit: 
                              </label>
                                 <input  type="text" class="form-control" name="unit" id="unit_list" value="<?php echo(isset($unit)?$unit:'');?>">
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">visit date: 
                              </label>
                              <!--div id="sandbox2">
                                    <input id="fromdate" name="fromdate" type="text" class="form-control" value="<?php echo(isset($fromdate)?$fromdate:'');?>">
                                 </div-->
                                 <!--div id="sandbox">
                                    <input id="todate" name="todate" type="text" class="form-control" value="<?php echo(isset($todate)?$todate:'');?>">
				                     </div-->
                              <div id="sandbox">
                                    <input id="fromdate" name="date" type="text" class="form-control" value="<?php echo(isset($date)?$date:'');?>">
                                 </div>
                                 
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">purpose: 
                              </label>
                                  {{ Form::select('purpose', ['' => '--Visiting Purpose--'] + $types, $purpose, ['class'=>'form-control']) }}
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">booking type: 
                              </label>
                                  {{ Form::select('booking_type', ['1' => 'Pre Registration','2'=>'Walk-In'], $booking_type, ['class'=>'form-control']) }}
                           </div>
                           </div>
                       
                        <div class="col-lg-6 mt0-4">
                           <div class="form-group ">
                          
                              
							    <a href="{{ url('/opslogin/exportvisitors?option='.$option.'&bookingid='.$bookingid.'&unit='.$unit.'&date='.$date.'&purpose='.$purpose.'&booking_type='.$booking_type) }}" class="submit  float-right">print</a>
                         <a href="{{url("/opslogin/visitor-summary")}}"  class="submit  mr-2 ml-2 float-right">clear</a>
							  
                        <button type="submit" class="submit  float-right">search</button>
                     </div>
                           
                        </div>
                       

                     </div>
                  </form>

 <div class="overflowscroll2">
               <table class="gap">
                  @if(isset($permission) && $permission->create==1)
                    <!--div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/visitor-summary/create")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + NEW WALK IN</a>
                           </div>
                       </div>
                    </div-->
                    @endif
                     <thead>
                        <tr>
                           <th>booking id</th>
                           <th>block</th>
                           <th>unit no</th>
                           <th>invited by</th>
                           <th>date of visit</th>
                           <th>entry date</th>
                           <th>entry time</th>
                           <th>visitor no</th>
                           <th>purpose</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($bookings)

                       @foreach($bookings as $k => $booking)
                        <tr>
                           <td class="roundleft">{{$booking->ticket}} 
                           @if($booking->view_status ==0)
                           &nbsp;<span class="badge badge-pill badge-danger text-white">New</span>
                           @endif
                           </td>
                            <td class="spacer"> {{isset($booking->getunit->buildinginfo->building)?$booking->getunit->buildinginfo->building:''}}
                           </td>
                           <td class="spacer"> {{isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):''}}
                           </td>
                           <td class="spacer"><?php
                           if($booking->booking_type ==1)
                              echo isset($booking->user->name)?Crypt::decryptString($booking->user->name):'';
                           else
                              echo "Walk-In";
                           ?></td>
                           
                           <td class="spacer">{{date('d/m/y',strtotime($booking->visiting_date))}}
                           </td>
                           <td class="spacer">
                              @php
                              if($booking->booking_type ==2){
                                 echo ($booking->booking_type ==2)?date('d/m/y',strtotime($booking->created_at)):'';
                              }else if($booking->visited_count->count() >0){
                                 $VisitorBookingObj = new \App\Models\v2\VisitorBooking();
                                 echo $VisitorBookingObj->visitor_entry_date($booking->id);
                              }
                              @endphp
                           </td>
                           <td class="spacer">
                           @php
                              if($booking->booking_type ==2){
                                 echo ($booking->booking_type ==2)?date('H:i',strtotime($booking->created_at)):'';
                              }else if($booking->visited_count->count() >0){
                                 $VisitorBookingObj = new \App\Models\v2\VisitorBooking();
                                 echo $VisitorBookingObj->visitor_entry_time($booking->id);
                              }
                              @endphp
                           </td>
                           <td class="spacer"><?php echo $booking->visitors->count();?></td>
                           <td class="spacer">{{isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:''}}
                           
                           <td class="spacer">
                              <?php
                              if($booking->visited_count->count() >0 && $booking->visited_count->count() >= $booking->visitors->count())
                                 $status = "Entered";
                              else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
                                 $status = $booking->visited_count->count()." Entered";
                              else if($booking->registered_count->count() >0 && $booking->registered_count->count() == $booking->invitedemails->count())
                                 $status = "Registration Success";
                              else if($booking->registered_count->count() >0 && $booking->registered_count->count() <= $booking->visitors->count())
                                 $status = $booking->registered_count->count()." Registered";
                              else if($booking->status==0)
                                 $status = "Pending";
                              else if($booking->status==1)
                                 $status = "Cancelled";
                              else  
                                 $status = "Entered";
                                 
                           echo $status;

                              ?></td>
                              <td class="roundright">
							    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                       @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/visitor-summary/$booking->id/edit")}}">Edit</a>
                           @endif
                         @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/visitor-summary/delete/$booking->id")}}');" >Delete</a>
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

@foreach($defactsByPurpose as $d)
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


