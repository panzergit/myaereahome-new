@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(3,$permission->role_id); 
@endphp

<div class="status">
  <h1>NEW DEFECTS LIST / JOINT INSPECTION
 </h1>
</div>

  <div class="containerwidth">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

                  <table class="table usertable ">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/defect/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                           <th>TICKET NO</th>
                           <th>UNIT NO</th>
                           <th>SUBMITTED BY</th>
                           <th>SUBMITTED DATE</th>
                           <th>APPT DATE</th>
                           <th>APPT TIME</th>
                           <th>LIST</th>
                           <th>STATUS</th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($defects)

                       @foreach($defects as $k => $defect)
                      
                        <tr>
                           <td>{{$defect->ticket}}</td>
                           <td>{{isset($defect->user->userinfo->getunit->unit)?Crypt::decryptString($defect->user->userinfo->getunit->unit):''}}</td>
                           <td>{{Crypt::decryptString($defect->user->name)}}</td>
                           <td>{{date('d/m/y',strtotime($defect->created_at))}}</td>
                           <td>{{isset($defect->inspection->appt_date)?date('d/m/y',strtotime($defect->inspection->appt_date)):''}}</td>
                           <td>{{isset($defect->inspection->appt_time)?$defect->inspection->appt_time:''}}</td>
                           <td>   <a href="#" data-toggle="tooltip" data-placement="top" title="List"><img src="{{url('assets/admin/img/Files.png')}}"></a></td>
                           
                           <td>@php
                              if(isset($defect->status)){
                                if($defect->status==0)
                                  echo "OPEN";
                                else if($defect->status==1)
                                  echo "CLOSED";
                                else
                                  echo "IN PROGRESS";
                              }
                              @endphp
                            </td>
                           
                           <td>
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/defects/$defect->id/edit")}}"><img src="{{url('assets/admin/img/Edit.png')}}"></a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a href="#" onclick="delete_record('{{url("opslogin/defects/delete/$defect->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                           @endif
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  <div class="col-lg-10">
					<div  class="form-group row">
						@if ($defects->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($defects->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $defects->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($defects->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $defects->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($defects->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $defects->lastPage()) as $i)
									@if($i >= $defects->currentPage() - 2 && $i <= $defects->currentPage() + 2)
										@if ($i == $defects->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $defects->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($defects->currentPage() < $defects->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($defects->currentPage() < $defects->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $defects->appends($_GET)->url($defects->lastPage()) }}">{{ $defects->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($defects->hasMorePages())
									<li><a href="{{ $defects->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection



