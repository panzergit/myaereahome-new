
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
  
   $permission = $permission->check_permission(53,$permission->role_id); 
@endphp

 <div class="status">
    <h1>holiday settings- update  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     @if(isset($eform->view) && $eform->view==1 )
                        <li><a href="{{url('/opslogin/configuration/eform_setting#eformsettings')}}">E-Form Settings </a></li>
                     @endif

                     @if(isset($payment->view) && $payment->view==1 )
                        <li><a href="{{url('/opslogin/configuration/payment_setting#paymentsettings')}}">Payment Settings </a></li>
                     @endif

                     @if(isset($holiday->view) && $holiday->view==1 )
                        <li   class="activeul" ><a href="{{url('/opslogin/configuration/holiday_setting#holidayssettings')}}">Public Holidays Settings  </a></li>
                     @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

    

                {!! Form::model($HolidayObj,['method' =>'PATCH','class'=>'forunit','files' => true,'url' => url('opslogin/configuration/holiday_setting/'.$HolidayObj->id)]) !!}
 <div class="row asignbg editbg">
                <div class="col-lg-9">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-form-label">
                          <label>public holidays : <p class="pcp">
                          (YYYY-MM-DD) <br>
Separate the dates with comma, <br>
example 2021-10-01,2021-10-31</p></label>
                        </label>
                        <div class="col-sm-8">
                        {{ Form::textarea('public_holidays', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'yyyy-mm-dd']) }}
                        <span class="resp">  (YYYY-MM-DD) Separate the dates with comma, example 2021-10-01,2021-10-31 </span>
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



