<!doctype html public "-//w3c//dtd html 4.01//en" "http://www.w3.org/tr/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <title>Invoice - {{$Unitinvoice->invoice_no}}</title>
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
            <p style=";font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif">@php echo isset($Unitinvoice->propertydetail->mcst_code)?"MCST:".$Unitinvoice->propertydetail->mcst_code:'' @endphp</b><br>{{isset($invoice->comp_name)?$invoice->comp_name:''}}<br> @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp </p>

			
          </td>
		  
		   <td style="background:#ffffff;color:#454545;text-align:left;  width:16%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:33%; vertical-align: top;">
            <p style=";font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif; ">INVOICE / STATEMENT</b></p>
            <table style="width:100%; margin-top: -10px;  border-collapse: collapse;">
              <tr>
                <td style="font-family: 'Lato', sans-serif;;font-size:10px;">Invoice No:</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{$Unitinvoice->invoice_no}}</td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Invoice Date :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{isset($invoice->month)?date('d/m/Y',strtotime($invoice->month)):''}}</td>
              </tr>
              <tr>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">Due Date :</td>
                <td style="font-family: 'Lato', sans-serif;font-size:10px;">{{isset($invoice->due_date)?date('d/m/Y',strtotime($invoice->due_date)):''}}</td>
              </tr>
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
     <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse; margin-bottom:30px; margin-top:60px;">
	  <tr>
	  <td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
              <p style="padding-left:2px; border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:4px 6px; line-height: 1.6;"><b>Mailing Address</b> <br/>
          @if(isset($primary_contact)) 
          {{$primary_contact->name}} {{isset($primary_contact->userinfo->last_name)?$primary_contact->userinfo->last_name:''}}
          <br />
          @php echo isset($primary_contact->userinfo->mailing_address)?nl2br($primary_contact->userinfo->mailing_address):'' @endphp 
          <br/>
          @php echo isset($primary_contact->userinfo->postal_code)?"Singapore ".nl2br($primary_contact->userinfo->postal_code):'' @endphp                     
          @endif</p>

			
          </td>
		  <td style="background:#ffffff;color:#454545;text-align:left;  width:16%; vertical-align: top;"></td>
          <td style="background:#ffffff;color:#454545;text-align:left;  width:33%; vertical-align: top;">
           <p style="padding-left:2px; border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:4px 6px; line-height: 1.6;">	<b>Billing Address</b> <br/>@if(isset($purchasers)) @foreach($purchasers as $k => $purchaser) @php if($k >0) echo ", "; @endphp {{$purchaser->name}} {{isset($purchaser->userinfo->last_name)?$purchaser->userinfo->last_name:''}}</b> @endforeach @endif<br />@php echo isset($Unitinvoice->propertydetail->company_name)?$Unitinvoice->propertydetail->company_name:'' @endphp <br/> #{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}}
                  <br /> @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp</p>
          </td>
        </tr>
      </table>
    </header>
    <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff; border-collapse: collapse; margin-top:10px; margin-bottom:10px; padding-left:22px;" class="fulltab">
      <tr>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:18%; ">Reference Type</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:11%;">Reference No</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:left;font-size:9px; width:30%;"> Description</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:11%;"> Amount Due S$</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; text-align:right; padding-right:8px; font-size:9px; width:10%;"> Paid S$</th>
        <th style="padding-left:8px; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif; font-size:9px; width:10%; text-align: right; padding-right:8px;">Balance S$</th>
      </tr> 
      @if(isset($Unitinvoice->paymentdetails)) @foreach($Unitinvoice->paymentdetails as $detail) 
      <tr>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->referencetypes->reference_type)?$detail->referencetypes->reference_type:''}}</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->reference_no)?$detail->reference_no:''}}</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px; line-height: 1.6;">{{isset($detail->detail)?$detail->detail:''}}</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->total_amount,2)}}</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;"> @php $paid = $detail->total_amount - $detail->amount; echo number_format($paid,2) @endphp </td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; line-height: 1.6;">{{number_format($detail->amount,2)}}</td>
      </tr> 
      
      @endforeach @endif 

      @php $advance_amount  = 0; @endphp
      @if(isset($Unitinvoice->AdvancePayment))
        @php $advance_amount = $Unitinvoice->AdvancePayment->amount; @endphp
        <tr>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"></td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"></td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;font-weight:bold">Advance Payment</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; "></td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; "> {{number_format($Unitinvoice->AdvancePayment->amount,2)}}</td>
        <td style="vertical-align: text-top; border-bottom: 0px solid #fff!important; border-top: 0px solid #fff!important;  padding-left:8px; border-right: 1px solid #d9d9d9; border-left: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;     text-align: right; padding-right:8px; ">({{number_format($Unitinvoice->AdvancePayment->amount,2)}})</td>
      </tr> 
      @endif
      <tr>
        <td style="padding-left:8px; border: 1px solid #d9d9d9; " colspan="4"></td>
        <td style="padding-right:8px; text-align:right; border: 1px solid #d9d9d9; font-family: 'Lato', sans-serif;font-size:9px;"><b>{{($Unitinvoice->payable_amount < 0)?'EXCESS AMOUNT':'AMOUNT DUE'}}:</b></td>
        <td style="padding-left:8px;  border: 1px solid #d9d9d9;  font-family: 'Lato', sans-serif;font-size:9px; text-align: right; padding-right:8px;"><b>S$
        @php 
        if($Unitinvoice->payable_amount > 0) {
          $invoice_amount  = $Unitinvoice->payable_amount - $advance_amount;
          echo number_format($invoice_amount,2);
        }
        else echo "(".number_format((0-$Unitinvoice->payable_amount),2).")"; 
        @endphp</b></td>
      </tr>
    </table>
    <!--p style="font-family: 'Lato', sans-serif; font-size:10px; ">@php echo nl2br($invoice->notes) @endphp </p-->
	<textarea style="font-family: 'Lato', sans-serif; font-size:10px; border: 1px solid #c0c0c0 ; height: auto; text-align:justify; line-height:11px; white-space: pre-line; background: #dee2e6; padding:6px; margin-left:22px;">@php echo nl2br($invoice->notes) @endphp</textarea>
							  
    <footer style="padding: -15px; padding-left:22px;">
      <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;   ">
        <tr>
          <td style="background:#ffffff;text-align:left;; width:100%">
            <table style="width:100%; border-collapse: collapse;">
              <tr>
                <td style=" text-align: center;
                                          background: #d9d9d9;font-size:10px;">----- Please detach this portion and return with crossed Cheque ----</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table border="0" align="center" style="margin:0 auto; width:100%; background:#ffffff;  border-collapse: collapse;   ">
        <tr>
          <td style="background:#ffffff; text-align:left;  width:50%; vertical-align: top; margin-top: 15px;">
            <table style="width:100%;  border-collapse: collapse; margin-top: 7px;">
              <tr style="    background: #d9d9d9;font-size:10px;">
                <th style="font-size:10px; padding-left:2px; text-align:left; ">Account No: {{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}</th>
              </tr>
              <tr style="border:1px solid black;">
                <td style="padding-left:2px; border: 1px solid #d9d9d9;font-size:10px; line-height: 1.6;"> @if(isset($purchasers)) @foreach($purchasers as $k => $purchaser) @php if($k >0) echo ", "; @endphp <b>{{$purchaser->name}} {{isset($purchaser->userinfo->last_name)?$purchaser->userinfo->last_name:''}}</b> @endforeach @endif<br /> #{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}}
                  <br /> @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp
                </td>
              </tr>
            </table>
          </td>
          <td style="background:#ffffff;text-align:left;  width:50%">
            <table style="width:100%">
              <tr>
                <td style="vertical-align: text-top; font-size:10px; width:30%"><b>Mail To : </b></td>
                <td style="font-size:10px; width:70%; line-height: 1.6;">
                  {{isset($invoice->comp_name)?$invoice->comp_name:''}}<br /> @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp
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
                <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Email To: </b></td>
                <td style="font-size:10px;  width:70%; padding-bottom:-10px;"> @php echo isset($Unitinvoice->propertydetail->enquiry_email)?nl2br($Unitinvoice->propertydetail->enquiry_email):'' @endphp </td>
              </tr>
              <tr>
                <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Call At: </b></td>
                <td style="font-size:10px;  width:70%; padding-bottom:-10px;">@php echo isset($Unitinvoice->propertydetail->enquiry_contact)?nl2br($Unitinvoice->propertydetail->enquiry_contact):'' @endphp</td>
              </tr>
			  <tr>
                <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Invoice No:</b></td>
                <td style="font-size:10px; width:70%; padding-bottom:-10px;">{{$Unitinvoice->invoice_no}}</td>
              </tr>
              <tr>
                <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Invoice Date:</b></td>
                <td style="font-size:10px; width:70%; padding-bottom:-10px;">{{isset($invoice->month)?date('d M Y',strtotime($invoice->month)):''}}</td>
              </tr>
              <tr>
                <td style="vertical-align: text-top; font-size:10px; width:30%; padding-bottom:-10px;"><b>Amount Payable:</b></td>
                <td style="font-size:10px; width:70%; padding-bottom:-10px;">${{$Unitinvoice->payable_amount}}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; ">
        <tr>
          <td style="background:#ffffff;text-align:left;  width:100%">
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
      </table>
    </footer>
  </body>
</html>