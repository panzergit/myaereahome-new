<!DOCTYPE html>
<html>
<head>
<title>Statement - {{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}</title>
  <!--link href='http://fonts.googleapis.com/css?family=YOURFONTFAMILY' rel='stylesheet' type='text/css'-->
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
    </style>
</head>
<body style="background:#fff;">

	   <div style="page-break-after: auto; height: 100vh;">
      <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse;">
        <tr>
		<td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
             <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
            <p style=";font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif">@php echo isset($Unitinvoice->propertydetail->mcst_code)?"MCST:".$Unitinvoice->propertydetail->mcst_code:'' @endphp</b><br>{{isset($invoice->comp_name)?$invoice->comp_name:''}}<br> @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp </p>
	
		
			
          </td>
		  <td style="background:#ffffff;color:#454545;text-align:left;  width:16%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:33%; vertical-align: top;">
            <p style=";font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif; ">INVOICE / STATEMENT</b></p>
            <table style="width:100%; margin-top: -10px;  border-collapse: collapse;">
            
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Account No:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}</td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Share Value :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{$Unitinvoice->unit_share}}</td>
              </tr>
            </table>
			
          </td>
        </tr>
    
      </table>

      <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse; margin-bottom:0px; margin-top:-10px;">
               <tr>
                  <td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
                  <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
				         <p style="; border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:2px 6px; line-height: 1.6;"><b>Mailing Address</b> <br/>
                     @if(isset($primary_contact)) 
                        {{$primary_contact->first_name}} {{isset($primary_contact->last_name)?$primary_contact->last_name:''}}
                        <br />
                        @php echo isset($primary_contact->mailing_address)?nl2br($primary_contact->mailing_address):'' @endphp  
                        <br/>
                        @php echo isset($primary_contact->country)?$primary_contact->usercountry->country_name:'' @endphp  
                        @php echo isset($primary_contact->postal_code)?" ".nl2br($primary_contact->postal_code):'' @endphp                    
                        @endif</p>
				      </td>
				      <td style="background:#ffffff;color:#454545;text-align:left;  width:16%; vertical-align: top;"></td>
                  <td style="background:#ffffff;color:#454545;text-align:left;  width:33%; vertical-align: top;">
		               <p style=" border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:2px 6px; line-height: 1.6;">	<b>Billing Address</b> <br/>@if(isset($purchasers))
                        @php $counts =0; @endphp @foreach($purchasers as $k => $purchaser)  @if($purchaser->status ==1) @php if($counts >0) echo ", "; @endphp {{$purchaser->first_name}} {{isset($purchaser->last_name)?$purchaser->last_name:''}} @php $counts++; @endphp @endif</b> @endforeach @endif<br />@php echo isset($Unitinvoice->propertydetail->company_name)?$Unitinvoice->propertydetail->company_name:'' @endphp <br/> #{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}}
                                 <br /> @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp</p>
		            </td>
               </tr>
            </table>
      @if(isset($invoicesLists) && !empty($invoicesLists))
         @foreach($invoicesLists as $invoicesList)

            @php
            $InvoiceInfo = $invoicesList['InvoiceInfo'];
            $InvoiceMoreInfo = $invoicesList['InvoiceMoreInfo'];
            $LastInvoice = $invoicesList['LastInvoice'];
            $LastInvoicePayments = $invoicesList['LastInvoicePayments'];
            $previousDetails = $invoicesList['previousDetails'];
            $currentDetails = $invoicesList['currentDetails'];
            $CurrentInvoicePayments = $invoicesList['CurrentInvoicePayments'];
            $balance_amount = $invoicesList['balance_amount'];
            @endphp
			  
     <table style=" padding-left:22px; margin-bottom:0px; border-collapse: collapse;">
               <tr>
                  <td style="font-family: 'Lato', sans-serif; font-size:11px; width:30%;">
                     Invoice No:{{ $InvoiceInfo->invoice_no}}
                  </td>
                  <td  style="font-family: 'Lato', sans-serif; font-size:11px; text-align:right; width:70%;">
                     Invoice Date:{{isset($InvoiceMoreInfo->month)?date('d/m/Y',strtotime($InvoiceMoreInfo->month)):''}}
                  </td>
               </tr>
            </table>

            <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff; border-collapse: collapse; margin-top:6px; margin-bottom:6px;  padding-left:22px;" class="fulltab">
               <tr>
                  <td style="background:#ffffff;color:#000;text-align:left;  width:100%">
                     <table style="width:100%; border-collapse: collapse;">
                        <tr style="">
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:6%; ">Date</th>
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:9%;">Reference No</th>
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:38%;"> Description</th>
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:11%;"> Amount Due S$</th>
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:10%; text-align: right; padding-right:8px;"> Paid S$</th>
                           <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; font-size:9px; width:8%; text-align: right; padding-right:8px;">Balance S$</th>
                        </tr>
                        @if(isset($previousDetails)) 
                           @foreach($previousDetails as $detail) 
                               @if($detail->total_amount >0 && $detail->status==0)
                                 <tr>
                                    <td style="    vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):''}}</td>
                                                
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                                                   
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->detail)?$detail->detail:''}}</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00 </td>
                                    <td style=" vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
                                 </tr>
                              @endif 
                           @endforeach 
                        @endif

                        @if(isset($LastInvoicePayments))
                           @foreach($LastInvoicePayments as $LastInvoicePayment)
                              <tr>
                                 <td style="    vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                                            
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                                             
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php 
                                                         if($LastInvoicePayment->payment_option ==1)
                                                            echo "Cheque";
                                                         else if($LastInvoicePayment->payment_option ==2)
                                                            echo "Bank Transfer";
                                                         else if($LastInvoicePayment->payment_option ==4){
                                                            echo "Excess payment";
                                                            $ExcessObj = new \App\Model\v2\FinanceInvoice();
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

                                                      @if($LastInvoicePayment->payment_option !=4  && $LastInvoicePayment->status !=2)
                                                         payment
                                                      @endif</td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php 
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
                                             @endphp</td>
                                 <td style=" vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(@php 
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
                           @endforeach
                        @endif

                        @if(isset($currentDetails)) 
                           @foreach($currentDetails as $detail) 
                              @if($detail->total_amount >0)
                                 <tr>
                                    <td style="    vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($Unitinvoice->invoice_date)?date('d/m/y',strtotime($Unitinvoice->invoice_date)):''}}</td>
                                             
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                                             
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->detail)?$detail->detail:''}}</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->total_amount,2)}}</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">
                                             @php
                                                $paid = $detail->total_amount - $detail->amount;
                                                echo number_format($paid,2)
                                             @endphp
                                             </td>
                                    <td style=" vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
                                 </tr>
                              @endif
                           @endforeach
                        @endif

                        @if(isset($CurrentInvoicePayments))
                           @foreach($CurrentInvoicePayments as $LastInvoicePayment)
                              @if($LastInvoicePayment->payment_option !=4 )
                                 <tr>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"> @php 
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
                                                   @if($LastInvoicePayment->payment_option !=4  && $LastInvoicePayment->status !=2)
                                                         payment
                                                      @endif</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php 
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
                                             @endphp</td>
                                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(@php 
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
                                    

                        @php $advance_amount  = 0; @endphp
                        @if(isset($Unitinvoice->AdvancePayment))
                           @php $advance_amount = $Unitinvoice->AdvancePayment->amount; @endphp
                           @if(1==2)
                              <tr>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"></td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"></td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;font-weight:bold">Advance Payment</td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; "></td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; "> {{number_format($Unitinvoice->AdvancePayment->amount,2)}}</td>
                                 <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; ">({{number_format($Unitinvoice->AdvancePayment->amount,2)}})</td>
                              </tr> 
                           @endif
                        @endif
                        <tr>
                           <td style="padding-left:8px; border: 1px solid #d9d9d9; " colspan="4"></td>
                           <td style="padding-right:8px; text-align:right; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"><b>{{($balance_amount < 0)?'EXCESS AMOUNT':'AMOUNT DUE'}}:</b></td>
                           <td style="padding-left:8px;  border: 1px solid #d9d9d9;  font-family: 'Lato', sans-serif;font-size:9px; text-align: right; padding-right:8px;"><b>$@php
                                                                  if($balance_amount > 0)
                                                                     echo number_format($balance_amount,2);
                                                                  else
                                                                     echo number_format((0-$balance_amount),2); 
                                                               @endphp</b></td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
         @endforeach
      @endif


<table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;   ">
        <tr>
          <td style="background:#ffffff;text-align:left;; width:100%">
            <table style="width:100%; border-collapse: collapse;">
              <tr>
                <td><p style="font-family: 'Lato', sans-serif; font-size:10px; border: 1px solid #c0c0c0 ; height: auto; text-align:justify; line-height:11px; white-space: pre-line; background: #dee2e6; padding:6px; margin-left:22px;">@php echo nl2br($invoice->notes) @endphp</p></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>		  
					<div style='page-break-inside:avoid;vertical-align:bottom; '>	
		  <footer style="padding: -15px; padding-left:22px; page-break-before:; "  size="1">
			<table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;" >
                                 <tr>
                                    <td style="background:#ffffff;color:#000;text-align:left;  width:100%">
                                       <table style="width:100%; border-collapse: collapse;">
									     <tr >
			  <td style=" text-align: center;">
			  ----------------------------------------------------------------------------------------------------------------------------------
			 
			  </td>
			    </tr >
                                          <tr>
                                             <td style="       text-align: center;
                                                background: #d9d9d9;font-size:10px;">----- Please detach this portion and return with crossed Cheque ----</td>
                                          </tr>
                                       </table>
                                    </td>
                                 </tr>
                              </table>
                              <table border="0" align="center" style="margin:0 auto; width:100%; background:#ffffff;  border-collapse: collapse;" >
                                 <tr>
                                    <td style="background:#ffffff; color:#000;text-align:left;  width:50%; vertical-align: top; margin-top: 15px;">
                                       <table style="width:100%;  border-collapse: collapse; margin-top: 7px;">
                                          <tr style="  text-align:left;  background: #d9d9d9;font-size:10px;">
                                             <th style="font-size:10px; padding-left:2px; text-align:left;">Account No: {{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}</th>
                                          </tr>
                                          <tr style="border:1px solid black;">
                                             <td style="padding-left:2px; border: 1px solid #d9d9d9;font-size:10px; line-height: 1.6;" > @if(isset($purchasers))
                                             @foreach($purchasers as $k => $purchaser)
                                                @php if($k >0)
                                                   echo ", "; 
                                                @endphp

                                                <b>{{$purchaser->name}} {{isset($purchaser->last_name)?$purchaser->last_name:''}}</b>
                                             @endforeach
                                          @endif<br />
                                          #{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}} 
                                       <br />
                                          @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp
                                             </td>
                                          </tr>
                                       </table>
                                       
                                    </td>
                                    <td style="background:#ffffff;color:#000;text-align:left;  width:50%">
                                       <table style="width:100%">
                                          <tr>
                                             <td style="vertical-align: text-top; font-size:10px; width:30%"><b>Mail To : </b></td>
                                             <td style="font-size:10px; width:70%; line-height: 1.6;"> 
                                             {{isset($invoice->comp_name)?$invoice->comp_name:''}}<br />
                                             @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp 
                                             </td>
                                          </tr>
                                       </table>
                                       <table style="width:100%">
                                          <tr>
                                             <td style="font-size:10px;"><b>FOR ACCOUNT ENQUIRY</b></td>
                                          </tr>
                                       </table>
                                       <table style="width:100%;font-size:10px;">
                                          <tr>
										  <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Email To : </b></td>
                                             <td style="font-size:10px; width:70%; padding-bottom:-10px;"> @php echo isset($Unitinvoice->propertydetail->enquiry_email)?nl2br($Unitinvoice->propertydetail->enquiry_email):'' @endphp</td>
                                          </tr>
										   <tr>
										  <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Call At : </b></td>
                                             <td style="font-size:10px; width:70%; padding-bottom:-10px;"> @php echo isset($Unitinvoice->propertydetail->enquiry_contact)?nl2br($Unitinvoice->propertydetail->enquiry_contact):'' @endphp</td>
                                          </tr>
										    <tr>
                                             <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Invoice No:</b></td>
                                             <td style="font-size:10px; width:70%; padding-bottom:-10px;">{{$Unitinvoice->invoice_no}}</td>
                                          </tr>
                                          <tr>
										    <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Invoice Date:  </b></td>
                                             <td style="font-size:10px; width:70%; padding-bottom:-10px;">{{isset($invoice->month)?date('d M Y',strtotime($invoice->month)):''}}</td>
                                          </tr>
                                          <tr>
										    <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Amount Payable:</b></td>
                                             <td style="font-size:10px; width:70%; padding-bottom:-10px;">${{$latest_balance_amount}}</td>
                                          </tr>
                                       </table>
                                    </td>
                                 </tr>
                              </table> 
      <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff;" >
                                 <tr>
                                    <td style="background:#ffffff;color:#000;text-align:left; padding:0px 0px; width:100%">
                                       <table style="width:100%">
                                          <tr>
                                             <td style="font-size:10px;">Cheque No: ______________________________</td>
                                          </tr>
                                       </table>
                                       <table style="width:100%">
                                          <tr>
                                             <td style="font-size:10px;">Indicate your Account Number, Condo Or MCST Number, Unit & Contact Number on the reverse side of your cheque.</td>
                                          </tr>
                                       </table>
                                       <table style="width:100%; margin-bottom: 40px;">
                                          <tr>
                                             <td style="font-size:10px;">Responding Address: ______________________________</td>
                                          </tr>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="background:#ffffff;color:#000;text-align:left; padding:0px 0px; width:100%">
                                       <table style="width:100%">
                                          <tr>
                                             <td style="font-size:10px;">Print Date: {{$print_date}}</td>
                                          </tr>
                                       </table>
                                       
                                    </td>
                                 </tr>
                              </table>
                              
							  </footer>
							  </div>
							  </div>
	  
   

</body>
</html>

