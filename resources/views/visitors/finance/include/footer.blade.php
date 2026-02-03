
<div class="footer">
        <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;   ">
            <tr>
                <td style="background:#ffffff;text-align:left;; width:100%; ">
                    <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td>
                                <!--p class="line" style="  font-family: 'Lato', sans-serif; font-size:10px; border: 1px solid #c0c0c0 ;  text-align:justify; line-height:11px;  background: #dee2e6; padding:6px; margin-left:22px; height:22%; ">@php echo nl2br($invoice->notes) @endphp 
								</p-->
								<div class="line" style="  font-family: 'Lato', sans-serif; font-size:10px; border: 1px solid #c0c0c0 ;  text-align:justify; line-height:11px;  background: #dee2e6; padding:6px; margin-left:22px; height:auto; padding-top:20px; margin-top:20px; padding-bottom:20px; margin-bottom:20px;">@php echo nl2br($invoice->notes) @endphp 
								</div>
								
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style='page-break-inside:avoid;vertical-align:bottom; '>
            <footer style="padding: -15px; padding-left:22px; page-break-before:; " size="1">
                <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;   ">
                    <tr>
                        <td style="background:#ffffff;text-align:left;; width:100%">
                            <table style="width:100%; border-collapse: collapse;">
                                <tr>
                                    <td style=" text-align: center;">
                                        ----------------------------------------------------------------------------------------------------------------------------------
                                    </td>
                                </tr>
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
                                    <th style="font-size:10px; padding-left:2px; text-align:left; ">Account No: {{isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):''}}-#{{isset($Unitinvoice->getunit->unit)?Crypt::decryptString($Unitinvoice->getunit->unit):''}}</th>
                                </tr>
                                <tr style="border:1px solid black;">
                                    <td style="padding-left:2px; border: 1px solid #d9d9d9;font-size:10px; line-height: 1.6;"> @if(isset($purchasers)) @foreach($purchasers as $k => $purchaser) @php if($k >0) echo ", "; @endphp <b> {{Crypt::decryptString($purchaser->first_name)}} {{isset($purchaser->last_name)?Crypt::decryptString($purchaser->last_name):''}}</b> @endforeach @endif
                                        <br /> #{{isset($Unitinvoice->getunit->unit)?Crypt::decryptString($Unitinvoice->getunit->unit):''}}, {{isset($Unitinvoice->getunit->buildinginfo->building)?$Unitinvoice->getunit->buildinginfo->building:''}}
                                        <br /> @php echo isset($Unitinvoice->propertydetail->company_address)?nl2br($Unitinvoice->propertydetail->company_address):'' @endphp
                                    </td>
                                </tr>
                                    @if($qr_file !='')
								      <tr>
									    <td>
                                            <!--img src="{{url('public/assets/img/qrcode2.jpg')}}" style="width: 50px; height: 50px;"-->
                                            <img src="{{$qr_file}}" alt="" style="width: 80px; height: 80px; margin-top:13px; margin-left:-2px;">
									    </td>
									  </tr>
									  <tr>
                                        <td>
                                            <img src="{{ asset('assets/img/paynow1.jpg') }}" alt="" style="width: 60px; height: 10px; margin-left:8px;">
                                        </td>
									  </tr>
                                    @endif
                            </table>
                        </td>
                        <td style="background:#ffffff;text-align:left;  width:50%">
                            <table style="width:100%">
                                <tr>
                                    <td style="vertical-align: text-top; font-size:10px; width:30%"><b>Mail To : </b></td>
                                    <td style="font-size:10px; width:70%; line-height: 1.6;">
                                        {{isset($invoice->comp_name)?$invoice->comp_name:''}}
                                        <br /> @php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp
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
                                    <td style="font-size:10px; width:70%; padding-bottom:-10px;">$@php
                                 if($balance_amount > 0)
                                    echo number_format($balance_amount,2);
                                 else
                                    echo "0.00"; 
                              @endphp</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table border="0" align="center" style="margin:0 auto;  width:100%; background:#ffffff; border-collapse: collapse;">
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
								 <tr>
                                    <td style="font-size:10px; padding-top: 8px;">Print Date: {{$print_date}}</td>
                                    <td style="font-size:10px; padding-top: 8px; text-align:right;">Page {{$page_count}} of {{$total_pages}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!--tr>
                        <td style="background:#ffffff;color:#000;text-align:left; padding:0px 0px; width:100%">
                            <table style="width:100%">
                                <tr>
                                    <td style="font-size:10px;">Print Date: {{$print_date}}</td>
                                </tr>
                            </table>

                        </td>
                    </tr-->
                </table>
            </footer>
        </div>
	</div>