@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
    $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(7,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>User Registrations</h1>
</div>
  <div class="row">
                <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   ><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li class="activeul"><a href="#">Registrations @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a></li>
                     @endif
                  </ul>
         </div>
      </div>
      <div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Building and Unit should be created before bulk upload of User.<br/><br />Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/user/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
<form action="{{url('/opslogin/registrations/search')}}" method="get" role="search" class="forunit forbottom">
           
                     <div class="row asignbg">
					<div class="col-lg-3">
                           <div class="form-group">
                                 <div id="sandbox2">
                                    <input id="fromdate" name="fromdate" type="text" class="form-control" value="<?php echo(isset($fromdate)?$fromdate:'');?>" placeholder="Start Date">
                              </div>
                           </div>
                           </div>
                                 <div class="col-lg-3">                      
                           <div class="form-group ">
                                 <div id="sandbox">
                                    <input id="todate" name="todate" type="text" class="form-control" value="<?php echo(isset($todate)?$todate:'');?>" placeholder="End Date">
                              </div>
                           </div>
                           </div>
						
						 <div class="col-lg-3">
                     <div class="form-group">
                     <!--{{ Form::select('building', ['' => '--Select Block--'] + $buildings, $building, ['class'=>'form-control','id'=>'building','onchange'=>'getbuldunits()' ]) }} -->
                      {{ Form::select('building', ['' => '--Select Block--'] + $buildings, $building, ['class'=>'form-control','id'=>'building' ]) }}
                     </div>
						 </div>
						 <div class="col-lg-3">
                           <!--<div class="form-group ">
                              {{ Form::select('unit', ['' => '--Select Unit--'], (isset($unit)?$unit:''), ['class'=>'form-control','id'=>'bunit']) }}
                           </div>-->
                           <div class="form-group ">
                              <input  type="text" class="form-control" name="unit" id="unit" value="<?php echo(isset($unit)?$unit:'');?>" placeholder="Enter Unit">
                           </div>
                        </div>
						
                         <div class="col-lg-3">
                           <div class="form-group mt0-3">
						    <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>" placeholder="Enter First Name">
						    </div>
                        </div>
						 <div class="col-lg-3">
                           <div class="form-group mt0-3">
						     <input  type="text" class="form-control" name="last_name" id="last_name" value="<?php echo(isset($last_name)?$last_name:'');?>" placeholder="Enter Last Name">
						    </div>
                        </div>
                     <div class="col-lg-3">
                        <div class="form-group mt0-3">
						         <input  type="text" class="form-control" name="email" id="email" value="<?php echo(isset($email)?$email:'');?>" placeholder="Enter email">
						      </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group mt0-3">
						         {{ Form::select('role', ['' => '--Select Role--']+$roles, $role, ['class'=>'form-control','id'=>'role']) }}
						      </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group mt0-3">
						         {{ Form::select('status', ['' => '--Select Status--',1=>'Pending','2'=>"Approved","3"=>"Rejected"], $status, ['class'=>'form-control','id'=>'login_from']) }}
						      </div>
                     </div>
						 <div class="col-lg-6"></div>
						 <div class="col-lg-3">
                           <div class="form-group mt0-3">
                              <input type="hidden" id="property" value="{{$account_id}}" >
						   <a href="{{url("/opslogin/registrations")}}"  class="submit  float-right ml-2">clear</a>
						    <button type="submit" class="submit float-right">search</button>
						    </div>
                        </div>
                     

                     </div>
                  </form>
<style>
.mb-112{ margin-top: 30px;}
</style>
           <div class="overflowscroll2 mb-112">
                        <table class="gap">
                 
                     <thead>
                        <tr>
                           <th>Block</th>
                           <th>Unit </th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Phone</th>
                           <th>Role</th>
                           <th>Status</th>
                           
                           <th>Registered Date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($registrations)
                      

                       @foreach($registrations as $k => $reg)
                        <tr>
                           <td class="roundleft">{{isset($reg->buildinginfo->building)?$reg->buildinginfo->building:''}}</td>
                           <td class="spacer">{{isset($reg->getunit->unit)?"#".Crypt::decryptString($reg->getunit->unit):''}}</td>
                           <td class="spacer">{{$reg->first_name}} {{$reg->last_name}}</td>
                           <td class="spacer">{{$reg->email}}</td>
                           <td class="spacer">{{$reg->phone}}</td>
                           <td class="spacer">{{isset($reg->role->name)?$reg->role->name:''}}</td>
                           <td class="spacer">@php  
                              if($reg->status ==2)
                                 echo "Approved";
                              else if($reg->status ==3)
                                 echo "Rejected";
                              else
                                 echo "Pending";
                              @endphp
                           </td>
                           <td class="spacer">{{date('d/m/y',strtotime($reg->created_at))}}</td>
                           <!--<td class="spacer">{{($reg->approved_date != '0000-00-00 00:00:00' && $reg->approved_date != '')?date('d/m/y',strtotime($reg->approved_date)):''}}</td>-->
                           <td class="roundright">
                              <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                          @if(isset($permission) && $permission->edit==1 )
                                             <a class="dropdown-item" href="{{url("opslogin/registrations/view/$reg->id")}}">View</a>
                                          @endif
                                          @if(isset($permission) && $permission->edit==1 && $reg->status ==1)
                                             <a class="dropdown-item" href="{{url("opslogin/registrations/approve/$reg->id")}}">Approve</a>
                                          @endif
                                          @if(isset($permission) && $permission->edit==1 && $reg->status ==3)
                                             <a class="dropdown-item" href="{{url("opslogin/registrations/approve/$reg->id")}}">Revert & Approve</a>
                                          @endif
                                           @if(isset($permission) && $permission->edit==1 && $reg->status ==1)
                                              <a data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$reg->id}}" class=" dropdown-item open-dialog">Reject</a>
                                          @endif
                                          @if(isset($permission) && $permission->delete==1 && $reg->status !=2)
                                             <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/registrations/delete/$reg->id")}}');">Delete</a>
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
						@if ($registrations->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($registrations->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $registrations->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($registrations->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $registrations->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($registrations->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $registrations->lastPage()) as $i)
									@if($i >= $registrations->currentPage() - 2 && $i <= $registrations->currentPage() + 2)
										@if ($i == $registrations->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $registrations->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($registrations->currentPage() < $registrations->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($registrations->currentPage() < $registrations->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $registrations->appends($_GET)->url($registrations->lastPage()) }}">{{ $registrations->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($registrations->hasMorePages())
									<li><a href="{{ $registrations->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
				</div>	

            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/registrations/cancelregistration'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Registration - Reject</h5>
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
               <input type="hidden" name="status"value="3">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
             {!! Form::close() !!}
          </div>
        </div>
@endsection
<script>
function getbuldunits(){
 
 var property =  $("#property").val();
 var building =  $("#building").val();
$.ajax({
                     url : "{!!URL:: route('getunits')!!}",
                     dataType : "json",
                     data:{
                        property:property,
                        building:building
                     },
                     success:function(data)
                     {
                        $('#bunit').empty();
                         $("#bunit").append('<option value="" selected="selected">--Select Unit--</option>')
                        $.each(data, function(id,rec){
                           if(bunit == id)
                              $("#bunit").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#bunit").append('<option value="'+ id +'">'+ rec +'</option>')
                           
                           

                        });
                     }
                  });
}
</script>

