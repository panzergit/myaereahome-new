@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>door access card : payment</h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
                     <li  class="activeul"><a href="{{url('/opslogin/eform/dooraccess#ef')}}">Door Access Card </a></li>
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
           
           
			   <div class="col-lg-12 asignFace">
                  <h2>official use only</h2>
               </div>
              
               
                  
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/eform/dooraccess/paymentsave/'.$eformObj->id)]) !!}
				  <div class="asignbg">
				  <div class="col-lg-12 ">
                  <h3>deposit payment</h3>
               </div>
                 <div class="col-lg-12">
                      <div class="form-group row">
                        <label  class="col-sm-3 col-12 col-form-label">
                          <label>payment received by: 
                          </label>
                        </label>
                        <div class="col-sm-4 col-12">
                        {{ Form::select('payment_option', [''=>'SELECT OPTION','1'=>'CHEQUE','2'=>'BANK TRANSFER',3=>"CASH"], isset($eformObj->payment->payment_option)?$eformObj->payment->payment_option:'', ['class'=>'form-control','id'=>'payment_option', 'onchange'=>'getfields()']) }}

                        </div>      
                        </div>
                      </div>
                           <div class="col-lg-12" id="cheque" style='display:{{(isset($eformObj->payment) && $eformObj->payment->payment_option==1)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Cheque Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">cheque amount : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    {{ Form::text('cheque_amount', isset($eformObj->payment->cheque_amount)?$eformObj->payment->cheque_amount:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">cheque number : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    {{ Form::text('cheque_no', isset($eformObj->payment->cheque_no)?$eformObj->payment->cheque_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                 <div id="sandbox3">
                                 {{ Form::text('cheque_received_date', isset($eformObj->payment->cheque_received_date)?$eformObj->payment->cheque_received_date:'', ['id'=>'datetext1','class'=>'form-control','placeholder' => '']) }}
                                 </div>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">bank : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    {{ Form::text('cheque_bank', isset($eformObj->payment->cheque_bank)?$eformObj->payment->cheque_bank:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-12" id="bt" style='display:{{(isset($eformObj->payment) && $eformObj->payment->payment_option==2)?"block":"none"}}'>
                              <div class="form-group row" >
                                 <div class="col-lg-12 ">
                                          <h3>Bank Information</h3>
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">date received: 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                 <div id="sandbox">
                                    {{ Form::text('bt_received_date', isset($eformObj->payment->bt_received_date)?$eformObj->payment->bt_received_date:'', ['id'=>'fromdate', 'class'=>'form-control','placeholder' => '']) }}
                                 </div> </div>
                              </div>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">amount received : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    {{ Form::text('bt_amount_received', isset($eformObj->payment->bt_amount_received)?$eformObj->payment->bt_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                              
                           </div>
                           <div class="col-lg-12" id="cash" style='display:{{(isset($eformObj->payment) &&$eformObj->payment->payment_option==3)?"block":"none"}}'>
                           <div class="form-group row" >
                              <div class="col-lg-12 ">
                                       <h3>Cash Payment Information</h3>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">amount received : 
                              </label>
                              <div class="col-sm-4 col-8">
                                 {{ Form::text('cash_amount_received', isset($eformObj->payment->cash_amount_received)?$eformObj->payment->cash_amount_received:'', ['class'=>'form-control','placeholder' => '']) }}
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-3 col-4 col-form-label">date received : 
                              </label>
                              <div class="col-sm-4 col-8">
                                 <div id="sandbox">
                                    {{ Form::text('cash_received_date', isset($eformObj->payment->cash_received_date)?$eformObj->payment->cash_received_date:'', ['id'=>'datetext', 'class'=>'form-control','placeholder' => '']) }}
                                 </div> 
                              </div>
                           </div>
                           
                        </div>

                        <div class="col-lg-12" id="receipt" style='display:{{(isset($eformObj->payment) &&$eformObj->payment->receipt_no!="")?"block":"none"}}'>
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">receipt number : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    {{ Form::text('receipt_no', isset($eformObj->payment->receipt_no)?$eformObj->payment->receipt_no:'', ['class'=>'form-control','placeholder' => '']) }}
                                 </div>
                              </div>
                           @if(isset($eformObj->payment->signature))
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">name of management received : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    <div id="sandbox2">
                                    {{ Form::text('manager_received', isset($eformObj->payment->manager_received)?$eformObj->payment->manager_received:'', ['id'=>'todate','class'=>'form-control','required' => true,'placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                           
                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">signature of management : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    <a href="#" target="_blank"><img src="data:image/png;base64, {{$eformObj->payment->signature}}" class="viewsig"/></a>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label  class="col-sm-3 col-4 col-form-label">date of singnature : 
                                 </label>
                                 <div class="col-sm-4 col-8">
                                    <div id="sandbox4">
                                       {{ Form::text('date_of_signature', isset($eformObj->payment->date_of_signature)?$eformObj->payment->date_of_signature:'', ['class'=>'form-control','required' => true,'id' =>'datetext2','placeholder' => '']) }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                           @endif
                          
                 

                       
                        <div class="row" id="bt">
                        <div class="col-lg-12">
						    <input type="hidden" name="payment_id" value="{{isset($eformObj->payment->id)?$eformObj->payment->id:''}}">
                                 <button type="submit" class="submit mr-2 float-left">update</button>
                                 <a href="{{url("/opslogin/eform/moveinout")}}"  class="Delete  float-left">cancel</a>
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
    </script>
@stop


