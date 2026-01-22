@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(48,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.chkstatus{    color: #5D5D5D;
    font-weight: 700;
    font-size: 14px;
    text-decoration: underline;}
	
.loading-dots {
  display: inline-block;
font-size: 18px;
  animation: dots 2.4s infinite;
  opacity: 0;
}

.loading-dots:nth-child(1) {
  animation-delay: 0.2s; color: #5D5D5D;     font-size: 18px;
    margin-right: 8px;
}

.loading-dots:nth-child(2) {
  animation-delay: 0.4s; color: #5D5D5D;     font-size: 18px;
    margin-right: 8px;
}

.loading-dots:nth-child(3) {
  animation-delay: 0.8s; color: #5D5D5D;    font-size: 18px;
    margin-right: 8px;
} 

@keyframes dots {
  30% {
    -webkit-transform: translateY(-5px) scale(1.9);
    transform: translateY(-5px) scale(1.9);
    opacity: 1;
    text-shadow: 0 15px 10px black;
  }
  48% {
    -webkit-transform: scale(1);
    transform: scale(1);
  }
  100% {
    -webkit-transform: translateY(0);
    transform: translateY(0);
    opacity: 0;
  }
}
</style>
<div class="status">
  <h1>device management</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/device')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/device/create')}}">Add device</a></li>
                     @endif
                  </ul>
               </div>
               </div>
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<form action="{{url('/opslogin/device/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-2">
                        <div class="form-group ">
                              <label>device name: 
                             
                              </label>
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                            
                           </div>
						    </div>
							  <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">device serial no: 
                              
                              </label>
                                 <input  type="text" name="serial_no" class="form-control" value="<?php echo(isset($serial_no)?$serial_no:'');?>" id="unit_list">
                           </div>
                           </div>
                             <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">status: 
                              
                              </label>
                                  {{ Form::select('status', ['' => 'All','1' => 'Active','0'=>'Inactive'], $status, ['class'=>'form-control']) }}
                           </div>
                           </div>
                       
                        <div class="col-lg-6">
                           <div class="form-group mt0-4">
                              
							
							   <a href="{{ url('/opslogin/exportdevice?option='.$option.'&name='.$name.'&serial_no='.$serial_no.'&status='.$status) }}" class="submit  float-right">print</a>
                        <a href="{{url("/opslogin/device")}}"  class="submit ml-2 mr-2 float-right">clear</a>
                        <button type="submit" class="submit  float-right">search</button>
                     </div>
                           <!--div class="form-group row">
                           <div class="col-sm-12">
                               
                           </div> </div>
                           <div class="form-group row">
                           <div class="col-sm-12">
                             
                           </div> </div-->
                        </div>
                      

                     </div>
                  </form>
				   <div class="overflowscroll2">
                  <table class="gap ">
                
                     <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>device name</th>
                           <th>serial no</th>
                           <!--<th>model</th>-->
                           <th>location</th>
                           <th>proximity settings</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($devices)

                       @foreach($devices as $k => $dept)
                        @php
                           $deviceClsObj = new \App\Models\v7\Device();
                           $locations = $deviceClsObj->getLocations($dept->locations); 
                        @endphp
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td class="spacer">{{$dept->device_name}}</td>
                           <td class="spacer">{{$dept->device_serial_no}}</td>
                           <!--<td>{{isset($result['deviceModelName'])?$result['deviceModelName']:""}}</td>-->
                           <td class="spacer">
                              @php 
                              if($locations !=''){
                                 foreach($locations as $loc){
                                    echo nl2br($loc->building);
                                 }
                              }
                              @endphp
                           </td>
                           <td class="spacer">{{($dept->proximity_setting ==1)?'ON':'OFF'}}</td>
                           <td class="spacer"><a href="#" onclick="device_status({{$account_id}},{{$dept->id}},'dev_status_{{$dept->id}}')"><div id='dev_status_{{$dept->id}}'><span class="chkstatus">Check Status</span></div></a></td>
                           
                           <td class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                       @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/device/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/device/restart/$dept->id")}}">Restart</a>
                           @endif
                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/device/batchassign/$dept->id")}}">Assign Batch User</a>
                           <a class="dropdown-item" href="{{url("opslogin/device/batchassignemp/$dept->id")}}">Assign Batch Employee</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/device/delete/$dept->id")}}');" >Delete</a>
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
						@if ($devices->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($devices->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $devices->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($devices->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $devices->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($devices->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $devices->lastPage()) as $i)
									@if($i >= $devices->currentPage() - 2 && $i <= $devices->currentPage() + 2)
										@if ($i == $devices->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $devices->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif 
								@endforeach
								@if($devices->currentPage() < $devices->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($devices->currentPage() < $devices->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $devices->appends($_GET)->url($devices->lastPage()) }}">{{ $devices->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($devices->hasMorePages())
									<li><a href="{{ $devices->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>

<script type="text/javascript">
	 function device_status(account_id,device_serial_no,display_id){
     $("#"+display_id).html("<span class='loading-dots'>.</span> <span class='loading-dots'>.</span><span class='loading-dots'>.</span>");	
		$('#error').html('');
		var serial_no = device_serial_no;
      var account_id = account_id;
		var token = "{!! csrf_token() !!}";
		$.ajax({
         url : "{!!URL:: route('devicestatus')!!}",
         dataType : "json",	 
			data: {'serial_no': serial_no,'account_id':account_id},
			success: function(response) {
				console.log(response);
				if(response == 1) {	
					$("#"+display_id).html("<span style='color:#00B050'>Online</span>");					
				}
            else if(response == 2) {				
					$("#"+display_id).html("<span style='color:#FF0000'>Offline</span>");					
				}
            else{
               $("#"+display_id).html("<span style='color:#FF0000'>Not Available</span>");
            }
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});	
	}
   </script>
