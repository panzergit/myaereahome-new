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
   <h1>Digital Access - Call Unit Record</h1>
</div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($dooropen->view) && $dooropen->view==1 )
                        <li><a href="{{url('/opslogin/digitalaccess/dooropen#odr')}}">Normal / Face ID</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/remotedooropen')}}">Remote Records</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/bluetoothdooropen#odr')}}">Bluetooth Records</a></li>
                     @endif
                     @if(isset($doorfailed->view) && $doorfailed->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/dooropenfailed#odr')}}">Failed Records</a></li>
                     @endif
                     @if(isset($callunit->view) && $callunit->view==1 )
                        <li class="activeul" ><a href="{{url('/opslogin/digitalaccess/callunit#odr')}}">Call Unit</a></li>
                     @endif
                     @if(isset($qrcode->view) && $qrcode->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/qropenrecords#odr')}}">QR Code Records</a></li>
                     @endif
                  </ul>
               </div>
               </div>
<div class="">
<form action="{{url('/opslogin/digitalaccess/searchcallunit')}}" method="get" role="search" class="forunit">
                     <div class="">
                        <div class="row asignbg">
						<div class="col-lg-3">
                        <div class="form-group ">
                              <label class=""> device name : 
                              </label>
                              {{ Form::select('doorName', [''=>'Select']+$devices, $doorName, ['class'=>'form-control']) }}
						
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class=""> unit : 
                              </label>
                              <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">

                           </div>
                           </div>
						  
						  <div class="col-lg-6">
                           <div class="form-group row">
                              <label class="col-lg-12">
                              <label class="">open door date & time : 
                              
                              </label>
                              </label>
                              <div class="col-sm-6 col-6">
							  <label class="control-label">start date </label>
                             <div id="sandbox3">
						<input id="datetext1" type="text" class="form-control" name="startDate" value="<?php echo(isset($startDate)?$startDate:'');?>">
				                           </div>
                              </div>
							   <div class="col-sm-6 col-6">
							  <label class="control-label">end date </label>
                             <div id="sandbox4">
						<input id="datetext2" type="text" class="form-control" name="endDate" value="<?php echo(isset($endDate)?$endDate:'');?>">
				                           </div>
                              </div>
                           </div>
                        <div class="form-group row">
                              <div class="col-sm-6 col-6">
							  <label class="control-label">start time </label>
                          
						<input id="" type="time" class="form-control" name="startTime" value="<?php echo(isset($startTime)?$startTime:'');?>">
				                           
                              </div>
							   <div class="col-sm-6 col-6">
							  <label class="control-label">end time </label>
                             <div id="sandbox288">
						<input id="datetime" type="time" class="form-control" name="endTime" value="<?php echo(isset($endTime)?$endTime:'');?>">
				                           </div>
                              </div>
                           </div>
                           </div>
                             
                              <div class="col-lg-6"></div>
                              <div class="col-lg-6 mt0-2">
                              <a href="{{url("/opslogin/digitalaccess/callunit")}}"  class="submit ml-2 float-right">clear</a>
							 <button type="submit" class="submit  float-right">search</button>
                              </div>
							  
                     </div>
                  </form>
				   <div class="overflowscroll2">
                   <table class="gap">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/record/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                          
                           <th>date</th>
                           <th>time</th>
                           <th>block</th>
                           <th>unit</th>
                           <th>photo</th>
                           <th>device</th>
                           <th>device no.</th>
                           <!--<th>ACTIONS</th>-->
                        </tr>
                     </thead>
                     <tbody>
                      @if($records)

                       @foreach($records as $k => $record)
                       @php
                       $bt = new \App\Models\v7\CallPushRecord();
                       $rec = $bt->deviceDetail($record->devSn); 
 
                       //print_r($rec);
                       @endphp
                        <tr>
                           <td  class="roundleft">{{date('d/m/y',strtotime($record->created_at))}}</td>
                           <td  class="spacer">{{date('G:i A',strtotime($record->created_at))}}</td>
                           <td  class="spacer">{{($buildinglists[$record->roomCode])?$buildinglists[$record->roomCode]:''}}</td>

                           <td  class="spacer">{{($unitlists[$record->roomCode])?$unitlists[$record->roomCode]:''}}</td>
                           <td  class="spacer"> @if(isset($record->captureImage))

                                 <a href="{{substr($record->captureImage,0,-1)}}" target="_blank">
                                    <img src="{{substr($record->captureImage,0,-1)}}" class="viewimg phvert">
                                 </a>
                              @endif</td>
                              <td>{{isset($rec->device_name)?$rec->device_name:''}}</td>

                           <td  class="roundright">{{isset($record->devSn)?$record->devSn:''}}</td>

                          <!-- <td>
                          
                           @if(isset($permission) && $permission->delete==1)
                           <a href="#" onclick="delete_record('{{url("opslogin/record/delete/$record->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                           @endif
                           </td>-->
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


