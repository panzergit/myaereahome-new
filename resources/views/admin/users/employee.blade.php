@extends('layouts.adminnew')




@section('content')


<div class="status mtsuperadmin">
  <h1>manage user lists</h1>
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
                     <li   class="activeul"><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
					    <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
					  <li><a href="{{url('/opslogin/user/logs')}}">Login History</a></li>
                  </ul>
               </div>
               </div>
  <form action="{{url('/opslogin/user/adminsearch')}}" method="get" role="search" class="forunit">
           
                      <div class="row asignbg">
                        
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">first name: 
                             
                              </label>
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                            
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
                          
						    <!--<a href="{{ url('/opslogin/exportusers?option='.$option.'&unit='.$unit.'&name='.$name.'&role='.$role) }}" class="submit  float-right">print</a>-->
						    <a href="{{url("/opslogin/user")}}"  class="submit ml-2 mr-2  float-right">clear</a>
                              <button type="submit" class="submit  float-right">search</button>
                        </div>
                     
                     </div>
                  </form>


                   <div class="overflowscroll2">
                        <table class="gap">
                     <thead>
                        <tr>
                        @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           
                           <th>photo</th>
                           <th>first name</th>
                           <th>last name</th>
                           <th>assigned role</th>
                           <th>password</th>
                           <th>app</th>
                           <th>contact</th>
                           <th>start date</th>
                           <th>end date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($users)
                        @foreach($users as $k => $user)
                        <tr>
                        @if(Auth::user()->role_id ==1)
                        <td class="roundleft">
                        @if($property >0)
                           {{$property_name}}
                        @else
                           {{isset($user->getuser->propertyinfo->company_name)?$user->getuser->propertyinfo->company_name:''}}
                        @endif
                        </td>
                        @endif
                           
                           <td class="spacer">
                              @if(isset($user->profile_picture) && $user->profile_picture !='')
                                 <a href="{{$file_path}}/{{$user->profile_picture}}" target="_blank">
                                    <img src="{{$file_path}}/{{$user->profile_picture}}" class="viewimg phvert">
                                 </a>
                              @endif
                           </td>
                           <td class="spacer">{{Crypt::decryptString($user->first_name)}}  </td>
                           <td class='spacer'><a href="#" alt="{{isset($user->last_name)?Crypt::decryptString($user->last_name):''}}" title="{{isset($user->last_name)?Crypt::decryptString($user->last_name):''}}" style="color:#5D5D5D" >{{isset($user->last_name)?Str::limit(Crypt::decryptString($user->last_name),20):''}}</a></td>                           <td class="spacer">{{isset($user->getuser->role->name)?$user->getuser->role->name:''}}
                           @if($user->primary_contact ==1)<span style="color:red">* </span>@endif
                           </td>
                           <td class="spacer">{{($user->password!='')?'Yes':'No'}}</td>
                           <td class="spacer">
                           @php
                           if(isset($user->getos))
                           {
                              if($user->getos->login_from==1)
                                 echo "IOS";
                              else  if($user->getos->login_from==2)
                                 echo "Android";
                           }
                           
                           @endphp
                           </td>
                           <td class='spacer'>{{isset($user->phone)?Crypt::decryptString($user->phone):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($user->created_at))}}</td>
                           <td class="spacer">{{($user->deactivated_date != '0000-00-00' && $user->deactivated_date != '')?date('d/m/y',strtotime($user->deactivated_date)):''}}</td>
                           <td class="roundright">
                            <!--<a href="{{url("admin/user/rights/$user->id")}}"><img src="{{url('assets/admin/img/confirm.png')}}" alt=""></a>-->
							   <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                       <a class="dropdown-item" href="{{url("opslogin/user/userproperties/$user->id")}}" >Assign Property</a>
                                       <a class="dropdown-item" href="{{url("opslogin/user/$user->id/edit")}}">Edit</a>
                                       <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/user/delete/$user->id")}}');">Delete</a>
                                       @if($user->status ==0)
                                          <a  class="dropdown-item" href="#" onclick="activate_record('{{url("opslogin/user/activate/$user->id")}}');">Deactive</a>
                                          @else
                                          <a  class="dropdown-item" href="#"  onclick="deactivate_record('{{url("opslogin/user/deactivate/$user->id")}}');" >Active</a>
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
						@if ($users->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($users->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $users->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($users->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $users->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($users->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $users->lastPage()) as $i)
									@if($i >= $users->currentPage() - 2 && $i <= $users->currentPage() + 2)
										@if ($i == $users->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $users->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($users->currentPage() < $users->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($users->currentPage() < $users->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $users->appends($_GET)->url($users->lastPage()) }}">{{ $users->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($users->hasMorePages())
									<li><a href="{{ $users->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
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
		$('#exportusers').click(function() {
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
			
			var url = "{{ url('/opslogin/exportusers') }}?unit="+unit+"&name="+name+"&role="+role+"&option="+option;
			window.open(url, '_blank');
		});		    
	});
</script>
@endsection




