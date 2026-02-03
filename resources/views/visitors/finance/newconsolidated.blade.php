<!DOCTYPE html>
<html>
<head>
<title>Statement - {{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}</title>
  <!--link href='http://fonts.googleapis.com/css?family=YOURFONTFAMILY' rel='stylesheet' type='text/css'-->
   <style>
      @page {
        margin: 1cm 1cm;
      }
   .footer {

position: absolute;
         bottom:-50;         display: block;

		
          }
		
    </style>
</head>
<body style="background:#fff;">
	   <div style="cons">
      <!-- Header Start-->
         @include('finance.include.statheader')
      <!-- Header End-->
      @php
         $row_count = 0;
         $page_count = 1;
      @endphp
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
            @php  
              if($page_count ==1 && $row_count ==44){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else if($page_count >1 && $row_count ==58){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
            <!-- body Start-->
            @include('finance.include.tablehead')
            
            @if(isset($previousDetails)) @foreach($previousDetails as $k => $detail) @if($detail->total_amount >0 && $detail->status==0)

            <tr>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($LastInvoice->invoice_date)?date('d/m/y',strtotime($LastInvoice->invoice_date)):''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->detail)?$detail->detail:''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
            </tr>
            @php  
              if($page_count ==1 && $row_count ==40){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else if($page_count >1 && $row_count ==58){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp

          
            @endif @endforeach @endif
            <!-- Last Payment details Start-->
            @if(isset($LastInvoicePayments)) @foreach($LastInvoicePayments as $LastInvoicePayment)
              @if(isset($LastInvoicePayment->payment_option) && $LastInvoicePayment->payment_option !=4 )
              <tr>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">@php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4){ echo "Excess payment"; $ExcessObj = new \App\Model\v2\FinanceInvoice();
                      $ExcessPaymentDate = $ExcessObj->excesspaymentdate($LastInvoice->id,$LastInvoice->unit_no); if(isset($ExcessPaymentDate) && $ExcessPaymentDate !='') echo " (paid on :".date('d/m/y',strtotime($ExcessPaymentDate)).")"; } else if($LastInvoicePayment->payment_option
                      ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                      ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                     } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                      else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                      @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                  </td>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                      ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                      number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                  </td>
                  <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                      ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                      number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                  </td>
              </tr>
              @php  
              if($page_count ==1 && $row_count ==40){ 
                  $row_count =0;
                  @endphp
                    @include('finance.include.tablefooter')
                    @include('finance.include.pagenumber')
                    @include('finance.include.tablehead')
                  @php 
                  $page_count++;
                }
                else if($page_count >1 && $row_count ==58){ 
                  $row_count =0;
                  @endphp
                    @include('finance.include.tablefooter')
                    @include('finance.include.pagenumber')
                    @include('finance.include.tablehead')
                  @php 
                  $page_count++;
                }
                else{ $row_count++;}
              @endphp
                @if($LastInvoicePayment->status==2 )
                  <tr>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">
                      @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                          ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                          ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                          if($LastInvoicePayment->status ==2) echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":''; } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                          else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                          @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                      </td>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                          ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                          number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                      </td>
                      <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(-@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                          ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                          number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                      </td>
                  </tr>
                  @php  
                  if($page_count ==1 && $row_count ==40){ 
                      $row_count =0;
                      @endphp
                        @include('finance.include.tablefooter')
                        @include('finance.include.pagenumber')
                        @include('finance.include.tablehead')
                      @php 
                      $page_count++;
                    }
                    else if($page_count >1 && $row_count ==58){ 
                      $row_count =0;
                      @endphp
                        @include('finance.include.tablefooter')
                        @include('finance.include.pagenumber')
                        @include('finance.include.tablehead')
                      @php 
                      $page_count++;
                    }
                    else{ $row_count++;}
                  @endphp
                  @endif
            @endif
            @endforeach @endif @if(isset($currentDetails)) @foreach($currentDetails as $detail) @if($detail->total_amount >0)
            <tr>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($Unitinvoice->invoice_date)?date('d/m/y',strtotime($Unitinvoice->invoice_date)):''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->detail)?$detail->detail:''}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->total_amount,2)}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->total_amount,2)}}</td>
            </tr>
            @php  
            if($page_count ==1 && $row_count ==40){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else if($page_count >1 && $row_count ==58){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
            @endif @endforeach @endif @if(isset($CurrentInvoicePayments)) @foreach($CurrentInvoicePayments as $LastInvoicePayment) @if(isset($LastInvoicePayment->payment_option) && $LastInvoicePayment->payment_option !=4 )
            <tr>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->payment_received_date))}}</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">
                @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                    ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                    ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                    } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                    else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                    @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                </td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                </td>
                <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                    ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                    number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                </td>
            </tr>
            @php  
            if($page_count ==1 && $row_count ==40){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else if($page_count >1 && $row_count ==58){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
              @if($LastInvoicePayment->status==2 )
                <tr>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{date('d/m/y',strtotime($LastInvoicePayment->bounced_cheque_date))}}</td>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;"></td>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">
                    @php if($LastInvoicePayment->payment_option ==1) echo "Cheque"; else if($LastInvoicePayment->payment_option ==2) echo "Bank Transfer"; else if($LastInvoicePayment->payment_option ==4) echo "Excess Paid"; else if($LastInvoicePayment->payment_option
                        ==5) echo "Online Payment"; else if($LastInvoicePayment->payment_option ==7){ if($LastInvoicePayment->add_amt_received_by ==1) echo "Cheque"; else if($LastInvoicePayment->add_amt_received_by ==2) echo "Bank Transfer"; else if($LastInvoicePayment->add_amt_received_by
                        ==3) echo "Cash"; } else if($LastInvoicePayment->payment_option ==6) echo "Waiver"; else echo "Cash"; @endphp @php if($LastInvoicePayment->payment_option ==1){ echo isset($LastInvoicePayment->cheque_no)?$LastInvoicePayment->cheque_no:'';
                        if($LastInvoicePayment->status ==2) echo isset($LastInvoicePayment->remarks)?" (".$LastInvoicePayment->remarks.")":''; } else if($LastInvoicePayment->payment_option ==5) echo isset($LastInvoicePayment->transaction_id)?$LastInvoicePayment->transaction_id:'';
                        else if($LastInvoicePayment->payment_option ==6) echo isset($LastInvoicePayment->credit_notes)?$LastInvoicePayment->credit_notes:''; else if($LastInvoicePayment->payment_option ==7) echo isset($LastInvoicePayment->add_amt_notes)?$LastInvoicePayment->add_amt_notes:'';
                        @endphp @if($LastInvoicePayment->payment_option !=4 && $LastInvoicePayment->status !=2) payment @endif
                    </td>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">0.00</td>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp
                    </td>
                    <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">(-@php if($LastInvoicePayment->payment_option ==1) echo number_format($LastInvoicePayment->cheque_amount,2); else if($LastInvoicePayment->payment_option ==2) echo number_format($LastInvoicePayment->bt_amount_received,2); else if($LastInvoicePayment->payment_option
                        ==5) echo number_format($LastInvoicePayment->online_amount_received,2); else if($LastInvoicePayment->payment_option ==6) echo number_format($LastInvoicePayment->credit_amount,2); else if($LastInvoicePayment->payment_option ==7) echo
                        number_format($LastInvoicePayment->add_amt_received,2); else echo number_format($LastInvoicePayment->cash_amount_received,2); @endphp)
                    </td>
                </tr>
                @php  
                if($page_count ==1 && $row_count ==40){ 
                    $row_count =0;
                    @endphp
                      @include('finance.include.tablefooter')
                      @include('finance.include.pagenumber')
                      @include('finance.include.tablehead')
                    @php 
                    $page_count++;
                  }
                  else if($page_count >1 && $row_count ==58){ 
                    $row_count =0;
                    @endphp
                      @include('finance.include.tablefooter')
                      @include('finance.include.pagenumber')
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
            @php  
            if($page_count ==1 && $row_count ==40){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else if($page_count >1 && $row_count ==58){ 
                $row_count =0;
                @endphp
                  @include('finance.include.tablefooter')
                  @include('finance.include.pagenumber')
                  @include('finance.include.tablehead')
                @php 
                $page_count++;
              }
              else{ $row_count++;}
            @endphp
          @include('finance.include.tablefooter')
		
         @endforeach
      @endif


      <!-- footer Start-->
         @include('finance.include.statfooter')
		 
		<!-- footer End-->
	</div>
	  
   

</body>
</html>

