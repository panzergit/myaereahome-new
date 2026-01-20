@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(33,$permission->role_id); 
@endphp

<div class="status">
  <h1>Resident’s file new upload (weekly)</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/residents-uploads#rfu')}}">Summary</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/residents-uploads/new#rfu')}}">New upload</a></li>
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
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/submission/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                    <thead>
                           <tr>
                              <th>s/n</th>
                              <th>unit</th>
                              <th>upload by</th>
                              <th>upload date </th>
                              <th>category</th>
                              <th>status</th>
                              <th>updated on</th>
                              <th>actions</th>
                           </tr>
                        </thead>
                     <tbody>
                      @if($submissions)

                       @foreach($submissions as $k => $submission)
                        <tr>
                        <td class="roundleft">{{$k+1}}</td>
                           <td class="spacer">{{isset($submission->user->userinfo->getunit->unit)?Crypt::decryptString($submission->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($submission->user->name)?Crypt::decryptString($submission->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($submission->created_at))}}</td>
                           <td class="spacer">{{isset($submission->category->docs_category)?$submission->category->docs_category:''}}</td>
                          
                           <td class="spacer">@php
                  if(isset($submission->status)){
                    if($submission->status==0)
                      echo "NEW";
                     else if($submission->status==1)
                      echo "PROCESSING";
                    else
                      echo "PROCESSED";
                  }
                  @endphp</td>
                  <td class="spacer">{{($submission->created_at !=$submission->updated_at)?date('d/m/y',strtotime($submission->updated_at)):''}}</td>
                           <td class="roundright">
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                      @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/residents-uploads/$submission->id/edit")}}" >Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/residents-uploads/delete/$submission->id")}}');" >Delete</a>
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
						@if ($submissions->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($submissions->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $submissions->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($submissions->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $submissions->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($submissions->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $submissions->lastPage()) as $i)
									@if($i >= $submissions->currentPage() - 2 && $i <= $submissions->currentPage() + 2)
										@if ($i == $submissions->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $submissions->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($submissions->currentPage() < $submissions->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($submissions->currentPage() < $submissions->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $submissions->appends($_GET)->url($submissions->lastPage()) }}">{{ $submissions->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($submissions->hasMorePages())
									<li><a href="{{ $submissions->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
               </div>
@endsection



