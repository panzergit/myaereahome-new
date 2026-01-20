@extends('layouts.adminnew')

<style>
   .usertable2 td:last-child {
    text-align: left!important;
}
   .w20{    width: 20px;}
   button:disabled,
button[disabled]{
  background-color: #DFCFB5!important;
  color: #5D5D5D!important;
}
.dletevms {
    text-align: center;
       margin-bottom: 15px;
    display: block;
    background: #8F7F65;
    width: 150px;
    height: 34px;
    color: #fff;
    font-weight: 600;
    padding: 6px 20px;
    border: none;
    text-transform: capitalize;
    box-shadow: 0px 2px 4px #00000029;
    border-radius: 22px;
    font: normal normal 900 14px/0px Lato;
    letter-spacing: 0.5px;
    line-height: 24px!important;
}
.dletevms:hover {
      border: none;
    color: #5D5D5D;
    background-color: #DFCFB5;
    text-decoration: none;
    outline: none;
}
.scolor span{color:#5D5D5D!important;}
</style>
@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(61,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>Manage Invoice Report</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                     <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     @endif
                     <li><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                     <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
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

<form action="{{url('/opslogin/report_search')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                        <div class="form-group ">
                              <label class="">batch no: 
                              </label>
                              <input  type="text" name="batch_file_no" class="form-control" value="<?php echo(isset($batch_file_no)?$batch_file_no:'');?>" >
                           </div>
                          </div>
						  <div class="col-lg-3">
						         <div class="form-group">
                              <label class="">invoice no: 
                              </label>
                              <input  type="text" name="invoice_no" class="form-control" value="<?php echo(isset($invoice_no)?$invoice_no:'');?>" >
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">building: 
                              </label>
                                  {{ Form::select('building', ['' => '--Building--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">unit no: 
                              </label>
                              <input  type="text" name="unit" class="form-control" value="{{(isset($unit)?$unit:'')}}" id="unit_list">
                            
                           </div>
                           </div>
                        <div class="col-lg-3">
                           <div class="form-group">
                                 <label class=""> start date : 
                              </label>
                                 <div id="sandbox2">
                                    <input id="fromdate" name="fromdate" type="text" class="form-control" value="<?php echo(isset($fromdate)?$fromdate:'');?>">
                              </div>
                           </div>
                           </div>
                                  <div class="col-lg-3">                    
                           <div class="form-group">
                                 <label class=""> end date :
                              </label>
                                 <div id="sandbox">
                                    <input id="todate" name="todate" type="text" class="form-control" value="<?php echo(isset($todate)?$todate:'');?>">
                              </div>
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">status: 
                              </label>
                              {{ Form::select('status', ['' => '--All Status--','1'=>'Payment Pending','2'=>'Partially Paid','3'=>"Paid"], $status, ['class'=>'form-control','id'=>'status']) }}
                              </div>
                              </div>
<div class="col-lg-3">
                         <div class="form-group mt0-4">
							 
                          
                          <a href="{{url("/opslogin/invoice_report")}}"  class="submit ml-2 float-right">clear</a>
                          <button type="submit" class="submit  float-right">search</button>
                         <!--a href="#" class="submit mt-0  clres2">PRINT</a-->
							  </div>
							  </div>

                       

                     </div>
                  </form>
				   <div class="overflowscroll2 ">
				      <div class="row">
				      <div class="col-lg-6 col-6">
                        <button name="anmelden" class="button dletevms" id="btncheck" onclick="bulk_delete('bulkdelte')" disabled>Delete</button>
                     </div>
                     @if($unit_print ==1)
					         <div class="col-lg-6 col-6">
                           <input type="hidden" id="unitno" value="{{$unitno}}">
					            <button type="submit" class="submit  float-right" onclick="bulk_print('{{$visitor_app_url}}/consolidatedPrint')">Print</button>
					         </div>
                     @endif
					 </div>
                     <form action="{{url('/opslogin/invoice/bulkdelete')}}" method="post"  id="list_form" class="forunit forbottom">
                        {!! Form::token() !!}
                        <table class="gap">
                        
                           <thead>
                              <tr>
                                 <th style="width:0px;">
                                 <label class="containeruser1" style="padding-left: 0px;"><br>Select all
                                                <input type="checkbox" id="ckbCheckAll" class="checknew checkAll1" value="1">
                                                <span class="checkmarkuser1"></span>
                                                </label>
                                 </th>
                                    <th>invoice</th>
                                    <th>batch no</th>
                                    <th>building</th>
                                    <th>unit no</th>
                                    <th>total amount</th>
                                    <th>invoice date</th>
                                    <th>due date</th>
                                    <th>payment status</th>
                                    @if($property_info->qrcode_option ==2)
                                       <th>screenshot</th>
                                    @endif
                                    <th>actions</th>
                              </tr>
                           </thead>
                           <tbody >
                              @if($invoices)
                                 @foreach($invoices as $k => $appt)
                                 <tr data-toggle="tooltip" data-placement="top" title="{{$appt->remarks}}">
                                    <td class="roundleft">
                                       <label class="containeruser1">
                                             <input type="checkbox" name="invoices[]" value="{{$appt->id}}" class="checkBoxClass check">
                                             <span class="checkmarkuser1" style="top: -12px;"></span>
                                             </label>
                                    </td>
                                    <td class="spacer">{{$appt->invoice_no}}</td>
                                    <td class="spacer">{{$appt->batch_file_no}}</td>
                                    <td class="spacer">{{isset($appt->getunit->buildinginfo->building)?$appt->getunit->buildinginfo->building:''}}</td>
                                    <td class="spacer">{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                                    <td class="spacer">
                                    @php 
                                       if($appt->payable_amount > 0)
                                          echo number_format($appt->payable_amount,2);
                                       else
                                          echo "(".number_format((0-$appt->payable_amount),2).")"; 
                                    @endphp</td>
                                    <td class="spacer" >{{date('d/m/y',strtotime($appt->invoice_date))}}</td>
                                    <td class="spacer">{{date('d/m/y',strtotime($appt->due_date))}}</td>
									
                                    <td class="spacer">
                                       @php
                                          if(isset($appt->status)){
                                             if($appt->status !=3){
                                                $financeObj = new \App\Models\v2\FinanceInvoice();
                                                $ref_invoice = $financeObj->CheckNewInvoice($appt->id,$appt->unit_no);
                                                //print_r($ref_invoice);
                                             }

                                             if($appt->status==1){
                                                $rec = $financeObj->CheckOverDue($appt->id);
                                                
                                                if(isset($ref_invoice->id))
                                                   echo "Unpaid/ref.".$ref_invoice->invoice_no;
                                                else
                                                   echo $rec;
                                             }                                      
                                             else  if($appt->status==2){
                                                echo "Partial Paid";
                                                if(isset($ref_invoice->id))
                                                   echo "/ref.".$ref_invoice->invoice_no;
                                             }
                                             else  if($appt->status==4)
                                                echo "Pending Verification";
                                             else 
                                                echo "Paid";
                                          
                                          }
                                          
                                       @endphp
                                    </td>
									
                                    @if($property_info->qrcode_option ==2)
                                       <td class="spacer" style="text-align:center">
                                       @if(isset($appt->PaymentLog) && $appt->PaymentLog->type ==1)
                                          <a href="{{$file_path}}/{{$appt->PaymentLog->screenshot}}" target="_blank"><img src="{{$file_path}}/{{$appt->PaymentLog->screenshot}}" class="w20"></a>
                                       @endif
                                       </td>
                                    @endif
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
                                    <a class="dropdown-item" href="{{url("opslogin/invoice/$appt->id/edit")}}">Edit</a>

                                    @endif
                                    @if(isset($permission) && $permission->delete==1)
                                    <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/invoicedelete/$appt->id")}}');">Delete</a>
                                    @endif
                                    @if($appt->remarks!='' && strlen(trim($appt->remarks)) >3)
                                    <!--a class="dropdown-item" href="#"  data-toggle="tooltip" data-placement="top" title="{{$appt->remarks}}">Remarks</a-->
                                    @endif
                                             </div>
                                          </div>
                              
                                    </td>
                                 @endforeach

                              @endif   
                           </tbody>
                     </table>
                  </form>
				   <div class="col-lg-12 col-12">
                        <button name="anmelden" class="button dletevms" id="btncheck2" onclick="bulk_delete('bulkdelte')" disabled>Delete</button>
                     </div>
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
