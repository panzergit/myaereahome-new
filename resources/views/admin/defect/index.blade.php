@extends('layouts.adminnew')
@section('content')
@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(3,$permission->role_id); 
@endphp
<style>
.view_detail{
    cursor:pointer ;
}
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
   .table1 thead th {
        vertical-align: text-top;
    }
    .usertable td span{
        color: rgb(255 255 255)!important;
        -webkit-text-fill-color: rgb(255 255 255)!important;
    }
	.Contractindex a{    color: #212529;} 
	.contwidth32{}
	.contwidth32 .containers {
    width: 115px;
    display: table-cell;
}
	.contwidth32 input{}
	.contwidth32 span{}
	.contwidth32 b {
    position: absolute;
    background: #e70b0b;
    right: 5px;
    top: -10px;
    color: #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    font-size: 12px;
}
.containers input:checked~.checkmark {
    border-radius: 8px;
}
.containers input:checked~.checkmark {
    background-color: #fff;
    border: 2px solid gray;
    font-weight: 600;
    border-radius: 8px;
}
.checkmark {
    border-radius: 8px;
}
.containers input:checked~.checkmark {
    background-color: #fff;
    border: 2px solid gray;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px;
    font-size: 14px;
}
.containers {
    margin-bottom: 0px!important;
    padding-top: 0px!important;
}

.view_detail a{
    color: #5D5D5D!important;
}
</style>
<div class="status">
  <h1>new defects list / joint inspection </h1>
</div>

  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li class="@if(!request()->has('view')) activeul @endif"><a href="{{url('/opslogin/defects')}}">Dashboard</a></li>
                     <li class="@if(request()->has('view') && request()->view=='summary') activeul @endif"><a href="{{url('/opslogin/defects').'?view=summary'}}">Summary</a></li>
                  </ul>
               </div>
               </div>
<div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

@if(!request()->has('view') )
<form method="get" role="search" class="forunit">
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
                <label class="">Units</label>
                <input  type="text" name="units" class="form-control" value="<?php echo(request()->has('units')?request()->units:'');?>" id="unit_list">

                <!--<select class="form-control" name="units">
                    <option value="">--select--</option>
                    @foreach($units as $u)
                    <option @if(request()->has('units') && trim(request()->units)!='' && request()->units==$u->id) selected="selected" @endif value="{{$u->id}}">{{$u->unit}}</option>
                    @endforeach
                </select>-->
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="">Locations</label>
                <select class="form-control" name="locations" id="location" onchange='gettypes()'>
                    <option value="">--select--</option>
                    @foreach($locations as $l)
                    <option @if(request()->has('locations') && trim(request()->locations)!='' && request()->locations==$l->id) selected="selected" @endif value="{{$l->id}}">{{$l->defect_location}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="">Types</label>
                <select class="form-control" name="types" id="types">
                    <option value="">--select--</option>
                    @if(!empty($types))
                        @foreach($types as $type)
                        <option @if(request()->has('types') && trim(request()->types)!='' && request()->types==$type->id) selected="selected" @endif value="{{$type->id}}">{{$type->defect_type}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <!--<div class="col-lg-3">
           <div class="form-group ">
                <label class="">status: </label>
                @php
                if(request()->has('status') && trim(request()->status)!='') $status=request()->status;
                @endphp
                {{ Form::select('status', $defectStatus, $status, ['class'=>'form-control']) }}
            </div>
        </div>-->
	 
    </div>
	            
					  
        <div class="col-lg-12">
            <div class="form-group mt0-4">
                <input type="hidden" name="search_for" value="dashboard" />
                <a href="{{url("/opslogin/defects")}}"  class="submit mr-2 ml-2 float-right">clear</a>
                <input type="hidden" name="temp_type" id="temp_type" value="">

                <button type="submit" class="submit float-right">search</button>
			</div>  
		</div> 
</form>
@php 
$location ='';
$type ='';
$unit ='';
$fromdate = date('Y')."-01-01";
$todate = date('Y-m-d H:i:s');

if(request()->has('locations') && trim(request()->locations)!='')
    $location = request()->locations;
if(request()->has('types') && trim(request()->types)!='')
    $types = request()->types;
if(request()->has('year') && $i==date('Y'))
    $fromdate = request()->has('year')."-01-01";
if(request()->has('units') && trim(request()->units)!='')
    $unit = request()->units;
@endphp
<div class="clearfix"></div>
<div class="row Contractindex">
   <div class="col-lg-2 col-3">
     <a href='{{url("/opslogin/defects/search?view=summary&locations=$location&unit=$unit&types=$type&fromdate=$fromdate&todate=$todate")}}'>
      <div class="Contractbox">
         <h5>Total Tickets</h5>
         <p>{{$totalDefects}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href='{{url("/opslogin/defects/search?view=summary&status=0&locations=$location&unit=$unit&types=$type&fromdate=$fromdate&todate=$todate")}}'>
      <div class="Contractbox">
         <h5>Total Tickets (New)</h5>
         <p>{{$totalNewDefects}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/defects/search?view=summary&status=3&locations=$location&unit=$unit&types=$type&fromdate=$fromdate&todate=$todate")}}">
      <div class="Contractbox">
         <h5>Total Tickets (Scheduled)</h5>
         <p>{{$totalScheduledDefects}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/defects/search?view=summary&status=4&locations=$location&unit=$unit&types=$type&fromdate=$fromdate&todate=$todate")}}">
      <div class="Contractbox">
         <h5>Total Tickets (In Progress)</h5>
         <p>{{$totalInprogressDefects}}</p>
      </div>
	  </a>
   </div>
   <div class="col-lg-2 col-3">
      <a href="{{url("/opslogin/defects/search?view=summary&status=1&locations=$location&unit=$unit&types=$type&fromdate=$fromdate&todate=$todate")}}">
      <div class="Contractbox">
         <h5>Total Tickets (Closed)</h5>
         <p>{{$totalCompletedDefects}}</p>
      </div>
	  </a>
   </div>
</div>

<div class="row">
   <div class="col-lg-6 countp monthbg pb-3">
      <h2 class="text-center">Defects by Month</h2>
      <div id="chartContainer01" style="height: 370px; width: 100%;">
      </div>
      <!-- <div class="removewatermark01"></div> -->
   </div>
   <div class="col-lg-6 monthbg pb-3">
      <h2 class="text-center">Defects by Location</h2>
      <div id="chartContainer02" style="height: 370px; width: 100%;">
      </div>
      <div class="removewatermark"></div>
   </div>
</div>


@endif

@if(request()->has('view') && request()->view=='summary')
<form action="{{url('/opslogin/defects/search')}}" method="get" role="search" class="forunit">
                     <div class="row asignbg">
					  <div class="col-lg-3">
                        <div class="form-group">
                              <label class="">ticket: 
                              </label>
                                 <input  type="text" class="form-control" name="ticket" id="ticket" value="<?php echo(isset($ticket)?$ticket:'');?>">
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">unit: 
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
                           </div>
						   <div class="col-lg-3">
						    <div class="form-group">
                              <label class="">name: 
                              </label>
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                              
                           </div>
                           </div>
						   <div class="col-lg-3">
                            <div class="form-group">
                                <label class="">Locations</label>
                                <select class="form-control" name="locations" id="location" onchange='gettypes()'>
                                    <option value="">--select--</option>
                                    @foreach($locations as $l)
                                    <option @if(request()->has('locations') && trim(request()->locations)!='' && request()->locations==$l->id) selected="selected" @endif value="{{$l->id}}">{{$l->defect_location}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="">Types</label>
                                <select class="form-control" name="types" id="types">
                                    <option value="">--select--</option>
                                    @if(!empty($types))
                                        @foreach($types as $type)
                                        <option @if(request()->has('types') && trim(request()->types)!='' && request()->types==$type->id) selected="selected" @endif value="{{$type->id}}">{{$type->defect_type}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
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
                           <div class="form-group ">
                              <label class="">status: 
                              </label>
                                  {{ Form::select('status', $defectStatus, $status, ['class'=>'form-control','id'=>'role']) }}
                             
                         </div>
                         </div>
                         <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">Rectification in Days: 
                              </label>
                                  {{ Form::select('indays',  [''=>'--ALL--','7' => '7 Days and Above','14'=>'14 Days and Above','21' => '21 Days and Above','28'=>'28 Days and Above'], $indays, ['class'=>'form-control','id'=>'role']) }}
                             
                         </div>
                         </div>

                        
                     </div>
					  <div class="col-lg-12 asignFace">
                  <h2>filter Block</h2>
                 </div>
			     <div class="row asignbg">
				 <div class="row ml-2">
                          <div class="col-lg-12">              
                          <div class="contwidth32">
						  <label class="containers " >
                           <input type="radio" value="all" name="block" @if((!request()->has('block')) || (request()->has('block') && request()->block=='all')) checked="checked" @endif>
                           <span class="checkmark">All</span>
                           </label>
                           @foreach($blocks as $block)
						   <label class="containers ">
                                <input type="radio" value="{{$block['id']}}" @if(request()->has('block') && request()->block==$block['id']) checked="checked" @endif name="block">
                                <span class="checkmark">{{$block['building']}}</span>
						        <b>{{$block['defects'] ?? 0}}</b>
                           </label>
                           @endforeach
						   </div>
						   </div>
                </div>
		        </div>
				<div class="col-lg-12 mb-5 pb-2">
                         <div class="form-group mt0-4 ">
                         <a href="{{ url('/opslogin/exportdefects?option='.$option.'&ticket='.$ticket.'&unit='.$unit.'&name='.$name.'&status='.$status) }}" class="submit float-right">print</a>
                         <a href="{{url("/opslogin/defects?view=summary")}}"  class="submit mr-2 ml-2 float-right">clear</a>
                         <input type="hidden" name="view" value="summary">
                         <input type="hidden" name="temp_type" id="temp_type" value="">
                         <button type="submit" class="submit  float-right">search</button>
							  </div>  
							  </div>  
                  </form>

  <div class="overflowscroll2">
                 <table class="gap">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/defect/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>status</th>
                           <th style="width:100px">block</th>
                           <th style="width:100px">unit no</th>
                           <th>submitted <br>date</th>
                           <th>rectification <br>in day(s)</th>
                           <th style="width:100px">appt<br> date & time</th>
                           <th>appt <br>status</th>
                           <th>completion<br> date</th>
                           <th>reference <br> id</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($defects)

                       @foreach($defects as $k => $defect)
                            @php
                                //$Date = \Carbon\Carbon::parse($defect->inspection_owner_timestamp);
                                $inspectionDate = ($defect->inspection_owner_timestamp !='')?date("Y-m-d",strtotime($defect->inspection_owner_timestamp)):'';
                                $givenDate = \Carbon\Carbon::parse($inspectionDate);
                                $days = $givenDate->diffInDays(\Carbon\Carbon::now());
                                $color = '#5D5D5D';
                                switch($days)
                                {
                                    case ($days>=7 && $days<14):
                                        $color = '#ff9966';
                                        break;
                                    case ($days>=14 && $days<21):
                                        $color = '#CC0000';
                                        break;
                                    case ($days>=21 && $days<28):
                                        $color = '#ff0000';
                                        break;
                                    case ($days>=28):
                                        $color = '#800000';
                                        break;
                                    default:
                                        $color='#5D5D5D';
                                }
                            @endphp
                        <tr data-defect-id="{{$defect->id}}">
                           <td class='roundleft view_detail'>{{$defect->ticket}}@if($defect->view_status ==0)&nbsp;<span class="badge badge-pill badge-danger text-white">New</span>@endif</td>
                           <td class='spacer view_detail'>
                           @php
                              if(isset($defect->status)){
                              if($defect->status==0)
                                 echo "OPEN";
                              else if($defect->status==1)
                                 echo "CLOSED";
                              else if($defect->status==3)
                                 echo "ON SCHEDULE";
                              else if($defect->status==5)
                                 echo "COMPLETED-PENDING RESIDENT UPDATE";
                              else if($defect->status==6)
                                 echo "COMPLETED-FINAL INSPECTION SCHEDULED";
                              else
                                 echo "IN PROGRESS";
                              }
                          
                                
                            @endphp
                           </td>
                            <td class='spacer view_detail'>
                           <a href="#"   data-toggle="tooltip" data-placement="top" title="{{isset($defect->user->name)?Crypt::decryptString($defect->user->name):''}}" data-original-title="{{isset($defect->user->name)?Crypt::decryptString($defect->user->name):''}}" >{{isset($defect->getbuilding->building)?$defect->getbuilding->building:''}}
                           </a></td>
                           <td class='spacer view_detail'>
                           <a href="#"   data-toggle="tooltip" data-placement="top" title="{{isset($defect->user->name)?Crypt::decryptString($defect->user->name):''}}" data-original-title="{{isset($defect->user->name)?Crypt::decryptString($defect->user->name):''}}" >{{isset($defect->getunit->unit)?Crypt::decryptString($defect->getunit->unit):''}}
                           </a></td>
                          
                           <td class='spacer view_detail'>{{date('d/m/y',strtotime($defect->created_at))}}</td>
                           <td class='spacer view_detail'>
                                @if($days > 0)
                                    <a href="#" style="color:{{$color}}"   data-toggle="tooltip" data-placement="top" title="{{date('d/m/y',strtotime($defect->inspection_owner_timestamp))}}" data-original-title="{{date('d/m/y',strtotime($defect->inspection_owner_timestamp))}}" >
                                    {{$days.' '.($days>1 ? 'Days' : 'Day')}}
                                    </a>
                                @endif
                            </td>
                           <td class='spacer view_detail'>{{isset($defect->inspection->appt_date)?date('d/m/y',strtotime($defect->inspection->appt_date)):''}} {{isset($defect->inspection->appt_time)?$defect->inspection->appt_time:''}}</td>
                           
                           <td class='spacer view_detail'>@php 
                           if(isset($defect->inspection->status) && $defect->inspection->status==0)
                                    echo "New";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==1)
                                    echo "Cancelled";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==2)
                                    echo "On Schedule";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==3)
                                    echo "Done";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==4)
                                    echo "In Progress";
                           @endphp
                           </td>
                           <td class="spacer view_detail">{{($defect->completion_date !='0000-00-00')?date('d/m/y',strtotime($defect->completion_date)):''}}</td>
                           <td class="view_detail">{{$defect->ref_id}}</td>
                           
                            <td class='roundright'>
						        <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                        <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ url('opslogin/defects/'.$defect->id)}}" >Defect Detail</a>
                                        <a class="dropdown-item" href="{{$visitor_app_url}}/generate-pdf/{{$defect->id}}" target="_blank">View PDF</a>
                                    @if($defect->status!=1)
                                        @if(isset($permission) && $permission->edit==1)
                                        <a class="dropdown-item" href="{{url("opslogin/defects/list/$defect->id")}} ">Defect Update</a>
                                        @if($defect->handover_status ==0)
                                        <a class="dropdown-item" href="{{url("opslogin/defects/$defect->id/edit")}} ">Join Inspection</a>
                                        @endif
                                        @if($defect->handover_status ==1 || $defect->handover_status ==2)
                                        <a class="dropdown-item" href="{{url("opslogin/defects/handover/$defect->id")}}">Hand Over</a>
                                        @endif
                                        @if(in_array($defect->status,[6]))
                                        <a class="dropdown-item" href="{{url("opslogin/defects/final-inspection/$defect->id")}} ">Final Inspection</a>
                                        @endif
                                        @endif
                                        @if(isset($permission) && $permission->delete==1)
                                        <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/defects/delete/$defect->id")}}');" >Delete</a>
                                        @endif
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
						@if ($defects->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($defects->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $defects->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($defects->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $defects->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($defects->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $defects->lastPage()) as $i)
									@if($i >= $defects->currentPage() - 2 && $i <= $defects->currentPage() + 2)
										@if ($i == $defects->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $defects->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($defects->currentPage() < $defects->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($defects->currentPage() < $defects->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $defects->appends($_GET)->url($defects->lastPage()) }}">{{ $defects->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($defects->hasMorePages())
									<li><a href="{{ $defects->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
				@endif
               </div>
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
@foreach($dates as $d)
    ydates.push({
        x : new Date("{{ explode('-',$d['date'])[0] }}", "{{ explode('-',$d['date'])[1] }}", "{{ explode('-',$d['date'])[2] }}"),
        y : parseInt("{{ $d['defects'] }}"),
        indexLabel : "{{ date('M Y',strtotime($d['date'])) }}"
    });
@endforeach

@foreach($defactsByLocations as $d)
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