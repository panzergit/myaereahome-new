@extends('layouts.adminnew')


@section('content')
@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(5,$permission->role_id); 
@endphp
<div class="status">
  <h1>new facility booking</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li @if(request()->has('view') && request()->view=='dashboard') class="activeul" @endif><a href="{{url('/opslogin/facility?view=dashboard')}}">Dashboard</a></li>
                     <li @if(request()->has('view') && request()->view=='summary') class="activeul" @endif><a href="{{url('/opslogin/facility?view=summary')}}">Summary</a></li>
                     <li   class="activeul"><a href="{{url('/opslogin/facility/new')}}">New facility bookings</a></li>
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
                           <th>facility</th>
                           <th>booked by</th>
                           <th>unit</th>
                           <th>booking date</th>
                           <th>booking time</th>
                           <th>deposit fee</th>
                           <th>booking status</th>
                           <th>payment status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($bookings)

                        @foreach($bookings as $k => $booking)
                        <tr>
                           <td class="roundleft">{{isset($booking->gettype->facility_type)?$booking->gettype->facility_type:''}}</td>
                           <td class="spacer">{{isset($booking->getname->name)?Crypt::decryptString($booking->getname->name):''}}</td>
                           <td class="spacer">{{isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($booking->booking_date))}}</td>
                           <td class="spacer">{{$booking->booking_time}}</td>
                           <td class="spacer">@php echo number_format(($booking->deposit_fee+$booking->booking_fee),2) @endphp</td>
                           <td class="spacer">
                           @php
                            if(isset($booking->status)){
                              if($booking->status==0)
                                echo "New";
                              else if($booking->status==1)
                                echo "Cancelled";
                              else
                                echo "Confirmed";
                            }
                            @endphp
                            </td>
                            <td class="spacer">
                           @php
                              if($booking->payment_required==2){
                                 echo "Not Required";
                              }
                              else{
                                 if(isset($booking->payment_status)){
                                    if($booking->payment_status==2)
                                       echo "Received";
                                    else 
                                       echo "Not Received";
                                 }
                              }
                            @endphp
                            </td>

                            <td class="roundright">
                            @if(isset($permission) && $permission->edit==1)
                            <span data-toggle="modal" data-target="#confirmModalCenter" data-id="{{$booking->id}}" class="open-dialog-confirm">
                            <a href="#" data-toggle="tooltip" data-placement="top" title="Confirm">
    <img src="{{url('assets/admin/img/tick.png')}}" class="tickimg phvert">         
    </a>
</span>
                            <!--a href="#" data-toggle="modal" data-target="#confirmModalCenter" data-id="{{$booking->id}}" class="open-dialog-confirm" data-toggle="tooltip" data-placement="top" title="Confirm"><img src="{{url('assets/admin/img/confirm.png')}}"></a-->
                            @endif
                         @if(isset($permission) && $permission->delete==1)
                         <span data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$booking->id}}" class="open-dialog">
                         <a href="#" data-toggle="tooltip" data-placement="top" title="Cancel">
    <img src="{{url('assets/admin/img/cross.png')}}" class="tickimg phvert">         
    </a>
</span>
                            <!--a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$booking->id}}" class="open-dialog" data-toggle="tooltip" data-placement="top" title="Cancel"><img src="{{url('assets/admin/img/cancel.png')}}"></a-->
                            @endif
                            </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-12">
					<div  class="">
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


                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/facility/cancellationrefund'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Booking - Cancel</h5>
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
               <input type="hidden" name="status"value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
             {!! Form::close() !!}
          </div>
        </div>


          <div class="modal fade" id="confirmModalCenter" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/facility/updateconfirmstatus'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Booking - Confirm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
               Are you sure want to confirm?
              
               
              </div>
              <div class="modal-body">
               <input type="hidden" name="Id" id="Id" value="">
               <input type="hidden" name="status"value="2">
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
