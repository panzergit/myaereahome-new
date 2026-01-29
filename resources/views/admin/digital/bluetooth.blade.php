@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $dooropen =  $permission->check_menu_permission(56,$permission->role_id,1);
   $bluetooth =  $permission->check_menu_permission(69,$permission->role_id,1);
   $callunit =  $permission->check_menu_permission(67,$permission->role_id,1);
   $doorfailed =  $permission->check_menu_permission(66,$permission->role_id,1);
   $qrcode =  $permission->check_menu_permission(68,$permission->role_id,1);
   $permission = $permission->check_permission(23,$permission->role_id); 
@endphp

<div class="status">
   <h1>Bluetooth - Open Door Record</h1>
</div>
 <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($dooropen->view) && $dooropen->view==1 )
                        <li><a href="{{url('/opslogin/digitalaccess/dooropen')}}">Normal / Face ID</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li   ><a href="{{url('/opslogin/digitalaccess/remotedooropen')}}">Remote Records</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li class="activeul"><a href="{{url('/opslogin/digitalaccess/bluetoothdooropen')}}">Bluetooth Records</a></li>
                     @endif
                     @if(isset($doorfailed->view) && $doorfailed->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/dooropenfailed')}}">Failed Records</a></li>
                     @endif
                     @if(isset($callunit->view) && $callunit->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/callunit')}}">Call Unit</a></li>
                     @endif
                     @if(isset($qrcode->view) && $qrcode->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/qropenrecords')}}">QR Code Records</a></li>
                     @endif
                  </ul>
               </div>
               </div>
<div class="">
<form action="{{url('/opslogin/digitalaccess/bluetoothdooropen')}}" method="get" role="search" class="forunit">
                     <div class="">
                        <div class="row asignbg">
						<div class="col-lg-6">
						 <div class="row">
						 <label class="col-lg-12">
                              <label class="">&nbsp;
                              </label>
                              </label>
						 <div class="col-lg-6">
                        <div class="form-group">
                            <input type="hidden" name="search" value="1" />
                              <label class=""> device name : 
                              </label>
                                {{ Form::select('doorName', [''=>'--select--']+$devices, (request()->has('doorName') ? request()->doorName : ''), ['class'=>'form-control']) }}
                           </div>
                           </div>
						    <div class="col-lg-6">
                           <div class="form-group ">
                              <label class=""> unit : 
                              </label>
                              <select class="form-control" name="unit">
                                  <option value="">--select--</option>
                                  @foreach($searchUnits as $u)
                                  <option @if(request()->has('unit') && request()->unit==$u['id']) selected @endif value="{{$u['id']}}">{{$u['name']}}</option>
                                  @endforeach
                              </select>
                           </div>
                           </div>
						    <div class="col-lg-6">
						    <div class="form-group ">
                              <label class="">name :
                              </label>
                              <input  type="text" class="form-control" name="name" id="name" value="{{ request()->has('name') ? request()->name : '' }}">
                            
                           </div>
                           </div>
						    <div class="col-lg-6">
                           <div class="form-group ">
                              <label class=""> Building : 
                              </label>
                              <select class="form-control" name="building" id='building' onchange='getbuldunits()'>
                                  <option value="">--select--</option>
                                   @foreach($buildings as $b)
                                  <option @if(request()->has('building') && request()->building==$b['id']) selected @endif value="{{$b['id']}}">{{$b['building']}}</option>
                                  @endforeach
                              </select>
                             
                           </div>
                           </div>
                           </div>
                           </div>
						   <div class="col-lg-6">
                           <div class="form-group row">
                              <div class="col-sm-12">
                              <label class="">open door date & time : 
                              
                              </label>
                              </div>
                              <div class="col-sm-6 col-6">
							  <label class="control-label">start date </label>
                             <div id="sandbox3">
						<input id="datetext1" type="text" class="form-control" name="startDate" value="{{ request()->has('startDate') ? request()->startDate : '' }}">
				                           </div>
                              </div>
							   <div class="col-sm-6 col-6">
							  <label class="control-label">end date </label>
                             <div id="sandbox4">
						<input id="datetext2" type="text" class="form-control" name="endDate" value="{{ request()->has('endDate') ? request()->endDate : '' }}">
				                           </div>
                              </div>
                           </div>
                        <div class="form-group row">
                              
                              <div class="col-sm-6 col-6">
							  <label class="control-label">start time </label>
                          
						<input id="" type="time" class="form-control" name="startTime" value="{{ request()->has('startTime') ? request()->startTime : '' }}">
				                           
                              </div>
							   <div class="col-sm-6 col-6">
							  <label class="control-label">end time </label>
                             <div id="sandbox288">
						<input id="datetime" type="time" class="form-control" name="endTime" value="{{ request()->has('endTime') ? request()->endTime : '' }}">
				                           </div>
                              </div>
                           </div>
                           </div>
						    <div class="col-lg-8"></div>
						    <div class="col-lg-4">
						    <div class="form-group mt0-2">
                              <a href="{{url("/opslogin/digitalaccess/bluetoothdooropen")}}"  class="submit ml-2 float-right">clear</a>
							 <button type="submit" class="submit  float-right">search</button>
							  
                           </div>
                           </div>
                     </div>
                  </form>
				   <div class="overflowscroll2">
                  <table class="gap">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/faceid/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                           <th>open door date</th>
                           <th>time</th>
                           <th>person name</th>
                           <th>block</th>
                           <th>unit</th>
                           <th>device name</th>
                           <th>device no.</th>
                           <th>action</th>
                           <th>status</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($records)

                       @foreach($records as $k => $list)
                       @php
                       $bt = new \App\Models\v7\BluetoothDoorOpen();
                       $rec = $bt->deviceDetail($list->devSn);  
                      
                       @endphp
                        <tr>
                           <td class="roundleft">{{date('d/m/y',strtotime($list->call_date_time))}}</td>
                           <td class="spacer">{{date('G:i A',strtotime($list->call_date_time))}}</td>
                           <td class="spacer">{{isset($list->user->name)?Crypt::decryptString($list->user->name):''}}</td>
                           <td class="spacer">{{isset($list->user->getunit->buildinginfo->building)?$list->user->getunit->buildinginfo->building:''}}</td>
                           <td class="spacer">{{isset($list->user->getunit->unit)?"#".Crypt::decryptString($list->user->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($list->devName)?$list->devName:''}}</td>
                           <td class="spacer">{{isset($list->devSn)?$list->devSn:''}}</td>
                           <td class="spacer">{{($list->action_type==1)?'Proximity':'Manual'}}</td>
                           <td class="roundright">{{($list->status==1)?"Success":'Fail'}}</td>
                           
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($records->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($records->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $records->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($records->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $records->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($records->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $records->lastPage()) as $i)
									@if($i >= $records->currentPage() - 2 && $i <= $records->currentPage() + 2)
										@if ($i == $records->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $records->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($records->currentPage() < $records->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($records->currentPage() < $records->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $records->appends($_GET)->url($records->lastPage()) }}">{{ $records->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($records->hasMorePages())
									<li><a href="{{ $records->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
               </div>
@endsection


