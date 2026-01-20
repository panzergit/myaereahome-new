@extends('layouts.adminnew')




@section('content')
<style>
.col12width{        flex: 0 0 103%;
        max-width: 103%;     position: relative;
    top: -65px;     height: 0px;
}
.zfacilty{    z-index: -999;}
</style>
 <style>
	  .moreselect1 {
  display: none;
}
.dropdown-menu{    left: -10px!important;}
.selected span {
    color: #4CAF50!important;
}
.bootstrap-select .dropdown-menu li a span.text {
    display: inline-block;
    color: #000!important;
}
.bootstrap-select button{    padding: 0px 10px !important; background: transparent; border: transparent;}
.bootstrap-select button:focus{    padding: 0px 10px !important; background: transparent; border: transparent; transition:none!important;}
.bootstrap-select button:hover{    padding: 0px 10px !important; background: transparent; border: transparent; transition:none!important;}
.bootstrap-select .dropdown-toggle .filter-option-inner-inner {
    color: #767d85;
}
.bootstrap-select.form-control {
       height: 34px;
    line-height: 34px;
    margin-bottom: 0px;
    background: #D0D0D0 0% 0% no-repeat padding-box;
    border-radius: 34px
}
.btn-light:not(:disabled):not(.disabled).active, .btn-light:not(:disabled):not(.disabled):active, .show>.btn-light.dropdown-toggle{padding: 0px 10px !important; background: transparent; border: transparent;}
.selectpicker{display:none}
	.abselect{position: absolute;
    top: 25px;
    width: 89%;
    display: none;     left: 15px;}
	.bootstrap-select .dropdown-toggle .filter-option-inner-inner {
    color: #9fa4a9;
    font-weight: 600;
    font-size: 14px;
}
.bootstrap-select button:hover {   padding: 0px 0px !important;
    background: transparent;
    border: transparent;
    margin-top: -5px;}
.bootstrap-select button {
    padding: 0px 0px !important;
    background: transparent;
    border: transparent;
    margin-top: -5px;
}
      </style>
 <div class="status">
    <h1>manage device - update </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/device')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/device/create')}}">Add device</a></li>
                  </ul>
               </div>
               </div>

       <div class="">
                 {!! Form::model($DeviceObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/device/'.$DeviceObj->id)]) !!}

                  <div class="row asignbg editbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-12">
                           <div class="form-group">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                <div class="col-lg-3">
                           <div class="form-group ">
                              <label>device name :</label>
                                {{ Form::text('device_name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Device Name']) }}
                           </div>
						   </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label>serial no :</label>
                              {{ Form::text('device_serial_no', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Device Serial Number']) }}
                           </div>
                           </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
                              <label>Device Type :</label>
                              {{ Form::select('device_type', ['' => '--Select Type--','1'=>'Ordinary Door Machine','2'=>'Shared Building Entrance Access'], null, ['class'=>'form-control wauto','required' => true,'id'=>'device_type','onChange'=>'SelectLocationType()']) }}  
                           </div>
						    
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label>location :</label>
                              @php 
                                 if($DeviceObj->device_type ==2){
                                    $locations_display = "display:block";
                                    $location_display = "display:none";
                                 }else{
                                    $locations_display = "display:none";
                                    $location_display = "display:block";
                                 }
                                 $locations = explode(",",$DeviceObj->locations);
                              @endphp
                              {{ Form::select('locations[]', $buildings, $locations, ['class'=>'form-control wauto selectpicker','required' => 'required','id'=>'locations','title'=>'--Select Location--','multiple'=>'multiple', 'style'=>$locations_display]) }}  
                              {{ Form::select('location', ['' => '--Select Location--'] + $buildings, $DeviceObj->locations, ['class'=>'form-control wauto abselect','required' => 'required','id'=>'location', 'style'=>$location_display]) }}  
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label>proximity setting :</label>
                              {{ Form::select('proximity_setting', ['1' => 'ON','0'=>'OFF'], null, ['class'=>'form-control wauto','required' => true]) }}
                           </div>
                           </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
                              <label>Facility Location:(optional)</label>
                              {{ Form::select('facility_type', ['' => '--Select Facility--'] + $facilities, null, ['class'=>'form-control','id'=>'facility','onchange'=>'getFacilityfields()']) }}  
                           </div>
                           </div>
                           @php 
                           $facility_div_display = "display:none";
                           if(old('facility_type') >0 || $DeviceObj->facility_type >0)
                              $facility_div_display = "display:flex";
                           @endphp 
						   <div class="clearfix"></div>
                           <div id="facility_fields" style="{{$facility_div_display}}" class="col-lg-12 row ">
                    
                                 
                                 <div class="col-lg-3">
                                    <div class="form-group ">
                                       <label>Accessibility Start Time:</label>
                                       {{ Form::select('start_time', ['' => '--Start Time--'] + $time_values, null, ['class'=>'form-control','id'=>'start_time']) }}  
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group pl-2">
                                       <label>accessibility End Time:</label>
                                       {{ Form::select('end_time', ['' => '--End Time--'] + $time_values, null, ['class'=>'form-control','id'=>'faciend_timelity']) }}  
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group pl-3">
                                       <label>Accessibility in Advance:(hr)</label>
                                       {{ Form::select('entry_allowed_in_advance', ['' => '--Select--'] + $advance_entry, null, ['class'=>'form-control','id'=>'entry_allowed_in_advance']) }}  
                                    </div>
                                 </div>
                            
                           </div>
                           <!--<div class="form-group row">
                              <label  class="col-sm-4 col-form-label">STATUS:</label>
                              <div class="col-sm-5">
                              {{ Form::select('status', ['1' => 'Active','2'=>'Inactive','3'=>'Faulty'], null, ['class'=>'form-control','id'=>'status']) }}
                              </div>
                           </div> -->
                
                
               
                     </div>


                     
                       
            
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


