<!DOCTYPE html>
<html>

<!--body bgcolor="#efefef;"-->
<body >
<style>
@media print
{
  .body {
    background: #efefef!important;
    font-size: 9pt;
  }
}

        p{font-size: 9pt;}
        b{font-size: 9t;}
        span{font-size: 9pt;}
        i{font-size: 9pt;}
        td{font-size: 9pt;}
</style>
   <table width="100%" border="0" cellspacing="0" cellpadding="0">
     
                           <tr>
                              <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
                                 <p style="text-align:left">DEFECT INSPECTION - PDF 
                                 </p>
                              </td>
                           </tr>
                           <tr>
                              <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;">
                                 <table border="0" align="center" cellpadding="4" cellspacing="0" style="width:100%;">
								
                                    <tr>
                                       <td style="color:#454545;"  width="33%">
                                          <b  style=" width: 122px;
                                             display: inline-flex;     padding-top: -15px;">Ticket :</b>  <i>{{$defectObj->ticket}}</i>
                                       </td>
                                      
                                       <td style="color:#454545;"  width="33%">
                                          <b  style=" width: 105px;
                                             display:inline-flex;">Appt date :</b> <i> {{isset($defectObj->inspection->appt_date)?date('d/m/y',strtotime($defectObj->inspection->appt_date)):''}} </i>
                                       </td>
                                       <td style="color:#454545;"  width="33%">
                                          <b  style=" width: 90px;
                                             display: inline-flex; ">Submitted by : </b> <i>{{isset($defectObj->user->name)?Crypt::decryptString($defectObj->user->name):''}}</i>
                                       </td>
                                      
                                    </tr>
                                    <tr>
                                    <td style="color:#454545;"  width="33%">
                                          <b  style=" width: 122px;
                                             display: inline-flex;     padding-top: -15px;">Ticket status : </b> <i>@php
                  if(isset($defectObj->status)){
                    if($defectObj->status==0)
                      echo "OPEN";
                     else if($defectObj->status==1)
                      echo "CLOSED";
                    else if($defectObj->status==3)
                      echo "ON SCHEDULE";
                    else
                      echo "IN PROGRESS";
                  }
                  @endphp</i>
                                       </td>
                                       <td style="color:#454545;"  width="33%">
                                          <b  style=" width: 105px;
                                             display: inline-flex;">Appt time : </b> <i> {{isset($defectObj->inspection->appt_time)?$defectObj->inspection->appt_time:''}}</i>
                                       </td>
                                       <td style="color:#454545;"  width="33%">
                                          <b>Submitted date : </b> <i> {{date('d/m/y',strtotime($defectObj->created_at))}}</i>
                                       </td>
                                      
                                    </tr>
                                    <tr>
                                    <td style="color:#454545;"  width="33%">
                                          <b style=" width: 122px;
                                             display: inline-flex;     padding-top: -15px;">Unit no : </b> <i>#{{isset($defectObj->getunit->unit)?Crypt::decryptString($defectObj->getunit->unit):''}}</i>
                                       </td>
                                       <td style="color:#454545;"  width="33%">
                                          <b   style=" width: 110px;
                                             display: inline-flex;">Appt status : </b> <i>@php
                  if(isset($defectObj->inspection->status)){
                    if($defectObj->inspection->status==0)
                      echo "NEW";
                     else if($defectObj->inspection->status==1)
                      echo "CANCELLED";
                    else if($defectObj->inspection->status==2)
                      echo "ON SCHEDULE";
                    else if($defectObj->inspection->status==4)
                      echo "IN PROGRESS";
                    else
                      echo "DONE";
                  }
                  @endphp</i>
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
                                 margin-left: 20px;">DEFECTS LIST</b></td>
                           </tr>
                           @if($defectObj->submissions)

                                @foreach($defectObj->submissions as $k => $defect)
                            <tr>
                              <td style="background:#ffffff; color:#454545;text-align:left; padding:10px 15px; padding-bottom:20px;">
                                 <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;" >
                                    <tr>
                                       <td style="color:#454545; vertical-align: top;" width="1%" >
                                          <p style="margin-left:6px; font-weight: bold; margin-top: 0px; margin-bottom: 0px;">{{$k+1}}.</p>
                                       </td>
                                       <td style="color:#454545; vertical-align: top;" width="40%" >
                                       @if(!empty($defect->upload))	
										                                             <div style="display: inline-block;
   position: relative;
    top: 25px;">
                                       <p style="margin-left:6px; font-weight: bold; margin-top: 0px; margin-bottom: 0px;">Defect Image</p>
                                          <img src="{{$file_path}}/{{$defect->upload}}" width="100px" height="100px" style="width:100px; height:100px; margin-top: 10px;     margin-left: 6px;" class="resimg">
										  										  </div>
                                          @endif

										  @if(!empty($defect->rectified_image))	
                                          <div style="display: inline-block;
    position: absolute;">
                                             <p style="margin-left:6px; font-weight: bold; margin-top: 0px;margin-bottom: 0px;">Rectified Image</p>
                                             <img src="{{$file_path}}/{{$defect->rectified_image}}" width="150px" height="100px" style="width:100px; height:100px; margin-top: 10px;     margin-left: 6px;" class="resimg" style="150px">
                                          </div>
                                       @endif
                                       </td>
                                       
                                       <td style="color:#454545; vertical-align: top;
                                          " width="60%" >
                                          <table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%; margin-top:-6px;">
                                             <tr>
                                                <td style="color:#454545; width:44%;">
                                                   <b>Location :</b>
                                                </td>
                                                <td style="color:#454545;">
                                                {{isset($defect->getlocation->defect_location)?$defect->getlocation->defect_location:''}}
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="color:#454545; width:44%;">
                                                   <b>Defect type:</b>
                                                </td>
                                                <td style="color:#454545; ">
                                                {{isset($defect->gettype->defect_type)?$defect->gettype->defect_type:''}}
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="color:#454545; width:44%;">
                                                   <b> User remarks:</b> 
                                                </td>
                                                <td style="color:#454545;">
                                                {{$defect->notes}}
                                                </td>
                                             </tr>
                                             @if($defect->remarks !='')
                                             <tr>
                                                <td style="color:#454545; width:44%;">
                                                   <b> 	Defects team comments:</b> 
                                                </td>
                                                <td style="color:#454545;">
                                                   <span> {{$defect->remarks}} </span>
                                                </td>
                                             </tr>
											
                                             @endif
                                             <tr>
                                                <td style="color:#454545; width:44%;">
                                                   <b> 	Defects Status:</b> 
                                                </td>
                                                <td style="color:#454545;">
                                                   <span> @php
                                                      if(isset($defect->status)){
                                                         if($defect->status==2){
                                                            if($defect->defect_status==0)
                                                               echo "Pending";
                                                            else if($defect->defect_status==1)
                                                               echo "Fixed";
                                                            else if($defect->defect_status==2)
                                                               echo $defect->handover_message;
                                                         }
                                                         else{
                                                            if($defect->status==3)
                                                               echo "Not on issue";
                                                         }
                                                      }
                                                      @endphp</span>
                                                </td>
                                             </tr>
                                              @if($defect->owner_status ==2)
                                                <tr>
                                                   <td style="color:#454545; width:44%;">
                                                      <b>Owner's status:</b> 
                                                   </td>
                                                   <td style="color:#454545;">
                                                      <span>Disagree</span>
                                                   </td>
                                                </tr>
                                             @endif
                                             @if($defect->owner_final_remarks !='')
                                                <tr>
                                                   <td style="color:#454545; width:44%;">
                                                      <b> Owner's Final Remarks:</b> 
                                                   </td>
                                                   <td style="color:#454545;">
                                                      <span>{{$defect->owner_final_remarks}} </span>
                                                   </td>
                                                </tr>
                                             @endif
                                          </table>
                                       </td>
<!--td style="color:#454545; vertical-align: top;
                                          " width="40%" >
                                 <b style="text-align:left;">Remarks:</b>
                                 <p style="text-align:left; margin-top:0px; margin-bottom:0px;"> I can still see the scratches </p>
                              </td-->
                                    </tr>
								
                                 </table>
						
                              </td>
                           </tr>

                           @endforeach

                      @endif 

 <tr>
                        <td style="width:100%">
                            <table style="width:100%; border-collapse: collapse;">
                                <tr>
                                    <td style=" text-align: center; padding-bottom:20px;  padding-top:10px">-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                      <tr>
                              <td>
                              <table border="0" align="center" cellpadding="4" cellspacing="0" style="width:100%;">
                              <tr>
                                       @if(isset($defectObj->signature) && $defectObj->signature !='')
                                          <td style="color:#454545;padding-left:20px; padding-bottom:20px"  width="33%">
                                                <b>Submission Signature: </b><p style="margin-top:0px; margin-bottom:0px;"><img src="{{$file_path}}/{{$defectObj->signature}}" width="120px"/></p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">Submitted by {{$signatureUserName}} </p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">{{$signatureUserTime}}</p>
                                          </td>
                                       @endif
                                       @if(isset($defectObj->inspection_team_signature) && $defectObj->inspection_team_signature !='') 
                                          <td style="color:#454545; padding-bottom:20px"  width="33%">
                                                <b>Inspection Team Signature: </b><p style="margin-top:0px; margin-bottom:0px;"><img src="{{$file_path}}/{{$defectObj->inspection_team_signature}}" width="120px"/></p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">Inspected by {{$inspectedTeamName}}</p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">{{$inspectedTeamTime}}</p>
                                          </td>
                                       @endif       
                                       @if(isset($defectObj->inspection_owner_signature) && $defectObj->inspection_owner_signature !='') 
                                          <td style="color:#454545; padding-bottom:20px"  width="33%">
                                                <b>Inspection Owner Signature: </b><p style="margin-top:0px; margin-bottom:0px;"><img src="{{$file_path}}/{{$defectObj->inspection_owner_signature}}" width="120px"/></p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">Inspected by {{$inspectedOwnerName}}</p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">{{$inspectedOwnerTime}}</p>
                                          </td>
                                       @endif
                                    </tr>
                                    <tr>
                                       @if(isset($defectObj->handover_team_signature) && $defectObj->handover_team_signature !='') 
                                          <td style="color:#454545; padding-left:20px; padding-bottom:20px"  width="33%">
                                                <b>Handover Team Signature: </b><p style="margin-top:0px; margin-bottom:0px;"><img src="{{$file_path}}/{{$defectObj->handover_team_signature}}" width="120px"/></p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">Handovered by {{$handOverTeamName}}</p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">{{$handOverTeamTime}}</p>
                                          </td>
                                       @endif
                                       @if(isset($defectObj->handover_owner_signature) && $defectObj->handover_owner_signature !='') 
                                          <td style="color:#454545; padding-bottom:20px"  width="33%">
                                                <b>Handover Owner Signature: </b><p style="margin-top:0px; margin-bottom:0px;"><img src="{{$file_path}}/{{$defectObj->handover_owner_signature}}" width="120px"/></p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">Handovered by {{$handOverOwnerName}}</p>
                                                <p style="margin-top:0px; margin-bottom:0px; font-size:8pt;">{{$handOverOwnerTime}}</p>
                                          </td>
                                       @endif       
                                       <td>
                                       </td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                           
                        
              
   </table>
</body>
</html>