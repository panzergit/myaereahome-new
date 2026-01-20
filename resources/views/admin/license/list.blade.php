@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
    $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $user_permission =  $permission->check_menu_permission(7,$permission->role_id,1);

   $permission = $permission->check_permission(32,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>License Plate</h1>
</div>
  <div class="row">
                <div class="col-lg-12">
                  <ul class="summarytab">
                     <li  class="activeul" ><a href="{{url('/opslogin/user')}}">Summary</a></li>
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
                        <li ><a href="{{url('/opslogin/registration')}}">Registration @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a></li>
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

<div class="">
            <div class=" forunit forchange devicehead">
            {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/licenseplate/save'), 'files' => false]) !!}
            <h2 class="mt-3">Add License Plate</h2>
				      <div class="row asignbg" >
                     <div class="col-lg-3">
                           <div class="form-group">
                              <label>unit no :</label>
                                  <select class="form-control wauto"  name="unit1">
                                    @foreach($units_data as $data)
                                       <option value="{{$data['id']}}">{{$data['block']}} - {{$data['unit']}}</option>
                                    @endforeach
                                    </select>
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="form-group ">
                              <label>License Plate :</label>
                                {{ Form::text('license_plate1', null, ['class'=>'form-control','placeholder' => 'Enter License Plate']) }}
                           
                           </div>
                           </div>
                      
					 <div class="col-lg-3">
                           <div class="form-group ">
                              <input type="hidden" name="user_info_id" value="{{$moreInfoObj->id}}">
                           <input type="hidden" name="user_id" value="{{$moreInfoObj->user_id}}">

                                       <button type="submit" class="submit mt0-4 ml-3 float-right ">submit</button>
                                  
                                 </div>
                           </div>
                  </div>

                        
                        
                        </div>
               
               {!! Form::close() !!}
</div>

               <div class="">

               <div class="devicehead">
               <h2>Assigned License Plate(s)</h2>

           <div class="overflowscroll2 mb-112">
                        <table class="gap">
                 
                     <thead>
                        <tr>
                              <th>S.No</th>
                              <th>Unit</th>
                              <th>License Plate</th>
                              <th>assined date</th>
                              <th>action</th>
                           </tr>
                     </thead>
                     <tbody>
                      @if($license_plates)
                      @foreach($license_plates as $k => $license)
                        <tr>
                           <td>{{$k+1}}</td>
                              <td>#{{isset($license->addunitinfo->unit)?Crypt::decryptString($license->addunitinfo->unit):''}}</td>
                              <td>{{$license->license_plate}}</td>
                              <td>{{date('d/m/y',strtotime($license->created_at))}}</td>
                          
                           <td class="roundright">
                              <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                          @if(isset($user_permission) && $user_permission->edit==1)
                                             <a  class="dropdown-item" href="{{url("opslogin/licenseplate/$license->id/edit")}}" ">Edit
                                             </a>
                                          @endif
                                          @if(isset($user_permission) && $user_permission->delete==1)
                                             <a  class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/licenseplate/delete/$license->id")}}');" >Delete</a>
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
						@if ($license_plates->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($license_plates->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $license_plates->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($license_plates->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $license_plates->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($license_plates->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $license_plates->lastPage()) as $i)
									@if($i >= $license_plates->currentPage() - 2 && $i <= $license_plates->currentPage() + 2)
										@if ($i == $license_plates->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $license_plates->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($license_plates->currentPage() < $license_plates->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($license_plates->currentPage() < $license_plates->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $license_plates->appends($_GET)->url($license_plates->lastPage()) }}">{{ $license_plates->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($license_plates->hasMorePages())
									<li><a href="{{ $license_plates->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
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

