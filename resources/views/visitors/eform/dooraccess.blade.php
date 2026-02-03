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
               <b style="text-align:center">DOOR ACCESS CARD APPLICATION - PDF 
               </b>
            </td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;  padding-bottom: 250px;" >
               <table border="0"  cellpadding="0" cellspacing="0" width="50%" style="float:left">
			   <tr>
                     <td style="color:#454545; vertical-align: top;" >
                        <table border="0"  cellpadding="3" cellspacing="0" width='100%' style='width:100%'>
						<tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Ticket :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->ticket}}</i>
                              </td>
                           </tr>
						<tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Name of resident :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{isset($eformObj->user->name)?Crypt::decryptString($eformObj->user->name):''}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Contact no :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->contact_no}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Declared by :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->declared_by}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Name of nominee  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->in_charge_name}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee contact no :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->nominee_contact_no}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Tenancy period :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{date("d/m/y",strtotime($eformObj->tenancy_start))}} - {{date('d/m/y',strtotime($eformObj->tenancy_end))}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >No.of residence schlage <br />** CARD/FOB required  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->no_of_schlage_required}}</i>
                              </td>
                           </tr>
						    <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Owner signature:</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i><span><img src="data:image/png;base64, {{$eformObj->owner_signature}}" class="viewsig" width="100px"/></span></i>
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
                                <i>{{date('d/m/y',strtotime($eformObj->created_at))}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Unit no  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>#{{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Email  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->email}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Relationship  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{($eformObj->relationship ==1)?"Family":"Tenant"}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Passport / NRIC no  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->passport_no}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >No.of access card required :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->no_of_card_required}}</i>
                              </td>
                           </tr>
						     <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee contact email  :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i>{{$eformObj->nominee_email}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee signature :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
                                <i><span><img src="data:image/png;base64, {{$eformObj->nominee_signature}}" class="viewsig" width="100px"/></span></i>
                              </td>
                           </tr>
						      </table>
            </td>
         </tr>
		  </table>
            </td>
         </tr>
		
         <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">MANAGEMENT UPDATE</b></td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0" cellpadding="0" cellspacing="0" style="width:50%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        ">
                        <table border="0"  cellpadding="3" cellspacing="0" style="width:100%;">
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
            </td>
         </tr>
		 
         
         @if(isset($eformObj->payment))
		  <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">PAYMENT INFORMATION</b></td>
         </tr>

         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0"  cellpadding="0" cellspacing="0" style="width:50%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        " >
                        <table border="0"cellpadding="3" cellspacing="0" style="width:100%;">
                           <tr>
                              <td style="color:#454545;" width="55%">
                                 <b>Payment received by :</b>
                              </td>
                              <td style="color:#454545;" width="45%">
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
                                 <td style="color:#454545;" width="55%">
                                    <b>Cheque number :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                    {{$eformObj->payment->cheque_no}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Cheque Amount :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->cheque_amount}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($eformObj->payment->cheque_received_date)?date('d/m/y',strtotime($eformObj->payment->cheque_received_date)):''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Bank :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->cheque_bank}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->payment_option ==2) 
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->bt_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($eformObj->payment->bt_received_date)?date('d/m/y',strtotime($eformObj->payment->bt_received_date)):''}}
                                 </td>
                              </tr>
                           @endif

                           @if($eformObj->payment->payment_option ==3)
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Amount Received:</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->cash_amount_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($eformObj->payment->cash_received_date)?date('d/m/y',strtotime($eformObj->payment->cash_received_date)):''}}
                                 </td>
                              </tr>
                           @endif

                           <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Name of management received:</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{$eformObj->payment->manager_received}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Date Received :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($eformObj->payment->date_of_signature)?date('d/m/y',strtotime($eformObj->payment->date_of_signature)):''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Management signature:</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 <span><img src="data:image/png;base64, {{$eformObj->payment->signature}}" class="viewsig" width="100px"/></span> 
                                 </td>
                              </tr>
                              
                        </table>
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