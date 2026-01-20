@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(2,$permission->role_id); 
   //print_r($permission);
@endphp

<style>
    @if(!request()->has('view'))
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
    @endif    
</style>
<div class="status">
  <h1>Appointment For Key Collection</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li @if(!request()->has('view')) class="activeul" @endif><a href="{{url('/opslogin/takeover_appt/lists')}}">Dashboard</a></li>
                     <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/takeover_appt/lists')}}?view=summary">Summary</a></li>
                     <li @if(request()->has('view') && request()->view=='new') class="activeul" @endif><a href="{{url('/opslogin/takeover_appt')}}?view=new">New appointments</a></li>
                  </ul>
               </div>
               </div>

    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

@if(!request()->has('view'))
    <div>
        <div class="row">
            <div class="col-lg-6 monthbg pb-3">
      <h2 class="text-center">Appointments by Status(%)</h2>
      <div id="chartContainer02" style="height: 370px; width: 100%;">
      </div>
      <div class="removewatermark"></div>
   </div>
   <div class="col-lg-6 countp monthbg pb-3 d-none">
      <h2 class="text-center">Appointments by Status(Numbers)</h2>
      <div id="chartContainer01" style="height: 370px; width: 100%;">
      </div>
   </div>
   <div class="col-lg-6 countp monthbg pb-3">
      <h2 class="text-center">Key Collections by Status</h2>
      <div id="chartContainer03" style="height: 370px; width: 100%;">
      </div>
   </div>
   
</div>
    </div>
@endif

@if(request()->has('view') && request()->view=='summary')
  <div class="">
   <form action="{{url('/opslogin/takeover_appt/search')}}" method="get" role="search" class="forunit">
         <input type="hidden" value="summary" name="view" >  
                     <div class="row asignbg">
                        <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">unit:
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
						   </div>
                           @if(1==2)
							      <div class="col-lg-2">
                           <div class="form-group">
                              <label class="">name: 
                              </label>
                                 <input  type="text" class="form-control" name="name" value="<?php echo(isset($name)?$name:'');?>">
                           </div>
                           </div>
                           @endif
						      <div class="col-lg-2">
                           <div class="form-group ">
                              <label class=""> month 
                              </label>
                                 <div id="sandbox2">
						<input id="datepickermonth" type="text" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
				                           </div>
                           </div>
                           </div>
   <div class="col-lg-2">
                           <div class="form-group">
                              <label class="">status: 
                              </label>
                                  {{ Form::select('status', ['' => '--All Status--','1'=>'Cancelled','2'=>'On Schedule','3'=>"Done"], $status, ['class'=>'form-control','id'=>'status']) }}
                           </div>
                           </div>
                        
                        <div class="col-lg-6">
                           <div class="form-group mt0-4">
                           
                             
							     <a href="{{ url('/opslogin/exporttakeover?option='.$option.'&month='.$month.'&unit='.$unit.'&name='.$name.'&status='.$status) }}" class="submit  float-right">print</a>
                          <a href="{{url('/opslogin/takeover_appt/lists')}}?view=summary"  class="submit ml-2 mr-2 float-right">clear</a>
                          <button type="submit" class="submit  float-right">search</button>
                        </div>
                           <!--div class="form-group row">
                           <div class="col-12">
                             
                           </div></div>
                           <div class="form-group row">
                              <div class="col-12">
                             
                           </div></div-->
                        </div>
                      

                     </div>
                  </form>
                  <div>


 <div class="overflowscroll2">
                 <table class="gap">
                     <thead>
                        <tr>
                           <th>unit no</th>
                           <th>booked by</th>
                           <th>appointment date</th>
                           <th>appointment  time</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($units)

                        @foreach($units as $k => $appt)
                        <tr>
                           <td class="roundleft">{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($appt->getname->name)?Crypt::decryptString($appt->getname->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($appt->appt_date))}}</td>
                           <td class="spacer">{{$appt->appt_time}}</td>
                           <td class="spacer">@php
                  if(isset($appt->status)){
                    if($appt->status==0)
                      echo "New";
                    else  if($appt->status==1)
                      echo "Cancelled";
                     else  if($appt->status==2)
                      echo "On Schedule";
                       else  if($appt->status==3)
                      echo "Done";
                  }
                  @endphp</td>

                           <td class="roundright">
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/takeover_appt/$appt->id/edit")}}"  data-toggle="tooltip" data-placement="top" title="Edit"><img src="{{url('assets/admin/img/edit.png')}}" class="viewimg phvert"></a>
                           @endif
                           </td>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-10">
					<div  class="form-group row">
						@if ($units->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($units->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $units->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($units->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $units->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($units->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $units->lastPage()) as $i)
									@if($i >= $units->currentPage() - 2 && $i <= $units->currentPage() + 2)
										@if ($i == $units->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $units->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($units->currentPage() < $units->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($units->currentPage() < $units->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $units->appends($_GET)->url($units->lastPage()) }}">{{ $units->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($units->hasMorePages())
									<li><a href="{{ $units->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

                </div>
@endif
@if(request()->has('view') && request()->view=='new')

@endif
@endsection

@section('customJS')
<script>
    
    $(document).ready(function(e){
        $('.view_detail').on('click',function(e){
            let defectId = $(this).parent('tr').data('defect-id');
            window.location.href = "{{ url('opslogin/defects').'/' }}"+defectId;
        });
    });
    
    @if(!request()->has('view'))
    let ydates = [];
    let defLocs = [];
    let chartThreeData = [];
@foreach($app_by_status_count as $d)
    ydates.push({
        x : parseInt("{{ $d['value'] }}"),
        y : parseInt("{{ $d['value'] }}"),
        indexLabel : "{{ $d['name'] }}"
    });
@endforeach

@foreach($app_by_status as $d)
    defLocs.push({
        exploded: true,
        y : ("{{ $d['value'] }}"),
        name : "{{ $d['name'] }}"
    });
@endforeach

@foreach($chartThree as $d)
    chartThreeData.push({
        y : parseInt("{{ $d['y'] }}"),
        label : "{{ $d['label'] }}"
    });
@endforeach
console.log(chartThreeData)

window.onload = function () {
    CanvasJS.addColorSet("greenShades",
                         [//colorSet Array
         
                         "#1A2E67",            
                         ]);
         var chart01 = new CanvasJS.Chart("chartContainer01", {
         	animationEnabled: true,
         	colorSet: "greenShades",
            // axisX: {
            //     interval: 1,
            //     intervalType: "month"
            // },
        //  	axisY: {
        //         gridThickness: 1,
        //     },
        //     axisY2: {
        //         suffix: "%",
        //         gridThickness: 0,
        //     },	
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
                    name: "Total Appoinments",
                    showInLegend: true,
                    dataPoints: ydates
                }
            ]
         });
         //chart01.render();
         
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
         
         // chart 3
         var chartThree = new CanvasJS.Chart("chartContainer03", {
        	animationEnabled: true,
        	axisX: {
        		interval: 1
        	},
        	axisY2: {
        		interlacedColor: "rgba(1,77,101,.2)",
        		gridColor: "rgba(1,77,101,.1)",
        	},
        	data: [{
        		type: "bar",
        		name: "companies",
        		color: "#014D65",
        		axisYType: "secondary",
        		dataPoints: chartThreeData
        	}]
        });
    chartThree.render();
          }
    @endif
</script>
@endsection