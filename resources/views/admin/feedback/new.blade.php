@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(6,$permission->role_id); 
@endphp

<div class="status">
  <h1>new submitted feedback list </h1>
</div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/feedbacks/summary#fb')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/feedbacks/new#fb')}}">New feedback</a></li>
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
                         <th>ticket</th>
                         <th>submitted date</th>
                         <th>category</th>
                         <th>unit no</th>
                         <th>submitted by</th>
                         <th>status</th>
                         <th>updated on</th>
                         <th>actions</th>
                      </tr>
                   </thead>
                   <tbody>
                    @if($feedbacks)

                     @foreach($feedbacks as $k => $feedback)
                      <tr>
                         <td class="roundleft">{{$feedback->ticket}}</td>
                         <td class="spacer">{{date('d/m/y',strtotime($feedback->created_at))}}</td>
                         <td class="spacer">{{isset($feedback->getoption->feedback_option)?$feedback->getoption->feedback_option:''}}</td>
                         <td class="spacer">{{isset($feedback->getunit->unit)?Crypt::decryptString($feedback->getunit->unit):''}}</td>
                         <td class="spacer">{{isset($feedback->user->name)?Crypt::decryptString($feedback->user->name):''}}</td>
                         
                         <td class="spacer">@php
                            if(isset($feedback->status)){
                              if($feedback->status==0)
                                 echo "OPEN";
                              else if($feedback->status==1)
                                 echo "CLOSED";
                              else
                                 echo "IN PROGRESS";
                              
                            }
                            @endphp
                         </td>
                <td class="spacer">{{date('d/m/y',strtotime($feedback->updated_at))}}</td>
                         <td class="roundright">
						  <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                      @if(isset($permission) && $permission->edit==1)
                         <a class="dropdown-item"  href="{{url("opslogin/feedbacks/$feedback->id/edit")}}" data-toggle="tooltip" data-placement="top" title="Edit">Edit</a>
                         @endif
                         @if(isset($permission) && $permission->delete==1)
                         <a class="dropdown-item"  href="#" onclick="delete_record('{{url("opslogin/feedbacks/delete/$feedback->id")}}');" data-toggle="tooltip" data-placement="top" title="Delete">Delete</a>
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
						@if ($feedbacks->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($feedbacks->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $feedbacks->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($feedbacks->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $feedbacks->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($feedbacks->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $feedbacks->lastPage()) as $i)
									@if($i >= $feedbacks->currentPage() - 2 && $i <= $feedbacks->currentPage() + 2)
										@if ($i == $feedbacks->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $feedbacks->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($feedbacks->currentPage() < $feedbacks->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($feedbacks->currentPage() < $feedbacks->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $feedbacks->appends($_GET)->url($feedbacks->lastPage()) }}">{{ $feedbacks->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($feedbacks->hasMorePages())
									<li><a href="{{ $feedbacks->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

            
               </div>
@endsection



