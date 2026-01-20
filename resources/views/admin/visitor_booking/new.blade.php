@extends('layouts.adminnew')


@section('content')
@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(34,$permission->role_id); 
@endphp

<div class="status">
  <h1>visitor management - new </h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                    <li ><a href="{{url('/opslogin/visitor-summary?view=dashboard')}}">Dashboard</a></li>
                     <li  ><a href="{{url('/opslogin/visitor-summary?view=summary')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/visitor-summary/create')}}">Add New Walk In</a></li>
                     <li   class="activeul"><a href="{{url('/opslogin/visitor-summary/new#vm')}}">New Visitors</a></li>
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
                   
                     <thead>
                        <tr>
                           <th>booking id</th>
                           <th>unit no</th>
                           <th>invited by</th>
                           <th>date of visit</th>
                           <th>visitor no</th>
                           <th>purpose</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($bookings)

                       @foreach($bookings as $k => $booking)
                        <tr>
                           <td class="roundleft">{{$booking->ticket}}</td>
                           <td class="spacer">{{isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($booking->user->name)?Crypt::decryptString($booking->user->name):''}}</td>
                           
                           <td class="spacer">{{date('d/m/y',strtotime($booking->visiting_date))}}
                           </td>
                           <td class="spacer"><?php echo $booking->visitors->count();?></td>
                           <td class="spacer">{{isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:''}}
                           <td class="spacer">
                              <?php
                              if($booking->visited_count->count() >0 && $booking->visited_count->count() >= $booking->visitors->count())
                              $status = "Entered";
                           else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
                              $status = $booking->visited_count->count()." Entered";
                           else if($booking->registered_count->count() >0 && $booking->registered_count->count() == $booking->invitedemails->count())
                              $status = "Registration Success";
                           else if($booking->registered_count->count() >0 && $booking->registered_count->count() <= $booking->visitors->count())
                              $status = $booking->registered_count->count()." Registered";
                           else if($booking->status==0)
                              $status = "Pending";
                           else if($booking->status==1)
                              $status = "Cancelled";
                           else  
                              $status = "Entered";
                              echo $status;
                              ?></td>
                           <td class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                   @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/visitor-summary/$booking->id/edit")}}">Edit</a>
                           @endif
                         @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/visitor-summary/delete/$booking->id")}}');" >Delete</a>
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
						@if ($bookings->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($bookings->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $bookings->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($bookings->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $bookings->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($bookings->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $bookings->lastPage()) as $i)
									@if($i >= $bookings->currentPage() - 2 && $i <= $bookings->currentPage() + 2)
										@if ($i == $bookings->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $bookings->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($bookings->currentPage() < $bookings->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($bookings->currentPage() < $bookings->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $bookings->appends($_GET)->url($bookings->lastPage()) }}">{{ $bookings->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($bookings->hasMorePages())
									<li><a href="{{ $bookings->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>

               
@endsection



