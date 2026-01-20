@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = $permission->id;
   $permission = $permission->check_permission(24,$permission->role_id); 
@endphp

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
   $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
   $sharesetting =  $permission->check_menu_permission(73,$permission->role_id,1);
   $permission = $permission->check_permission(24,$permission->role_id); 
@endphp

<div class="status">
  <h1>manage unit lists</h1>
</div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if(isset($role->view) && $role->view==1 )
                        <li ><a href="{{url('/opslogin/configuration/role#rolesettings')}}">Manage Role </a></li>
                     @endif

                     @if(isset($building->view) && $building->view==1 )
                        <li><a href="{{url('/opslogin/configuration/building#buildingsettings')}}">Manage Block </a></li>
                     @endif

                     @if(isset($unit->view) && $unit->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
                     @endif

                     @if(isset($sharesetting->view) && $sharesetting->view==1 )
                        <li><a href="{{url('/opslogin/configuration/sharesettings#unitsettings')}}">Manage Mgmt/Sinking Fund </a></li>
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
                
                  @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                  <div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Building should be created before bulk upload of unit.<br/><br />Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/configuration/unit/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
                    <div class="row">
                      <div class="col-lg-4">
                               <a href="{{url("/opslogin/configuration/unit/create")}}"  class="submit mt-2 ml-0 float-left" style="width:auto"> + Add New</a>
                       </div>

                       <div class="col-lg-8">
                               <!--a href="{{url("/opslogin/configuration/unit/uploadcsv")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> IMPORT FROM CSV</a-->
                               <a href="#myModalcnf"  data-toggle="modal"  class="submit mt-2 mr-0 float-right" style="width:auto"> import from csv</a>
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
                           <th>building</th>
                           <th>unit no</th>
                           <th>id</th>
                           <th>size</th>
                           <th>share value</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($units)

                       @foreach($units as $k => $dept)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                              <td  class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                           @endif
                           <td  class="spacer">{{isset($dept->buildinginfo->building)?$dept->buildinginfo->building:''}}</td>
                           <td  class="spacer">{{\Crypt::decryptString($dept->unit)}}</td>
                           <td  class="spacer">{{\Crypt::decryptString($dept->code)}}</td>
                           <td  class="spacer">{{$dept->size}}</td>
                           <td  class="spacer">{{intval($dept->share_amount)}}</td>
                           
                           <td  class="roundright">
						   <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                     @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/unit/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/unit/delete/$dept->id")}}');">Delete</a>
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
@endsection

