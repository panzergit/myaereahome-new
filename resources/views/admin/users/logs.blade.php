@extends('layouts.adminnew')




@section('content')


<div class="status">
  <h1>user log-in history </h1>
</div>

  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
  @endif
    <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
					    <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
					  <li    class="activeul"><a href="{{url('/opslogin/user/logs')}}">Login History</a></li>
                  </ul>
               </div>
               </div>
  <form action="{{url('/opslogin/user/logsearch')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-4">
                        <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">
                              <label class="containerbut">property: 
                              <input type="radio" name="option" value="property" id="option2" checked="">
                              
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-8 col-8">
                                  {{ Form::select('property', ['a' => 'Property'] + $properties, $property, ['class'=>'form-control','id'=>'prop']) }}
                              </div>
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">
                              <label class="containerbut">unit: 
                              <input type="radio" name="option" value="unit" id="unit" {{($option=='unit')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-8 col-8">
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                              </div>
                           </div>
                           </div>
                          <div class="col-lg-5">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">
                              <label class="containerbut">first name: 
                              <input type="radio" name="option" value="name" id="option1" {{($option=='name')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-8 col-8">
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                              </div>
                           </div>
                           </div>
                            <div class="col-lg-4">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">
                              <label class="containerbut">role: 
                              <input type="radio" name="option" value="role" id="option2" {{($option=='role')?'checked':''}}>
                              <span class="checkmarkbut"></span>
                              </label>
                              </label>
                              <div class="col-sm-8 col-8">
                                  {{ Form::select('role', ['a' => '--User Role--'] + $roles, $role, ['class'=>'form-control','id'=>'role']) }}
                              </div>
                           </div>
                           </div>
                       
                          <div class="col-sm-8 mt0-3">
						    <a href="{{ url('/opslogin/exportlogs?option='.$option.'&unit='.$unit.'&name='.$name.'&role='.$role) }}" class="submit  float-right">print</a>
						     <a href="{{url("/opslogin/user/logs")}}"  class="submit mr-2 ml-2 float-right">clear</a>
                              <button type="submit" class="submit  float-right">search</button>
                           </div> 

                     </div>
                  </form>

  <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                           <th>property</th>
                           <th>unit</th>
                           <th>name</th>
                           <th>login from</th>
                           <th>devide info</th>
                           <th>log-in date</th>
                           <th>log-in time</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($logs)
                        @foreach($logs as $k => $log)
                        <tr>
                           <td class="roundleft">{{isset($log->property)?$log->property->company_name:''}}</td>
                           <td class="spacer">{{isset($log->user->getunit->unit)?Crypt::decryptString($log->user->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($log->user->name)?$log->user->name:''}}</td>
                           <td class="spacer">{{($log->login_from==1)?"ISO":"Android"}}</td>
                           <td class="spacer">{{$log->device_info}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($log->created_at))}}</td>
                           <td class="roundright">{{date('H:i',strtotime($log->created_at))}}</td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
</div>
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($logs->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($logs->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $logs->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($logs->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $logs->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($logs->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $logs->lastPage()) as $i)
									@if($i >= $logs->currentPage() - 2 && $i <= $logs->currentPage() + 2)
										@if ($i == $logs->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $logs->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($logs->currentPage() < $logs->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($logs->currentPage() < $logs->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $logs->appends($_GET)->url($logs->lastPage()) }}">{{ $logs->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($logs->hasMorePages())
									<li><a href="{{ $logs->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
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
<script>
	$(document).ready(function(e){
		$('#exportlogs').click(function() {
			var unit = $('#unit_list').val();
			var name = $('#name').val();
			var role = $('#role').val();
			var option = '';
			
			if($("#option").is(":checked")) {
				option = 'unit';
			} else if($("#option1").is(":checked")) {
				option = 'name';
			} else if($("#option2").is(":checked")) {
				option = 'role';
			}
			
			var url = "{{ url('/opslogin/exportlogs') }}?unit="+unit+"&name="+name+"&role="+role+"&option="+option;
			window.open(url, '_blank');
		});		    
	});
</script>
@endsection




