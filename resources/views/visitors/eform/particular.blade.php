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
               <b style="text-align:center">UPDATE PARTICULARS APPLICATION - PDF 
               </b>
            </td>
         </tr>
         <tr>
           <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px; padding-bottom: 120px;" >
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
                                 <b >Email :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->email}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Address :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->address}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Owner signature:</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
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
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{date('d/m/y',strtotime($eformObj->created_at))}}</i>
                              </td>
                           </tr>
						   	<tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Unit no  :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>#{{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}}</i>
                              </td>
                           </tr>
						    </table>
            </td>
         </tr>
		 </table>
            </td>
         </tr>
         @if($eformObj->owners)
         <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">PARTICLUARS OF OWNER(S)</b></td>
         </tr>
         
            @foreach($eformObj->owners as $k => $owner)
            <tr>
               <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
                  <table border="0"  cellpadding="0" cellspacing="0" style="width:50%;">
                     <tr>
                        <td style="color:#454545; vertical-align: top;
                           ">
                           <table border="0" cellpadding="6" cellspacing="0" style="width:100%;">
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Owner name :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($owner->owner_name)?$owner->owner_name:''}}
                                 </td>
                              </tr>
                              
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> NRIC number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($owner->owner_nric)?$owner->owner_nric:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> Contact number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($owner->owner_contact_no)?$owner->owner_contact_no:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> Vehicle number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($owner->owner_vehicle_no)?$owner->owner_vehicle_no:''}}
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         @endforeach
       @endif 
       @if($eformObj->tenants)
         <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">PARTICLUARS OF TENANT(S)</b></td>
         </tr>
         
            @foreach($eformObj->tenants as $k => $tenant)
            <tr>
               <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
                  <table border="0" cellpadding="0" cellspacing="0" style="width:50%;">
                     <tr>
                        <td style="color:#454545; vertical-align: top;
                           " >
                           <table border="0"  cellpadding="6" cellspacing="0" style="width:100%;">
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b>Tenant name :</b>
                                 </td>
                                 <td style="color:#454545;" width="45%"> 
                                 {{isset($tenant->tenant_name)?$tenant->tenant_name:''}}
                                 </td>
                              </tr>
                              
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> NRIC number :</b> 
                                 </td>
                                 <td style="color:#454545;"width="45%">
                                 {{isset($tenant->tenant_nric)?$tenant->tenant_nric:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> Contact number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($tenant->tenant_contact_no)?$tenant->tenant_contact_no:''}}
                                 </td>
                              </tr>
                              <tr>
                                 <td style="color:#454545;" width="55%">
                                    <b> Vehicle number :</b> 
                                 </td>
                                 <td style="color:#454545;" width="45%">
                                 {{isset($tenant->tenant_vehicle_no)?$tenant->tenant_vehicle_no:''}}
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         @endforeach
       @endif 
         <tr>
            <td><b style="text-align: left;
               padding-bottom: 8px;
               display: block;
               margin-top: 10px;
               margin-left: 20px;">MANAGEMENT UPDATE</b></td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0"  cellpadding="0" cellspacing="0" style="width:50%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        " >
                        <table border="0"  cellpadding="6" cellspacing="0" style="width:100%;">
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
		 
         
        
         
               </table>
            </td>
         </tr>
      </table>
   </body>
</html>