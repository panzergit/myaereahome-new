@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>eform settings - update </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li  class="activeul"><a href="{{url('/opslogin/configuration/eform_setting#eformsettings')}}">E-Form Settings </a></li>
                     <li ><a href="{{url('/opslogin/configuration/payment_setting#paymentsettings')}}">Payment Settings </a></li>
                     <li ><a href="{{url('/opslogin/configuration/holiday_setting#holidayssettings')}}">Public Holidays Settings  </a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
            {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/configuration/eform_setting/'.$eformObj->id)]) !!}

               <div class="row asignbg editbg">

                  @if(@Auth::user()->role_id ==1)
                     <div class="col-lg-8">
                        <div class="form-group row">
                           <label  class="col-sm-4  col-form-label">
                              <label>Property:</label>
                           </label>
                           <div class="col-sm-5">
                              {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                        </div>
                     </div>
                  @endif
                 <div class="col-lg-8">
                     <div class="form-group row">
                        <label  class="col-sm-4 col-3 col-form-label">eform type:</label>
                        <div class="col-sm-5 col-9">
                        {{ Form::select('eform_type', ['' => '--Select Form--'] + $eforms, $eformObj->eform_type, ['class'=>'form-control','required' => true,'id'=>'formtype','onchange'=>'getrenofields()']) }}
 </div>
                     </div>
                  </div>

                  <div class="col-lg-8">
                        <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">
                           <label>general information: 
                           </label>
                           </label>
                           <div class="col-sm-8">
                           {{ Form::textarea('general_info', null, ['class'=>'form-control','rows'=>3,'required' => false]) }}
                           </div>
                           </div>
                        </div>
                        <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>refund amount: 
                          </label>
                        </label>
                        <div class="col-sm-3">
                        {{ Form::text('refund_amount', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Refund Amount']) }}                              
                        </div>      
                        </div>
                      </div>
                      <div class="col-lg-8" id="padding_fee" style='display:{{(isset($eformObj->eform_type) && ($eformObj->eform_type==40 || $eformObj->eform_type==41))?"block":"none"}}'>
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>padding lift fee: 
                          </label>
                        </label>
                        <div class="col-sm-4">
                        {{ Form::text('padding_lift_fee', null, ['class'=>'form-control','placeholder' => 'Enter Padding Lift Fee']) }}                              
                        </div>      
                        </div>
                      </div>
                      <!--<div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>PAYABLE TO: 
                          </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::text('payable_to', null, ['class'=>'form-control','required' => true,'placeholder' => 'Payable To']) }}                              
                        </div>      
                        </div>
                      </div> -->

                      <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>payment mode available: 
                          </label>
                        </label>
                        <!--div class="col-sm-8">
                        <div class="form-group checkbook row">
                        {{ Form::checkbox('payment_mode_cheque', '1', null,['class'=>'form-control']) }}<label>CHEQUE</label>
                       </div>
                       <div class="form-group checkbook row">
                        {{ Form::checkbox('payment_mode_bank', '1',null,['class'=>'form-control']) }}<label>BANK TRANSFER</label>
                        </div>            
                        <div class="form-group checkbook row">
                        {{ Form::checkbox('payment_mode_cash', '1',null,['class'=>'form-control']) }}<label>CASH</label>    
                        </div>
                        </div--> 
                        <div class="col-sm-2 col-4">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('payment_mode_cheque', '1', null,['class'=>'form-control']) }}<label>cheque</label>
                       </div>
                        </div>
                        <div class="col-sm-3 col-5">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('payment_mode_bank', '1',null,['class'=>'form-control']) }}<label>bank transfer</label>
                        </div> 
                        </div>
                        <div class="col-sm-2 col-3">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('payment_mode_cash', '1',null,['class'=>'form-control']) }}<label>cash</label>    
                        </div>
                        </div>     
                        </div>
                      </div>
                      
                    <div class="col-lg-8" id="renovation" style='display:{{(isset($eformObj->eform_type) && $eformObj->eform_type==41)?"block":"none"}}'>
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>no. of hacking days allow: 
                          </label>
                        </label>
                        <div class="col-sm-3">
                        {{ Form::text('hacking_work_permitted_days', null, ['class'=>'form-control','placeholder' => '']) }}                              
                        </div>      
                        </div>
                 
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>hacking work not allow on: 
                          </label>
                        </label>
                        <!--div class="col-sm-8">
                        <div class="form-group checkbook row">
                        {{ Form::checkbox('hacking_work_not_permitted_saturday', '1', null,['class'=>'form-control']) }}<label>SATURDAY</label>
                       </div>
                       <div class="form-group checkbook row">
                        {{ Form::checkbox('hacking_work_not_permitted_sunday', '1',null,['class'=>'form-control']) }}<label>SUNDAY</label>
                        </div>            
                        <div class="form-group checkbook row">
                        {{ Form::checkbox('hacking_work_not_permitted_holiday', '1',null,['class'=>'form-control']) }}<label>PUBLIC HOLIDAYS</label>    
                        </div>
                        </div--> 
                        <div class="col-sm-3 col-4">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('hacking_work_not_permitted_saturday', '1', null,['class'=>'form-control']) }}<label>saturday</label>
                       </div>
                        </div>  
                        <div class="col-sm-2 col-5">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('hacking_work_not_permitted_sunday', '1',null,['class'=>'form-control']) }}<label>sunday</label>
                        </div>
                        </div>  
                        <div class="col-sm-3 col-3">
                        <div class="form-group checkbook paycheck row">
                        {{ Form::checkbox('hacking_work_not_permitted_holiday', '1',null,['class'=>'form-control']) }}<label>public holidays</label>    
                        </div>
                        </div>     
                        </div>
                     
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>hacking work timing: 
                          </label>
                        </label>
                        <div class="col-sm-3">
                        {{ Form::text('hacking_work_start_time', null, ['class'=>'form-control','placeholder' => '']) }}                              
                        </div> 
                        <div class="col-sm-1"><label>To 
                          </label>                           
                        </div>  
                        <div class="col-sm-4">
                        {{ Form::text('hacking_work_end_time', null, ['class'=>'form-control','placeholder' => '']) }}                              
                        </div>      
                        </div>
                      </div>

                      <div class="col-lg-8">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>official notes: 
                          </label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('official_notes', null, ['class'=>'form-control','rows'=>3,'required' => false]) }}
                             
                        </div>
                      </div>
                    </div>
               </div>
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    
                    
            {!! Form::close() !!}
         </div>   
         

</section>
<script type="text/javascript">

     

function getrenofields(){
   if($("#formtype").val() ==41){
      $("#renovation").show(); 
      $("#padding_fee").show();
   }
   else if($("#formtype").val() ==40){
      $("#renovation").hide(); 
      $("#padding_fee").show();
   }
   else{
      $("#renovation").hide(); 
      $("#padding_fee").hide();
   }
}
    </script>
@stop


