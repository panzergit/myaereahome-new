@extends('layouts.adminnew')

@php
$reportobj = new \App\Models\v7\ChatBox();
$newreport_count = $reportobj->report_new_count();
@endphp
<style>
.allnotif{    position: relative;}
.allnotif span{       right: -18px;
    top: -10px;
    padding: 2px 7px;}
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
.pl30{    padding-left: 30px!important;}
</style>
@section('content')


<div class="status mtsuperadmin">
  <h1>ResiChat</h1>
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
                     <li   class="activeul "><a href="#">Summary </a></li>
                     <li><a href="{{url('/opslogin/resichat/allreports')}}" class="allnotif">All Reports <span class="notification">{{$newreport_count}}</span></a> </li>
                     <li class="pl30"><a href="{{url('/opslogin/resichat/blockedusers')}}" >Blocked Users</a> </li>
                    
                  </ul>
               </div>
               </div>
  <form action="{{url('/opslogin/resichat/search')}}" method="get" role="search" class="forunit">
           
                      <div class="row asignbg">
                        
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">name: 
                              </label>
                                 <input  type="text" class="form-control" name="first_name" id="first_name" value="<?php echo(isset($first_name)?$first_name:'');?>">
                            
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">ticket: 
                              
                              </label>
                                 <input  type="text" class="form-control" name="ticket" id="last_name" value="<?php echo(isset($ticket)?$ticket:'');?>">
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">Category: 
                              </label>
                                  {{ Form::select('category', ['' => '--Select Category--'] + $categories, $category, ['class'=>'form-control','id'=>'property']) }}
                            
                           </div>
                           </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
                           <label class="">Subject: 
                              </label>
						    <input  type="text" class="form-control" name="subject" id="subject" value="<?php echo(isset($subject)?$subject:'');?>">
						    </div>
                        </div>
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6 mt0-3">
                          
						  
						    <a href="{{url("/opslogin/resichat/")}}"  class="submit ml-2 mr-2  float-right">clear</a>
                              <button type="submit" class="submit  float-right">search</button>
                        </div>
                     
                     </div>
                  </form>


                   <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                           <th>created by</th>
                           <th>ticket</th>
                           <th>subject</th>
                           <th>category</th>
                           <th>Comments</th>
                           <th style="    width: 85px;">Report</th>
                           <th>modified date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($lists)
                        @foreach($lists as $k => $user)
                        <tr>
                          
                           <td class="roundleft">{{isset($user->user->name)?Crypt::decryptString($user->user->name):''}}</td>
                           <td class="spacer">{{$user->ticket}} </td>
                           <td class="spacer">{{$user->subject}} </td>
                           <td class="spacer">{{isset($user->cat_info->name)?$user->cat_info->name:''}} </td>
                           <td class="spacer">{{$user->comment_count()}} </td>
                           <td class="spacer">{{$user->report_count()}}</td>
                           <td class="spacer">{{($user->created_at != '0000-00-00' && $user->created_at != '')?date('d/m/y',strtotime($user->created_at)):''}}</td>
                           <td class="roundright">
                           
							   <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                          @if($user->status ==1)
                                             <a class="dropdown-item" href="{{url("opslogin/resichat/deactivate/$user->id")}}">De-Activate</a>
                                          @else
                                             <a class="dropdown-item" href="{{url("opslogin/resichat/activate/$user->id")}}">Activate</a>
                                          @endif
                                          <a class="dropdown-item" href="{{url("opslogin/resichat/replies/$user->id")}}">Comments</a>
                                          <a class="dropdown-item" href="{{url("opslogin/resichat/reports/$user->id")}}">Reports</a>
                                          <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/resichat/delete/$user->id")}}');">Delete</a>
                          
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




