@extends('layouts.adminnew')

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

<style>
   .invoice h4{    margin-bottom: 0px;}
   .forchange p {
    color: #fff !important;
    text-align: justify;
     line-height: 24px;
    margin-top: 0px;
}
.w20{    padding-right: 7px;}
#datepicker02{font-weight: 600;
    font-size: 14px;}
	.active {
    color: #ffff !important;
}
</style>
 <div class="status">
    <h1>{{$invoiceObj->invoice_no}} : payment</h1>
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
                    <li  class="activeul"><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                    @endif
                    @if(isset($permission) && $permission->view==1 && $admin_id !=1)
                    <li  ><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                    @endif
                    @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                    @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
      <!--div class="show">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div-->
       <div>
            <div class=" forunit forchange">
           
               <div class="col-lg-12 asignFace">
            <h2>official use only</h2>
         </div>
                  
                 {!! Form::model($invoiceObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/invoice/paymentsave/'.$invoiceObj->id), 'onSubmit' =>'return validatePaymentForm();']) !!}
                 <div class="">
                 <div class="row asignbg">
                 <div class="col-lg-7">

                  @if($invoiceObj->active_status ==1)
                     <div class="col-lg-">
                        <div class="form-group row ">
                           <label  class="col-sm-4 col-form-label">
                           <label>payment received by: 
                           </label>
                           </label>
                           <div class="col-sm-6">
                              @if($balance_amount > 0 )
                                 {{ Form::select('payment_option', [''=>'SELECT OPTION','1'=>'CHEQUE','2'=>'BANK TRANSFER',3=>"CASH",6=>'WAIVER'],null, ['class'=>'form-control','id'=>'payment_option', 'onchange'=>'getfields()','required'=>'true']) }}
                              @else
                                 {{ Form::select('payment_option', [''=>'SELECT OPTION',7=>'PAYMENT',6=>'WAIVER'],null, ['class'=>'form-control','id'=>'payment_option', 'onchange'=>'getfields()']) }}
                              @endif
                           </div>
                               
                        </div>                    
                      </div>
                     @endif
                      @php
                        $old_option1 ="style='display:none'";
                        $old_option2 ="style='display:none'";
                        $old_option3 ="style='display:none'";
                        $old_option6 ="style='display:none'";
                        $old_option7 ="style='display:none'";
                        $receipt_option ="style='display:none'";
                        $table_option ="style='display:none'";
                        if(old('payment_option') ==1){
                           $old_option1 ="style='display:block'";
                           $receipt_option ="style='display:block'";
                           $table_option ="style='display:block'";
                        }
                        else if(old('payment_option') ==2){
                           $old_option2 ="style='display:block'";
                           $receipt_option ="style='display:block'";
                           $table_option ="style='display:block'";
                        }
                        else if(old('payment_option') ==3){
                           $old_option3 ="style='display:block'";
                           $receipt_option ="style='display:block'";
                           $table_option ="style='display:block'";
                        }
                        else if(old('payment_option') ==7){
                           $old_option7 ="style='display:block'";
                           $receipt_option ="style='display:block'";
                           $table_option ="style='display:block'";
                        }
                        else if(old('payment_option') ==6){
                           $old_option6 ="style='display:block'";
                           $table_option ="style='display:block'";
                        }
                      @endphp
                           <div class="col-lg-" id="cheque" @php echo $old_option1 @endphp>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>cheque payment information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque amount : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('cheque_amount','', ['class'=>'form-control','placeholder' => '','id'=>'cheque_amount']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque number : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('cheque_no', '', ['class'=>'form-control','placeholder' => '','id'=>'cheque_no']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                 <div id="sandbox3">
                                 {{ Form::text('cheque_received_date','', ['id'=>'datetext1','class'=>'form-control','placeholder' => '']) }}
                                 </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">bank : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('cheque_bank', '', ['class'=>'form-control','placeholder' => '','id'=>'cheque_bank']) }}
                                 </div>
                              </div>

                           </div>
                           <div class="col-lg-" id="bt" @php echo $old_option2 @endphp >
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>bank transfer information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    <div id="sandbox">
                                    {{ Form::text('bt_received_date', '', ['id'=>'fromdate','class'=>'form-control','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('bt_amount_received', '', ['class'=>'form-control','placeholder' => '','id'=>'bt_amount_received']) }}
                                 </div>
                              </div>
                              
                           </div>
                           <div class="col-lg-" id="cash" @php echo $old_option3 @endphp>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>cash payment information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('cash_amount_received', '', ['class'=>'form-control','placeholder' => '','id'=>'cash_amount_received']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">DATE RECEIVED : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    <div id="sandbox4">
                                    {{ Form::text('cash_received_date', '', ['id'=>'datetext2','class'=>'form-control','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-" id="additional" @php echo $old_option7 @endphp>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>additional payment information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received by: 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                 {{ Form::select('add_amt_received_by', ['1'=>'Cheque','2'=>'Bank Transfer',3=>"Cash"],null, ['class'=>'form-control','id'=>'add_amt_received_by']) }}
                                 
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('add_amt_received', '', ['class'=>'form-control','placeholder' => '','id'=>'cash_amount_received']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    <div id="sandbox6">
                                    {{ Form::text('add_amt_date', '', ['id'=>'datetext6','class'=>'form-control','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">notes : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::textarea('add_amt_notes', '', ['class'=>'form-control','placeholder' => '','id'=>'add_amt_notes','rows'=>3]) }}
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-" id="credit" @php echo $old_option6 @endphp>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>waiver information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">waiver amount : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('credit_amount', '', ['class'=>'form-control','placeholder' => '','id'=>'credit_amount']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date: 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    <div id="sandbox5">
                                    {{ Form::text('credit_date', '', ['id'=>'datetext5','class'=>'form-control','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">notes : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::textarea('credit_notes', '', ['class'=>'form-control','placeholder' => '','id'=>'credit_notes','rows'=>3]) }}
                                 </div>
                              </div>
                           </div>

                        <div class="col-lg-" id="receipt" @php echo $receipt_option @endphp>
                           <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">receipt number : 
                                 </label>
                                 <div class="col-sm-6 col-6">
                                    {{ Form::text('receipt_no', '', ['class'=>'form-control','placeholder' => '','id'=>'receipt_no']) }}
                                 </div>
                              </div>
                           </div>
                          
                     </div>

                     <div class="col-lg-5 invoice">
                        <div class="form-group row ">
                           <div class="col-sm-4">
                              <a target="_blank" href="{{$visitor_app_url}}/payment-pdf/{{$invoiceObj->id}}" class="submit nt0 float-left">print</a>
                           </div>
                        </div>
                    
                           <h3>invoice details</h3>
                     
                        <div class="col-lg-">
                           <div class="form-group row ">
                              <label  class="col-sm-6 col-6 col-form-label">
                              building : 
                              
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                             <h4> {{isset($invoiceObj->getunit->buildinginfo->building)?$invoiceObj->getunit->buildinginfo->building:''}}                              </label>
                                 </h4>  </div> 
                        </div>
                        <div class="col-lg-">
                           <div class="form-group row ">
                              <label  class="col-sm-6 col-6 col-form-label">
                             unit no: 
                              
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                              <h4>   #{{isset($invoiceObj->getunit->unit)?Crypt::decryptString($invoiceObj->getunit->unit):''}}<h4> 
                              </label>
                           </div> 
                        </div>
                        <div class="col-lg-">
                           <div class="form-group row ">
                              <label  class="col-sm-6 col-6 col-form-label">
                              invoice amount: 
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                              <h4>$@php
                                 if($invoiceObj->previous_bill_balance >0 && $invoiceObj->previous_bill_balance_type ==2){
                                  $invoice_amt = ($invoiceObj->previous_bill_balance + $invoiceObj->payable_amount);
                                  echo number_format($invoice_amt,2);
                                 }
                                 else{
                                    echo number_format($invoiceObj->payable_amount,2);
                                 }
                              @endphp 
                              <input type="hidden" id="invoice_amount" name="invoice_amount" value="{{$invoiceObj->payable_amount}}">
                              </h4>
                              </label>
                           </div> 
                           
                        </div>
                        @if($invoiceObj->previous_bill_balance >0 && $invoiceObj->previous_bill_balance_type ==2)
                        <div class="col-lg-">
                           <div class="form-group row">
                              <label  class="col-sm-6 col-6 col-form-label">
                             previous excess payment: 
                              
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                              <h4>($@php echo number_format($invoiceObj->previous_bill_balance,2); @endphp)
                              </h4>
                              </label>
                           </div>    
                        </div>
                        @endif
                        <div class="col-lg-">
                           <div class="form-group row ">
                              <label  class="col-sm-6 col-6 col-form-label">
                              received amount: 
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                              <h4>${{number_format($amount_received,2)}} 
                              </h4>
                              </label>
                           </div> 
                           
                        </div>
                        <div class="col-lg-">
                           <div class="form-group row ">
                              <label  class="col-sm-6 col-6 col-form-label">
                              {{($balance_amount < 0)?'EXCESS PAID':'balance'}} amount: 
                           
                              </label>
                              <label  class="col-sm-6 col-6 col-form-label">
                              <h4>$@php
                                 if($balance_amount > 0)
                                    echo number_format($balance_amount,2);
                                 else
                                    echo number_format((0-$balance_amount),2); 
                              @endphp
                              <input type="hidden" id="balance" name="balance_amount" value="{{$balance_amount}}">
                              </h4>
                              </label>
                           </div> 
                           
                        </div>
                     </div>
                     </div>
                     
                     <div class="" id="details" @php echo $table_option @endphp>
                        <div class="form-group"> 
                        @if(isset($invoiceObj->paymentdetails))
                           <table class="gap">
                              <thead>
                                 <tr>
                                    <th style="width: 16%;">reference type</th>
                                    <th style="width: 42%;">detail</th>
                                    <th style="width: 13%;">amount s$</th>
                                    <th style="width: 7%; padding-left: 15px!important;">paid s$</th>
                                    <th style="width: 10%;">balance s$</th>
                                    <th style="width: 12%;">allocation s$</th>
                                 </tr>
                              </thead>
                              <tbody id="myTable">
                              @php
                                 $oldamount_value = array();
                                 if(old('amount') !=''){
                                    foreach(old('amount') as $k => $oldamount){
                                       $oldamount_value[$k] = $oldamount;
                                    }
                                 }
                                
                              @endphp

                              @foreach($invoiceObj->paymentdetails as $key =>  $detail)
                              @php
                              $paid_amount = $detail->total_amount - $detail->balance;
                              @endphp
                                 
                                 <tr id="add_field" >
                                       <td class="roundleft">{{isset($detail->referencetypes->reference_type)?$detail->referencetypes->reference_type:''}}</td>
                                       <td class="spacer">{{isset($detail->detail)?$detail->detail:''}}</td>
                                       <td class="spacer"><div style="float: left; width:45%">{{number_format($detail->total_amount,2)}} </div>
                                          @if($detail->balance >0)  
                                             @if(isset($detail->referencetypes) && $detail->referencetypes->id  == 3) 
                                             <div class="waved" style="float: right; width:55%"><input type="checkbox" id="wave{{$detail->id}}" name="wave[{{$detail->id}}]" value="1" style=" width: 15px!important;margin: 0px!important; height: 15px;" onclick='deactive_allocation("amount{{$key}}","wave{{$detail->id}}",{{$detail->balance}})'>&nbsp;Waiver
                                             </div>
                                             @endif
                                          @endif
                                       </td>
                                       <td class="spacer" style="padding-left: 15px!important;">
                                       
                                       {{number_format($paid_amount,2)}}</td>
                                       <td class="spacer">{{number_format($detail->balance,2)}}</td>
                                       <td class="roundright">   
                                       @if($detail->balance >0)  
                                          
                                          <input type="hidden" class="form-control" id="key" name="info_detail[{{$key}}]" value="{{$detail->id}}" >
                                          <input type="hidden" class="form-control" id="reference{{$key}}" name="reference[{{$detail->id}}]" value="{{$detail->reference_type}}" > 
                                          <input type="hidden" class="form-control" id="bal_amount{{$key}}" name="bal_amount[{{$detail->id}}]" value="{{$detail->balance}}" > 
                                          <input type="number" class="form-control alocation" id="amount{{$key}}" name="amount[{{$detail->id}}]" max="" step="0.01" value="{{(isset($oldamount_value[$detail->id]))?$oldamount_value[$detail->id]:''}}">
                                       @endif
                                       </td>
                                    </tr>
                                   
                                 @endforeach
                              </tbody>
                           </table>
                           @endif
                        </div>
                     
                     
                     @if($invoiceObj->active_status ==1)
                     <div class="col-lg-7 mt-2">
                           <div class="form-group row">
                           <!--label  class="col-sm-4 col-form-label"> </label-->
                           <label  class="col-sm-10 col-form-label">
                                <!-- <input type="hidden" name="payment_id" value="{{$invoiceObj->id}}"> -->
                                 <button type="submit" class="submit mt-2 mr-3 ml-3 float-right">submit</button>
                                 <a href="{{url("/opslogin/invoice")}}"  class="submit mt-2 float-left">back</a>
                                 </label>
                                 
                           </div>   
                        </div>
						    </div>
                      @endif
                      </div>
							  <!-- 2 -->
                       <div class="col-lg-12 p-0 mb-4 mt-5">
                           <h3>recent payment transaction</h3>
                           <div class="overflowscroll" style="    overflow-x: inherit">
                     <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>payment received by</th>
                           <th>cheque / transation no / details</th>
                           <th>amount</th>
                           <th>received date</th>
                           <th>receipt no</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($invoiceObj->payments)
                       @php $count =0;@endphp
                        @foreach($invoiceObj->payments as $k => $payment)
                        @if($payment->payment_option !=4 )
                        @php  $count++; @endphp
                        <tr>
                           <td>
                              @php 
                                    if($payment->payment_option ==1)
                                       echo "Cheque";
                                    else if($payment->payment_option ==2)
                                       echo "Bank Transfer";
                                    else if($payment->payment_option ==4)
                                       echo "Excess Paid";
                                    else if($payment->payment_option ==5)
                                       echo "Online Payment";
                                    else if($payment->payment_option ==7)
                                       echo "Payment";
                                    else if($payment->payment_option ==6)
                                       echo "Waiver";
                                    else
                                       echo "Cash";
                              @endphp
                           </td>
                           <td>@php
                              if($payment->payment_option ==1){
                                 echo isset($payment->cheque_no)?$payment->cheque_no:'';
                                 if($payment->status ==2)
                                    echo isset($payment->remarks)?" (".$payment->remarks.")":'';
                              }
                              else if($payment->payment_option ==5) 
                                 echo isset($payment->transaction_id)?$payment->transaction_id:'';
                              else if($payment->payment_option ==7) 
                                 echo isset($payment->add_amt_notes)?$payment->add_amt_notes:'';
                              else if($payment->payment_option ==6) 
                                 echo isset($payment->credit_notes)?$payment->credit_notes:'';
                              @endphp
                           </td>
                           <td class="tooltip1">   
                              @if(isset($payment->paymentdetails))            
                                 <span class="tooltiptext">
                                 @foreach($payment->paymentdetails as $k => $paymentdetail)
                                    <p>{{$paymentdetail->paidtype->reference_type}} {{$paymentdetail->detail->detail}} : <b>${{$paymentdetail->amount}}</b></p>
                                 @endforeach
                                 </span>
                              @endif
                           $@php 
                                    if($payment->payment_option ==1)
                                       echo number_format($payment->cheque_amount,2); 
                                    else if($payment->payment_option ==2)
                                       echo number_format($payment->bt_amount_received,2);
                                    else if($payment->payment_option ==5)
                                       echo number_format($payment->online_amount_received,2); 
                                    else if($payment->payment_option ==6)
                                       echo number_format($payment->credit_amount,2);  
                                    else if($payment->payment_option ==7)
                                       echo number_format($payment->add_amt_received,2);   
                                    else
                                       echo number_format($payment->cash_amount_received,2); 
                              @endphp
                           </td>
                           <td>
                           @php 
                                 echo date('d/m/y',strtotime($payment->payment_received_date));
                              @endphp</td>
                           <td>{{isset($payment->receipt_no)?$payment->receipt_no:''}}</td>
                           <td>
                           @if($count ==1 && $invoiceObj->active_status ==1)
                              <a href="#"  onclick="delete_record('{{url("opslogin/invoicepayment/delete/$payment->id")}}');" data-toggle="tooltip" data-placement="top" title="Delete"><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg w20 phvert"></a>
                           @endif
                           @if($count ==1 && $payment->payment_option ==1)
                              <a data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$payment->id}}" class="open-dialog">
                                 <img src="{{url('assets/admin/img/bounced.png')}}" class="viewimg w20 phvert" style="width: 35px;" data-toggle="tooltip" data-placement="top" title="Bunced Cheque">
                              </a>
                           @endif
                           </td>
                        </tr>
                        @endif
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  </div>
                  </div>
				 
                  <div class="col-lg-12 p-0 mb-4 mt-5">
                           <h3>payment received history</h3>
                           <div class="overflowscroll" style="    overflow-x: inherit">
                     <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>payment received by</th>
                           <th>cheque / transation no / details</th>
                           <th>amount</th>
                           <th>received date</th>
                           <th>receipt no</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($paymentHistory)
                        @foreach($paymentHistory as $k => $payment)
                        @if($payment->payment_option !=4 )
                        <tr>
                           <td>
                           @php 
                                    if($payment->payment_option ==1)
                                       echo "Cheque";
                                    else if($payment->payment_option ==2)
                                       echo "Bank Transfer";
                                    else if($payment->payment_option ==4)
                                       echo "Excess Paid";
                                    else if($payment->payment_option ==5)
                                       echo "Online Payment";
                                    else if($payment->payment_option ==7)
                                       echo "Payment";
                                    else if($payment->payment_option ==6)
                                       echo "Waiver";
                                    else
                                       echo "Cash";
                              @endphp
                           </td>
                           <td>@php
                              if($payment->payment_option ==1){
                                 echo isset($payment->cheque_no)?$payment->cheque_no:'';
                                 if($payment->status ==2)
                                    echo isset($payment->remarks)?" (".$payment->remarks.")":'';
                              }
                              else if($payment->payment_option ==5) 
                                 echo isset($payment->transaction_id)?$payment->transaction_id:'';
                              else if($payment->payment_option ==7) 
                                 echo isset($payment->add_amt_notes)?$payment->add_amt_notes:'';
                              else if($payment->payment_option ==6) 
                                 echo isset($payment->credit_notes)?$payment->credit_notes:'';
                              @endphp</td>
                           <td class="tooltip1">   
                              @if(isset($payment->paymentdetails))            
                                 <span class="tooltiptext">
                                    @foreach($payment->paymentdetails as $k => $paymentdetail)
                                       <p>{{$paymentdetail->paidtype->reference_type}} {{$paymentdetail->detail->detail}} : <b>${{$paymentdetail->amount}}</b></p>
                                    @endforeach
                                 </span>
                              @endif
                           $@php 
                                    if($payment->payment_option ==1)
                                       echo number_format($payment->cheque_amount,2); 
                                    else if($payment->payment_option ==2)
                                       echo number_format($payment->bt_amount_received,2); 
                                    else if($payment->payment_option ==6)
                                       echo number_format($payment->credit_amount,2); 
                                    else if($payment->payment_option ==7)
                                       echo number_format($payment->add_amt_received,2);   
                                    else
                                       echo number_format($payment->cash_amount_received,2); 
                              @endphp
                           </td>
                           <td>
                              @php 
                                 echo date('d/m/y',strtotime($payment->payment_received_date));
                              @endphp</td>
                           <td>{{isset($payment->receipt_no)?$payment->receipt_no:''}}</td>
                          </tr>
                          @endif
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
                  </div>
                  </div>
                  <div class="col-lg-12 pl-1">
                         <div class="form-group row">
                               <a href="{{url($return_url)}}"  class="submit mt-0 float-left " style="width:190px;  margin: 0 12px;"> return to invoices</a>
                           </div>
                    </div> 
                  </div>                      
                      </div>

                    {!! Form::close() !!}
               
                  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
          {!! Form::open(['method' => 'POST', 'url' => url('opslogin/invoicepayment/bounceback'), 'files' => false, 'autocomplete' => 'off']) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Payment Transaction - Cancel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
			   <div class="form-group" id="sandbox02">
                              <input class="form-control" id="datepicker02" name="bounced_date" type="text" placeholder="Select Date">
                           </div>
                <label>Remark:</label>
                <textarea class="form-control" required="" rows="4" name="remark" cols="50"></textarea>
              </div>
              <div class="modal-body">
              <input type="hidden" name="bookId" id="bookId" value="">
               <input type="hidden" name="status"value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
            </div>
         </div>


</section>
 <script type="text/javascript">
 function refreshPage(){
    window.location.reload();
} 
 </script>
 <script type="text/javascript">

      function validatePaymentForm(){
       
         return true;
      }
      function deactive_allocation(alocation_id,checkboxid,balance){

         var alocation = "#"+alocation_id;
         var checkbox_id = "#"+checkboxid;
         if($(checkbox_id).is(":checked")){
            $(alocation). val(balance);
            $(alocation). attr('readonly',true);
         }
         else{
            $(alocation).val('');
            $(alocation). attr('readonly',false);
         }
      }

      function getfields(){
            $("#cheque_amount").prop('required',false);
            $("#cheque_no").prop('required',false);
            $("#datetext1").prop('required',false);
            $("#cheque_bank").prop('required',false);
            $(".waved").hide()
            $("#fromdate").prop('required',false);
            $("#bt_amount_received").prop('required',false);
            $(".alocation"). attr('disabled',false);
            $(".alocation"). val('');
            $("#cash_amount_received").prop('required',false);
            $("#datetext2").prop('required',false);
            //$("#receipt_no").prop('required',false);

         if($("#payment_option").val() ==1){
            $("#cheque").show(); 
            $("#receipt").show()
            $("#cash").hide(); 
            $("#bt").hide(); 
            $("#additional").hide(); 
            $("#credit").hide();
            $("#cheque_amount").prop('required',true);
            $("#cheque_no").prop('required',true);
            $("#datetext1").prop('required',true);
            $("#cheque_bank").prop('required',true);
         }
         else if($("#payment_option").val() ==2){
            $("#bt").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#cash").hide(); 
            $("#additional").hide(); 
            $("#credit").hide();
            $("#fromdate").prop('required',true);
            $("#bt_amount_received").prop('required',true);

         }
         else if($("#payment_option").val() ==3){
            $("#cash").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#additional").hide(); 
            $("#credit").hide();
            $("#cash_amount_received").prop('required',true);
            $("#datetext2").prop('required',true);
            //$("#receipt_no").prop('required',true);
         }
         else if($("#payment_option").val() ==7){
            $("#additional").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#cash").hide(); 
            $("#credit").hide();
            $("#add_amt_received").prop('required',true);
            $("#datetext7").prop('required',true);
            //$("#receipt_no").prop('required',true);
         }
         else if($("#payment_option").val() ==6){
            $("#credit").show(); 
            $("#cash").hide(); 
            $("#receipt").hide()
            $("#cheque").hide();
            $("#additional").hide();  
            $("#bt").hide(); 
            $("#credit_amount").prop('required',true);
            //$("#receipt_no").prop('required',true);
            $(".waved").show()
         }
         else {
            $("#cash").hide(); 
            $("#receipt").hide()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#credit").hide(); 
         }

         $("#details").show(); 
      }

      
    </script>
@stop


