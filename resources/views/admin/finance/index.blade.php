@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(61,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>Invoice Batch List</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                     <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     @endif
                     <li   class="activeul"><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                     <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                     <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
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

   <form action="{{url('/opslogin/invoice/batchsearch')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-4">
                        
                           <div class="form-group">
                              <label class="">batch no:
                              </label>
                                 <input  type="text" name="batch_file_no" class="form-control" value="<?php echo(isset($batch_file_no)?$batch_file_no:'');?>" >
                           </div>
                           </div>
                         <div class="col-lg-4">
                           <div class="form-group ">
                              <label class=""> batch month 
                              </label>
                              <div id="sandbox2">
						               <input type="text" id="datepickermonth" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
				                  </div>
                           </div>

                          
                        </div>
                        <div class="col-lg-4">
                           <div class="form-group mt0-4">
                           <a href="{{url("/opslogin/invoice")}}"  class="submit ml-2 float-right">clear</a>
                           <button type="submit" class="submit  float-right">search</button>  
                        </div>
                         
                           
                        </div>
                     </div>
                  </form>
                  <div>

       <div class="overflowscroll2">
                <table class="gap">
                  <!--<div class="col-lg-12">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/invoice/create")}}"  class="submit mt-0 float-left" style="width:190px;     margin: 0 12px;"> CREATE INVOICE</a>
                           </div>
                    </div> -->
                     <thead>
                        <tr>
                          
                           <th>batch no</th>
                           <th>no.of invoice(s)</th>
                           <th>created by</th>
                           <th>created date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($invoices)

                        @foreach($invoices as $k => $inv)
                        @php
                           //$invObj = new \App\Models\v2\FinanceInvoice();
                           //$result = $invObj->invoicecounts($inv->id);
                           
                        @endphp
                        <tr>
                           <td  class="roundleft">{{$inv->batch_no}}</td>
                           <td  class="spacer">{{isset($inv->invoices)?$inv->invoices->count():''}}</td>
                           <td  class="spacer">{{isset($inv->admininfo->name)?Crypt::decryptString($inv->admininfo->name):''}}</td>
                           <td  class="spacer">{{date('d/m/y',strtotime($inv->created_at))}}</td>
                           
                           <td  class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                        @if(isset($permission) && $permission->view==1)
                           <a class="dropdown-item" href="{{$visitor_app_url}}/batchinvoices/{{$inv->id}}" target="blank">Print PDF</a>
                           @endif
                           @if(isset($permission) && $permission->view==1)
                           <a class="dropdown-item" href="{{url("opslogin/invoice/lists/$inv->id")}}" >View</a>
                           
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/batchdelete/$inv->id")}}');" >Delete</a>
                           @endif
                           @if(isset($permission) && $permission->edit==1)
                              @if($inv->notification_status ==0)
                                 <a class="dropdown-item" href="{{url("opslogin/invoice/sendnotification/$inv->id")}}" >Send Notification</a>
                              @else
                                 <a class="dropdown-item" href="#" onclick="resend_notification('{{url("opslogin/invoice/sendnotification/$inv->id")}}');" >Send Notification</a>
                              @endif
                           
                           @endif
                                    </div>
                                 </div>
                          
                           </td>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($invoices->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($invoices->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $invoices->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($invoices->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $invoices->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($invoices->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $invoices->lastPage()) as $i)
									@if($i >= $invoices->currentPage() - 2 && $i <= $invoices->currentPage() + 2)
										@if ($i == $invoices->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $invoices->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($invoices->currentPage() < $invoices->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($invoices->currentPage() < $invoices->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $invoices->appends($_GET)->url($invoices->lastPage()) }}">{{ $invoices->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($invoices->hasMorePages())
									<li><a href="{{ $invoices->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

                </div>
@endsection
