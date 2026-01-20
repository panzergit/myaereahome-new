@extends('layouts.adminnew')




@section('content')

@php
$reportobj = new \App\Models\v7\ChatBox();
$newreport_count = $reportobj->report_new_count();
@endphp
<style>
.allnotif{    position: relative;}
.allnotif span{      right: -8px;
    top: 0px;
    padding: 2px 4px;}
	.tablenoti { position: relative;}
	.tablenoti span{    padding: 2px 4px;}
	.tablenoti span:beffore{   }
	
	.left:before {
    content: '';
    border-right: 6px solid #f31a2f;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    position: absolute;
     left: -1px;
    bottom: -2px;
    -moz-transform: rotate(135deg);
    -o-transform: rotate(135deg);
    -webkit-transform: rotate(135deg);
    transform: rotate(191deg);
}
</style>
<div class="status mtsuperadmin topich row">
  <div class="col-lg-6">
  <h1>ResiChat - Replies</h1>
  <h3>Topic : {{$ChatObj->subject}}</h3>
    </div>
    <div class="col-lg-6">
  <a href="{{url("opslogin/resichat")}}" class='submitchat  float-right'>Back to Summary</a>
  </div>
  
</div>

  <div>
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
  @endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li  class="activeul"><a href="{{url('/opslogin/resichat')}}">Summary</a></li>
                     <li  class="allnotif"><a href="{{url('/opslogin/resichat')}}">All Reports <span class="notification">{{$newreport_count}}</span></a></li>
                    
                  </ul>
               </div>
               </div>

                   <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                           <th>replied by</th>
                           <th>Comment</th>
                           <th>Reports</th>
                           <th>Replied on</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($lists)
                        @foreach($lists as $k => $user)
                        <tr>
                          
                           <td class="roundleft">{{isset($user->user->name)?Crypt::decryptString($user->user->name):''}}</td>
                           <td class="spacer">{{$user->comment}} </td>
                           <td class="spacer">{{$user->report_count()}}</td>
                           <td class="spacer">{{($user->created_at != '0000-00-00' && $user->created_at != '')?date('d/m/y',strtotime($user->created_at)):''}}</td>
                           <td class="roundright">
                           
							   <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                       <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/resichat/replies/delete/$user->id")}}');">Delete</a>
                          
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
						@if ($lists->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($lists->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $lists->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($lists->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $lists->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($lists->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $lists->lastPage()) as $i)
									@if($i >= $lists->currentPage() - 2 && $i <= $lists->currentPage() + 2)
										@if ($i == $lists->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $lists->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($lists->currentPage() < $lists->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($lists->currentPage() < $lists->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $lists->appends($_GET)->url($lists->lastPage()) }}">{{ $lists->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($lists->hasMorePages())
									<li><a href="{{ $lists->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

@section('customJS')

@endsection




