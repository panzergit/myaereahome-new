@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>renovation : payment</h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
                     <li><a href="{{url('/opslogin/eform/regvehicle#ef')}}"> Vehicle IU </a></li>
                     <li><a href="{{url('/opslogin/eform/changeaddress#ef')}}"> Mailing Address </a></li>
                     <li><a href="{{url('/opslogin/eform/particular#ef')}}">Particulars </a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
      <!--div class="show">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div-->
       <div class="">
            <div class=" ">
			<div class="col-lg-12 asignFace">
                  <h2>official use only</h2>
               </div>
              
                  
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/eform/renovation/paymentsave/'.$eformObj->id)]) !!}
				 <div class="row asignbg">
                 <div class="col-lg-6">
                 <div class="col-lg-12 p-0 mb-4">
                  <h3>deposit payment</h3>
               </div>
                 <div class="col-lg-">
                      <div class="form-group row">
                        <label  class="col-sm-4 col-12 col-form-label">
                          <label>payment received by: 
                          </label>
                        </label>
                        <div class="col-sm-6 col-12">
                        {{ Form::select('payment_option', [''=>'SELECT OPTION','1'=>'CHEQUE','2'=>'BANK TRANSFER',3=>"CASH"], isset($eformObj->payment->payment_option)?$eformObj->payment->payment_option:'', ['class'=>'form-control','id'=>'payment_option', 'onchange'=>'getfields()']) }}

                        </div>      
                        </div>
                       
                      </div>
                           <div class="col-lg-" id="cheque" style='display:{{(isset($eformObj->payment) && $eformObj->payment->payment_option==1)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Cheque Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque amount : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('cheque_amount', isset($eformObj->payment->cheque_amount)?$eformObj->payment->cheque_amount:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque number : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('cheque_no', isset($eformObj->payment->cheque_no)?$eformObj->payment->cheque_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                 <div id="sandbox3">
                                 {{ Form::text('cheque_received_date',  (isset($eformObj->payment->cheque_received_date) && $eformObj->payment->cheque_received_date !='0000-00-00')?$eformObj->payment->cheque_received_date:'', ['id'=>'datetext1','class'=>'form-control','placeholder' => '']) }}
                                 </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">bank : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('cheque_bank', isset($eformObj->payment->cheque_bank)?$eformObj->payment->cheque_bank:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-" id="bt" style='display:{{(isset($eformObj->payment) && $eformObj->payment->payment_option==2)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Bank Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                 <div id="sandbox">
                                    {{ Form::text('bt_received_date', (isset($eformObj->payment->bt_received_date) && $eformObj->payment->bt_received_date !='0000-00-00')?$eformObj->payment->bt_received_date:'', ['id'=>'fromdate', 'class'=>'form-control','placeholder' => '']) }}
                                 </div></div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('bt_amount_received', isset($eformObj->payment->bt_amount_received)?$eformObj->payment->bt_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              
                           </div>
                           <div class="col-lg-" id="cash" style='display:{{(isset($eformObj->payment) &&$eformObj->payment->payment_option==3)?"block":"none"}}'>
                           <div class="form-group row" >
                              <div class="col-lg-12 ">
                                       <h3>Cash Payment Information</h3>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-4 col-form-label">amount received : 
                              </label>
                              <div class="col-sm-6 col-8">
                                 {{ Form::text('cash_amount_received', isset($eformObj->payment->cash_amount_received)?$eformObj->payment->cash_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-4 col-form-label">date received : 
                              </label>
                              <div class="col-sm-6 col-8">
                              <div id="sandbox4">
                                 {{ Form::text('cash_received_date', (isset($eformObj->payment->cash_received_date) && $eformObj->payment->cash_received_date !='0000-00-00')?$eformObj->payment->cash_received_date:'', ['id'=>'datetext2', 'class'=>'form-control','placeholder' => '']) }}
                              </div></div>
                           </div>
                           
                        </div>

                        <div class="col-lg-" id="receipt" style='display:{{(isset($eformObj->payment) &&$eformObj->payment->receipt_no!="")?"block":"none"}}'>
                           <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">receipt number : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('receipt_no', isset($eformObj->payment->receipt_no)?$eformObj->payment->receipt_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           </div>
                           @if(isset($eformObj->payment->signature))
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">name of management received : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('manager_received', isset($eformObj->payment->manager_received)?$eformObj->payment->manager_received:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                                 </div>
                              </div>
                           
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">signature of management : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <img src="data:image/png;base64, {{$eformObj->payment->signature}}" class="viewsig"/>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date of singnature : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('date_of_signature', isset($eformObj->payment->date_of_signature)?$eformObj->payment->date_of_signature:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                                 </div>
                              
                              </div>
                             
                           @endif
						   </div>
                              <!-- 2 -->
                              <div class="col-lg-6">
							   <div class="col-lg-12 p-0 mb-4">
                  <h3>lift padding payment</h3>
               </div>
                 <div class="col-lg-">
                      <div class="form-group row mb-3">
                        <label  class="col-sm-4 col-12 col-form-label">
                          <label>payment received by: 
                          </label>
                        </label>
                        <div class="col-sm-6 col-12">
                        {{ Form::select('lift_payment_option', [''=>'SELECT OPTION','1'=>'CHEQUE','2'=>'BANK TRANSFER',3=>"CASH"], isset($eformObj->payment->lift_payment_option)?$eformObj->payment->lift_payment_option:'', ['class'=>'form-control','id'=>'lift_payment_option', 'onchange'=>'getliftfields()']) }}

                              </div>

                        </div> 
                     
                      </div>
                      <div class="col-lg-" id="lift_cheque" style='display:{{(isset($eformObj->payment) && $eformObj->payment->lift_payment_option==1)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Cheque Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque amount : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_cheque_amount', isset($eformObj->payment->lift_cheque_amount)?$eformObj->payment->lift_cheque_amount:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">cheque number : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_cheque_no', isset($eformObj->payment->lift_cheque_no)?$eformObj->payment->lift_cheque_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                 <div id="sandbox5">
                                 {{ Form::text('lift_cheque_received_date', (isset($eformObj->payment->lift_cheque_received_date) && $eformObj->payment->lift_cheque_received_date !='0000-00-00')?$eformObj->payment->lift_cheque_received_date:'', ['id'=>'datetext5','class'=>'form-control','placeholder' => '']) }}
                                 </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">bank : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_cheque_bank', isset($eformObj->payment->lift_cheque_bank)?$eformObj->payment->lift_cheque_bank:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>

                           </div>
                           <div class="col-lg-" id="lift_bt" style='display:{{(isset($eformObj->payment) && $eformObj->payment->lift_payment_option==2)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Bank Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <div id="sandbox6">
                                    {{ Form::text('lift_bt_received_date', (isset($eformObj->payment->lift_bt_received_date) && $eformObj->payment->lift_bt_received_date !='0000-00-00')?$eformObj->payment->lift_bt_received_date:'', ['id'=>'datetext6','class'=>'form-control','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_bt_amount_received', isset($eformObj->payment->lift_bt_amount_received)?$eformObj->payment->lift_bt_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              
                           </div>
                           <div class="col-lg-" id="lift_cash" style='display:{{(isset($eformObj->payment) &&$eformObj->payment->lift_payment_option==3)?"block":"none"}}'>
                           <div class="form-group row" >
                              <div class="col-lg-12 ">
                                       <h3>Cash Payment Information</h3>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-4 col-form-label">amount received : 
                              </label>
                              <div class="col-sm-6 col-8">
                                 {{ Form::text('lift_cash_amount_received', isset($eformObj->payment->lift_cash_amount_received)?$eformObj->payment->lift_cash_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-4 col-form-label">date received : 
                              </label>
                              <div class="col-sm-6 col-8">
                                 <div id="sandbox7">
                                 {{ Form::text('lift_cash_received_date', (isset($eformObj->payment->lift_cash_received_date) && $eformObj->payment->lift_cash_received_date !='0000-00-00')?$eformObj->payment->lift_cash_received_date:'', ['id'=>'datetext7','class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           </div>
                           
                        </div>

                        <div class="col-lg-" id="lift_receipt" style='display:{{(isset($eformObj->payment->lift_payment_option) &&$eformObj->payment->receipt_no!="")?"block":"none"}}'>
                           <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">receipt number : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_receipt_no', isset($eformObj->payment->lift_receipt_no)?$eformObj->payment->lift_receipt_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           </div>
                           @if(isset($eformObj->payment->lift_signature))
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">name of management received : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_manager_received', isset($eformObj->payment->lift_manager_received)?$eformObj->payment->lift_manager_received:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                                 </div>
                              </div>
                           
                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">signature of management : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    <a href="#" target="_blank"><img src="data:image/png;base64, {{$eformObj->payment->lift_signature}}" class="viewsig"/></a>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label  class="col-sm-4 col-4 col-form-label">date of signature : 
                                 </label>
                                 <div class="col-sm-6 col-8">
                                    {{ Form::text('lift_date_of_signature', isset($eformObj->payment->lift_date_of_signature)?$eformObj->payment->lift_date_of_signature:'', ['class'=>'form-control','required' => true,'placeholder' => '']) }}
                                 </div>
                              </div>
                              
                           @endif
						    </div>
							  <!-- 2 -->
							    <div class="col-lg-12" id="bt">
                           <div class="form-group row">
                            
                              <div class="col-sm-11 col-12 mt-2">
                                 <input type="hidden" name="payment_id" value="{{isset($eformObj->payment->id)?$eformObj->payment->id:''}}">
                                 <button type="submit" class="submit  2 ml-2  float-right">update</button>
                                 <a href="{{url("/opslogin/eform/renovation")}}"  class="submit  float-right">cancel</a>
                              </div>
                           </div>   
                        </div>
                        </div>
                        </div>
                      
                        						
                     </div>
                  

                    

                    {!! Form::close() !!}
               
               
            </div>
         </div>


</section>
 <script type="text/javascript">


      function getfields(){
         if($("#payment_option").val() ==1){
            $("#cheque").show(); 
            $("#receipt").show()
            $("#cash").hide(); 
            $("#bt").hide(); 
         }
         else if($("#payment_option").val() ==2){
            $("#bt").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#cash").hide(); 

         }
         else if($("#payment_option").val() ==3){
            $("#cash").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#bt").hide(); 
         }
         else {
            $("#cash").hide(); 
            $("#receipt").hide()
            $("#cheque").hide(); 
            $("#bt").hide(); 
         }
      }
      function getliftfields(){
         //alert($("#lift_payment_option").val())

         if($("#lift_payment_option").val() ==1){
            $("#lift_cheque").show(); 
            $("#lift_receipt").show()
            $("#lift_cash").hide(); 
            $("#lift_bt").hide(); 
         }
         else if($("#lift_payment_option").val() ==2){
            $("#lift_bt").show(); 
            $("#lift_receipt").show()
            $("#lift_cheque").hide(); 
            $("#lift_cash").hide(); 

         }
         else if($("#lift_payment_option").val() ==3){
            $("#lift_cash").show(); 
            $("#lift_receipt").show()
            $("#lift_cheque").hide(); 
            $("#lift_bt").hide(); 
         }
         else {
            $("#lift_cash").hide(); 
            $("#lift_receipt").hide()
            $("#lift_cheque").hide(); 
            $("#lift_bt").hide(); 
         }
      }
    </script>
@stop


