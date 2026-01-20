@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $rm =  $permission->check_menu_permission(61,$permission->role_id,1);
   $batch =  $permission->check_menu_permission(71,$permission->role_id,1);
   $individual =  $permission->check_menu_permission(72,$permission->role_id,1);

   $permission = $permission->check_permission(71,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>Manage Invoice</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                    
                    @if(isset($rm) && $rm->view==1 && $admin_id !=1)
                    <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                    @endif
                    @if(isset($rm) && $rm->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                    @endif
                    @if(isset($batch) && $batch->view==1 && $admin_id !=1)
                    <li   class="activeul" ><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                    @endif
                    @if(isset($individual) && $individual->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                    @endif
                    @if(isset($permission) && $permission->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                    @endif
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

   <form action="{{url('/opslogin/invoice/search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                        <div class="form-group ">
                              <label class="">invoice no:
                              </label>
                                 <input  type="text" name="invoice_no" class="form-control" value="<?php echo(isset($invoice_no)?$invoice_no:'');?>" >
                           </div>
						   </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">batch no:
                              </label>
                                 <input  type="text" name="batch_file_no" class="form-control" value="<?php echo(isset($batch_file_no)?$batch_file_no:'');?>" >
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
                           <div class="form-group">
                              <label class=""> invoice date 
                              </label>
                              <div id="sandbox2">
						               <input type="text" id="fromdate" class="form-control" name="month" value="<?php echo(isset($month)?$month:'');?>">
                              </div>
                           </div>
                           </div>
 <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">status: 
                              </label>
                                  {{ Form::select('status', ['' => '--All Status--','1'=>'Payment Pending','2'=>'Partially Paid','3'=>"Paid"], $status, ['class'=>'form-control','id'=>'status']) }}
                           </div>
                           </div>
                        
                        <div class="col-lg-6"></div>
                        <div class="col-lg-3">
                           <div class="form-group mt0-4">
                              <input type="hidden" name="info_id" value="{{$info_id}}">
                             
							   <a href="{{url("/opslogin/invoice")}}"  class="submit ml-2 float-right">clear</a>
                        <button type="submit" class="submit  float-right">search</button>
							    <!--a href="#" class="submit  float-right">print</a-->
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
                    </div>  -->
                     <thead>
                        <tr>
                           <th>invoice</th>
                           <th>batch no</th>
                           <th>building</th>
                           <th>unit no</th>
                           <th>total amount</th>
                           <th>created at</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($invoices)

                        @foreach($invoices as $k => $appt)
                        <tr>
                           <td  class="roundleft">{{$appt->invoice_no}}</td>
                           <td  class="spacer">{{$appt->batch_file_no}}</td>
                           <td class="spacer">{{isset($appt->getunit->buildinginfo->building)?$appt->getunit->buildinginfo->building:''}}</td>
                           <td class="spacer">{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                           <td class="spacer">
                           @php 
                              if($appt->payable_amount > 0)
                                 echo number_format($appt->payable_amount,2);
                              else
                                 echo "(".number_format((0-$appt->payable_amount),2).")"; 
                           @endphp
                           </td>
                           <td class="spacer">{{date('d/m/y',strtotime($appt->created_at))}}</td>
                           <td class="spacer">
                              @php
                                 if(isset($appt->status)){
                                    if($appt->status==1)
                                       echo "Payment Pending";
                                    else  if($appt->status==2)
                                       echo "Partially Paid";
                                    else 
                                       echo "Paid";
                                 
                                 }
                              @endphp
                           </td>

                           <td class="roundright">
						     <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                     @if(isset($permission) && $permission->view==1)
                           <a class="dropdown-item" href="{{$visitor_app_url}}/invoice-pdf/{{$appt->id}}" target="_blank">Print PDF</a>
                          
                           <a class="dropdown-item" href="{{url("opslogin/invoiceview/$appt->id")}}">View</a>
                           @endif
                           @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/invoice/payment/$appt->id")}}">Payment</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/invoicedelete/$appt->id")}}');">Delete</a>
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
