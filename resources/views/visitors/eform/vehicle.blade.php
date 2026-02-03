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
               <b style="text-align:center">VEHICLE IU REGISTRATION APPLICATION - PDF 
               </b>
            </td>
         </tr>
         <tr>
            <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
               <table border="0" align="center" cellpadding="4" cellspacing="0" style="width:100%;">
                  <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%; display: inline-flex;     padding-top: -15px;">Ticket :</b>  <i>{{$eformObj->ticket}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%; display:inline-flex;">Submitted date  :</b> <i> {{date('d/m/y',strtotime($eformObj->created_at))}}</i>
                     </td>
                  </tr>
				    <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%;
                           display: inline-flex;     padding-top: -15px;">Name of resident :</b>  <i>{{isset($eformObj->user->name)?Crypt::decryptString($eformObj->user->name):''}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Unit no  :</b> <i>#{{isset($eformObj->user->getunit->unit)?Crypt::decryptString($eformObj->user->getunit->unit):''}}</i>
                     </td>
                  </tr>
				    <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%; display: inline-flex;     padding-top: -15px;">Contact no :</b>  <i> {{$eformObj->contact_no}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width:45%;
                           display:inline-flex;">Email  :</b> <i>{{$eformObj->email}}</i>
                     </td>
                  </tr>
				    <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%;
                           display: inline-flex;     padding-top: -15px;">Registered owner of vehicle :</b>  <i> {{$eformObj->owner_of_vehicle}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Vehicle licence plate  :</b> <i>   {{$eformObj->licence_no}}</i>
                     </td>
                  </tr>
                  <tr>
                     
                     <td style="color:#454545;" width="50%">
                        <b  style=" width: 55%;
                           display:inline-flex; ">IU Label no  :</b> <i> {{$eformObj->iu_number}}</i>
                     </td>
                    
                  </tr>
                  <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%;
                           display: inline-flex;     padding-top: -15px;">Declared by :</b>  <i> {{$eformObj->declared_by}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Relationship  :</b> <i>   {{($eformObj->relationship ==1)?"Family":"Tenant"}}</i>
                     </td>
                  </tr>
                  <tr>
                     
                     <td style="color:#454545;" width="50%">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Name of nominee  :</b> <i> {{$eformObj->in_charge_name}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Passport / NRIC no  :</b> <i>   {{$eformObj->passport_no}}</i>
                     </td>
                  </tr>
                  <tr>
                     <td style="color:#454545;" width="50%">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Nominee contact no :</b> <i> {{$eformObj->nominee_contact_no}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Nominee contact email  :</b> <i>   {{$eformObj->nominee_email}}</i>
                     </td>
                  </tr>
				    <tr>
					 <td style="color:#454545;">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Tenancy period :</b> <i> {{date("d/m/y",strtotime($eformObj->tenancy_start))}} - {{date('d/m/y',strtotime($eformObj->tenancy_end))}}</i>
                     </td>
                     
                  </tr>
                  <tr>
                     <td style="color:#454545;"  width="50%">
                        <b  style=" width: 55%;
                           display: inline-flex;     padding-top: -15px;">Owner of nominee vehicle :</b>  <i> {{$eformObj->owner_of_nominee_vehicle}}</i>
                     </td>
                     <td style="color:#454545;">
                        <b  style=" width: 45%;
                           display:inline-flex;">Vehicle licence plate  :</b> <i>   {{$eformObj->nominee_vehicle_licence_no}}</i>
                     </td>
                  </tr>
                  <tr>
                     
                     <td style="color:#454545;" width="50%">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Nominee vehicle IU Label no  :</b> <i> {{$eformObj->nominee_vehicle_iu_number}}</i>
                     </td>
                    
                  </tr>
                  <tr>
                  <td style="color:#454545;">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Owner signature:</b> 
                           <span><img src="data:image/png;base64, {{$eformObj->owner_signature}}" class="viewsig" width="100px"/></span>
                     </td>
                     <td style="color:#454545;"  width="50%">
                        <b  style="width: 45%;
                           display: inline-flex;     padding-top: -15px;">Nominee signature :</b> 
                             <span><img src="data:image/png;base64, {{$eformObj->nominee_signature}}" class="viewsig" width="100px"/></span> 
                           
                     </td>
                     
                  </tr>
                  <tr>
                  <td style="color:#454545;">
                        <b  style=" width: 55%;
                           display:inline-flex; ">Nominee contact no:</b> 
                         <i>{{$eformObj->nominee_contact_no}}</i>
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
               <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;">
                  <tr>
                     <td style="color:#454545; vertical-align: top;
                        " width="100%" class="width50">
                        <table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%;">
                           <tr>
                              <td style="color:#454545; width:27%;">
                                 <b>Status :</b>
                              </td>
                              <td style="color:#454545;">
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
                              <td style="color:#454545; width:27%;">
                                 <b>Remarks :</b>
                              </td>
                              <td style="color:#454545;">
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