<!doctype html public "-//w3c//dtd html 4.01//en" "http://www.w3.org/tr/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <title>Invoice - {{$invoiceObj->invoice_no}}</title>
    <style>
      @page {
        margin: 1cm 1cm;
      }

      footer {
        position: fixed;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
      }
	  br{margin-bottom: 0px;}
    </style>
  </head>
  <body>
    <header>
      <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse;">
        <tr>
		<td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
          </td>
		  
		   <td style="background:#ffffff;color:#454545;text-align:left;  width:15%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:34%; vertical-align: top;">
            <p style=" font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif; ">PAYMENT STATEMENT</b></p>
            <table style="width:100%; margin-top: -10px;  border-collapse: collapse;">
              <tr>
                <td style="font-family: 'Lato', sans-serif;;font-size:10px;">Invoice No:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{$invoiceObj->invoice_no}}</td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Building :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{isset($invoiceObj->getunit->buildinginfo->building)?$invoiceObj->getunit->buildinginfo->building:''}}  </td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Unit No :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">#{{isset($invoiceObj->getunit->unit)?$invoiceObj->getunit->unit:''}}</td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Invoice Amount:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">$@php
                                 if($invoiceObj->previous_bill_balance >0 && $invoiceObj->previous_bill_balance_type ==2){
                                  $invoice_amt = ($invoiceObj->previous_bill_balance + $invoiceObj->payable_amount);
                                  echo number_format($invoice_amt,2);
                                 }
                                 else{
                                    echo number_format($invoiceObj->payable_amount,2);
                                 }
                              @endphp</td>
              </tr>
              @if($invoiceObj->previous_bill_balance >0 && $invoiceObj->previous_bill_balance_type ==2)
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Previous Excess Payment:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">($@php echo number_format($invoiceObj->previous_bill_balance,2); @endphp)</td>
              </tr>
              @endif
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Received Amount:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">${{number_format($amount_received,2)}} </td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{($balance_amount < 0)?'EXCESS PAID':'BALANCE'}} AMOUNT: :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">$@php
                                 if($balance_amount > 0)
                                    echo number_format($balance_amount,2);
                                 else
                                    echo number_format((0-$balance_amount),2); 
                              @endphp</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
     
    </header>
    <h5 style="padding-left:22px; font-size:10px; "> RECENT PAYMENT TRANSACTION</h5>
    <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff; border-collapse: collapse; margin-top:10px; margin-bottom:10px; padding-left:22px;" class="fulltab">
      <tr>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:12%;">Payment Received by</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:35%;"> Cheque / Transation No / Details</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:8%;">Amount S$</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:9%;"> Received Date</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; font-size:9px; width:8%; text-align: right; padding-right:8px;">Receipt No</th>
      </tr> 
      @if($invoiceObj->payments) @php $count =0;@endphp
        @foreach($invoiceObj->payments as $k => $payment)
          @php  $count++; @endphp
      <tr>
        
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php 
                                    if($payment->payment_option ==1)
                                       echo "Cheque";
                                    else if($payment->payment_option ==2)
                                       echo "Bank Transfer";
                                    else if($payment->payment_option ==4)
                                       echo "Excess Paid";
                                    else if($payment->payment_option ==5)
                                       echo "Online Payment";
                                    else if($payment->payment_option ==6)
                                       echo "Waiver";
                                    else
                                       echo "Cash";
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php
                              if($payment->payment_option ==1)
                                 echo isset($payment->cheque_no)?$payment->cheque_no:'';
                              else if($payment->payment_option ==5) 
                                 echo isset($payment->transaction_id)?$payment->transaction_id:'';
                              else if($payment->payment_option ==6) 
                                 echo isset($payment->credit_notes)?$payment->credit_notes:'';
                              @endphp
                              <br />
                              @if(isset($payment->paymentdetails)) 
                                 @foreach($payment->paymentdetails as $k => $paymentdetail)
                                    {{$paymentdetail->paidtype->reference_type}} {{$paymentdetail->detail->detail}} : <b>${{$paymentdetail->amount}}</b><br/>
                                 @endforeach
                                 
                              @endif
                              </td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;"> $@php 
                                    if($payment->payment_option ==1)
                                       echo number_format($payment->cheque_amount,2); 
                                    else if($payment->payment_option ==2)
                                       echo number_format($payment->bt_amount_received,2);
                                    else if($payment->payment_option ==5)
                                       echo number_format($payment->online_amount_received,2); 
                                    else if($payment->payment_option ==6)
                                       echo number_format($payment->credit_amount,2);   
                                    else
                                       echo number_format($payment->cash_amount_received,2); 
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php 
                                 echo date('d/m/y',strtotime($payment->payment_received_date));
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{isset($payment->receipt_no)?$payment->receipt_no:''}}</td>
      </tr> 
      @endforeach @endif 
      </table>
      <h5 style="padding-left:22px; font-size:10px; "> PAYMENT RECEIVED HISTORY</h5>
    <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff; border-collapse: collapse; margin-top:10px; margin-bottom:10px; padding-left:22px;" class="fulltab">
      <tr>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:12%;">Payment Received by</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:35%;"> Cheque / Transation no / Details</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:8%;">Amount S$</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:9%;"> Received Date</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; font-size:9px; width:8%; text-align: right; padding-right:8px;">Receipt No</th>
      </tr> 
      @if($paymentHistory)
        @foreach($paymentHistory as $k => $payment)
      <tr>
        
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php 
                                    if($payment->payment_option ==1)
                                       echo "Cheque";
                                    else if($payment->payment_option ==2)
                                       echo "Bank Transfer";
                                    else if($payment->payment_option ==4)
                                       echo "Excess Paid";
                                    else if($payment->payment_option ==5)
                                       echo "Online Payment";
                                    else if($payment->payment_option ==6)
                                       echo "Waiver";
                                    else
                                       echo "Cash";
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php
                              if($payment->payment_option ==1)
                                 echo isset($payment->cheque_no)?$payment->cheque_no:'';
                              else if($payment->payment_option ==5) 
                                 echo isset($payment->transaction_id)?$payment->transaction_id:'';
                              else if($payment->payment_option ==6) 
                                 echo isset($payment->credit_notes)?$payment->credit_notes:'';
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;"> $@php 
                                    if($payment->payment_option ==1)
                                       echo number_format($payment->cheque_amount,2); 
                                    else if($payment->payment_option ==2)
                                       echo number_format($payment->bt_amount_received,2);
                                    else if($payment->payment_option ==5)
                                       echo number_format($payment->online_amount_received,2); 
                                    else if($payment->payment_option ==6)
                                       echo number_format($payment->credit_amount,2);   
                                    else
                                       echo number_format($payment->cash_amount_received,2); 
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php 
                                 echo date('d/m/y',strtotime($payment->payment_received_date));
                              @endphp</td>
        <td style="vertical-align: text-top; border-bottom: 1px solid #d9d9d9!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{isset($payment->receipt_no)?$payment->receipt_no:''}}</td>
      </tr> 
      @endforeach @endif 
       
    </table>

  </body>
</html>