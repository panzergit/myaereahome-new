
@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $user_permission =  $permission->check_menu_permission(7,$permission->role_id,1);
@endphp
  <div class="status">
    <h1>User Management - User Info</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                  </ul>
               </div>
               </div>
<div class="">
                       {!! Form::model($UserObj,['method' =>'PATCH','url' => url('user/'.$UserObj->id),'class'=>'forunit']) !!}

                     <div class="row asignbg editbg p-3">
                     @if(@Auth::user()->role_id ==1)
                <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">
          <label>Property:</label>
                </label>
                              <div class="col-sm-5">
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                              </div>
                           </div>
                </div>
                @else
                <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                @endif
                
                <div class="col-lg-12" >
                  <div class="" id="primary_div" style="display:none">
                     <div class="form-group row">
					 <label class="containeruser1">primary contact
                                 <input type="checkbox"  id="primary" name="primary_contact" value="1" style="margin-bottom: 0px; height: auto;" @if(isset($userPurchaseRec) && $userPurchaseRec->primary_contact ==1) checked="checked" @endif>  
                                 <span class="checkmarkuser1"></span>
                                 </label>
                        <!--div class="">
                           <input type="checkbox"  id="primary" name="primary_contact" value="1" style="margin-bottom: 0px; height: auto;" @if($UserObj->primary_contact ==1) checked="checked" @endif> &nbsp;&nbsp;&nbsp; <label>PRIMARY CONTACT </label>
                        </div-->
                     </div>
                  </div>
               </div>
               <div class="row">
                <div class="col-lg-3">
                           <div class="form-group ">
          <label>Assign role:</label>
             {{ Form::select('role_id', ['' => '--Select Role--'] + $roles, $UserObj->role_id, ['class'=>'form-control','required' => true,'id'=>'role' ,'onchange'=>'getunits()']) }}
                           </div> </div>
						 
               

                <div class="col-lg-3">
                           <div class="form-group ">
          <label>first name <span>*</span>: </label>
            {{ Form::text('name', isset($UserMoreInfoObj->first_name)?\Crypt::decryptString($UserMoreInfoObj->first_name):'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						     <div class="col-lg-3">
						    <div class="form-group ">
          <label>Last name <span>*</span>: </label>
            {{ Form::text('last_name', isset($UserMoreInfoObj->last_name)?\Crypt::decryptString($UserMoreInfoObj->last_name):'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>

						   <div class="col-lg-3">
						      <div class="form-group ">
          <label>contact <span>*</span>: </label>
             {{ Form::text('phone', isset($UserMoreInfoObj->phone)?\Crypt::decryptString($UserMoreInfoObj->phone):'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   						   <div class="col-lg-3">
						     <div class="form-group ">
          <label>email <span>*</span>: </label>
             {{ Form::text('email', null, ['class'=>'form-control','required' => true]) }}
                           
                           </div>
                           </div>
						   <div class="col-lg-3">
						      <div class="form-group ">
          <label>company: </label>
            {{ Form::text('company_name', isset($UserMoreInfoObj->company_name)?$UserMoreInfoObj->company_name:'', ['class'=>'form-control']) }}
                           </div>
                           </div>
             
         
  <div class="col-lg-3">
				                           <div class="form-group ">
          <label>mailing add <span>*</span>: </label>
                       {{ Form::textarea('mailing_address', isset($UserMoreInfoObj->mailing_address)?$UserMoreInfoObj->mailing_address:'', ['class'=>'form-control','required' => true,'rows'=>4]) }}

                           </div>
                           </div>
						    
                <div class="col-lg-3">
                           <div class="form-group">
                              <label>password:</label>
                              {{ Form::input('password', 'password','', ['class'=>'form-control','placeholder' => 'Enter Password']) }}
                        
                        </div>
                        </div>
				
           <div class="col-lg-3">
                           <div class="form-group">
          <label>country <span>*</span>: </label>
                              {{ Form::select('country', $countries, isset($UserMoreInfoObj->country)?$UserMoreInfoObj->country:'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
          <label>postal code <span>*</span>: </label>
                              {{ Form::text('postal_code', isset($UserMoreInfoObj->postal_code)?$UserMoreInfoObj->postal_code:'', ['class'=>'form-control']) }}
                           </div>
                           </div>
               @if(isset($faceids) && count($faceids) >0)
                  <div class="col-lg-6 row pr-0">
                  <label class="col-lg-12">Face ID Picture :</label>
                     @foreach($faceids as $faceid)
                        <div class="form-group col-lg-3">
                           <div class="d-flex flex-sm-row flex-column">
                              <div class="mr-auto p-2">
                                 <div class="image-upload uplodinline">
                                    @if(isset($faceid->face_picture))
                                       <label for="file-input" class="file-55 file100" >
                                          <img src="{{$file_path}}/{{$faceid->face_picture}}" class="announcementimg" >
                                       </label>
                                    @endif

                                 </div>
                              </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
               @endif
               
           
	</div>
   </div>
   </form>
     @if($license_plates)
               <div class="devicehead">
                  <h2>License Plates</h2> <a href="{{url("opslogin/licenseplate/add/$UserMoreInfoObj->id")}}" ><button class="submitwidth float-right">Add License Plates </button></a>
                  <div class=""> 
                     <table class="table usertable1">
                        <thead>
                           <tr>
                              <th>S.No</th>
                              <th>Unit</th>
                              <th>License Plate</th>
                              <th>assined date</th>
                              <th>action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($license_plates as $k => $license)
                           <tr>
                              <td>{{$k+1}}</td>
                              <td>#{{isset($license->addunitinfo->unit)?Crypt::decryptString($license->addunitinfo->unit):''}}</td>
                              <td>{{$license->license_plate}}</td>
                              <td>{{date('d/m/y',strtotime($license->created_at))}}</td>
                              <td >
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
                        </tbody>
                     </table>
                  </div>
               </div>
         @endif
         @if($PurchaserUnits)
            @php  $count =count($PurchaserUnits);@endphp
               <div class="devicehead">
                  <h2>Assigned Units</h2> <a href="{{url("opslogin/user/userunits/$UserMoreInfoObj->id")}}" ><button class="submitwidth float-right">Edit Assigned Units </button></a>
                  <div class="overflowscroll4"> 
                     <table class="table usertable1">
                        <thead>
                           <tr>
                              <th>Bulding</th>
                              <th>unit</th>
                              <th>role</th>
                              <th>primary contact</th>
                              <th>assined date</th>
                              <th>receive device call<th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($PurchaserUnits as $k => $PurchaserUnit)
                           <tr>
                              <td>{{isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:''}}</td>
                              <td>#{{isset($PurchaserUnit->addunitinfo)?\Crypt::decryptString($PurchaserUnit->addunitinfo->unit):''}}</td>
                              <td>{{isset($PurchaserUnit->role->name)?Str::limit($PurchaserUnit->role->name,20):''}}</td>
                              <td><label class="containeruser1">{{($PurchaserUnit->primary_contact==1)?"Yes":"No"}}</label></td>
                              <td>{{date('d/m/y',strtotime($PurchaserUnit->created_at))}}</td>
                              <td><label class="containeruser1"> {{($PurchaserUnit->receive_call==1)?"Yes":"No"}}</label></td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
         @endif

         @if(!empty($data))
            @php  $count =count($PurchaserUnits);@endphp
               <div class="devicehead">
                  <h2>Assigned Devices</h2> <a href="{{url("opslogin/user/userdevices/$UserMoreInfoObj->id")}}"> <button type="submit" class="submitwidth float-right">Edit Assigned Devices </button></a>
                  @foreach($data as $k => $record)
                     @php $record_id = $record['id'];  @endphp
                    
                              <h3>{{$record['building']}}, #{{\Crypt::decryptString($record['unit'])}}</h3>
							  
                           
                     <div class="overflowscroll">
                        <table class="table usertable1">
                           <thead>
                              <tr>
                                 <th>Device Name</th>
                                 <th>Serial No.</th>
                                 <th>Location</th>
                                 <th>Bluetooth Door Open</th>
                                 <th>Remote Door Open</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($record['devices'] as $device)
                                 @if(isset($device['id']))
                                    @php
                                       $diviceid = $device['id'];
                                       $bluetooth_status = $device['user_bluethooth_checked_status'];
                                       $remote_status = $device['user_remote_checked_status'];
                                    @endphp
                                    <tr>
                                       <td> {{isset($device['device_name'])?$device['device_name']:''}}</td>
                                       <td>{{isset($device['device_serial_no'])?$device['device_serial_no']:''}}</td>
                                       <td>{{isset($device['location'])?$device['location']:''}}</td>
                                       <td>@if($bluetooth_status ==1)
                                             <img src="{{url('assets/admin/img/tick.png')}}" class="tick">
                                          @else
                                          <img src="{{url('assets/admin/img/cross.png')}}" class="cross">
                                          @endif
                                       </td>
                                       <td>
                                          @if($remote_status ==1)
                                             <img src="{{url('assets/admin/img/tick.png')}}" class="tick">
                                          @else
                                          <img src="{{url('assets/admin/img/cross.png')}}" class="cross">
                                          @endif
									            </td>
                                    </tr>
                                 @endif
                              @endforeach
                           </tbody>
                        </table>

                     </div>
                     

                     <div class="form-group row">
                              <label  class="col-sm-2 col-5 col-form-label mt-3">
                                 <label class="infobl">receive device call: </br> (For door open access) </label>
                              </label>
                              <div class="col-sm-2 col-7 col-form-label mt-3 pl-0">
                              <label class="infobl">  {{($record['receive_call']==1)?"Yes":"No"}}  </label>
                              </div>
                           </div>
                  @endforeach
               </div>
         @endif

         @if(!empty($userlists))
            @php  $count =count($PurchaserUnits);@endphp
               <div class="devicehead">
                  <h2>System Access</h2> <a href="{{url("opslogin/user/useraccess/$UserMoreInfoObj->id")}}"><button type="submit" class="submitwidth float-right">Edit System Access </button></a>
                  @foreach($userlists as $k => $record)
                     @php $record_id = $record['id'];  @endphp
                    
                        <h3>{{$record['building']}}, {{\Crypt::decryptString($record['unit'])}}</h3>
                   
                     <div class="overflowscroll">
                        <table class="table usertable1">
                           <thead>
                              <tr>
                                 @foreach($modules as $module)
                                    <th>{{$module->name}}</th>
                                 @endforeach
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 @foreach($modules as $module)
                                    @php
                                       $view =false;
                                       $array_exist=false;
                                       if(isset($record['access'][$module->id])) {
                                          $array_exist = true;
                                       if($record['access'][$module->id] ==1)
                                          $view=1;
                                       }
                                    @endphp 
                                       <td> 
                                          <div class="form-group">
                                             <label class="containeruser">
                                                <input type="checkbox"   name="mod_{{$module->id}}_pid_{{$record['id']}}"  value="1" class ='form-check-input aclass{{$k+1}} viewCheckBox' {{ (isset($view) && $view  ==1) ?'checked':'' }}>
                                                <span class="checkmarkuser"></span>
                                             </label>
                                          </div>
                                       </td>
                                 @endforeach
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  @endforeach
               </div>
         @endif
      </div>
   </div>
</section>


@stop

