@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = $permission->id;
   $permission = $permission->check_permission(63,$permission->role_id); 
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
   $permission = $permission->check_permission(73,$permission->role_id); 
@endphp

<div class="status">
  <h1>Manage Mgmt/Sinking Fund</h1>
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
                        <li ><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
                     @endif

                     @if(isset($sharesetting->view) && $sharesetting->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/sharesettings#unitsettings')}}">Manage Mgmt/Sinking Fund </a></li>
                     @endif
                  </ul>
               </div>
               </div>
  <div>
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
 <div class="overflowscroll2">
                     <table class="gap">
                  @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/configuration/sharesettings/create")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + add new settings</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <thead>
                        <tr>
                           <th>#</th>
                          
                           <th>management fund amt</th>
                           <th>sinking fund amt</th>
                           <th>share value</th>
                           <th>no of billing month(s)</th>
                           <th>interest(%)</th>
                           <th>tax(%)</th>
                           <th>due</th>
                           <th>start date</th>
                           <th>end date</th>
                           <th>status</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($shares)

                       @foreach($shares as $k => $dept)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                         
                           <td  class="spacer">S${{$dept->management_fund_share}}</td>
                           <td  class="spacer">S${{$dept->sinking_fund_share}}</td>
                           <td  class="spacer">{{$dept->share_amount}}</td>
                           <td  class="spacer">{{$dept->no_of_billing_month}}</td>
                           <td  class="spacer">{{($dept->interest ==2)?$dept->int_percentage:"NA"}}</td>
                           <td  class="spacer">{{($dept->tax ==2)?$dept->tax_percentage:"NA"}}</td>
                           <td  class="spacer">
                              {{$dept->due_period_value}} {{($dept->due_period_type ==2)?"Month(s)":"Day(s)"}}
                             
                           </td>
                           <td  class="spacer">{{date('d/m/y',strtotime($dept->created_at))}}</td>
                           <td  class="spacer">{{($dept->created_at != $dept->updated_at)?date('d/m/y',strtotime($dept->updated_at)):''}}</td>
                           <td  class="roundright">{{($dept->status ==1)?"Active":"Not-Active"}}</td>
                           
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  </div>
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($shares->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($shares->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $shares->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($shares->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $shares->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($shares->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $shares->lastPage()) as $i)
									@if($i >= $shares->currentPage() - 2 && $i <= $shares->currentPage() + 2)
										@if ($i == $shares->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $shares->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($shares->currentPage() < $shares->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($shares->currentPage() < $shares->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $shares->appends($_GET)->url($shares->lastPage()) }}">{{ $shares->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($shares->hasMorePages())
									<li><a href="{{ $shares->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

