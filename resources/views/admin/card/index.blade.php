@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(38,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>access card management</h1>
</div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/card')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1 && 1==2)
                        <li><a href="{{url('/opslogin/card/create')}}">Add new card</a></li>
                     @endif
                  </ul>
               </div>
               </div>
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<form action="{{url('/opslogin/card/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-2">
                        <div class="form-group ">
                              <label class="">card:
                              </label>
                                 <input  type="text" class="form-control" name="card" id="card" value="<?php echo(isset($card)?$card:'');?>">
                             
                           </div>
						   </div>
                     <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">Building:  </label>
                              </label>
                                 <input  type="text" name="building" class="form-control" value="<?php echo(isset($building)?$building:'');?>" id="building_list">
                           </div>
                           </div>
						    <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">unit:  </label>
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
                           </div>
                            <div class="col-lg-2">
                           <div class="form-group ">
                              <label class="">status: 
                              </label>
                                  {{ Form::select('status', ['1' => 'Active','2'=>'Inactive','3'=>'Faulty','4'=>'Loss','5'=>'Stolen'], $status, ['class'=>'form-control']) }}
                           </div>
                           </div>
                        
                        <div class="col-lg-4 col-12">
						<label class="">&nbsp;
                              </label>
                           <div class="form-group">
						    
                              
							  
							  <a href="{{ url('/opslogin/exportcard?option='.$option.'&unit='.$unit.'&card='.$card.'&status='.$status) }}" class="submit  float-right">print</a>
                       <a href="{{url("/opslogin/card")}}"  class="submit mr-2 ml-2 float-right">clear</a>
                       <button type="submit" class="submit  float-right">search</button>
                     </div>
                           <!--div class="form-group row">
                           <div class="col-sm-12">
                              
                           </div> </div>
                           <div class="form-group row">
                           <div class="col-sm-12">
                              <a href="{{ url('/opslogin/exportcard?option='.$option.'&unit='.$unit.'&card='.$card.'&status='.$status) }}" class="submit nt0 float-left">print</a>
                           </div> </div-->
                        </div>

                     </div>
                  </form>
				  <div class="overflowscroll2">
                  <table class="gap">
                
                     <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>Block</th>
                           <th>unit no</th>
                           <th>card no</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($cards)

                       @foreach($cards as $k => $dept)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td  class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td  class="spacer">{{ $dept->getbuilding->building ?? ''}}</td>
                           <td  class="spacer"> #{{isset($dept->getunit)?Crypt::decryptString($dept->getunit->unit):''}}</td>
                           <td  class="spacer">{{$dept->card}}</td>
                           <td  class="spacer"><?php
                           if($dept->status ==1)
                              echo "Active";
                           else if($dept->status ==2)
                           echo "Inactive";
                           else if($dept->status ==3)
                           echo "Faulty";
                           else if($dept->status ==4)
                           echo "Loss";
                           else if($dept->status ==5)
                           echo "Stolen";
                          
                           ?></td>
                           
                           <td  class="roundright">
						   <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                         @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/card/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/card/delete/$dept->id")}}');">Delete</a>
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
					<div  class="form-group">
						@if ($cards->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($cards->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $cards->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($cards->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $cards->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($cards->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $cards->lastPage()) as $i)
									@if($i >= $cards->currentPage() - 2 && $i <= $cards->currentPage() + 2)
										@if ($i == $cards->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $cards->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif 
								@endforeach
								@if($cards->currentPage() < $cards->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($cards->currentPage() < $cards->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $cards->appends($_GET)->url($cards->lastPage()) }}">{{ $cards->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($cards->hasMorePages())
									<li><a href="{{ $cards->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

               </div>
@endsection

