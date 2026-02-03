<div class="header">
        <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse;">
            <tr>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
                    <p style=";font-size:10px; margin-top: 0px;"><b style="margin-bottom: 0px;font-size: 10px;  font-family: 'Lato', sans-serif">@php echo isset($Unitinvoice->propertydetail->mcst_code)?"MCST:".$Unitinvoice->propertydetail->mcst_code:'' @endphp</b>
                        <br>{{isset($invoice->comp_name)?$invoice->comp_name:''}}
                        <br> @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp </p>
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
        <table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;  border-collapse: collapse; margin-bottom:0px; margin-top:5px;">
            <tr>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:3%; vertical-align: top;"></td>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:50%;     vertical-align: top;     ">
                    <p style="padding-left:2px; border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:4px 6px; line-height: 1.6;"><b>Mailing Address</b>
                        <br/> @if(isset($primary_contact)) {{$primary_contact->first_name}} {{isset($primary_contact->last_name)?$primary_contact->last_name:''}}
                        <br /> @php echo isset($primary_contact->mailing_address)?nl2br($primary_contact->mailing_address):'' @endphp
                        <br/> @php echo isset($primary_contact->country)?$primary_contact->usercountry->country_name:'' @endphp @php echo isset($primary_contact->postal_code)?" ".nl2br($primary_contact->postal_code):'' @endphp @endif
                    </p>
                </td>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:16%; vertical-align: top;"></td>
                <td style="background:#ffffff;color:#454545;text-align:left;  width:33%; vertical-align: top;">
                    <p style="padding-left:2px; border: 1px solid #9e9e9e; font-family: 'Lato', sans-serif;font-size:10px; text-align:left; padding:4px 6px; line-height: 1.6;"> <b>Billing Address</b>
                        <br/>@if(isset($purchasers)) @php $counts =0; @endphp @foreach($purchasers as $k => $purchaser) @if($purchaser->status ==1) @php if($counts >0) echo ", "; @endphp {{$purchaser->first_name}} {{isset($purchaser->last_name)?$purchaser->last_name:''}}
                        @php $counts++; @endphp @endif</b> @endforeach @endif
                        <br />@php echo isset($Unitinvoice->propertydetail->company_name)?$Unitinvoice->propertydetail->company_name:'' @endphp
                        <br/> #{{isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}}
                        <br /> @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp</p>
                </td>
            </tr>
        </table>
	</div>