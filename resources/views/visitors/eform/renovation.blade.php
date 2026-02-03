<!DOCTYPE html>
<html>
   <body >
      <style>
         html { -webkit-print-color-adjust: exact; }
         @media print
         {
         .body {
         background: #efefef!important;
         font-size: 9pt;
         }
         }
         table{
         page-break-inside: avoid;
         }
         table tr{
         page-break-inside: avoid;
         }
         table tr td i{
         page-break-inside: avoid;
         }
         p{font-size: 9pt;}
         b{font-size: 9t;}
         span{font-size: 9pt;}
         i{font-size: 9pt;}
         td{font-size: 9pt; text-transform: capitalize;}
      </style>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:center;padding:10px 15px;">
               <b style="text-align:center">RENOVATION APPLICATION - PDF 
               </b>
            </td>
         </tr>
          <!-- toptext -->
		  <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;  padding-bottom: 220px;" >
               <table border="0"  cellpadding="0" cellspacing="0" width="50%" style="float:left">
			   <tr>
                     <td style="color:#454545; vertical-align: top;" >
                        <table border="0"  cellpadding="3" cellspacing="0" width='100%' style='width:100%'>
				
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Ticket :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->ticket}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Name of resident :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{isset($eformObj->user->name)?Crypt::decryptString($eformObj->user->name):''}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Contact no :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->contact_no}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Name of contractor company :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->reno_comp}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Address of company  :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->comp_address}}</i>
                              </td>
                           </tr>
						 
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Work Start - End :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{date("d/m/y",strtotime($eformObj->reno_start))}} - {{date('d/m/y',strtotime($eformObj->reno_end))}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Owner signature:</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <span><img src="data:image/png;base64, {{$eformObj->owner_signature}}" class="viewsig" width="100px"/></span>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee contact no:</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->nominee_contact_no}}</i>
                              </td>
                           </tr>
			      </table>
            </td>
         </tr>
		 </table>
		  <table border="0"  cellpadding="0" cellspacing="0" width="50%" style="float:right">
			   <tr>
                     <td style="color:#454545; vertical-align: top;" >
                        <table border="0"  cellpadding="3" cellspacing="0" width='100%' style='width:100%'>
				
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Submitted date  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i> {{date('d/m/y',strtotime($eformObj->created_at))}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Unit no  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i> {{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Email  :</b>
                              </td>
                              <td style="color:#454545; " width="45%">
                                <i> {{$eformObj->email}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Person in-charge  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i> {{$eformObj->in_charge_name}}</i>
                              </td>
                           </tr>
						     <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Contact number  :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->comp_contact_no}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Hacking Work Start - End :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{date('d/m/y',strtotime($eformObj->hacking_work_start))}} -  {{date('d/m/y',strtotime($eformObj->hacking_work_end))}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee signature :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <span><img src="data:image/png;base64, {{$eformObj->nominee_signature}}" class="viewsig" width="100px"/></span> 
                              </td>
                           </tr>
			      </table>
            </td>
         </tr>
		 </table>
            </td>
         </tr>
         <!-- toptext end-->
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;" >
               <table border="0"  cellpadding="0" cellspacing="0" width="50%" style="float:left">
			   <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 4px;">MANAGEMENT UPDATE</b></td>
         </tr>
                  <tr>
                           <td style="color:#454545; vertical-align: top;" >
                        <table border="0"  cellpadding="3" cellspacing="0" width='100%' style='width:100%'>
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Status :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                 @php 
                                    if($eformObj->status ==0)
                                       echo "New";
                                    else if($eformObj->status ==1)
                                       echo "Cancelled";
                                    else if($eformObj->status ==2)
                                       echo "In Progress";
                                    else if($eformObj->status ==3)
                                       echo "Approved";
                                    else if($eformObj->status ==4)
                                       echo "Rejected";
                                    else if($eformObj->status ==5)
                                       echo "Payment Pending";
                                    else if($eformObj->status ==6)
                                       echo "Refunded";
                                 @endphp
                              </td>
                           </tr>
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Remarks :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                              {{$eformObj->remarks}}
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
               </table>
			     @if($eformObj->sub_con)
            @foreach($eformObj->sub_con as $k => $contractor)
                  <table border="0" cellpadding="0" cellspacing="0" width="50%" style="float:right">
				  				     <tr>
            <td style="width:100%;"><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 0px;
               margin-left: 4px;">WORKMEN/ SUB-CONTRACTOR LIST</b></td>
         </tr>
                     <tr>
                        <td style="color:#454545; vertical-align: top;
                           " width="60%" class="width50">
                           <table border="0" align="center" cellpadding="3" cellspacing="0" style="width:100%;">
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Workmen / sub-contractor :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($contractor->workman)?$contractor->workman:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>ID type:</b>
                                 </td>
                                 <td style="color:#454545; " width="45%">
                                 @php
                                    if($contractor->id_type ==1)
                                       echo "Passport";
                                    else if($contractor->id_type ==2)
                                       echo "NRIC";
                                    else
                                       echo "Work Permit";

                                 @endphp
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b> 	ID number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($contractor->id_number)?$contractor->id_number:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b> 		Expiry date of work permit :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                    <span>{{isset($contractor->permit_expiry)?date('d/m/y',strtotime($contractor->permit_expiry)):''}}</span>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
         @endforeach
       @endif 
            </td>
         </tr>
		   <tr>
            <td>
<b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 100px;
               margin-left: 20px;">PAYMENT INFORMATION</b></td>
         </tr>

         @if(isset($eformObj->payment))
	

         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;  padding-bottom: 250px;">
             
               <table border="0" cellpadding="0" cellspacing="0" width="50%" style="float:left">
			    			<tr>
            <td width="100%"><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 0px;
               margin-left: 4px;">DEPOSIT PAYMENT</b></td>
         </tr>
                  <tr>
                     <td style="color:#454545; vertical-align: top;"> 
                        <table border="0" cellpadding="3" cellspacing="0" width="100%">
                           <tr>
                              <td style="color:#454545;"  width="55%">
                                 <b>Payment received by :</b>
                              </td>
                              <td style="color:#454545;"  width="45%">
                                @php
                                    if($eformObj->payment->payment_option ==1)
                                       echo "Cheque";
                                    if($eformObj->payment->payment_option ==2)
                                       echo "Bank Transfer";
                                    if($eformObj->payment->payment_option ==3)
                                       echo "Cash";
                                @endphp
                              </td>
                           </tr>
                           @if($eformObj->payment->payment_option ==1)
                              <tr>
                                 <td style="color:#454545; "  width="55%">
                                    <b>Cheque number :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                    {{$eformObj->payment->cheque_no}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Cheque Amount :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->cheque_amount}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->cheque_received_date)?date('d/m/y',strtotime($eformObj->payment->cheque_received_date)):''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Bank :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->cheque_bank}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->payment_option ==2) 
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->bt_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->bt_received_date)?date('d/m/y',strtotime($eformObj->payment->bt_received_date)):''}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->payment_option ==3)
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->cash_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->cash_received_date)?date('d/m/y',strtotime($eformObj->payment->cash_received_date)):''}}
                                 </td>
                              </tr>
                           @endif
                           @if(isset($eformObj->payment->signature))

                           <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Name of management received:</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->manager_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->date_of_signature)?date('d/m/y',strtotime($eformObj->payment->date_of_signature)):''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Management signature:</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 <span><img src="data:image/png;base64, {{$eformObj->payment->signature}}" class="viewsig" width="100px"/></span> 
                                 </td>
                              </tr>
                           @endif
                        </table>
                     </td>
                  </tr>
               </table>
			       <table border="0"  cellpadding="0" cellspacing="0"  width="50%" style="float:right">
				 				<tr>
            <td width="100%"><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 0px;
               margin-left: 4px;">LIFT PADDING PAYMENT</b></td>
         </tr>
                  <tr>
                      <td style="color:#454545; vertical-align: top;
                        "> 
                        <table border="0"  cellpadding="3" cellspacing="0" width="100%">
                           <tr>
                              <td style="color:#454545;"  width="55%">
                                 <b>Payment received by :</b>
                              </td>
                              <td style="color:#454545;"  width="45%">
                                @php
                                    if($eformObj->payment->lift_payment_option ==1)
                                       echo "Cheque";
                                    if($eformObj->payment->lift_payment_option ==2)
                                       echo "Bank Transfer";
                                    if($eformObj->payment->lift_payment_option ==3)
                                       echo "Cash";
                                @endphp
                              </td>
                           </tr>
                           @if($eformObj->payment->lift_payment_option ==1)
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Cheque number :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                    {{$eformObj->payment->lift_cheque_no}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Cheque Amount :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->lift_cheque_amount}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->lift_cheque_received_date) && $eformObj->payment->lift_cheque_received_date !='0000-00-00'?date('d/m/y',strtotime($eformObj->payment->lift_cheque_received_date)):''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Bank :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->lift_cheque_bank}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->lift_payment_option ==2) 
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->lift_bt_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{isset($eformObj->payment->lift_bt_received_date)?date('d/m/y',strtotime($eformObj->payment->lift_bt_received_date)):''}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->lift_payment_option ==3)
                              <tr>
                                 <td style="color:#454545;"  width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;"  width="45%">
                                 {{$eformObj->payment->lift_cash_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($eformObj->payment->lift_cash_received_date)?date('d/m/y',strtotime($eformObj->payment->lift_cash_received_date)):''}}
                                 </td>
                              </tr>
                           @endif
                         
                        </table>
                     </td>
                  </tr>
               </table>
			   
            </td>
         </tr>

         @endif

         @if(isset($eformObj->inspection))
		  <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">INSPECTION INFORMATION</b></td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0"  cellpadding="0" cellspacing="0" style="width:50%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        " width="60%" class="width50">
                        <table border="0" align="center" cellpadding="3" cellspacing="0" style="width:100%;">
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Actual date of completion :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                              {{isset($eformObj->inspection->date_of_completion)?date('d/m/y',strtotime($eformObj->inspection->date_of_completion)):''}}
                              </td>
                           </tr>
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Unit inspected by mangement :</b>
                              </td>
                              <td style="color:#454545; " width="45%">
                                 {{isset($eformObj->inspection->inspected_by)?$eformObj->inspection->inspected_by:''}}
                              </td>
                           </tr>
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Unit status :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 @php 
                                    if($eformObj->inspection->unit_in_order_or_not==1)
                                       echo "Unit in order & full amount refunded";
                                    else if($eformObj->inspection->unit_in_order_or_not==2)
                                       echo "Unit not in order";
                                 @endphp
                              </td>
                           </tr>
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Amount deducted from deposit :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->amount_deducted)?$eformObj->inspection->amount_deducted:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Amount balance to be refunded :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->refunded_amount)?$eformObj->inspection->refunded_amount:''}}</span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Amount claimable :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->amount_claimable)?$eformObj->inspection->amount_claimable:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%"> 
                                 <b> Acutal amount received :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->actual_amount_received)?$eformObj->inspection->actual_amount_received:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Received by name of resident :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->acknowledged_by)?$eformObj->inspection->acknowledged_by:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> PP / NRIC NO. Of resident :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>{{isset($eformObj->inspection->resident_nric)?$eformObj->inspection->resident_nric:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Date of resident signature :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span> {{isset($eformObj->inspection->resident_signature_date)?date('d/m/y',strtotime($eformObj->inspection->resident_signature_date)):''}}</span>
                              </td>
                           </tr>
                           @if(isset($eformObj->inspection->manager_signature))
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> Management Signature:</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                    <span><img src="data:image/png;base64, {{$eformObj->inspection->manager_signature}}" class="viewsig" width="100px"/></span>
                                 </td>
                              </tr>
                           @endif
         
                           <tr>
                              <td style="color:#454545; " width="55%">
                                 <b> Name of management received :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span>	{{isset($eformObj->inspection->manager_received)?$eformObj->inspection->manager_received:''}} </span>
                              </td>
                           </tr>
						         <tr>
                              <td style="color:#454545;" width="55%">
                                 <b> Date of management signature  :</b> 
                              </td>
                              <td style="color:#454545;" width="45%">
                                 <span> {{isset($eformObj->inspection->date_of_signature)?date('d/m/y',strtotime($eformObj->inspection->date_of_signature)):''}}
								         </span>
                              </td>
                           </tr>
						  
						  
                           </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
                  @endif

         @if(isset($eformObj->defects))
		  <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">DEFECTS</b></td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0"  cellpadding="0" cellspacing="0" style="width:50%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        " width="60%" class="width50">
                        @foreach($eformObj->defects as $defect)
                        <table border="0" cellpadding="3" cellspacing="0" style="width:100%;">
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Notes:</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                              {{$defect->notes}}
                              </td>
                           </tr>
                           
                           @if(isset($defect->image_base64))
                              <tr>
                                 <td style="color:#454545; " width="55%">
                                    <b> Uploaded Image:</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                    <span><img src="data:image/png;base64, {{$defect->image_base64}}" class="viewsig" width="100px"/></span>
                                 </td>
                              </tr>
                           @endif
                        </table>
                        @endforeach
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
                  @endif
               </table>
            </td>
         </tr>
      </table>
   </body>
</html>