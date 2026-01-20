@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(37,$permission->role_id); 
@endphp


@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
   $menu =  $permission->check_menu_permission(25,$permission->role_id,1);
   $feedback =  $permission->check_menu_permission(26,$permission->role_id,1);
   $defect =  $permission->check_menu_permission(27,$permission->role_id,1);
   $property =  $permission->check_menu_permission(28,$permission->role_id,1);
   $facility =  $permission->check_menu_permission(29,$permission->role_id,1);
   $vm =  $permission->check_menu_permission(37,$permission->role_id,1);
   $eforms =  $permission->check_menu_permission(39,$permission->role_id,1);
   $payment =  $permission->check_menu_permission(46,$permission->role_id,1);
   $holiday =  $permission->check_menu_permission(53,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
   $dashmenu =  $permission->check_menu_permission(55,$permission->role_id,1);
   $key_setting =  $permission->check_menu_permission(9,$permission->role_id,1);
   $inspection_setting =  $permission->check_menu_permission(57,$permission->role_id,1);
   $sharesetting =  $permission->check_menu_permission(63,$permission->role_id,1);
   $permission = $permission->check_permission(37,$permission->role_id); 
@endphp
<style>
.containeruserl {
    display: block;
    font: normal normal bold 12px/20px Helvetica!important;
    color: #5D5D5D!important;
    position: relative;
    padding-left: 25px;
    margin-bottom: 0px;
    cursor: pointer;
    font-size: 14px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.containeruserl input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.checkmarkuserl {
    position: absolute;
    top: 1px;
    left: 0;
    height: 19px;
    width: 19px;
    background-color: #D0D0D0;
}
.containeruserl .checkmarkuserl:after {
    left: 7px;
    top: 2px;
    width: 7px;
    height: 13px;
    border: solid #8F7F65;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}


.containeruserl:hover input ~ .checkmarkuserl {
  background-color: #ccc;
}

.containeruserl input:checked ~ .checkmarkuserl {
  background-color: #DFCFB5;
}

.checkmarkuserl:after {
  content: "";
  position: absolute;
  display: none;
}

.containeruserl input:checked ~ .checkmarkuserl:after {
  display: block;
}
</style>
<div class="status">
  <h1>visitor management - settings</h1>
</div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if( isset($property->view) && $property->view==1 )
                        <li><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
                     @endif

                     @if(Auth::user()->role_id ==1)
                        <li  ><a href="{{url('/opslogin/configuration/banner#settings')}}">Home Banner  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/ads#settings')}}">Ads  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/econcierge#settings')}}">E-Cconcierge  <br>Management</a></li>
                     @endif

                     @if((isset($key_setting) && $key_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/collectionappoinment')}}">Key Collection  <br>Appointment Settings</a></li>
                     @endif

                     @if((isset($inspection_setting) && $inspection_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/inspectionappoinment')}}">Defects Inspection  <br>Appointment Settings</a></li>
                     @endif

                     @if(2 ==1)
                        <li><a href="{{url('/opslogin/configuration/dashboard#settings')}}">Mobile App  <br>Dashboard Settings</a></li>
                     @endif

                     @if(isset($menu->view) && $menu->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/menu#menusettings')}}">Menu  <br>Management</a></li>
                     @endif

                     @if(isset($feedback->view) && $feedback->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/feedback#feedbacksettings')}}">Feedback  <br>Options</a></li>
                     @endif
                     
                     @if(isset($defect->view) && $defect->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/defect#defectsettings')}}">Defects  <br>Location</a></li>
                     @endif
                     
                     @if(isset($facility->view) && $facility->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                     @endif
                     
                     @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                        <li class="activeul"><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
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

                  @if(isset($permission) && $permission->create==1)
                  <div class="row">
                  <div class="col-lg-12">
                  <form action="{{url('/opslogin/configuration/purpose/settings')}}" method="post" >
                  {{ csrf_field() }} 

				  <div class="colbord forunit">
				  <h2>max no of visitors :</h2>
				  <div class="form-group row col-lg-12">
                              <label class="containerbut">  no limit  
                              <input type="radio" name="visitor_limit" value="0" {{(isset($propertyObj->visitor_limit) && $propertyObj->visitor_limit==0)?'checked':''}} >
                              <span class="checkmarkbut"></span>
                              </label>
                           </div>
						   <div class="form-group row col-lg-12">
                              <label class="containerbut">  set limit at:  
                              <input type="radio" name="visitor_limit" value="1" {{(isset($propertyObj->visitor_limit) && $propertyObj->visitor_limit==1)?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              <div class="col-sm-2">
							  <input type="text" class="form-control" name="visitors_allowed" value="{{isset($propertyObj->visitors_allowed)?$propertyObj->visitors_allowed:''}}">
                              </div>
							    <div class="col-sm-2">
							 <label class="col-form-label"> visitors</label>
							   </div>
							 
                           </div>
                           <!--<div class="form-group row col-lg-12">
                              <label class="containeruserl"> Visiting Start & End Date Required. 
                              <input type="checkbox" name="visiting_to_date_required" value="1" {{(isset($propertyObj->visiting_to_date_required) && $propertyObj->visiting_to_date_required==1)?'checked':''}} >
                              <span class="checkmarkuserl"></span>
                              </label>
                           </div> -->
						     <div class="row">
						    <div class=" col-lg-12">
							   <button type="submit" class="submit mb-3 float-right">update</button>
							</div>
							</div>
                           </div>
				  </form>
				  </div>
				  </div>
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/configuration/purpose/create")}}"  class="submit ml-3 float-left" style="width:auto"> + Add New</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <div class="overflowscroll2">
                  <table class="gap">
                     <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>visitng purpose</th>
                           <th>id required?</th>
                           <th>limit included?</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($lists)

                       @foreach($lists as $k => $dept)
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td class="spacer">{{$dept->visiting_purpose}}</td>
                           <td class="spacer"><?php echo ($dept->id_required ==1)?"Yes":"No"?></td>
                           <td class="spacer"><?php echo ($dept->limit_set ==1)?"Yes":"No"?></td>
                           
                           <td class="roundright">
						      <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/purpose/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/purpose/delete/$dept->id")}}');" >Delete</a>
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
						@if ($lists->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($lists->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $lists->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($lists->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $lists->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($lists->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $lists->lastPage()) as $i)
									@if($i >= $lists->currentPage() - 2 && $i <= $lists->currentPage() + 2)
										@if ($i == $lists->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $lists->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($lists->currentPage() < $lists->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($lists->currentPage() < $lists->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $lists->appends($_GET)->url($lists->lastPage()) }}">{{ $lists->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($lists->hasMorePages())
									<li><a href="{{ $lists->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

