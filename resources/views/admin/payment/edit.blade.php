
@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
@endphp

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $eform =  $permission->check_menu_permission(39,$permission->role_id,1);
   $payment =  $permission->check_menu_permission(46,$permission->role_id,1);
   $holiday =  $permission->check_menu_permission(53,$permission->role_id,1);
  
   $permission = $permission->check_permission(46,$permission->role_id); 
@endphp

 <div class="status">
    <h1>payment settings- update  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($eform->view) && $eform->view==1 )
                        <li><a href="{{url('/opslogin/configuration/eform_setting#eformsettings')}}">E-Form Settings </a></li>
                     @endif

                     @if(isset($payment->view) && $payment->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/payment_setting#paymentsettings')}}">Payment Settings </a></li>
                     @endif

                     @if(isset($holiday->view) && $holiday->view==1 )
                        <li   ><a href="{{url('/opslogin/configuration/holiday_setting#holidayssettings')}}">Public Holidays Settings  </a></li>
                     @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


                {!! Form::model($PaymentObj,['method' =>'PATCH','class'=>'forunit','onSubmit'=>'return validateThisFrom()','files' => true,'url' => url('opslogin/configuration/payment_setting/'.$PaymentObj->id)]) !!}
  <div class="row asignbg editbg">
                    <div class="col-lg-12 checkcolor">
                        <h3><input type="checkbox" name="terms1" id="terms1" value="1" {{($PaymentObj->cheque_payable_to !='')?"checked":""}}/>CHEQUE PAYMENT</h3>
                    </div>
                    <div class="col-lg-9">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>payable to :</label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('cheque_payable_to', null, ['class'=>'form-control','id'=>'payable_to',  'rows'=>3,'required' => false,'placeholder' => 'Enter cheque payment information']) }}
                        
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 mt-2 checkcolor">
                      <h3><input type="checkbox" name="terms2" id="terms2" value="1" {{($PaymentObj->account_holder_name !='')?"checked":""}}/>bank transfer payment</h3>
                    </div>
                          <div class="col-lg-9">
                            <div class="form-group row">
                            <label  class="col-sm-4 col-form-label">name of account holder : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('account_holder_name', NULL, ['class'=>'form-control','id'=>'account_holder_name','placeholder' => 'Enter account holder name']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">account number : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('account_number', NULL, ['class'=>'form-control','id'=>'account_number',  'placeholder' => 'Enter Account Number']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">account type : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('account_type', NULL, ['class'=>'form-control', 'id'=>'account_type', 'placeholder' => 'Enter Account Type']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">name of bank : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('bank_name', NULL, ['class'=>'form-control', 'id'=>'bank_name','placeholder' => 'Enter Name of Bank']) }}
                              </div>
                           </div>
                           
                           <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">address of bank : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('bank_address', NULL, ['class'=>'form-control', 'id'=>'bank_address',  'placeholder' => 'Enter Address of Bank']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                           <label  class="col-sm-4 col-form-label">bank swift code : 
                              </label>
                              <div class="col-sm-8">
                                 {{ Form::text('swift_code', NULL, ['class'=>'form-control', 'id'=>'swift_code', 'placeholder' => 'Enter Bank swift code']) }}
                              </div>
                           </div>
                          </div>

                    <div class="col-lg-12 mt-2 checkcolor">
                      <h3><input type="checkbox" name="terms3" id="terms3" value="1" {{($PaymentObj->cash_payment_info !='')?"checked":""}}/>cash payment</h3>
                    </div>
                    <div class="col-lg-9">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>information :</label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('cash_payment_info', null, ['class'=>'form-control','id'=>'cash_payment_info',  'rows'=>3,'placeholder' => 'Enter cash payment information']) }}
                        </div>
                      </div>
                    </div>
                    @if(isset($permission) && $permission->edit==1 )
                        <div class="col-lg-12">
                           <div class="form-group ">
                              <button type="submit" class="submit mt-2 float-right">submit</button>     
                           </div>
                        </div>
                     @endif
                    
                   

                  </div>
               
                       
            
                     <!--div class="row">
                        <div class="col-lg-9">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div-->
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
<script type="text/javascript">
function validateThisFrom(){
   if($('input[type=checkbox]:checked').length == 0)
   {
      alert ( "Please select at least one payment method" );
      return false;
   }

   if( $("#terms1").prop('checked')){
      if($("#payable_to").val() == "" )
      {
         alert( "Cheque payable to informtion should not be empty!" );
         return false;
      }   
   }
   if( $("#terms2").prop('checked')){
      if($("#account_holder_name").val() == "" )
      {
         alert( "Account holder name should not be empty!" );
         return false;
      }
      if($("#account_number").val() == "" )
      {
         alert( "Account number should not be empty!" );
         return false;
      }
      if($("#account_type").val() == "" )
      {
         alert( "Account type should not be empty!" );
         return false;
      }   
      if($("#bank_name").val() == "" )
      {
         alert( "Bank name should not be empty!" );
         return false;
      }
      if($("#bank_address").val() == "" )
      {
         alert( "Bank address should not be empty!" );
         return false;
      }
      if($("#swift_code").val() == "" )
      {
         alert( "Swift code should not be empty!" );
         return false;
      }

   }
   if( $("#terms3").prop('checked')){
      if($("#cash_payment_info").val() == "" )
      {
         alert( "Cash payment informtion should not be empty!" );
         return false;
      }   
   }
   return true;
}

</script>
@stop



