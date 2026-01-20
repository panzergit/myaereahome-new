@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(6,$permission->role_id); 
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
  <h1>submitted feedback list </h1>
</div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                    <li @if(request()->has('view') && request()->view=='dashboard') class="activeul" @endif><a href="{{url('/opslogin/feedbacks/summary?view=dashboard')}}">Dashboard</a></li>
                     <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/feedbacks/summary?view=summary')}}">Summary</a></li>
                     <!--li><a href="{{url('/opslogin/feedbacks/new#fb')}}">New feedback</a></li-->
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
<form action="{{url('/opslogin/feedbacks/summary')}}" method="get" role="search" class="forunit">
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
                              <label class="">Category :
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
                                 {{ Form::select('status', [''=>'ALL','0' => 'OPEN','2'=>'IN PORGRESS','1'=>'CLOSED'], (request()->has('status') ? request()->status : ''), ['class'=>'form-control','id'=>'status' ]) }}
                            
                              </div>
                           </div>
                           </div>
						    <div class="col-lg-8">
							</div>
						    <div class="col-lg-4 col-12 mt0-4">
						   <div class="form-group ">
                     <a href="{{url('opslogin/feedbacks/summary?view=dashboard')}}"  class="submit ml-2 mr-2 float-right ">clear</a>
                     <button type="submit" class="submit  float-right">search</button>	
                  </div>
							</div>
						    
                     </div>
                      
                  </form>
                  
                  <div class="row Contractindex">
   <div class="col-lg-3 col-3">
     <a href='{{url("/opslogin/feedbacks/summary?view=summary")}}'>
      <div class="Contractbox">
         <h5>Total Feedbacks</h5>
         <p>{{$totalFeedbacks ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href='{{url("/opslogin/feedbacks/search?view=summary&status=0&filter=feedback_submissions.created_at")}}'>
      <div class="Contractbox">
         <h5>Total Feedbacks (Open)</h5>
         <p>{{$totalFeedbacksOpen ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href="{{url("/opslogin/feedbacks/search?view=summary&status=2&filter=feedback_submissions.created_at")}}">
      <div class="Contractbox">
         <h5>Total Feedbacks (Inprogress)</h5>
         <p>{{$totalFeedbacksInprogress ?? 0}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-3 col-3">
      <a href="{{url("/opslogin/feedbacks/search?view=summary&status=1&filter=feedback_submissions.created_at")}}">
      <div class="Contractbox">
         <h5>Total Feedbacks (Closed)</h5>
         <p>{{$totalFeedbacksClosed ?? 0}}</p>
      </div>
	  </a>
   </div>
</div>

<div class="row">
   <div class="col-lg-6 countp monthbg pb-3">
      <h2 class="text-center">Feedback by Month</h2>
      <div id="chartContainer01" style="height: 370px; width: 100%;">
      </div>
      <!-- <div class="removewatermark01"></div> -->
   </div>
   <div class="col-lg-6 monthbg pb-3">
      <h2 class="text-center">Feedback by Category</h2>
      <div id="chartContainer02" style="height: 370px; width: 100%;">
      </div>
      <div class="removewatermark"></div>
   </div>
</div>
@endif

@if(request()->has('view') && request()->view=='summary')
<form action="{{url('/opslogin/feedbacks/search')}}" method="get" role="search" class="forunit">
            <input name="view" type="hidden" value="summary">
                     <div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group ">
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
                     <div class="form-group ">
                              <label class="">unit : 
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
                           </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">category :
                             
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('category', ['' => ''] + $types, $category, ['class'=>'form-control','id'=>'category']) }}
				                           </div>
									
                           </div>
                           </div>
                           @if(1==2)
							   <div class="col-lg-3">
						    <div class="form-group ">
                              <label class="">select month 
                              </label>
                                 <div id="sandbox2">
						<input id="datepickermonth" type="text" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
				                           </div>
                           </div>
                           </div>
                           @endif
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">ticket : 
                              </label>
                                 <div id="sandbox2">
						                  <input  type="text" class="form-control" name="ticket" id="ticket" value="<?php echo(isset($ticket)?$ticket:'');?>">
				                     </div>
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">status :
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('status', [''=>'ALL','0' => 'OPEN','2'=>'IN PORGRESS','1'=>'CLOSED'], $status, ['class'=>'form-control','id'=>'status' ]) }}
                            
                              </div>
                           </div>
                           </div>
						  
						  
                     </div>
                     
					   <div class="col-lg-12 asignFace">
                  <h2>filter</h2>
               </div>
                     <div class="row asignbg">
					  <div class="col-lg-5">
                           <div class="form-group row">
                              <div  class="col-sm-5 filerl col-5 ">
                                 <label class="containerbut">earliest date
                                 <input type="radio" name="filter" value="feedback_submissions.created_at" checked="">
                                 <span class="checkmarkbut"></span>
                                 </label>
                                                      </div>
                                                         <div  class="col-sm-4 filerl col-4">
                                          <label class="containerbut">category
                                 <input type="radio" name="filter" value="feedback_submissions.fb_option" {{($filter=='feedback_submissions.fb_option')?'checked':''}}>
                                 <span class="checkmarkbut"></span>
                                 </label>
                                    
                                                            </div>
                                                      <div  class="col-sm-3 filerl col-3">
                                                      <label class="containerbut">status
                                 <input type="radio" name="filter" value="feedback_submissions.status" {{($filter=='feedback_submissions.status')?'checked':''}}>
                                 <span class="checkmarkbut"></span>
                                 </label>
                              </div>
                           </div>
					 </div>
					  </div>
                <div class="row">
                 <div class="col-sm-8"></div>
                 <div class="col-sm-4">
                 <div class="form-group row">
							
                   
                     <a href="{{ url('/opslogin/exportfeedback?option='.$option.'&ticket='.$ticket.'&unit='.$unit.'&month='.$month.'&status='.$status.'&filter='.$filter) }}"  class="submit float-right">print</a>
                     <a href="{{url('/opslogin/feedbacks/summary?view=summary')}}"  class="submit ml-2 mr-2 float-right ">clear</a>
                     <button type="submit" class="submit float-right">search</button>	
                  </div>
                     </div>
                     </div>
                  </form>

<div class="overflowscroll2">
                  <table class="gap">
                   
                     <thead>
                        <tr>
                           <th>ticket</th>
                           <th>submitted date</th>
                           <th>category</th>
                           <th>Block</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>status</th>
                           <th>updated on</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($feedbacks)

                       @foreach($feedbacks as $k => $feedback)
                        <tr>
                           <td class="roundleft">{{$feedback->ticket}} 
                           @if($feedback->view_status ==0)
                           &nbsp;<span class="badge badge-pill badge-danger text-white">New</span>
                           @endif</td>
                           <td class="spacer">{{date('d/m/y',strtotime($feedback->created_at))}}</td>
                           <td class="spacer">{{isset($feedback->getoption->feedback_option)?$feedback->getoption->feedback_option:''}}</td>
                            <td class="spacer">{{isset($feedback->getunit->buildinginfo->building)?$feedback->getunit->buildinginfo->building:''}}</td>
                           <td class="spacer">{{isset($feedback->getunit->unit)?Crypt::decryptString($feedback->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($feedback->user->name)?Crypt::decryptString($feedback->user->name):''}}</td>
                           
                           <td class="spacer">@php
                              if(isset($feedback->status)){
                              if($feedback->status==0)
                                 echo "OPEN";
                              else if($feedback->status==1)
                                 echo "CLOSED";
                              else
                                 echo "IN PROGRESS";
                              }
                              @endphp
                           </td>
                  <td class="spacer">{{date('d/m/y',strtotime($feedback->updated_at))}}</td>
                           <td class="roundright">
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                 @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/feedbacks/$feedback->id/edit")}}">Edit</a>
                           @endif
                         @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/feedbacks/delete/$feedback->id")}}');">Delete</a>
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
						@if ($feedbacks->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($feedbacks->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $feedbacks->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($feedbacks->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $feedbacks->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($feedbacks->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $feedbacks->lastPage()) as $i)
									@if($i >= $feedbacks->currentPage() - 2 && $i <= $feedbacks->currentPage() + 2)
										@if ($i == $feedbacks->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $feedbacks->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($feedbacks->currentPage() < $feedbacks->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($feedbacks->currentPage() < $feedbacks->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $feedbacks->appends($_GET)->url($feedbacks->lastPage()) }}">{{ $feedbacks->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($feedbacks->hasMorePages())
									<li><a href="{{ $feedbacks->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
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

@foreach($defactsByCategory as $d)
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


