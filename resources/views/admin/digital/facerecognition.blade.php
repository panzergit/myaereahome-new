@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(50,$permission->role_id); 
@endphp

<div class="status">
   <h1>Digital Access - FACE RECOGNITION RECORD</h1>
</div>

<div class="containerwidth">
<form action="announcement-details.html" class="forunit" autocomplete="off">
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="form-group row">
                              <label class="col-sm-3 col-form-label">
                              <label class="containerbut"> DEVICE NAME : 
                              <input type="radio" name="radio">
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-3">
                                 <input type="text" class="form-control" value="">
                              </div>
                           </div>
						    <div class="form-group row">
                              <label class="col-sm-3 col-form-label">
                              <label class="containerbut">OPEN DOOR TYPE :
                              <input type="radio" name="radio">
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-3">
                             <div id="sandbox2">
						<input id="" type="text" class="form-control" value="">
				                           </div>
                              </div>
                           </div>
						  
                           <div class="form-group row">
                              <label class="col-sm-3 col-form-label">
                              <label class="containerbut">OPEN DOOR DATE & TIME : 
                              <input type="radio" name="radio">
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-3">
							  <label class="control-label">START DATE </label>
                             <div id="sandbox">
						<input id="datepicker1" type="text" class="form-control" value="">
				                           </div>
                              </div>
							   <div class="col-sm-3">
							  <label class="control-label">END DATE </label>
                             <div id="sandbox2">
						<input id="datepicker2" type="text" class="form-control" value="">
				                           </div>
                              </div>
                           </div>
                        <div class="form-group row">
                              <label class="col-sm-3 col-form-label">
                             
                              </label>
                              <div class="col-sm-3">
							  <label class="control-label">START TIME </label>
                          
						<input id="" type="time" class="form-control" value="">
				                           
                              </div>
							   <div class="col-sm-3">
							  <label class="control-label">END TIME </label>
                             <div id="sandbox288">
						<input id="datetime" type="time" class="form-control" value="">
				                           </div>
                              </div>
                           </div>
						    <div class="form-group row">
                             
                              <div class="col-sm-9">
							 <button type="submit" class="submit nt0 float-right">SEARCH</button>
                              </div>
							  
                           </div>
                     </div>
                  </form>
                  <table class="table usertable ">
                    <!--<div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/admin/configuration/faceid/create")}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div> -->
                     <thead>
                        <tr>
                          
                           <th>DATE</th>
                           <th>UNIT</th>
                           <th>SUBMITTED BY</th>
                           <th>PHOTO</th>
                           <th>REALTIONSHIP</th>
                           <th>APPROVED DATE</th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($faceids)

                       @foreach($faceids as $k => $faceid)
                        <tr>
                           <td>{{date('d/m/y',strtotime($faceid->created_at))}}</td>
                           <td>{{isset($faceid->user->getunit->unit)?Crypt::decryptString($faceid->user->getunit->unit):''}}</td>
                           <td>{{isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):''}}</td>
                           <td> @if(isset($faceid->face_picture))
                                 <a href="{{$file_path}}/{{$faceid->face_picture}}" target="_blank">
                                    <img src="{{$file_path}}/{{$faceid->face_picture}}" class="viewimg phvert">
                                 </a>
                              @endif</td>
                           <td>{{isset($faceid->optionname->option)?$faceid->optionname->option:''}}</td>
                           <td>{{date('d/m/y',strtotime($faceid->updated_at))}}</td>

                           <td>
                          
                           @if(isset($permission) && $permission->delete==1)
                           <a href="#" onclick="delete_record('{{url("opslogin/faceid/delete/$faceid->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                           @endif
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  <div class="col-lg-10">
					<div  class="form-group row">
						@if ($faceids->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($faceids->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $faceids->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($faceids->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $faceids->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($faceids->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $faceids->lastPage()) as $i)
									@if($i >= $faceids->currentPage() - 2 && $i <= $faceids->currentPage() + 2)
										@if ($i == $faceids->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $faceids->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($faceids->currentPage() < $faceids->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($faceids->currentPage() < $faceids->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $faceids->appends($_GET)->url($faceids->lastPage()) }}">{{ $faceids->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($faceids->hasMorePages())
									<li><a href="{{ $faceids->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
               </div>
@endsection


