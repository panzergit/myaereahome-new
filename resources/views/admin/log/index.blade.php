@extends('layouts.adminnew')




@section('content')


<div class="status mtsuperadmin">
  <h1>user management activity log</h1>
</div>

  <div>
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
  @endif
   
  <form action="{{url('/opslogin/loghistory/search')}}" method="get" role="search" class="forunit">
           
                      <div class="row asignbg">
                        
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">first name: 
                             
                              </label>
                                 <input  type="text" class="form-control" name="first_name" id="first_name" value="<?php echo(isset($first_name)?$first_name:'');?>">
                            
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">last name: 
                              
                              </label>
                                 <input  type="text" class="form-control" name="last_name" id="last_name" value="<?php echo(isset($last_name)?$last_name:'');?>">
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">property: 
                              </label>
                                  {{ Form::select('property', ['' => '--Select Property--'] + $properties, $property, ['class'=>'form-control','id'=>'property']) }}
                            
                           </div>
                           </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
                           <label class="">Email: 
                              </label>
						    <input  type="text" class="form-control" name="email" id="email" value="<?php echo(isset($email)?$email:'');?>" placeholder="Enter email">
						    </div>
                        </div>
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6 mt0-3">
                          
						  
						    <a href="{{url("/opslogin/loghistory/")}}"  class="submit ml-2 mr-2  float-right">clear</a>
                              <button type="submit" class="submit  float-right">search</button>
                        </div>
                     
                     </div>
                  </form>


                   <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                           <th>property</th>
                           <th>modified by</th>
                           <th>status</th>
                           <th>user id</th>
                           <th>first name</th>
                           <th>last name</th>
                           <th>email</th>
                           <th>modified date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($lists)
                        @foreach($lists as $k => $user)
                        <tr>
                           <td class="spacer">
                              {{isset($user->propertyinfo->company_name)?$user->propertyinfo->company_name:''}}
                           </td>

                           <td class="spacer">{{isset($user->adminInfo->name)?Crypt::decryptString($user->adminInfo->name):''}}</td>
                           <td class="spacer">{{isset($user->notes)?$user->notes:''}}</td>
                           <td class="spacer">{{$user->ref_id}} </td>
                           <td class="spacer">{{isset($user->userInfo->first_name)?Crypt::decryptString($user->userInfo->first_name):''}}</td>
                           <td class="spacer">{{isset($user->userInfo->last_name)?Crypt::decryptString($user->userInfo->last_name):''}}</td>
                           <td class="spacer">{{isset($user->userInfo->getuser->email)?$user->userInfo->getuser->email:''}}</td>
                           <td class="spacer">{{($user->created_at != '0000-00-00' && $user->created_at != '')?date('d/m/y',strtotime($user->created_at)):''}}</td>
                           <td class="roundright">
                           
							   <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                      
									  <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/loghistory/delete/$user->id")}}');">Delete</a>
                          
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




