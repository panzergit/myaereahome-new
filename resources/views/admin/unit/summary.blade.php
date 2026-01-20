@extends('layouts.adminnew')

<style>

.wrapper {
position: relative;
    margin: 0 auto;
    overflow: hidden;
    padding: 5px;
    height: 64px;
    height: 9%!important;
}
.gap th {
    padding-top: 0px!important;
}
.nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover {
    border-color: transparent!important;
}
.list {
    position:absolute;
    left:0px;
    top:0px;
  	min-width:3500px;
    margin-top:0px;
}

.list li{
	display:table-cell;
    position:relative;
    text-align:center;
    cursor:grab;
    cursor:-webkit-grab;
    color:#efefef;
    vertical-align:middle;
}

.scroller {
  text-align:center;
  cursor:pointer;
  display:none;
  padding:7px;
  padding-top:13px;
  white-space:no-wrap;
  vertical-align:middle;
  background-color:#fff;
}
.tab-content{          background: transparent;}
.bor3{           border: 0px solid transparent;
    border-radius: 0px;
    border-top: transparent!important;}
.tab-content  table{    margin: 0px!important;}
.nav-tabs .nav-link {
    background: transparent;
    color: #212529;
    padding: 4px 15px;
    text-transform: capitalize;
    text-align: left;
    color: #A2A2A2;
    font-size: 15px!important;
    font-weight: 400;
}

.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #495057!important;
    font-size: 15px!important;
    font-weight: 600;
    border-color: none!important;
    background-color: transparent!important;
    border-bottom: 2px solid #795548 !important;
    padding-bottom: 0px!important;
}

.nav-tabs {
      background: transparent!important;
}
.nav-tabs a:last-child{
   padding-right: 100px;
}
.nav-tabs{

   }
.nav-tabs .nav-link.active {    position: relative;}
.nav-tabs .nav-link.active:after {
    content: "";
    position: absolute;
    bottom: -12px;
    left: 45%;
    border: 5px solid transparent;
    border-top: 5px solid #795548;
}
</style>


@section('content')

@php 
   $permission = Auth::user();
   $user_permission =  $permission->check_menu_permission(7,$permission->role_id,1);
   $collection_permission = $permission->check_permission(2,$permission->role_id); 
   $facility_permission = $permission->check_permission(5,$permission->role_id); 
   $defect_permission = $permission->check_permission(3,$permission->role_id); 
   $feedback_permission = $permission->check_permission(6,$permission->role_id); 
   $move_permission = $permission->check_permission(40,$permission->role_id); 
   $renovation_permission = $permission->check_permission(41,$permission->role_id); 
   $dooraccess_permission = $permission->check_permission(42,$permission->role_id); 
   $vehicle_permission = $permission->check_permission(43,$permission->role_id); 
   $mailling_permission = $permission->check_permission(44,$permission->role_id); 
   $particular_permission = $permission->check_permission(45,$permission->role_id); 
   $vm_permission =  $permission->check_menu_permission(34,$permission->role_id,1); 
   $rm_permission=  $permission->check_menu_permission(60,$permission->role_id,1); 
   $card_permission =  $permission->check_menu_permission(38,$permission->role_id,1);
   $fileupload =  $permission->check_menu_permission(33,$permission->role_id,1);


@endphp


<!-- Content Header (Page header) -->

  <div class="status">
    <h1>unit summary</h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                  </ul>
               </div>
               </div>

      <div class="">

         <form action="{{url('/opslogin/unitsummary/search')}}" method="get" role="search" class="forunit">
            <div class="row asignbg">
             
                  @if(@Auth::user()->role_id ==1)
					   <div class="col-lg-2">
                        <div class="form-group">
                              <label>property:</label>
                            <h4>  {{isset($UnitObj->propertyinfo->company_name)?$UnitObj->propertyinfo->company_name:''}} </h4> 
                          
                        </div>
                         </div>
                  @endif
				 
				   <div class="col-lg-2">
                  <div class="form-group">
                        <label>building:</label>
                     {{ Form::select('building', ['' => '--Building--'] + $buildings, isset($UnitObj->buildinginfo->id)?$UnitObj->buildinginfo->id:'', ['class'=>'form-control','id'=>'building','required' => true]) }}
                     
                  </div>
                  </div>
				   <div class="col-lg-2">
                  <div class="form-group ">
                     <label>unit no :</label>
                     <input  type="text" name="unit" class="form-control" value="<?php echo(isset($UnitObj->unit)?\Crypt::decryptString($UnitObj->unit):'');?>" id="unit_list" required>
                    
                  </div>
                  </div>
				   <div class="col-lg-2">
                  <div class="form-group ">
                     <label>unit code :</label>
                    <input type="text" class="form-control" disabled value="{{\Crypt::decryptString($UnitObj->code)}}"> 
                  </div>
                  </div>
				   <div class="col-lg-2">
                  <div class="form-group ">
                     <label>unit size :</label>
                    <input type="text" class="form-control" disabled value="{{$UnitObj->size}}"> 
                  </div>
                  </div>
				   <div class="col-lg-2">
                  <div class="form-group ">
                     <label>unit share :</label>
				    <input type="text" class="form-control" disabled value="{{intval($UnitObj->share_amount)}}"> 
                  
                  </div>
                  </div>
				  
                  <div class="col-lg-12">
                     <div class="form-group mt0-3">
                        <input type="hidden" name="current_unit" value="{{$UnitObj->id}}">
                           <button type="submit" class="submit  float-right">change</button>
                     </div>                         
                  </div>
            </div>
         </form>

         <div class="col-xs-12 ">
         @php
            $active_class ='class="nav-item nav-link active" data-toggle="tab" aria-expanded="true"';
            $not_active_class  = 'class="nav-item nav-link"';

            $tab_active_class = 'class="tab-pane fade mt-1 active show" aria-expanded="true"';
            $tab_not_active_class = 'class="tab-pane fade mt-1 " aria-expanded="false"';
         @endphp
         <div class="w-100 ">
		 <div class="scroller scroller-right float-right "><img src="{{url('assets/img/rightarrow.png')}}" class="arrwidth"></div>
         <div class="scroller scroller-left float-right "><img src="{{url('assets/img/leftarrow.png')}}" class="arrwidth"></div>

<div class="wrapper">
               <nav class="nav nav-tabs list " id="myTab" role="tablist">
               @if((isset($user_permission) && $user_permission->view==1))
                  <a  data-toggle="tab" href="#tab1" role="tab" aria-controls="public" @if($tab==1) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif >Contact  <br> info</a>
               @endif
               
               @if((isset($collection_permission) && $collection_permission->view==1))
                  <a  href="#tab14" role="tab" data-toggle="tab" @if($tab==14) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>key <br>collection</a>
               @endif

               @if((isset($defect_permission) && $defect_permission->view==1))
                  <a  href="#tab2" role="tab" data-toggle="tab" @if($tab==2) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif >defects</a>
               @endif
               
               @if((isset($facility_permission) && $facility_permission->view==1))
                  <a  href="#tab3" role="tab" data-toggle="tab" @if($tab==3) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>facility <br>booking</a>
               @endif

               @if((isset($feedback_permission) && $feedback_permission->view==1))
                  <a  href="#tab4" role="tab" data-toggle="tab" @if($tab==4) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>feedback</a>
               @endif

               @if((isset($move_permission) && $move_permission->view==1))
                  <a  href="#tab5" role="tab" data-toggle="tab" @if($tab==5) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-moving <br> in & out</a>
               @endif

               @if((isset($renovation_permission) && $renovation_permission->view==1))
                  <a  href="#tab6" role="tab" data-toggle="tab" @if($tab==6) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-renovation <br> registration</a>
               @endif

               @if((isset($dooraccess_permission) && $dooraccess_permission->view==1))
                  <a  href="#tab7" role="tab" data-toggle="tab" @if($tab==7) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-access card  <br> registration</a>
               @endif

               @if((isset($vehicle_permission) && $vehicle_permission->view==1))
                  <a  href="#tab8" role="tab" data-toggle="tab" @if($tab==8) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-registration <br> for  vehicle iu</a>
               @endif

               @if((isset($mailling_permission) && $mailling_permission->view==1))
                  <a  href="#tab9" role="tab" data-toggle="tab" @if($tab==9) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-change of <br> mailing address</a>
               @endif
                                                                           
               @if((isset($particular_permission) && $particular_permission->view==1))
                  <a href="#tab10" role="tab" data-toggle="tab" @if($tab==10) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>E-update of <br> particulars</a>
               @endif

               @if((isset($card_permission) && $card_permission->view==1))
                  <a  href="#tab11" role="tab" data-toggle="tab" @if($tab==11) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>access cards  <br> management</a>
               @endif

               @if((isset($vm_permission) && $vm_permission->view==1))
                  <a  href="#tab12" role="tab" data-toggle="tab" @if($tab==12) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>visitors <br> management</a>
               @endif

               @if((isset($rm_permission) && $rm_permission->view==1))
                  <a href="#tab13" role="tab" data-toggle="tab" @if($tab==13) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>resident <br> management</a>
               @endif
               @if((isset($fileupload) && $fileupload->view==1))
                  <a  href="#tab15" role="tab" data-toggle="tab" @if($tab==15) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>resident <br> file upload</a>
               @endif
               @if((isset($user_permission) && $user_permission->view==1))
                  <a  href="#tab16" role="tab" data-toggle="tab" @if($tab==16) class="nav-item nav-link active" aria-expanded="true" @else class="nav-item nav-link" @endif>License <br> Plates</a>
               @endif

               </nav>
            </div>
            <div class="tab-content bor3" id="myTabContent">
            
            @if((isset($user_permission) && $user_permission->view==1))
            <!-- Contact info -->
               <div  id="tab1" role="tabpanel" aria-labelledby="group-dropdown1-tab" aria-expanded="true" @if($tab==1) class="tab-pane fade mt-1 active show" aria-expanded="true" @else class="tab-pane fade mt-1" aria-expanded="false" @endif>
			    <div class="overflowscroll2">
                  <table class="gap ">
                     <thead>
                        <tr>
                           <th>first name</th>
                           <th>last name</th>
                           <th>photo</th>
                           <th>assigned role</th>
                           <th>phone</th>
                           <th>status</th>
                           <th>start date</th>
                           <th>end date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                        
                        @if($users)
                        
                           @foreach($users as $k => $user)
                           
                           @php
                              $role_id =  isset($user->getuser->role_id)?$user->getuser->role_id:'';
                              $building_name = '';
                              $unit_name = '';
                              if(isset($unit) && $unit >0){
                                 $unitObj = new \App\Models\v7\Unit();
                                 //echo $unit;
                                 $moreinfo = new \App\Models\v7\UserMoreInfo();
                                 $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id,$unit);
                                 $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:'';
                                 //echo $role_id;
                                 $roleInfo = $moreinfo->roleInfo($role_id);
                                 $unitinfo = $unitObj->unit_info($user->user_id,$unit,$building);
                                 $building_name = isset($unitinfo->addubuildinginfo)?$unitinfo->addubuildinginfo->building:'';
                                 $unit_name = isset($unitinfo->addunitinfo)?"#".$unitinfo->addunitinfo->unit:'';

                                 $PurchaseUnitDetail = $moreinfo->userunitinfo($user->id,$UnitObj->id);

                                 $primary_contact = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->primary_contact:'';
                                 $UserPurchaseId = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->id:'';
                                 $purchaseStatus = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->status:'';
                              }
                              else if(in_array($user->getuser->role_id,$app_user_lists)){
                                 //echo "User :".$user->id." Property :".$user->account_id;
                                 $moreinfo = new \App\Models\v7\UserMoreInfo();
                                 $purchaseUnitInfo = $moreinfo->moreunitinfo($user->id,$user->account_id);
                                 $role_id = isset($purchaseUnitInfo->role_id)?$purchaseUnitInfo->role_id:'';

                                 $roleInfo = $moreinfo->roleInfo($role_id);
                                 $unitInfo = $moreinfo->unitInfo(isset($purchaseUnitInfo->unit_id)?$purchaseUnitInfo->unit_id:'');
                                 if(isset($unitInfo))
                                    $buildingInfo = $moreinfo->buildinginfo($unitInfo->building_id);
                                 $building_name = isset($buildingInfo)?$buildingInfo->building:'';
                                 $unit_name = isset($unitInfo)?"#".$unitInfo->unit:'';

                                 $PurchaseUnitDetail = $moreinfo->userunitinfo($user->id,$UnitObj->id);

                                 $primary_contact = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->primary_contact:'';
                                 $UserPurchaseId = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->id:'';
                                 $purchaseStatus = (isset($PurchaseUnitDetail))?$PurchaseUnitDetail->status:'';
                                 //exit;
                              }
                              
                              @endphp
                              <tr class='{{($user->status !=1)?"textdisabled":""}}'>
                                 <td class="roundleft">{{Crypt::decryptString($user->first_name)}}</td>
                                 <td class="spacer">{{isset($user->last_name)?Crypt::decryptString($user->last_name):''}}</td>
                                 <td class="spacer"> 
                                 @if(isset($user->profile_picture) && $user->profile_picture !='')
                                 <a href="{{$file_path}}/{{$user->profile_picture}}" target="_blank">
                                    <img src="{{$file_path}}/{{$user->profile_picture}}" class="viewimg phvert">
                                 </a>
                                 @endif
                                   
                                 </td>
                                 <td class="spacer"> 
                                 @if($role_id >0 && !in_array($role_id,$app_user_lists))
                                    <a href="#" alt="{{isset($user->getuser->role->name)?$user->getuser->role->name:''}}" title="{{isset($user->getuser->role->name)?$user->getuser->role->name:''}}" style="color:#5D5D5D">{{isset($user->getuser->role->name)?Str::limit($user->getuser->role->name,20):''}} </a>
                                 @else
                                    <a href="#" alt="{{isset($roleInfo->name)?$roleInfo->name:''}}" title="{{isset($roleInfo->name)?$roleInfo->name:''}}" style="color:#5D5D5D">{{isset($roleInfo->name)?Str::limit($roleInfo->name,20):''}}</a>
                                 @endif
                              @if(isset($primary_contact) && $primary_contact ==1)
                              <span style="color:red">* </span>
                              @endif</td>
                                 <td class="spacer">{{isset($user->phone)?Crypt::decryptString($user->phone):''}} </td>
                                 <td class="spacer">
                                 @php
                                    if($user->status ==2)
                                       echo "Account Deleted";
                                    else if($purchaseStatus ==1)
                                       echo "Active";
                                    else
                                       echo "Deactive";
                                 @endphp </td>

                                 <td class="spacer">{{date('d/m/y',strtotime($user->created_at))}}</td>
                                 <td class="spacer">{{($user->deactivated_date != '0000-00-00' && $user->status ==0)?date('d/m/y',strtotime($user->deactivated_date)):''}}</td>
                                 <td class="roundright">
                                    <div class="dropdown">
                                       <div class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                          @if($user->status !=1)
                                             <a class="dropdown-item" href="{{url("opslogin/user/$user->id/edit")}}">View</a>
                                          @else
                                             <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/userunit/delete/$UserPurchaseId")}}');">Delete</a>
                                             @if(($purchaseStatus ==0))
                                                <a class="dropdown-item" href="#" alt="Activate" title="Activate" onclick="activate_record('{{url("opslogin/userunit/activate/$UserPurchaseId")}}');">Activate</a>
                                             @else
                                                <a class="dropdown-item" href="#"  alt="De-Activate" title="De-Activate"  onclick="deactivate_record('{{url("opslogin/userunit/deactivate/$UserPurchaseId")}}');" >De-Activate</a>
                                             @endif
                                          @endif
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                             
                           @endforeach
                        @endif
                     </tbody>
                  </table>
               </div>
               </div>
               @endif


               @if((isset($defect_permission) && $defect_permission->view==1))
               <!-- Defects -->
               <div id="tab2" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==2) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                     <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>status</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted <br>date</th>
                           <th>appt<br> date & time</th>
                           <th>appt <br>status</th>
                           <th>completion<br> date</th>
                           <th>reference <br> id</th>
                           <th>list</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($defects)
                       @foreach($defects as $k => $defect)
                        <tr>
                           <td class="roundleft">{{$defect->ticket}}@if($defect->view_status ==0)&nbsp;<span class="badge badge-pill badge-danger text-white">New</span>@endif</td>
                           <td class="spacer">
                           @php
                              if(isset($defect->status)){
                              if($defect->status==0)
                                 echo "OPEN";
                                 else if($defect->status==1)
                                 echo "CLOSED";
                              else if($defect->status==3)
                                 echo "ON SCHEDULE";
                              else
                                 echo "IN PROGRESS";
                              }
                              @endphp
                           </td>
                           <td class="spacer">{{isset($defect->user->userinfo->getunit->unit)?Crypt::decryptString($defect->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($defect->user->name)?Crypt::decryptString($defect->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($defect->created_at))}}</td>
                           <td class="spacer">{{isset($defect->inspection->appt_date)?date('d/m/y',strtotime($defect->inspection->appt_date)):''}} {{isset($defect->inspection->appt_time)?$defect->inspection->appt_time:''}}
                          </td>
                           
                           <td class="spacer">@php 
                           if(isset($defect->inspection->status) && $defect->inspection->status==0)
                                    echo "New";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==1)
                                    echo "Cancelled";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==2)
                                    echo "On Schedule";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==3)
                                    echo "Done";
                                 else  if(isset($defect->inspection->status) && $defect->inspection->status==4)
                                    echo "In Progress";
                           @endphp
                           </td>
                           <td class="spacer">{{($defect->completion_date !='0000-00-00')?date('d/m/y',strtotime($defect->completion_date)):''}}</td>
                           <td>{{$defect->ref_id}}</td>
                           <td class="spacer"><a href="{{$visitor_app_url}}/generate-pdf/{{$defect->id}}"   data-toggle="tooltip" data-placement="top" title="List" data-original-title="List" target="_blank" ><img src="{{url('assets/admin/img/Condo.png')}}" class="viewimg phvert"></a></td>
                           
                           
                           <td class="roundright">
						    <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   @if(isset($defect_permission) && $defect_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/defects/$defect->id/edit")}} " >Joint Inspection</a>
                              @if($defect->handover_status ==1 || $defect->handover_status ==2)
                                 <a class="dropdown-item" href="{{url("opslogin/defects/handover/$defect->id")}}" >Hand Over</a>
                              @endif
                           @endif
                           @if(isset($defect_permission) && $defect_permission->delete==1)
                           <a class="dropdown-item float-right" href="#" onclick="delete_record('{{url("opslogin/defects/delete/$defect->id")}}');">delete</a>
                           @endif
                                                </div>
                                             </div>
                         
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif



               @if((isset($collection_permission) && $collection_permission->view==1))
               <!-- Key Collection -->
               <div id="tab14" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==14) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>unit no</th>
                           <th>booked by</th>
                           <th>appointment date</th>
                           <th>appointment  time</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($keycollections)

                        @foreach($keycollections as $k => $appt)
                        <tr>
                           <td class="roundleft">{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($appt->getname->name)?Crypt::decryptString($appt->getname->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($appt->appt_date))}}</td>
                           <td class="spacer">{{$appt->appt_time}}</td>
                           <td class="spacer">@php
                                 if(isset($appt->status)){
                                 if($appt->status==0)
                                    echo "New";
                                 else  if($appt->status==1)
                                    echo "Cancelled";
                                    else  if($appt->status==2)
                                    echo "On Schedule";
                                    else  if($appt->status==3)
                                    echo "Done";
                                 }
                                 @endphp
                           </td>

                           <td class="roundright">
						      <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                  @if(isset($collection_permission) && $collection_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/takeover_appt/$appt->id/edit")}}">Edit</a>
                           @endif
                                                </div>
                                             </div>
                          
                           </td>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($facility_permission) && $facility_permission->view==1))
               <!-- Facility -->
               <div id="tab3" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==3) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap">
                     <thead>
                        <tr>
                        <th>facility</th>
                           <th>booked by</th>
                           <th>unit</th>
                           <th>booking date</th>
                           <th>booking time</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                       @if($facilities)

                        @foreach($facilities as $k => $booking)
                        <tr>
                           <td class="roundleft">{{isset($booking->gettype->facility_type)?$booking->gettype->facility_type:''}}</td>
                           <td class="spacer">{{isset($booking->getname->name)?Crypt::decryptString($booking->getname->name):''}}</td>
                           <td class="spacer">{{isset($booking->getname->getunit->unit)?Crypt::decryptString($booking->getname->getunit->unit):''}}</td>

                           <td class="spacer">{{date('d/m/y',strtotime($booking->booking_date))}}</td>
                           <td class="spacer">{{$booking->booking_time}}</td>
                           <td class="spacer">
                           @php
                            if(isset($booking->status)){
                              if($booking->status==0)
                                echo "New";
                              else if($booking->status==1)
                                echo "Cancelled";
                              else
                                echo "Confirmed";
                            }
                            @endphp
                            </td>

                           <td class="roundright">
						    <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                 @if(isset($facility_permission) && $facility_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/facility/$booking->id/edit")}}">Edit</a>
                           @endif
                                                </div>
                                             </div>
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($feedback_permission) && $feedback_permission->view==1))
               <!-- Feedback -->
               <div  id="tab4" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==4) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket</th>
                           <th>submitted date</th>
                           <th>category</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>status</th>
                           <th>updated on</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($feedbacks)

                       @foreach($feedbacks as $k => $feedback)
                        <tr>
                           <td class="roundleft">{{$feedback->ticket}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($feedback->created_at))}}</td>
                           <td class="spacer">{{isset($feedback->getoption->feedback_option)?$feedback->getoption->feedback_option:''}}</td>
                           <td class="spacer">{{isset($feedback->user->userinfo->getunit->unit)?Crypt::decryptString($feedback->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($feedback->user->name)?Crypt::decryptString($feedback->user->name):''}}</td>
                           
                           <td class="spacer">@php
                              if(isset($feedback->status)){
                              if($feedback->status==0)
                                 echo "OPEN";
                              else if($feedback->status==1)
                                 echo "CLOSED";
                              else
                                 echo "IN PROGRESS";
                              }
                              @endphp
                           </td>
                  <td class="spacer">{{date('d/m/y',strtotime($feedback->updated_at))}}</td>
                           <td class="roundright">
						     <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   @if(isset($feedback_permission) && $feedback_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/feedbacks/$feedback->id/edit")}}">Edit</a>
                           @endif
                         @if(isset($feedback_permission) && $feedback_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/feedbacks/delete/$feedback->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                          
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($move_permission) && $move_permission->view==1))
               <!-- Moving IN & Out -->
               <div  id="tab5" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==5) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>moving start</th>
                           <th>moving end</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($moveinouts)

                       @foreach($moveinouts as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->moving_start))}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->moving_end))}}</td>
                           <td class="spacer">@php
                              if(isset($form->status)){
                                 if($form->status==0)
                                    echo "NEW";
                                 else if($form->status==1)
                                    echo "CANCELLED";
                                 else if($form->status==3)
                                    echo "APPROVED";
                                 else if($form->status==2)
                                    echo "IN PROGRESS";
                                 else if($form->status==5)
                                    echo "PAYMENT PENDING";
                                 else if($form->status==6)
                                    echo "REFUNDED";
                                 else 
                                    echo "REJECTED";
                              
                              }
                              @endphp
                           </td>
                           
                           <td class="roundright">
						     <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                    @if(isset($move_permission) && $move_permission->edit==1)
                           <!--<a href="{{$visitor_app_url}}/moveinginout-pdf/{{$form->id}}"  data-toggle="tooltip" data-placement="top" title="List" target="_blank"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->
                           <a class="dropdown-item" href="{{url("opslogin/eform/moveinout/$form->id/edit")}}">Edit</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/moveinout/payment/$form->id")}}">Payment</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/moveinout/inspection/$form->id")}}">Inspection</a>
                           @endif
                           @if(isset($move_permission) && $move_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/moveinout/delete/$form->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                          
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($renovation_permission) && $renovation_permission->view==1))
               <!-- Renovation -->
               <div  id="tab6" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==6) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap">
                     <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>work start</th>
                           <th>work end</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($renovations)

                       @foreach($renovations as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->reno_start))}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->reno_end))}}</td>
                           <td class="spacer">@php
                              if(isset($form->status)){
                                 if($form->status==0)
                                    echo "NEW";
                                 else if($form->status==1)
                                    echo "CANCELLED";
                                 else if($form->status==3)
                                    echo "APPROVED";
                                 else if($form->status==2)
                                    echo "IN PROGRESS";
                                 else if($form->status==5)
                                    echo "PAYMENT PENDING";
                                 else if($form->status==6)
                                    echo "REFUNDED";
                                 else 
                                    echo "REJECTED";
                              
                              }
                              @endphp
                           </td>
                           
                           <td class="roundright">
                           @if(isset($renovation_permission) && $renovation_permission->edit==1)
							    <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   <!--<a href="{{$visitor_app_url}}/renovation-pdf/{{$form->id}}"  data-toggle="tooltip" data-placement="top" title="List" target="_blank"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->
                           <a class="dropdown-item" href="{{url("opslogin/eform/renovation/$form->id/edit")}}">Edit</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/renovation/payment/$form->id")}}" >Payment</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/renovation/inspection/$form->id")}}" >Inspection</a>
                           @endif
                           @if(isset($renovation_permission) && $renovation_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/renovation/delete/$form->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                           
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($dooraccess_permission) && $dooraccess_permission->view==1))
               <!-- Door Access Card -->
               <div  id="tab7" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==7) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>tenancy start</th>
                           <th>tenancy end</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($dooraccess)

                       @foreach($dooraccess as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">{{($form->relationship ==2)?date('d/m/y',strtotime($form->tenancy_start)):''}}</td>
                           <td class="spacer">{{($form->relationship ==2)?date('d/m/y',strtotime($form->tenancy_end)):''}}</td>
                           <td>@php
                              if(isset($form->status)){
                                 if($form->status==0)
                                    echo "NEW";
                                 else if($form->status==1)
                                    echo "CANCELLED";
                                 else if($form->status==3)
                                    echo "APPROVED";
                                 else if($form->status==2)
                                    echo "IN PROGRESS";
                                 else 
                                    echo "REJECTED";
                              
                              }
                              @endphp
                           </td>
                           
                           <td class="roundright">
						    <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   @if(isset($dooraccess_permission) && $dooraccess_permission->edit==1)
                           <!--<a href="{{$visitor_app_url}}/dooraccess-pdf/{{$form->id}}"   target="_blank" data-toggle="tooltip" data-placement="top" title="List"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->
                           <a class="dropdown-item" href="{{url("opslogin/eform/dooraccess/$form->id/edit")}}">Edit</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/dooraccess/payment/$form->id")}}">Payment</a>
                           <a class="dropdown-item" href="{{url("opslogin/eform/dooraccess/acknowledgement/$form->id")}}">Handover</a>
                           @endif
                           @if(isset($dooraccess_permission) && $dooraccess_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/dooraccess/delete/$form->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                         
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($vehicle_permission) && $vehicle_permission->view==1))
               <!-- Vehicle IU -->
               <div  id="tab8" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==8) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>tenancy start</th>
                           <th>tenancy end</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($vehicleuis)

                       @foreach($vehicleuis as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">{{($form->relationship ==2)?date('d/m/y',strtotime($form->tenancy_start)):''}}</td>
                           <td class="spacer">{{($form->relationship ==2)?date('d/m/y',strtotime($form->tenancy_end)):''}}</td>
                           <td class="spacer">@php
                                 if(isset($form->status)){
                                    if($form->status==0)
                                       echo "NEW";
                                    else if($form->status==1)
                                       echo "CANCELLED";
                                    else if($form->status==3)
                                       echo "APPROVED";
                                    else if($form->status==2)
                                       echo "IN PROGRESS";
                                    else 
                                       echo "REJECTED";
                                 
                                 }
                                 @endphp
                           </td>
                           
                           <td class="roundright">
						       <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   @if(isset($vehicle_permission) && $vehicle_permission->edit==1)
                           <!--<a href="{{$visitor_app_url}}/vehicleiu-pdf/{{$form->id}}"   target="_blank" data-toggle="tooltip" data-placement="top" title="List"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->

                           <a class="dropdown-item" href="{{url("opslogin/eform/regvehicle/$form->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($vehicle_permission) && $vehicle_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/regvehicle/delete/$form->id")}}');" >Delete</a>
                           @endif
                                                </div>
                                             </div>
                         
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($mailling_permission) && $mailling_permission->view==1))
               <!-- Mailling Address -->
               <div id="tab9" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==9) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($addresses)

                       @foreach($addresses as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">@php
                              if(isset($form->status)){
                                 if($form->status==0)
                                    echo "NEW";
                                 else if($form->status==1)
                                    echo "CANCELLED";
                                 else if($form->status==3)
                                    echo "APPROVED";
                                 else if($form->status==2)
                                    echo "IN PROGRESS";
                                 else 
                                    echo "REJECTED";
                              
                              }
                              @endphp
                           </td>
                           
                           <td class="roundright">
						     <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                 @if(isset($mailling_permission) && $mailling_permission->edit==1)
                           <!--<a href="{{$visitor_app_url}}/address-pdf/{{$form->id}}"  data-toggle="tooltip" data-placement="top" title="List" target="_blank"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->

                           <a class="dropdown-item" href="{{url("opslogin/eform/changeaddress/$form->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($mailling_permission) && $mailling_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/changeaddress/delete/$form->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                          
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($particular_permission) && $particular_permission->view==1))
                <!-- Particluars -->
                <div  id="tab10" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==10) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
				   <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>ticket no</th>
                           <th>unit no</th>
                           <th>submitted by</th>
                           <th>submitted date</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                  @if($particulars)

                       @foreach($particulars as $k => $form)
                        <tr>
                           <td class="roundleft">{{$form->ticket}}</td>
                           <td class="spacer">{{isset($form->user->userinfo->getunit->unit)?Crypt::decryptString($form->user->userinfo->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($form->user->name)?Crypt::decryptString($form->user->name):''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($form->created_at))}}</td>
                           <td class="spacer">@php
                              if(isset($form->status)){
                                 if($form->status==0)
                                    echo "NEW";
                                 else if($form->status==1)
                                    echo "CANCELLED";
                                 else if($form->status==3)
                                    echo "APPROVED";
                                 else if($form->status==2)
                                    echo "IN PROGRESS";
                                 else 
                                    echo "REJECTED";
                              
                              }
                              @endphp
                           </td>
                           
                           <td class="roundright">
						   <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                     @if(isset($particular_permission) && $particular_permission->edit==1)
                           <!--<a href="{{$visitor_app_url}}/particulars-pdf/{{$form->id}}"  data-toggle="tooltip" data-placement="top" title="List" target="_blank"><img src="{{url('assets/admin/img/Files.png')}}"></a>-->

                           <a class="dropdown-item" href="{{url("opslogin/eform/particular/$form->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($particular_permission) && $particular_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/eform/particular/delete/$form->id")}}');" >Delete</a>
                           @endif
                                                </div>
                                             </div>
                        
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($card_permission) && $card_permission->view==1))
               <!-- Access Card -->
               <div id="tab11" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==11) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>#</th>
                           @if(Auth::user()->role_id ==1)
                           <th>property</th>
                        @endif
                           <th>card no</th>
                           <th>unit no</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($cards)

                       @foreach($cards as $k => $dept)
                        <tr>
                           <td class="roundleft">{{$k+1}}</td>
                           @if(Auth::user()->role_id ==1)
                        <td class="spacer">{{isset($dept->propertyinfo->company_name)?$dept->propertyinfo->company_name:''}}</td>
                        @endif
                           <td class="spacer">{{$dept->card}}</td>
                           <td class="spacer">{{isset($dept->getunit->unit)?Crypt::decryptString($dept->getunit->unit):''}}</td>
                           <td class="spacer"><?php
                           if($dept->status ==1)
                              echo "Active";
                           else if($dept->status ==2)
                           echo "Inactive";
                           else if($dept->status ==3)
                           echo "Faulty";
                           else if($dept->status ==4)
                           echo "Loss";
                           else if($dept->status ==5)
                           echo "Stolen";
                          
                           ?></td>
                           
                           <td class="roundright">
						     <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                    @if(isset($card_permission) && $card_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/card/$dept->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($card_permission) && $card_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/card/delete/$dept->id")}}');">Delete</a>
                           @endif
                                                </div>
                                             </div>
                        
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($vm_permission) && $vm_permission->view==1))
               <!-- Visitor Management -->
               <div id="tab12" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==12) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
			      <div class="overflowscroll2">
                  <table class="gap ">
                  <thead>
                        <tr>
                           <th>booking id</th>
                           <th>unit no</th>
                           <th>invited by</th>
                           <th>date of visit</th>
                           <th>entry date</th>
                           <th>entry time</th>
                           <th>visitor no</th>
                           <th>purpose</th>
                           <th>status</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($visitors_bookings)

                       @foreach($visitors_bookings as $k => $booking)
                        <tr>
                           <td class="roundleft">{{$booking->ticket}}</td>
                           <td class="spacer">
                           <?php
                           if($booking->booking_type==1)
                              echo isset($booking->user->userinfo->getunit->unit)?Crypt::decryptString($booking->user->userinfo->getunit->unit):'';
                           else
                              echo isset($booking->getunit->unit)?Crypt::decryptString($booking->getunit->unit):'';
                           ?>
                           </td>
                           <td class="spacer"><?php
                           if($booking->booking_type ==1)
                              echo isset($booking->user->name)?Crypt::decryptString($booking->user->name):'';
                           else
                              echo "Walk-In";
                           ?></td>
                           
                           <td class="spacer">{{date('d/m/y',strtotime($booking->visiting_date))}}
                           </td>
                           <td class="spacer">
                              {{($booking->booking_type ==2)?date('d/m/y',strtotime($booking->entry_date)):''}}
                           </td>
                           <td class="spacer">
                              {{($booking->booking_type ==2)?date('H:i',strtotime($booking->entry_date)):''}}
                           </td>
                           <td class="spacer"><?php echo $booking->visitors->count();?></td>
                           <td class="spacer">{{isset($booking->visitpurpose->visiting_purpose)?$booking->visitpurpose->visiting_purpose:''}}
                           
                           <td class="spacer">
                              <?php
                              if($booking->visited_count->count() >= $booking->visitors->count())
                                 echo "Entered";
                              else if($booking->visited_count->count() >0 && $booking->visited_count->count() < $booking->visitors->count())
                                 echo $booking->visited_count->count()." Entered";
                              else if($booking->status==0)
                                 echo "Pending";
                              else if($booking->status==1)
                                 echo "Cancelled";
                              else  
                                 echo "Entered";
                              ?></td>
                              <td class="roundright">
							  <div class="dropdown">
                                                <div class=" dropdown-toggle" data-toggle="dropdown">
                                                   <div class="three-dots"></div>
                                                </div>
                                                <div class="dropdown-menu">
                                                   @if(isset($vm_permission) && $vm_permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/visitor-summary/$booking->id/edit")}}">Edit</a>
                           @endif
                         @if(isset($vm_permission) && $vm_permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/visitor-summary/delete/$booking->id")}}');" >Delete</a>
                           @endif
                                                </div>
                                             </div>
                       
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>
               @endif

               

               @if((isset($rm_permission) && $rm_permission->view==1))
               <!-- Resident Management -->
                  <div id="tab13" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==13) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
                     <div class="overflowscroll2">
                        <table class="gap ">
                        <thead>
                              <tr>
                                 <th>invoice</th>
                                 <th>batch no</th>
                                 <th>building</th>
                                 <th>unit no</th>
                                 <th>total amount</th>
                                 <th>created at</th>
                                 <th>status</th>
                                 <th>actions</th>
                              </tr>
                           </thead>
                           <tbody>
                           @if($invoices)

                              @foreach($invoices as $k => $appt)
                              <tr>
                                 <td class="roundleft">{{$appt->invoice_no}}</td>
                                 <td class="spacer">{{$appt->batch_file_no}}</td>
                                 <td class="spacer">{{isset($appt->getunit->buildinginfo->building)?$appt->getunit->buildinginfo->building:''}}</td>
                                 <td class="spacer">{{isset($appt->getunit->unit)?Crypt::decryptString($appt->getunit->unit):''}}</td>
                                 <td class="spacer">{{$appt->payable_amount}}</td>
                                 <td class="spacer">{{date('d/m/y',strtotime($appt->created_at))}}</td>
                                 <td class="spacer">
                                    @php
                                       if(isset($appt->status)){
                                          if($appt->status==1)
                                             echo "Payment Pending";
                                          else  if($appt->status==2)
                                             echo "Partially Paid";
                                          else 
                                             echo "Paid";
                                       
                                       }
                                    @endphp
                                 </td>

                                 <td class="roundright">
                           <div class="dropdown">
                                                      <div class=" dropdown-toggle" data-toggle="dropdown">
                                                         <div class="three-dots"></div>
                                                      </div>
                                                      <div class="dropdown-menu">
                                                            @if(isset($rm_permission) && $rm_permission->view==1)
                                 <a class="dropdown-item" href="{{$visitor_app_url}}/invoice-pdf/{{$appt->id}}"  target="_blank">Print PDF</a>
                                 @endif
                                 @if(isset($rm_permission) && $rm_permission->view==1)
                                 <a class="dropdown-item" href="{{url("opslogin/invoiceview/$appt->id")}}">View</a>
                                 <a class="dropdown-item" href="{{url("opslogin/invoice/payment/$appt->id")}}">Payment</a>
                                 @endif
                                 @if(isset($rm_permission) && $rm_permission->delete==1)
                                 <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/invoicedelete/$appt->id")}}');">Delete</a>
                                 @endif
                                                      </div>
                                                   </div>
                           
                                 </td>
                                 </tr>
                           @endforeach

                           @endif   
                           </tbody>
                        </table>
                     </div>
                  </div>
               @endif

               @if((isset($fileupload) && $fileupload->view==1))
               <!-- Resident Management -->
                  <div id="tab15" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==15) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
                     <div class="overflowscroll2">
                        <table class="gap ">
                        <thead>
                              <tr>
                                 <th>s/n</th>
                                 <th>unit</th>
                                 <th>upload by</th>
                                 <th>upload date </th>
                                 <th>category</th>
                                 <th>status</th>
                                 <th>updated on</th>
                                 <th>actions</th>
                              </tr>
                           </thead>
                           <tbody>
                           @if($fileuploads)
                              @foreach($fileuploads as $k => $submission)
                              <tr>
                                 <td class="roundleft">{{$k+1}} @if($submission->view_status ==0)
                              &nbsp;<span class="badge badge-pill badge-danger text-white">New</span>
                              @endif</td>
                                 <td class="spacer">{{isset($submission->user->userinfo->getunit->unit)?Crypt::decryptString($submission->user->userinfo->getunit->unit):''}}</td>
                                 <td class="spacer">{{isset($submission->user->name)?Crypt::decryptString($submission->user->name):''}}</td>
                                 <td class="spacer">{{date('d/m/y',strtotime($submission->created_at))}}</td>
                                 <td class="spacer">
                                    {{$submission->cat_id}}{{isset($submission->category->docs_category)?$submission->category->docs_category:''}}
                                 </td>
                              
                                 <td class="spacer">
                                    @php
                                    if(isset($submission->status)){
                                    if($submission->status==0)
                                       echo "NEW";
                                       else if($submission->status==1)
                                       echo "PROCESSING";
                                    else
                                       echo "PROCESSED";
                                    }
                                    @endphp
                                 </td>
                                 <td class="spacer">
                                    {{($submission->created_at !=$submission->updated_at)?date('d/m/y',strtotime($submission->updated_at)):''}}
                                 </td>
                                 <td class="roundright" >
                                    <div class="dropdown">
                                       <div  class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                          @if(isset($fileupload) && $fileupload->edit==1)
                                             <a  class="dropdown-item" href="{{url("opslogin/residents-uploads/$submission->id/edit")}}" ">Edit
                                             </a>
                                          @endif
                                          @if(isset($fileupload) && $fileupload->delete==1)
                                             <a  class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/residents-uploads/delete/$submission->id")}}');" >Delete</a>
                                          @endif
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           @endforeach

                           @endif   
                           </tbody>
                        </table>
                     </div>
                  </div>
               @endif

               @if((isset($user_permission) && $user_permission->view==1))
               <!-- Resident Management -->
                  <div id="tab16" role="tabpanel" aria-labelledby="group-dropdown2-tab" @if($tab==16) class="tab-pane fade mt-2 active show" aria-expanded="true" @else class="tab-pane fade mt-2" aria-expanded="false" @endif>
                     <div class="overflowscroll2">
                        <table class="gap ">
                        <thead>
                              <tr>
                                 <th>s/n</th>
                                 <th>Block</th>
                                 <th>unit</th>
                                 <th>Name</th>
                                 <th>License Plate</th>
                                 <th>Added On </th>
                                 <th>actions</th>
                              </tr>
                           </thead>
                           <tbody>
                           @if($License_plates)
                              @foreach($License_plates as $k => $license)
                              <tr>
                                 <td class="roundleft">{{$k+1}}</td>
                                 <td class="spacer">{{isset($license->buildinginfo->building)?$license->buildinginfo->building:''}}</td>
                                 <td class="spacer">{{isset($license->addunitinfo->unit)?Crypt::decryptString($license->addunitinfo->unit):''}}</td>
                                 <td class="spacer">{{isset($license->usermoreinfo->first_name)?Crypt::decryptString($license->usermoreinfo->first_name):''}} {{isset($license->usermoreinfo->last_name)?Crypt::decryptString($license->usermoreinfo->last_name):''}}</td>
                                 <td class="spacer">{{$license->license_plate}}</td>
                                 <td class="spacer">{{date('d/m/y',strtotime($license->created_at))}}</td>
                                 
                                 <td class="roundright" >
                                    <div class="dropdown">
                                       <div  class=" dropdown-toggle" data-toggle="dropdown">
                                          <div class="three-dots"></div>
                                       </div>
                                       <div class="dropdown-menu">
                                          @if(isset($user_permission) && $user_permission->edit==1)
                                             <a  class="dropdown-item" href="{{url("opslogin/licenseplate/$license->id/edit")}}" ">Edit
                                             </a>
                                          @endif
                                          @if(isset($user_permission) && $user_permission->delete==1)
                                             <a  class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/licenseplate/delete/$license->id")}}');" >Delete</a>
                                          @endif
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           @endforeach
                              <div class="alert alert-info">Maximum 2 License Plates allowed per unit.</div>
                           @endif   
                           </tbody>
                        </table>
                     </div>
                  </div>
               @endif

            </div>
         </div>
      </div>
   </div>
</div>
</div>
</div>
</section>
@stop