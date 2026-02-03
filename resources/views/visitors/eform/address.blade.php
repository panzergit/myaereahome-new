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
            <td style="background:#ffffff; color:#454545;text-align:center;padding:10px 15px; " >
               <b style="text-align:center">CHANGING MAILING ADDRESS APPLICATION - PDF 
               </b>
            </td>
         </tr>
         <tr>
             <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px; padding-bottom:160px;" >
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
                                 <b >Declared by :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->declared_by}}</i>
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
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Email  :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i>{{$eformObj->email}}</i>
                              </td>
                           </tr>
						   <tr>
                              <td style="color:#454545;" width="55%">
                                 <b >Nominee signature :</b>
                              </td>
                              <td style="color:#454545; text-align:left;" width="45%">
                                 <i> <span><img src="data:image/png;base64, {{$eformObj->nominee_signature}}" class="viewsig" width="100px"/></span></i>
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