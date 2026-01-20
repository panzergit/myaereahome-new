@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;

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
   $permission = $permission->check_permission(23,$permission->role_id); 
@endphp

<div class="status">
  <h1>manage block lists</h1>
</div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if(isset($role->view) && $role->view==1 )
                        <li ><a href="{{url('/opslogin/configuration/role#rolesettings')}}">Manage Role </a></li>
                     @endif

                     @if(isset($building->view) && $building->view==1 )
                        <li   class="activeul"><a href="{{url('/opslogin/configuration/building#buildingsettings')}}">Manage Block </a></li>
                     @endif

                     @if(isset($unit->view) && $unit->view==1 )
                        <li><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
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
                  <table class="table usertable ">
                  @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                  <div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Warning Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Bulk upload support only CSV file("," comma delimited file).<br/><br />
            Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/configuration/building/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
                    <div class="row mt-2">
                      <div class="col-lg-4 col-6">
                               <a href="{{url("/opslogin/configuration/building/create")}}"  class="submit  ml-0 float-left" style="width:auto"> + Add New</a>
                       </div>

                       <div class="col-lg-8 col-6">
                               <a  href="#myModalcnf"  data-toggle="modal" class="submit  mr-0 float-right" style="width:auto"> import from csv</a>
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
                           <th>block</th>
                           <th>block id</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($buildings)

                       @foreach($buildings as $k => $dept)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td  class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td  class="spacer">{{$dept->building}}</td>
                           <td  class="spacer">{{$dept->building_no}}</td>
                           <td  class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                          @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/building/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/building/delete/$dept->id")}}');">Delete</a>
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
						@if ($buildings->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($buildings->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $buildings->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($buildings->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $buildings->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($buildings->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $buildings->lastPage()) as $i)
									@if($i >= $buildings->currentPage() - 2 && $i <= $buildings->currentPage() + 2)
										@if ($i == $buildings->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $buildings->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($buildings->currentPage() < $buildings->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($buildings->currentPage() < $buildings->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $buildings->appends($_GET)->url($buildings->lastPage()) }}">{{ $buildings->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($buildings->hasMorePages())
									<li><a href="{{ $buildings->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

