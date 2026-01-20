@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(2,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>New Appointment For Key Collection</h1>
</div>

  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/takeover_appt/lists#kc')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/takeover_appt#kc')}}">New appointments</a></li>
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
                           <th>unit no</th>
                           <th>booked by</th>
                           <th>appointment date</th>
                           <th>appointment  time</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($units)

                        @foreach($units as $k => $appt)
                        <tr>
                           <td class='roundleft'>{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                           <td class='spacer'>{{isset($appt->getname->name)?Crypt::decryptString($appt->getname->name):''}}</td>
                           <td class='spacer'>{{date('d/m/y',strtotime($appt->appt_date))}}</td>
                           <td class='spacer'>{{$appt->appt_time}}</td>
                           <td class='spacer'>@php
                  if(isset($appt->status)){
                    if($appt->status==0)
                      echo "New";
                    else
                      echo "Cancelled";
                  }
                  @endphp</td>

                           <td class='roundright'>
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                          @if(isset($permission) && $permission->edit==1)
                           <span data-toggle="modal" data-target="#confirmModalCenter" data-id="{{$appt->id}}" class="open-dialog-confirm"> 
                           <a class="dropdown-item"  href="#" title="Confirm">
                         Confirm       
    </a>
</span>
                           <!--a href="#" data-toggle="modal" data-target="#confirmModalCenter" data-id="{{$appt->id}}" class="open-dialog-confirm" data-toggle="tooltip" data-placement="top" title="Confirm"><img src="{{url('assets/admin/img/confirm.png')}}"></a-->
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <span data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$appt->id}}" class="open-dialog">
                           <a class="dropdown-item"  href="#" >
                          Cancel      
    </a>
</span>
                           <!--a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$appt->id}}" class="open-dialog" data-toggle="tooltip" data-placement="top" title="Cancel"><img src="{{url('assets/admin/img/cancel.png')}}"></a-->
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

               <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/book_appt/updatecancelstatus'), 'files' => false]) !!}
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
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/book_appt/updateconfirmstatus'), 'files' => false]) !!}
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

<script type="text/javascript">
 
</script>