@extends('layouts.adminnew')


@section('content')
@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(4,$permission->role_id); 
@endphp

<div class="status">
  <h1>Appointment For Defect Inspection</h1>
</div>

  <div class="containerwidth">
@if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
     <form action="{{url('opslogin/inspection_appt/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row">
                        <div class="col-lg-5">
                           <div class="form-group row">
                              <label  class="col-sm-5 col-6 col-form-label">
                              <label class="containerbut">UNIT: 
                              <input type="radio" name="option" value="unit" checked="">
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-7 col-6">
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                              </div>
                           </div>
                           @if(1==2)
                           <div class="form-group row">
                              <label  class="col-sm-5 col-6 col-form-label">
                              <label class="containerbut">NAME: 
                              <input type="radio" name="option" value="name" {{($option=='name')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-7 col-6">
                                 <input  type="text" class="form-control" name="name" value="<?php echo(isset($name)?$name:'');?>">
                              </div>
                           </div>
                           @endif
                           <div class="form-group row">
                              <label  class="col-sm-5 col-6 col-form-label">
                              <label class="containerbut"> MONTH 
                              <input type="radio" name="option" value="month" {{($option=='month')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-7 col-6">
                                 <div id="sandbox2">
						<input id="datepickermonth" type="text" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
				                           </div>
										
                              </div>
                           </div>

                           <div class="form-group row">
                              <label  class="col-sm-5 col-6 col-form-label">
                              <label class="containerbut">STATUS: 
                              <input type="radio" name="option" value="status" {{($option=='status')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-7 col-6">
                                  {{ Form::select('status', ['' => '--All Status--','1'=>'Cancelled','2'=>'On Schedule','3'=>"Done"], $status, ['class'=>'form-control','id'=>'status']) }}
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-2">
                           <div class="form-group row">
                           <div class="col-12">
                              <button type="submit" class="submit nt0 float-left">SEARCH</button>
                           </div></div>
                           <div class="form-group row">
                           <div class="col-12">
                               <a href="{{url("opslogin/inspection_appt/lists")}}"  class="submit nt0 float-left">CLEAR</a>
                           </div></div>
                           <div class="form-group row">
                              <div class="col-12">
                             <a href="{{ url('/opslogin/exportinspection?option='.$option.'&unit='.$unit.'&name='.$name.'&status='.$status) }}" class="submit nt0 float-left">PRINT</a>
                           </div></div>
                        </div>
                        <div class="col-lg-2">
                           
                           
                        </div>

                     </div>
                  </form>

                  <div>
                  <table class="table usertable usertable2">
                     <thead>
                        <tr>
                           <th>UNIT NO</th>
                           <th>BOOKED BY</th>
                           <th>APPOINTMENT DATE</th>
                           <th>APPOINTMENT  TIME</th>
                           <th>STATUS</th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($units)

                        @foreach($units as $k => $appt)
                        <tr>
                           <td>{{isset($appt->getunit->unit)?$appt->getunit->unit:''}}</td>
                           <td>{{isset($appt->getname->name)?$appt->getname->name:''}}</td>
                           <td>{{date('d/m/y',strtotime($appt->appt_date))}}</td>
                           <td>{{$appt->appt_time}}</td>
                           <td>@php
                  if(isset($appt->status)){
                     if($appt->status==0)
                        echo "New";
                     else  if($appt->status==1)
                        echo "Cancelled";
                     else  if($appt->status==2)
                        echo "On Schedule";
                     else  if($appt->status==3)
                        echo "Done";
                     else  if($appt->status==4)
                        echo "In Progress";

                  }
                  @endphp</td>

                           <td>
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/inspection_appt/$appt->id/edit")}}" data-toggle="tooltip" data-placement="top" title="Edit"><img src="{{url('assets/admin/img/edit.png')}}"></a>
                           @endif
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  <div class="col-lg-10">
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
@endsection
