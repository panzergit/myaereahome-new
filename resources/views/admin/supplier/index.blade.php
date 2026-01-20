@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(79,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.containeruserl {
    display: block;
    font: normal normal bold 12px/20px Helvetica!important;
    color: #5D5D5D!important;
    position: relative;
    padding-left: 25px;
    margin-bottom: 0px;
    cursor: pointer;
    font-size: 14px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.containeruserl input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.checkmarkuserl {
    position: absolute;
    top: 1px;
    left: 0;
    height: 19px;
    width: 19px;
    background-color: #D0D0D0;
}
.containeruserl .checkmarkuserl:after {
    left: 7px;
    top: 2px;
    width: 7px;
    height: 13px;
    border: solid #8F7F65;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}


.containeruserl:hover input ~ .checkmarkuserl {
  background-color: #ccc;
}

.containeruserl input:checked ~ .checkmarkuserl {
  background-color: #DFCFB5;
}

.checkmarkuserl:after {
  content: "";
  position: absolute;
  display: none;
}

.containeruserl input:checked ~ .checkmarkuserl:after {
  display: block;
}
</style>
<div class="status">
  <h1>supplier management </h1>
</div>
<div class="row">
               <div class="col-lg-12">
              <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/supplier')}}">Summary</a></li>
                    
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="{{url('/opslogin/supplier/create')}}">Create new supplier</a></li>
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

                  
                      <div class="overflowscroll2">
                     <div class="col-lg-12 p-0 ">
                        <table class="gap">
                           <tr>
                              <th>Supplier Display Name</th>
                              <th>Company Name</th>
                              <th>Email </th>
                              <th>Phone Number</th>
                              <th>Balance</th>
                              <th>Balance As Of</th>

                              <th>Action</th>
                           </tr>
                           @if($lists)
                              @foreach($lists as $k => $list)
                                 <tr>
                                    <td class="spacer">{{$list->supplier_display_name}}</td>
                                    <td class="spacer">{{$list->company_name}}</td>
                                    <td class='spacer'>{{$list->email}}</td>
                                    <td class='spacer'>{{$list->phone_number}}</td>
                                    <td class='spacer'>{{number_format($list->opening_balance,2)}}</td>
                                    <td class='spacer'>{{($list->opening_balance_date!='')?date('d/m/y',strtotime($list->opening_balance_date)):''}}</td>
                                    <td class='roundright'>
                                       <div class="dropdown">
                                          <div  class=" dropdown-toggle" data-toggle="dropdown">
                                             <div class="three-dots"></div>
                                          </div>
                                          <div class="dropdown-menu">
                                          @if(isset($permission) && $permission->edit==1)
                                             <a class="dropdown-item" href="{{url("opslogin/supplier/$list->id/edit")}}">Edit</a>
                                          @endif
                                          @if(isset($permission) && $permission->delete==1)
                                             <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/supplier/delete/$list->id")}}');">Delete</a>
                                          @endif
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                              @endforeach
                           @endif
                          
                        </table>
                     </div>
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

