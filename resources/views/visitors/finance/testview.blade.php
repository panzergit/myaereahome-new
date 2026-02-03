<!doctype html public "-//w3c//dtd html 4.01//en" "http://www.w3.org/tr/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <title>Invoice - {{$Unitinvoice->invoice_no}}</title>
    <style>
        @page {
            margin: 1cm 1cm;
          }
    
          .footer {
 position: absolute; 
         bottom:-50;
         right:0;
        /* margin-left: 500px;*/
        float: right;
          }
		  body { 

  }
   .line {

      }
    	  br{margin-bottom: 0px;}
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    
    @if($showPdf==0)
    <div class="line" id="notesdiv" style="font-family: 'Lato', sans-serif; font-size:10px; border: 1px solid #c0c0c0 ;  text-align:justify; line-height:11px;  background: #dee2e6; padding:6px; margin-left:22px; height:auto; padding-top:20px; margin-top:20px; padding-bottom:20px; margin-bottom:20px;">
        @php echo nl2br($invoice->notes) @endphp 
	</div>
	<p>preparing PDF...</p>
	@else
    <div style="page-break-after: auto; height: 100vh;">
	<!-- Header Start-->
        @include('finance.include.testheader')
		<!-- Header End-->
		<!-- body Start-->
    @include('finance.include.tablehead')
            @php 
            $rowList=$pdfCustomRows;
            echo $rowList;
            $row_count = 0;
            $page_count = 1;
            @endphp
            @if(isset($previousDetails)) @foreach($previousDetails as $k => $detail) @if($detail->total_amount >0 && $detail->status==0)

            <tr>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;
                    @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;
                    @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;
                    @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($detail->detail)?$detail->detail:''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;
                    @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{number_format($detail->amount,2)}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6; 
                    @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                <td style="vertical-align: text-top;  border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{number_format($detail->amount,2)}}</td>
            </tr>
            @php  
              if($row_count ==$rowList){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.footer')
                  @include('finance.include.header')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp

          
            @endif @endforeach @endif
            <!-- Last Payment details Start-->
            @if(isset($LastInvoicePayments)) @foreach($LastInvoicePayments as $LastInvoicePayment)
            <tr>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif"></td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">@php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4){ echo "Excess payment"; $ExcessObj = new \App\Model\v2\FinanceInvoice();
                    $ExcessPaymentDate = $ExcessObj->excesspaymentdate($LastInvoice->id,$LastInvoice->unit_no); if(isset($ExcessPaymentDate) && $ExcessPaymentDate !='') echo " (paid on :".date('d/m/y',strtotime($ExcessPaymentDate)).")"; } else if($LastInvoicePayment->payment_option
                    ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                    ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                    } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                    else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                    @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                </td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                </td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">(@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                </td>
            </tr>
            @php  
              if($row_count ==$rowList){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.footer')
                  @include('finance.include.header')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
              @if($LastInvoicePayment->status==2 )
                <tr>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                    <td style="vertical-align: text-top;  border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif"></td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">
                    @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                        ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                        ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                        if($LastInvoicePayment->status ==2) echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":''; } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                        else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                        @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                    </td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                    </td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">(-@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                    </td>
                </tr>
                
                @php  
                  if($row_count ==$rowList){ 
                    $row_count =0;
                    @endphp
                      @include('finance.include.tablefooter')
                      @include('finance.include.footer')
                      @include('finance.include.header')
                      @include('finance.include.tablehead')
                    @php 
                    $page_count++;
                  }
                  else{ $row_count++;}
                @endphp
              @endif
            @endforeach @endif @if(isset($currentDetails)) @foreach($currentDetails as $detail) @if($detail->total_amount >0)
            <tr>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($Unitinvoice->invoice_date)?date('d/m/y',strtotime($Unitinvoice->invoice_date)):''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{isset($detail->detail)?$detail->detail:''}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{number_format($detail->total_amount,2)}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{number_format($detail->total_amount,2)}}</td>
            </tr>
            @php  
              if($row_count ==$rowList){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.footer')
                  @include('finance.include.header')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
            @endif @endforeach @endif 
           
            @if(isset($CurrentInvoicePayments)) @foreach($CurrentInvoicePayments as $LastInvoicePayment)
            
            @if(isset($LastInvoicePayment->payment_option) && $LastInvoicePayment->payment_option !=4 )
            <tr>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif"></td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">
                @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                    ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                    ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                    } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                    else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                    @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                </td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                </td>
                <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">(@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                </td>
            </tr>

            @php  
              if($row_count ==$rowList){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.footer')
                  @include('finance.include.header')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
                @if($LastInvoicePayment->status==2 )
                  <tr>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6; @if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif"></td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">
                    @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                        ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                        ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                        if($LastInvoicePayment->status ==2) echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":''; } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                        else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                        @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                    </td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">0.00</td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                    </td>
                    <td style="vertical-align: text-top; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; text-align: right; padding-right:8px; line-height: 1.6;@if($row_count==$rowList) border-bottom: 1px solid #d9d9d9; @else border-bottom: 0px solid #fff!important; @endif">(-@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                    </td>
                  </tr>
                
                  @php  
                  if($row_count ==$rowList){ 
                    $row_count =0;
                    @endphp
                      @include('finance.include.tablefooter')
                      @include('finance.include.footer')
                      @include('finance.include.header')
                      @include('finance.include.tablehead')
                    @php 
                    $page_count++;
                  }
                  else{ $row_count++;}
                @endphp
              @endif
            @endif @endforeach @endif @php $advance_amount = 0; @endphp 


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
          @include('finance.include.tablefooter')
		<!-- body End-->
        <!--p style="font-family: 'Lato', sans-serif; font-size:10px; ">@php echo nl2br($invoice->notes) @endphp </p-->
		<!-- footer Start-->
    @include('finance.include.testfooter')
		<!-- footer End-->
    </div>
    @endif
</body>
<script>
    $(document).ready(function () {
        @if($showPdf==0)
        let notesHeight = document.getElementById("notesdiv").clientHeight;
        $('#notesdiv').remove();
        $.ajax({
            url: "/set-pdf-rows?height="+notesHeight,
            method: "GET",
            success: function (data) {
                if(data.status) window.location.reload();
                if(!data.status) alert("Error generating PDF");
            },
            error: function () {
                alert("Error generating PDF");
            }
        });
        @endif
     });
</script>
</html>