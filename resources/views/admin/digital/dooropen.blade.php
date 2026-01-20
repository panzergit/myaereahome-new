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
   <h1>Digital Access - Open Door Record</h1>
</div>
 <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($dooropen->view) && $dooropen->view==1 )
                        <li class="activeul"><a href="{{url('/opslogin/digitalaccess/dooropen#odr')}}">Normal / Face ID</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/remotedooropen')}}">Remote Records</a></li>
                     @endif
                     @if(isset($bluetooth->view) && $bluetooth->view==1 )
                        <li   ><a href="{{url('/opslogin/digitalaccess/bluetoothdooropen#odr')}}">Bluetooth Records</a></li>
                     @endif
                     @if(isset($doorfailed->view) && $doorfailed->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/dooropenfailed#odr')}}">Failed Records</a></li>
                     @endif
                     @if(isset($callunit->view) && $callunit->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/callunit#odr')}}">Call Unit</a></li>
                     @endif
                     @if(isset($qrcode->view) && $qrcode->view==1 )
                        <li ><a href="{{url('/opslogin/digitalaccess/qropenrecords#odr')}}">QR Code Records</a></li>
                     @endif
                  </ul>
               </div>
               </div>
<div class="">
<form action="{{url('/opslogin/digitalaccess/searchdooropen')}}" class="forunit" autocomplete="off">
                     <div class="">
                        <div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group ">
                              <label class=""> name :
                              </label>
                              <input id="" type="text" class="form-control" name="empName" value="<?php echo(isset($empName)?$empName:'');?>">
						
                           </div>
                           </div>
                        <div class="col-lg-3">
                           <div class="form-group ">
                              <label class=""> device name :
                              </label>
                              {{ Form::select('doorName', [''=>'Select']+$devices, $doorName, ['class'=>'form-control']) }}
						
                           </div>
                           </div>
						   <div class="col-lg-3">
						    <div class="form-group ">
                              <label class="">open door type :
                              </label>
                             <div id="sandbox2">
                             {{ Form::select('eventType', [''=>'Select',8=>'Remote Door Opening',19=>'Bluetooth Door Opening',21=>'Face Recognition Door Opening'], $eventType, ['class'=>'form-control']) }}
						
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
							  <label class="control-label">Start time </label>
                          
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
                        <div class="col-lg-6">
						</div>
                        <div class="col-lg-6">
						    <div class="form-group mt0-2">
                             
                              <a href="{{url("/opslogin/digitalaccess/dooropen")}}"  class="submit ml-2  float-right">clear</a>
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
                           <th>device name</th>
                           <th>device s.no</th>
                           <th>person name</th>
                           <th>open door type</th>
                           <th>photo captured</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($records)

                       @foreach($records as $k => $list)
                        <tr>
                           <td  class="roundleft">{{date('d/m/y',strtotime($list['eventTime']))}}</td>
                           <td  class="spacer">{{date('G:i A',strtotime($list['eventTime']))}}</td>
                           <td  class="spacer">{{isset($list['devName'])?$list['devName']:''}}</td>
                           <td  class="spacer">{{isset($list['devSn'])?"(".$list['devSn'].")":''}}</td>
                           <td  class="spacer">{{isset($list['empName'])?$list['empName']:''}}</td>
                           <td  class="spacer">
                              @php
                                 if($list['eventType'] ==19)
                                    echo "Bluetooth Door Opening";
                                 else if($list['eventType'] ==21)
                                    echo "Face Recognition Door Opening";
                                 else if($list['eventType'] ==8)
                                    echo "Remote Door Opening";
                                 else if($list['eventType'] ==23)
                                    echo "QR code Door Opening";
                                 else
                                    echo "Exit Button Door Opening";
                              @endphp
                           </td>
                           <td class="roundright noimg">
                              @if(isset($list['captureImage']))
                                 <a data-toggle="modal" data-target="#example{{$k}}" data-id="{{$list['captureImage']}}" class="open-dialog-digital">
                                    <img src="{{url('assets/admin/img/scenery.png')}}" class="viewimg phvert">
                                 </a>
                              @endif
						         </td>

                           <!--<td  class="roundright"> @if(isset($list['captureImage']))
                                 <a href="{{$list['captureImage']}}" target="_blank">
                                    <img src="{{$list['captureImage']}}" class="viewimg phvert">
                                 </a>
                              @endif</td> -->
                           
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  </div>
               </div>
@if($records)
@foreach($records as $k => $list)
<div class="modal" id="example{{$k}}">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">photo captured</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 @if(isset($list['captureImage']))
          <img src="{{$list['captureImage']}}" width="100%">
	     @endif
        </div>
      </div>
    </div>
</div>
@endforeach
@endif 
@endsection


