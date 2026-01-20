@extends('layouts.adminnew')
@php
$reportobj = new \App\Models\v7\MpAdsSubmission();
$newreport_count = $reportobj->report_allnew_count();
@endphp

<style>
.allnotif{    position: relative;}
.allnotif span{      right: -8px;
    top: 0px;
    padding: 2px 4px;}
	.tablenoti { position: relative;}
	.tablenoti span{    padding: 2px 7px;}
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
.notification1{}
.notification1 {
    position: absolute;
    right: 24px;
    top: 8px;
    background: #f31a2f;
    padding: 2px 8px;
    line-height: 17px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: #fff;
}
.pl30{    padding-left: 30px!important;}
</style>

@section('content')


<div class="status mtsuperadmin">
  <h1>Marketplace</h1>
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
                     <li  ><a href="{{url('/opslogin/marketplace')}}">Summary</a></li>
                     <li  class="activeul allnotif"><a href="#">All Reports <span class="notification">{{$newreport_count}}</span></a></li>
                     <li class="pl30"><a href="{{url('/opslogin/marketplace/blockedusers')}}" >Blocked Users</a> </li>
                  </ul>
               </div>
               </div>
  
                   <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                           <th>title</th>
                           <th>uploaded by</th>
                           <th>price</th>
                           <th>type</th>
                           <th>condition</th>
                           <th>Report</th>
                           <th>uploaded on</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($lists)
                        @foreach($lists as $k => $user)
                        <tr>
                           <td class="roundleft">{{$user->gettitle->title}}</td>
                           <td class="spacer">{{isset($user->user->name)?Crypt::decryptString($user->user->name):''}}</td>
                           <td class="spacer">{{$user->gettitle->price}} </td>
                           <td class="spacer">{{isset($user->gettitle->gettype->type)?$user->gettitle->gettype->type:''}} </td>
                           <td class="spacer">{{isset($user->gettitle->getcondition->type)?$user->gettitle->getcondition->type:''}} </td>
                           <td class="spacer tablenoti">{{$user->gettitle->report_count()}} 
                              @if($user->gettitle->new_count() >0 )
                                 <span class="notification1 left">{{$user->gettitle->new_count()}}</span>
                              @endif</td>
                           <td class="spacer">{{($user->created_at != '0000-00-00' && $user->created_at != '')?date('d/m/y',strtotime($user->created_at)):''}}</td>
                           <td class="roundright">
                           
							   <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                          <a class="dropdown-item" href="{{url("opslogin/marketplace/reports/$user->ref_id")}}">View All Reports</a>
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




