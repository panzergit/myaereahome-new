@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>Invoice - {{$Unitinvoice->invoice_no}} </h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                     <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                     <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
       {!! Form::model($invoice,['method' =>'PATCH','files' => true,'url' => url('opslogin/invoicesend/'.$invoice->id),'class'=>'forunit']) !!}

       
               <style>
                  .usertable td {
    font-size: 13px!important; padding: 0px 10px;}
                  .utablechange{    width: 100%!important;}
                  .utablechange td {
                    width: 100%;
                }
                .utablechange th{    width: 100px;
                    display: block;
                    border: none!important;}

                .utablechange th:nth-child(1) {
                  background-color: #d6d3d3;
                    width: 100%;
                }
                .flex-container {
                  display: flex;
                }

                .flex-container .tabelnew {
               
                    border: 1px solid #b6afaf;
                    color: #5D5D5D;
                    background: #fff;
                    
                }
                .flex-container .tabelnew p{margin-bottom: 0px;
                    border-bottom: 1px solid #5D5D5D; padding-left: 10px; color:#5D5D5D;}
                  .flex-container .tabelnew p:nth-child(1) {
                  background-color: #d9d9d9!important;
                    width: 100%; color:#5D5D5D;
                    width: 100px;
                }
                .flex-container .tabelnew p:nth-of-type(even) {
                    background-color: #a9f1f9;
                }
                .flex-container .tabelnew p:nth-of-type(odd) {
                    background-color: #fff;
                }
                .flex-container .tabelnew input {    height: auto!important;
    margin-bottom: 0px!important;}
                .alltable td{    color: #5D5D5D;
                  font-size: 16px;}
                  .araepay{margin-top: 20px;
                  margin-bottom: 20px;}
                  .Themanage h1{    font-size: 16px;
    color: #5D5D5D;
    font-weight: 600;
    margin-bottom: 5px;
    margin-top: -5px;}
                  .Themanage h4{ font-size: 14px;
    text-transform: uppercase; font-weight: 600;
    color: #5D5D5D;}
                  .Themanage h6{    color: #5D5D5D;
    width: 250px;
    font-size: 14px;
    line-height: 20px;
    font-weight: 600;
    margin-top: 13px;
                  margin-bottom: 20px;}
                  .araeall p{    font-size: 14px;
    line-height: 18px;
    text-align: justify;
    color: #5D5D5D;
    font-weight: 600;}
                  .accaddres{    width: 100%;
                  border: 1px solid #607d8b;}
                  .accaddres tr{}
                  .accaddres th{    background: #d9d9d9;
                  padding-left: 10px;}
                  .accaddres td{background: #fff;
                  padding-left: 10px; font-size: 14px;     font-weight: 600;}
                  .aprldate b{font-size: 14px;}
                  .aprldate label{font-size: 14px;}
                  .araeall table{    width: 100%;
                  color: #5D5D5D;
                  font-size: 16px;}
                  .portcheck{     margin-bottom: 20px;
                  margin-top: 20px;   width: 100%;
                  text-align: center;
                  background: #d9d9d9;
                  padding-left: 10px;
                  font-size: 16px;}
                  .foraccount{}
                  .foraccount b{font-size: 16px;
                  color: #5D5D5D;}
                  .foraccount p{color: #5D5D5D;font-size: 16px;}
				  .thecreate{    background: #fff;
    border-radius: 20px;
    padding: 20px;}
	.aprldate label{  font-size:14px;  color: #5D5D5D;        font-weight: 600;
    font-family: 'Lato', sans-serif !important;}
	.usertable thead {
    background: transparent;
}

	.aprldate{    margin-bottom: -5px;}
   .submitnew2{ color: #000;
        background: #a9f1f9;
    cursor: pointer;
}
   .submitnew2:hover{   background: #fff;  color: #000;}
   .araeall br{ margin-bottom: 0px;}
    </style>

<!--new-->
<div class="crearsection thecreate">
                     <div class="row">
                        <div class="col-lg-7 Themanage">
                           <h4>@php echo isset($Unitinvoice->propertydetail->mcst_code)?"MCST:".$Unitinvoice->propertydetail->mcst_code:'' @endphp</h4>
                           <h6>{{isset($invoice->comp_name)?$invoice->comp_name:''}}<br/>@php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp </h6>
                        </div>
                        <div class="col-lg-4 Themanage">
                           <h1><b>Invoice / Statement</b></h1>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5 col-5">
                                 <label><b>Invoice No:</b></label>
                              </div>
                              <div class="col-lg-7 col-7">
                                 <label>{{$Unitinvoice->invoice_no}}</label>
                              </div>
                           </div>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5 col-5">
                                 <label><b>Invoice Date:</b></label>
                              </div>
                              <div class="col-lg-7 col-7">
                                 <label>{{isset($Unitinvoice->invoice_date)?date('d/m/Y',strtotime($Unitinvoice->invoice_date)):''}}</label>
                              </div>
                           </div>

                           <div class=" row dateform aprldate">
                              <div class="col-lg-5 col-5">
                                 <label><b>Due Date:</b></label>
                              </div>
                              <div class="col-lg-7 col-7">
                                 <label>{{isset($Unitinvoice->due_date)?date('d/m/Y',strtotime($Unitinvoice->due_date)):''}}</label>
                              </div>
                           </div>
                          
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5 col-5">
                                 <label><b>Account No:</b></label>
                              </div>
                              <div class="col-lg-7 col-7">
                                 <label>{{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?Crypt::decryptString($Unitinvoice->getunit->unit):''}}</label>
                              </div>
                           </div>
						    <div class=" row dateform aprldate">
                              <div class="col-lg-5 col-5">
                                 <label><b>Share Value:</b></label>
                              </div>
                              <div class="col-lg-7 col-7">
                                 <label>{{$Unitinvoice->unit_share}}</label>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <table class="accaddres">
                              <tbody>
                                 
                                 <tr>
                                    <td colspan="2" class="addrespilt"><b>MAILING ADDRESS </b><br />
                                    @if(isset($primary_contact))
                                      {{Crypt::decryptString($primary_contact->first_name)}} {{isset($primary_contact->last_name)?Crypt::decryptString($primary_contact->last_name):''}}
                                   <br />
                                    @php echo isset($primary_contact->mailing_address)?nl2br($primary_contact->mailing_address):'' @endphp
                                    <br />
                                    @php echo isset($primary_contact->country)?$primary_contact->usercountry->country_name:'' @endphp
                                    @php echo isset($primary_contact->postal_code)?" ".nl2br($primary_contact->postal_code):'' @endphp
                                    @endif</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-4">
                           <table class="accaddres">
                              <tbody>
                                 
                                 <tr>
                                    <td colspan="2" class="addrespilt"><b>BILLING ADDRESS</b> <br />
                                    @if(isset($purchasers))
                                       @php $counts =0; @endphp
                                       @foreach($purchasers as $k => $purchaser)
                                          @if($purchaser->status ==1)
                                             @php if($counts >0)
                                                echo ", "; 
                                             @endphp

                                             {{Crypt::decryptString($purchaser->first_name)}} {{isset($purchaser->last_name)?Crypt::decryptString($purchaser->last_name):''}}
                                             @php $counts++; @endphp
                                          @endif
                                       @endforeach
                                    @endif<br />
                                   #{{isset($Unitinvoice->getunit->unit)?Crypt::decryptString($Unitinvoice->getunit->unit):''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}} 
                                   <br />
                                    @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12">
                           <table class="table usertable ">
                              <thead>
                                 <tr>
                                    <th style="width: 13%;">Date</th>
                                    <th style="width: 10%;">Reference No</th>
                                    <th style="width:42%;">Description</th>
                                    <th style="text-align: right; width:12%;">Amount Due S$</th>
                                    <th style="text-align: right; width:11%;">Paid S$</th>
                                    <th style="text-align: right; margin-right: 10px; border-top: none!important; width:12%; ">Balance S$</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @if(1==2)
                                    @if(isset($LastInvoice->CreditPayments))
                                       @foreach($LastInvoice->CreditPayments as $creditpayment)
                                       <tr>
                                          <td>{{date('d/m/y',strtotime($creditpayment->received_date))}}</td>
                                          <td></td>
                                          <td> <b>{{$creditpayment->credit_notes}} ${{$creditpayment->credit_amount}} Received </b></td>
                                          <td style="text-align: right;"></td>
                                          <td style="text-align: right;">
                                          </td>
                                          <td style="text-align: right; margin-right: 10px;"></td>
                                       </tr>
                                       @endforeach
                                    @endif
                                 @endif
                                 
                                    @if(isset($previousDetails)) <!-- Previous collection -->
                                       @foreach($previousDetails as $detail)
                                          @if($detail->total_amount >0 && $detail->status==0)
                                          <tr>
                                             <td>{{isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):''}}</td>
                                             <td>
                                             {{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                                             <td> {{isset($detail->detail)?$detail->detail:''}} </td>
                                             <!--<td style="text-align: right;">{{number_format($detail->total_amount,2)}}</td>-->
                                             <td style="text-align: right;">{{number_format($detail->amount,2)}}</td>
                                             <td style="text-align: right;">0.00</td>
                                             <!--<td style="text-align: right; margin-right: 10px;">{{number_format($detail->total_amount,2)}}</td>-->
                                             <td style="text-align: right; margin-right: 10px;">{{number_format($detail->amount,2)}}
                                             
                                          </tr>
                                          @endif
                                       @endforeach
                                    @endif
                                 <!-- Last Payment details Start-->
                                    @if(isset($LastInvoicePayments))
                                       @foreach($LastInvoicePayments as $LastInvoicePayment)
                                          <tr>
                                             <td>{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                                             <td></td>
                                             <td>
                                                      @php 
                                                         if($LastInvoicePayment->payment_option ==1)
                                                            echo "Cheque";
                                                         else if($LastInvoicePayment->payment_option ==2)
                                                            echo "Bank Transfer";
                                                         else if($LastInvoicePayment->payment_option ==4){
                                                            echo "Excess payment";
                                                            $ExcessObj = new \App\Models\v2\FinanceInvoice();
                                                            $ExcessPaymentDate = $ExcessObj->excesspaymentdate($LastInvoice->id,$LastInvoice->unit_no);
                                                            if(isset($ExcessPaymentDate) && $ExcessPaymentDate !='')
                                                            echo " (paid on :".date('d/m/y',strtotime($ExcessPaymentDate)).")";
                                                         }
                                                            
                                                         else if($LastInvoicePayment->payment_option ==5)
                                                            echo "Online Payment";
                                                         else if($LastInvoicePayment->payment_option ==7){
                                                            if($LastInvoicePayment->add_amt_received_by ==1)
                                                            echo "Cheque";
                                                            else if($LastInvoicePayment->add_amt_received_by ==2) 
                                                               echo "Bank Transfer";
                                                            else if($LastInvoicePayment->add_amt_received_by ==3) 
                                                               echo "Cash";
                                                         }
                                                         else if($LastInvoicePayment->payment_option ==6)
                                                            echo "Waiver";
                                                         else
                                                            echo "Cash";
                                                      @endphp
                                                      @php
                                                         if($LastInvoicePayment->payment_option ==1){
                                                            echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                                                            if($LastInvoicePayment->status ==2)
                                                               echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":'';
                                                         }
                                                         else if($LastInvoicePayment->payment_option ==5) 
                                                            echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                                                         else if($LastInvoicePayment->payment_option ==6) 
                                                            echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:'';
                                                         else if($LastInvoicePayment->payment_option ==7) 
                                                            echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                                                      @endphp 
                                                     
                                                      @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2)
                                                         payment
                                                      @endif
                                                      

                                                
                                             </td>
                                             <td style="text-align: right;">0.00</td>
                                             <td style="text-align: right;">@php 
                                                      if($LastInvoicePayment->payment_option ==1)
                                                         echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==2)
                                                         echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                      else if($LastInvoicePayment->payment_option ==5)
                                                         echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                      else if($LastInvoicePayment->payment_option ==6)
                                                         echo number_format($LastInvoicePayment->credit_amount,2);
                                                      else if($LastInvoicePayment->payment_option ==7)
                                                         echo number_format($LastInvoicePayment->add_amt_received,2);   
                                                      else
                                                         echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                @endphp
                                             </td>
                                             <td style="text-align: right; margin-right: 10px;">(@php 
                                                      if($LastInvoicePayment->payment_option ==1)
                                                         echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==2)
                                                         echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                      else if($LastInvoicePayment->payment_option ==5)
                                                         echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                      else if($LastInvoicePayment->payment_option ==6)
                                                         echo number_format($LastInvoicePayment->credit_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==7)
                                                      echo number_format($LastInvoicePayment->add_amt_received,2); 
                                                      else
                                                         echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                @endphp)</td>
                                          </tr>
                                          @if($LastInvoicePayment->status ==2)
                                          <tr>
                                             <td>{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                                             <td></td>
                                             <td>
                                                      @php 
                                                         if($LastInvoicePayment->payment_option ==1)
                                                            echo "Cheque";
                                                         else if($LastInvoicePayment->payment_option ==2)
                                                            echo "Bank Transfer";
                                                         else if($LastInvoicePayment->payment_option ==4){
                                                            echo "Excess payment";
                                                            $ExcessObj = new \App\Models\v2\FinanceInvoice();
                                                            $ExcessPaymentDate = $ExcessObj->excesspaymentdate($LastInvoice->id,$LastInvoice->unit_no);
                                                            if(isset($ExcessPaymentDate) && $ExcessPaymentDate !='')
                                                            echo " (paid on :".date('d/m/y',strtotime($ExcessPaymentDate)).")";
                                                         }
                                                            
                                                         else if($LastInvoicePayment->payment_option ==5)
                                                            echo "Online Payment";
                                                         else if($LastInvoicePayment->payment_option ==7){
                                                            if($LastInvoicePayment->add_amt_received_by ==1)
                                                            echo "Cheque";
                                                            else if($LastInvoicePayment->add_amt_received_by ==2) 
                                                               echo "Bank Transfer";
                                                            else if($LastInvoicePayment->add_amt_received_by ==3) 
                                                               echo "Cash";
                                                         }
                                                         else if($LastInvoicePayment->payment_option ==6)
                                                            echo "Waiver";
                                                         else
                                                            echo "Cash";
                                                      @endphp
                                                      @php
                                                         if($LastInvoicePayment->payment_option ==1){
                                                            echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                                                            if($LastInvoicePayment->status ==2)
                                                               echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":'';
                                                         }
                                                         else if($LastInvoicePayment->payment_option ==5) 
                                                            echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                                                         else if($LastInvoicePayment->payment_option ==6) 
                                                            echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:'';
                                                         else if($LastInvoicePayment->payment_option ==7) 
                                                            echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                                                      @endphp 
                                                     
                                                      @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2)
                                                         payment
                                                      @endif
                                                      

                                                
                                             </td>
                                             <td style="text-align: right;">0.00</td>
                                             <td style="text-align: right;">@php 
                                                      if($LastInvoicePayment->payment_option ==1)
                                                         echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==2)
                                                         echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                      else if($LastInvoicePayment->payment_option ==5)
                                                         echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                      else if($LastInvoicePayment->payment_option ==6)
                                                         echo number_format($LastInvoicePayment->credit_amount,2);
                                                      else if($LastInvoicePayment->payment_option ==7)
                                                         echo number_format($LastInvoicePayment->add_amt_received,2);   
                                                      else
                                                         echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                @endphp
                                             </td>
                                             <td style="text-align: right; margin-right: 10px;">(-@php 
                                                      if($LastInvoicePayment->payment_option ==1)
                                                         echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==2)
                                                         echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                      else if($LastInvoicePayment->payment_option ==5)
                                                         echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                      else if($LastInvoicePayment->payment_option ==6)
                                                         echo number_format($LastInvoicePayment->credit_amount,2); 
                                                      else if($LastInvoicePayment->payment_option ==7)
                                                      echo number_format($LastInvoicePayment->add_amt_received,2); 
                                                      else
                                                         echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                @endphp)</td>
                                          </tr>
                                          @endif
                                       @endforeach
                                    @endif
                                 
                                 <!-- Last Payment details End-->
                                 @if(isset($currentDetails)) <!-- Previous collection -->
                                    @foreach($currentDetails as $detail)
                                       @if($detail->total_amount >0)
                                       <tr>
                                          <td>{{isset($Unitinvoice->invoice_date)?date('d/m/y',strtotime($Unitinvoice->invoice_date)):''}}</td>
                                          <td>
                                          {{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                                          <td> {{isset($detail->detail)?$detail->detail:''}}</td>
                                          <td style="text-align: right;">{{number_format($detail->total_amount,2)}}</td>
                                          <td style="text-align: right;"> 
                                          @php
                                          if($detail->paid_by_credit ==2)
                                             echo "Wavier";
                                          else {
                                             $paid = $detail->total_amount - $detail->amount;
                                             echo number_format($paid,2);
                                          }
                                          @endphp</td>
                                          <td style="text-align: right; margin-right: 10px;">{{number_format($detail->amount,2)}}</td>
                                          
                                       </tr>
                                       @endif
                                    @endforeach
                                 @endif
                                 <!-- Current Payment details Start-->
                                 @if(isset($CurrentInvoicePayments))
                                    @foreach($CurrentInvoicePayments as $LastInvoicePayment)
                                    @if($LastInvoicePayment->payment_option !=4 )
                                       <tr>
                                          <td>{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                                          <td></td>
                                          <td>
                                                   @php 
                                                      if($LastInvoicePayment->payment_option ==1)
                                                         echo "Cheque";
                                                      else if($LastInvoicePayment->payment_option ==2)
                                                         echo "Bank Transfer";
                                                      else if($LastInvoicePayment->payment_option ==4)
                                                         echo "Excess Paid";
                                                      else if($LastInvoicePayment->payment_option ==5)
                                                         echo "Online Payment";
                                                      else if($LastInvoicePayment->payment_option ==7){
                                                         if($LastInvoicePayment->add_amt_received_by ==1)
                                                         echo "Cheque";
                                                         else if($LastInvoicePayment->add_amt_received_by ==2) 
                                                            echo "Bank Transfer";
                                                         else if($LastInvoicePayment->add_amt_received_by ==3) 
                                                            echo "Cash";
                                                      }
                                                      else if($LastInvoicePayment->payment_option ==6)
                                                         echo "Waiver";
                                                      else
                                                         echo "Cash";
                                                   @endphp
                                                   @php
                                                      if($LastInvoicePayment->payment_option ==1){
                                                         echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                                                      }
                                                      else if($LastInvoicePayment->payment_option ==5) 
                                                         echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                                                      else if($LastInvoicePayment->payment_option ==6) 
                                                         echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:'';
                                                      else if($LastInvoicePayment->payment_option ==7) 
                                                         echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                                                   @endphp 
                                                   @php
                                                   if($LastInvoicePayment->status !=2)
                                                      echo "payment";
                                                   @endphp
                                             
                                          </td>
                                          <td style="text-align: right;">0.00</td>
                                          <td style="text-align: right;">@php 
                                                   if($LastInvoicePayment->payment_option ==1)
                                                      echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                   else if($LastInvoicePayment->payment_option ==2)
                                                      echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                   else if($LastInvoicePayment->payment_option ==5)
                                                      echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                   else if($LastInvoicePayment->payment_option ==6)
                                                      echo number_format($LastInvoicePayment->credit_amount,2); 
                                                   else if($LastInvoicePayment->payment_option ==7)
                                                      echo number_format($LastInvoicePayment->add_amt_received,2);   
                                                   else
                                                      echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                             @endphp
                                          </td>
                                          <td style="text-align: right; margin-right: 10px;">(@php 
                                                   if($LastInvoicePayment->payment_option ==1)
                                                      echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                   else if($LastInvoicePayment->payment_option ==2)
                                                      echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                   else if($LastInvoicePayment->payment_option ==5)
                                                      echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                   else if($LastInvoicePayment->payment_option ==6)
                                                      echo number_format($LastInvoicePayment->credit_amount,2); 
                                                   else if($LastInvoicePayment->payment_option ==7)
                                                      echo number_format($LastInvoicePayment->add_amt_received,2);  
                                                   else
                                                      echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                             @endphp)</td>
                                       </tr>
                                          @if($LastInvoicePayment->status ==2)
                                             <tr>
                                                <td>{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                                                <td></td>
                                                <td>
                                                         @php 
                                                            if($LastInvoicePayment->payment_option ==1)
                                                               echo "Cheque";
                                                            else if($LastInvoicePayment->payment_option ==2)
                                                               echo "Bank Transfer";
                                                            else if($LastInvoicePayment->payment_option ==4){
                                                               echo "Excess payment";
                                                               $ExcessObj = new \App\Models\v2\FinanceInvoice();
                                                               $ExcessPaymentDate = $ExcessObj->excesspaymentdate($LastInvoice->id,$LastInvoice->unit_no);
                                                               if(isset($ExcessPaymentDate) && $ExcessPaymentDate !='')
                                                               echo " (paid on :".date('d/m/y',strtotime($ExcessPaymentDate)).")";
                                                            }
                                                               
                                                            else if($LastInvoicePayment->payment_option ==5)
                                                               echo "Online Payment";
                                                            else if($LastInvoicePayment->payment_option ==7){
                                                               if($LastInvoicePayment->add_amt_received_by ==1)
                                                               echo "Cheque";
                                                               else if($LastInvoicePayment->add_amt_received_by ==2) 
                                                                  echo "Bank Transfer";
                                                               else if($LastInvoicePayment->add_amt_received_by ==3) 
                                                                  echo "Cash";
                                                            }
                                                            else if($LastInvoicePayment->payment_option ==6)
                                                               echo "Waiver";
                                                            else
                                                               echo "Cash";
                                                         @endphp
                                                         @php
                                                            if($LastInvoicePayment->payment_option ==1){
                                                               echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                                                               if($LastInvoicePayment->status ==2)
                                                                  echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":'';
                                                            }
                                                            else if($LastInvoicePayment->payment_option ==5) 
                                                               echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                                                            else if($LastInvoicePayment->payment_option ==6) 
                                                               echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:'';
                                                            else if($LastInvoicePayment->payment_option ==7) 
                                                               echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                                                         @endphp 
                                                      
                                                         @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2)
                                                            payment
                                                         @endif
                                                         

                                                   
                                                </td>
                                                <td style="text-align: right;">0.00</td>
                                                <td style="text-align: right;">@php 
                                                         if($LastInvoicePayment->payment_option ==1)
                                                            echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                         else if($LastInvoicePayment->payment_option ==2)
                                                            echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                         else if($LastInvoicePayment->payment_option ==5)
                                                            echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                         else if($LastInvoicePayment->payment_option ==6)
                                                            echo number_format($LastInvoicePayment->credit_amount,2);
                                                         else if($LastInvoicePayment->payment_option ==7)
                                                            echo number_format($LastInvoicePayment->add_amt_received,2);   
                                                         else
                                                            echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                   @endphp
                                                </td>
                                                <td style="text-align: right; margin-right: 10px;">(-@php 
                                                         if($LastInvoicePayment->payment_option ==1)
                                                            echo number_format($LastInvoicePayment->cheque_amount,2); 
                                                         else if($LastInvoicePayment->payment_option ==2)
                                                            echo number_format($LastInvoicePayment->bt_amount_received,2);
                                                         else if($LastInvoicePayment->payment_option ==5)
                                                            echo number_format($LastInvoicePayment->online_amount_received,2); 
                                                         else if($LastInvoicePayment->payment_option ==6)
                                                            echo number_format($LastInvoicePayment->credit_amount,2); 
                                                         else if($LastInvoicePayment->payment_option ==7)
                                                         echo number_format($LastInvoicePayment->add_amt_received,2); 
                                                         else
                                                            echo number_format($LastInvoicePayment->cash_amount_received,2); 
                                                   @endphp)</td>
                                             </tr>
                                          @endif
                                       @endif
                                    @endforeach
                                 @endif
                                    <!-- Advance Payment details Start-->
                                 @php $advance_amount  = 0; @endphp
                                    @if(isset($Unitinvoice->AdvancePayment))
                                       @php $advance_amount = $Unitinvoice->AdvancePayment->amount; @endphp
                                       @if(1==2)
                                       <tr>
                                          <td></td>
                                          <td></td>
                                          <td> <b>Advance Payment</b></td>
                                          <td style="text-align: right;"></td>
                                          <td style="text-align: right;">
                                          {{$Unitinvoice->AdvancePayment->amount}}</td>
                                          <td style="text-align: right; margin-right: 10px;">({{number_format($Unitinvoice->AdvancePayment->amount,2)}})</td>
                                       </tr>
                                       @endif
                                    <!-- Advance Payment details End-->
                                 @endif
                                 <tr>
                                    <td colspan="3"></td>
                                    <td style="text-align: right;"colspan="2"><b>{{($balance_amount < 0)?'EXCESS AMOUNT':'AMOUNT DUE'}} :</b></td>
                                    <td style="text-align: right; margin-right: 10px;" colspan="2"><b>
                                    $@php
                                 if($balance_amount > 0)
                                    echo number_format($balance_amount,2);
                                 else
                                    echo number_format((0-$balance_amount),2); 
                              @endphp
                                    </b>
                                 </td>

                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12 araeall"><p>@php echo nl2br($invoice->notes) @endphp </p>
                           
                        </div>
                     </div>
                     
                     
                  </div>
				
					
                  {!! Form::close() !!}   
               </div>   
               
            </div>
         </div>
      </section>


@stop


