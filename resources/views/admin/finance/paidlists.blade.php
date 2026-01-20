@extends('layouts.adminnew')

<style>
   .usertable2 td:last-child {
    text-align: left!important;
}
</style>
@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $rm =  $permission->check_menu_permission(61,$permission->role_id,1);
   $batch =  $permission->check_menu_permission(71,$permission->role_id,1);
   $individual =  $permission->check_menu_permission(72,$permission->role_id,1);

   $permission = $permission->check_permission(61,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>Filter Payments</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
               @if(isset($rm) && $rm->view==1 && $admin_id !=1)
                    <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
					   @endif
                    @if(isset($rm) && $rm->create==1 && $admin_id !=1)
                    <li ><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                    @endif
                    @if(isset($batch) && $batch->view==1 && $admin_id !=1)
                    <li  ><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                    @endif
                    @if(isset($individual) && $individual->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                    @endif
                    @if(isset($permission) && $permission->view==1 && $admin_id !=1)
                    <li   class="activeul"><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                    @endif
                    @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                    @endif
                  </ul>
               </div>
               </div>
  <div>

    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<form action="{{url('/opslogin/paidlist_search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                       
                           <div class="form-group ">
                              <label class="">reference type: 
                              </label>
                                  {{ Form::select('type', ['' => '--Reference Type--'] + $types, $type, ['class'=>'form-control','id'=>'building']) }}
                           </div></div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">building: 
                              </label>
                                  {{ Form::select('building', ['' => '--Building--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">unit no: 
                              </label>
                              <input  type="text" name="unit" class="form-control" value="{{(isset($unit)?$unit:'')}}" id="unit_list">
                           
                           </div>
                           </div>
                        <div class="col-lg-3">
						  
                       <div class="form-group ">
                                 <label class=""> filter by : 
                              </label>
                                 <div id="sandbox2">
                                 {{ Form::select('date_option', ['1' => 'Invoice Date',2=>'Payment Date'], $date_option, ['class'=>'form-control']) }}
                              </div>
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                                 <label class=""> from date : 
                              </label>
                                 <div id="sandbox2">
                                    <input id="fromdate" name="fromdate" type="text" class="form-control" value="<?php echo(isset($fromdate)?$fromdate:'');?>">
                              </div>
                           </div>
                           </div>
                                     <div class="col-lg-3">                 
                           <div class="form-group ">
                                 <label class=""> to date :
                                 </label>
                                 <div id="sandbox">
                                    <input id="todate" name="todate" type="text" class="form-control" value="<?php echo(isset($todate)?$todate:'');?>">
                              </div>
                           </div>
                           </div>
                           
<div class="col-lg-3"></div>
<div class="col-lg-3">
                         <div class="form-group mt0-4">
							 
                      <a href="{{url("/opslogin/paidlists")}}"  class="submit ml-2 float-right">clear</a>
                      <button type="submit" class="submit  float-right">search</button>
                         <!--<a href="#" class="submit mt-0  clres2">PRINT</a> -->
						    
							  </div>
							  </div>

                       

                     </div>
                  </form>
				   <div class="overflowscroll2">
          <table class="gap">
                 
                     <thead>
                        <tr>
                           <th>building</th>
                           <th>unit no</th>
                           <th>invoice</th>
                           <th>invoice date</th>
                           <th>type</th>
                           <th>detail</th>
                           <th>balance($)</th>
                           <th>payment received</th>
                        </tr>
                     </thead>
                     <tbody>
                     @if($paidlists)
                        @foreach($paidlists as $k => $paidlist)
                              <tr >
                              <td class="roundleft" style="font-size:13px;">{{isset($paidlist->unitinfo->buildinginfo->building)?$paidlist->unitinfo->buildinginfo->building:''}}</td>
                                 <td class="spacer" style="font-size:13px;">#{{isset($paidlist->unitinfo->unit)?Crypt::decryptString($paidlist->unitinfo->unit):''}}</td>
                                 <td class="spacer" style="font-size:13px;"><a href='{{url("opslogin/invoice/payment/$paidlist->invoice_id")}}' target="_blank" style="color:#8F7F65">{{isset($paidlist->invoiceinfo->invoice_no)?$paidlist->invoiceinfo->invoice_no:''}}</a></td>
                                 <td class="spacer" style="font-size:13px;">{{isset($paidlist->invoiceinfo->invoice_date)?date('d/m/y',strtotime($paidlist->invoiceinfo->invoice_date)):''}}</td>
                                 <td class="spacer" style="font-size:13px;">{{isset($paidlist->referencetypes->reference_type)?$paidlist->referencetypes->reference_type:''}}</td>
                                 <td class="spacer" style="font-size:13px;">{{$paidlist->detail}}</td>
                                 <td class="spacer" style="font-size:13px;">{{$paidlist->total_amount}}</td>
                                 
                                 <td class="roundright" style="font-size:13px;">
                                 @php
                                    if(isset($paidlist->paymentdetails)){
                                       $paid_amt = 0;
                                       $payment_received_date ='';
                                       foreach($paidlist->paymentdetails as $paymentdetail){
                                          $paid_amt  += $paymentdetail->amount;
                                          $payment_received_date = $paymentdetail->payment_received_date;
                                       }
                                       if($paid_amt >0)
                                          echo "$".$paid_amt. " on ".date('d/m/y',strtotime($payment_received_date));
                                    }
                                 @endphp                                              
                              </tr>
                       @endforeach
                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-12">
					<div  class="form-group row">
						@if ($paidlists->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($paidlists->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $paidlists->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($paidlists->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $paidlists->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($paidlists->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $paidlists->lastPage()) as $i)
									@if($i >= $paidlists->currentPage() - 2 && $i <= $paidlists->currentPage() + 2)
										@if ($i == $paidlists->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $paidlists->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($paidlists->currentPage() < $paidlists->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($paidlists->currentPage() < $paidlists->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $paidlists->appends($_GET)->url($paidlists->lastPage()) }}">{{ $paidlists->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($paidlists->hasMorePages())
									<li><a href="{{ $paidlists->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	

                </div>
@endsection
