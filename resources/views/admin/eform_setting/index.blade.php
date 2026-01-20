@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $eform =  $permission->check_menu_permission(39,$permission->role_id,1);
   $payment =  $permission->check_menu_permission(46,$permission->role_id,1);
   $holiday =  $permission->check_menu_permission(53,$permission->role_id,1);
  
   $permission = $permission->check_permission(39,$permission->role_id); 
@endphp

<div class="status">
  <h1>eform settings lists</h1>
</div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($eform->view) && $eform->view==1 )
                        <li class="activeul"><a href="{{url('/opslogin/configuration/eform_setting#eformsettings')}}">E-Form Settings </a></li>
                     @endif

                     @if(isset($payment->view) && $payment->view==1 )
                        <li  ><a href="{{url('/opslogin/configuration/payment_setting#paymentsettings')}}">Payment Settings </a></li>
                     @endif

                     @if(isset($holiday->view) && $holiday->view==1 )
                        <li   ><a href="{{url('/opslogin/configuration/holiday_setting#holidayssettings')}}">Public Holidays Settings  </a></li>
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
<div class="overflowscroll2">
                  <table class="gap">
                  @if(isset($permission) && $permission->create==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group">
                               <a href="{{url("/opslogin/configuration/eform_setting/create")}}"  class="submit mt-5 ml-3 float-left" style="width:auto"> + add new</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>form</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($eforms)

                       @foreach($eforms as $k => $dept)
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td class="spacer">{{$dept->gettype->name}}</td>
                           <td class="roundright">
						      <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                      @if(isset($permission) && $permission->view==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/eform_setting/preview/$dept->id")}}">List</a>
                           @endif
                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/configuration/eform_setting/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/configuration/eform_setting/delete/$dept->id")}}');">Delete</a>
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
						@if ($eforms->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($eforms->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $eforms->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($eforms->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $eforms->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($eforms->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $eforms->lastPage()) as $i)
									@if($i >= $eforms->currentPage() - 2 && $i <= $eforms->currentPage() + 2)
										@if ($i == $eforms->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $eforms->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($eforms->currentPage() < $eforms->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($eforms->currentPage() < $eforms->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $eforms->appends($_GET)->url($eforms->lastPage()) }}">{{ $eforms->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($eforms->hasMorePages())
									<li><a href="{{ $eforms->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

