@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $moveinout =  $permission->check_menu_permission(40,$permission->role_id,1);
   $renovation =  $permission->check_menu_permission(41,$permission->role_id,1);
   $door =  $permission->check_menu_permission(42,$permission->role_id,1);
   $vehicle =  $permission->check_menu_permission(43,$permission->role_id,1);
   $mailing =  $permission->check_menu_permission(44,$permission->role_id,1);
   $particular =  $permission->check_menu_permission(45,$permission->role_id,1);
   $permission = $permission->check_permission(45,$permission->role_id); 
@endphp

<div class="status">
  <h1>update particulars applications</h1>
</div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if(isset($moveinout->view) && $moveinout->view==1 )
                     <li ><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     @endif
                     @if(isset($renovation->view) && $renovation->view==1 )
                     <li ><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     @endif
                     @if(isset($door->view) && $door->view==1 )
                     <li ><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     @endif
                     @if(isset($vehicle->view) && $vehicle->view==1 )
                     <li ><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
                     @endif
                     @if(isset($mailing->view) && $mailing->view==1 )
                     <li><a href="{{url('/opslogin/eform/changeaddress#ef')}}"> Mailing Address </a></li>
                     @endif
                     @if(isset($particular->view) && $particular->view==1 )
                     <li class="activeul"><a href="{{url('/opslogin/eform/particular#ef')}}">Particulars </a></li>
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

<form action="{{url('/opslogin/eform/particular/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                        <div class="form-group ">
                              <label class="">ticket: 
                              </label>
                                 <input  type="text" class="form-control" name="ticket" id="ticket" value="<?php echo(isset($ticket)?$ticket:'');?>">
                            
                           </div>
						      </div>
							   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">unit: 
                              
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                              </div>
                              </div>
                            <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">name: 
                              </label>
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                             
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">status: 
                              </label>
                                  {{ Form::select('status', ['' => '--ALL--',0=>'NEW','3'=>'APPROVED',2=>'IN PROGRESS',1=>"CANCELLED","4"=>"REJECTED"], $status, ['class'=>'form-control','id'=>'role']) }}
                           </div>
                           </div>
                     
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                           <div class="form-group mt0-2">
                            
							    <a href="{{ url('/opslogin/exportparticular?option='.$option.'&ticket='.$ticket.'&unit='.$unit.'&name='.$name.'&status='.$status) }}" class="submit  float-right">print</a>
                         <a href="{{url("/opslogin/eform/particular")}}"  class="submit mr-2 ml-2 float-right">clear</a>
							  
                         <button type="submit" class="submit  float-right">search</button> 
                        </div> 
                          
                        </div>
                     </div>
                  </form>

<div class="overflowscroll2">
                <table class="gap">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/form/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($forms)

                       @foreach($forms as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->getunit->unit)?Crypt::decryptString($form->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                             <td class="spacer">@php
                  if(isset($form->status)){
                     if($form->status==0)
                        echo "NEW";
                     else if($form->status==1)
                        echo "CANCELLED";
                     else if($form->status==3)
                        echo "APPROVED";
                     else if($form->status==2)
                        echo "IN PROGRESS";
                     else 
                        echo "REJECTED";
                  
                  }
                  @endphp</td>
                           
                           <td  class="roundright">
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{$visitor_app_url}}/particulars-pdf/{{$form->id}}" target="blank">List</a>

                           <a class="dropdown-item" href="{{url("opslogin/eform/particular/$form->id/edit")}}" >Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a  class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/particular/delete/$form->id")}}');">Delete</a>
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
						@if ($forms->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($forms->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $forms->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($forms->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $forms->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($forms->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $forms->lastPage()) as $i)
									@if($i >= $forms->currentPage() - 2 && $i <= $forms->currentPage() + 2)
										@if ($i == $forms->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $forms->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($forms->currentPage() < $forms->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($forms->currentPage() < $forms->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $forms->appends($_GET)->url($forms->lastPage()) }}">{{ $forms->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($forms->hasMorePages())
									<li><a href="{{ $forms->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
               </div>
@endsection



