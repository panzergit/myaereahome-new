@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = $permission->id;
   $account_id = Auth::user()->account_id;
   $reg_count = $permission->noOfReg($account_id);
   $permission = $permission->check_permission(24,$permission->role_id); 
@endphp

<div class="status">
  <h1>unit information</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                 <ul class="summarytab">
                     <li  ><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li ><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li  ><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li class="activeul"><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/registrations')}}">Registrations  @if(isset($reg_count) && $reg_count >0 )
                  <span class="notification17">{{$reg_count}}</span>
                  @endif</a> </li>
                   @endif

</ul>   
               </div>
               </div>
               <div id="myModalcnf" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Message</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Building and Unit should be created before bulk upload of User.<br/><br />Are you sure want to continue?</p>
			</div>
			<div class="modal-footer justify-content-center">
         <a href="{{url("/opslogin/user/uploadcsv")}}" class="btn btn-secondary">Confim</a>
				<a type="button" class="btn btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div> 
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<form action="{{url('/opslogin/unitlist/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                        <div class="form-group">
                             
                                  {{ Form::select('building', ['' => '--Building--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
                             
                           </div>
						   </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                           
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list" placeholder="Enter Unit Number">
                           </div>
                           </div>
                           <div class="col-lg-3">
                           </div>
                        
                        <div class="col-lg-3">
						 <a href="{{url("/opslogin/unitlist")}}"  class="submit ml-2 float-right">clear</a>
                              <button type="submit" class="submit float-right">search</button>
                        
                           <!--div class="form-group row">
                           <div class="col-sm-12">
                               <a href="{{url("/opslogin/unitlist")}}"  class="submit nt0 float-left">clear</a>
                           </div> </div-->
                           
                        </div>
                     

                     </div>
                  </form>
<div class="overflowscroll2">
                  <table class="gap ">
                  
                     <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>building</th>
                           <th>unit no</th>
                           <th>id</th>
                           <th>size</th>
                           <th>share value</th>
                           <th>view summary</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($units)

                       @foreach($units as $k => $dept)
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                              <td class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                           @endif
                           <td class="spacer">{{isset($dept->buildinginfo->building)?$dept->buildinginfo->building:''}}</td>
                           <td class="spacer">{{Crypt::decryptString($dept->unit)}}</td>
                           <td class="spacer">{{Crypt::decryptString($dept->code)}}</td>
                           <td class="spacer"> {{$dept->size}}</td>
                           <td class="spacer">{{intval($dept->share_amount)}}</td>
                           
                           <td  class="roundright">
                          
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/unit_summary/$dept->id")}}"><img src="{{url('assets/admin/img/Condo.png')}}" class="viewimg phvert"></a>

                           @endif
                          
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  </div>
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

