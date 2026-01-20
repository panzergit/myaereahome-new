@extends('layouts.adminnew')

@section('content')
<style>
.forunit h3 {font-style: italic;}
.forunit h2 {
    font-size: 18px;
    font-weight: 900;
    color: #fff;
	    margin-bottom: -15px;
}
.col-lg-12 {
    color: #000;
}
</style>
 <div class="status">
    <h1>manage system access :{{Crypt::decryptString($UserMoreInfoObj->first_name)}} {{Crypt::decryptString($UserMoreInfoObj->last_name)}}</h1>
  </div>
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
       <div>
            <div class="devicehead">
                   <h2>Assign System Access</h2>
            {!! Form::model($UserObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/user/assignaccess/'.$UserObj->id)]) !!}

                  @if(!empty($userlists))  
                     
                     @foreach($userlists as $k => $record)
                     
                        <h3>{{$record['building']}}, {{Crypt::decryptString($record['unit'])}}</h3>
               
					 <label class="containeruser1">
              <input type="checkbox" class="aid{{$k+1}}" name="device_13" value="1">Check All
               <span class="checkmarkuser1"></span>
               </label>
                        <div class="">
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
                                          <div class="">
										   <label class="containeruser1">
             <input type="checkbox"   name="mod_{{$module->id}}_pid_{{$record['id']}}"  value="1" class ='form-check-input aclass{{$k+1}} viewCheckBox' {{ (isset($view) && $view  ==1) ?'checked':'' }}>
               <span class="checkmarkuser1"></span>
               </label>
                                          
                                          <label class="form-check-label" for="exampleCheck1" ></label>
                                          </div>
                                       </td>
                                    @endforeach
                                 </tr>
                              </tbody>
                           </table>	
                        </div>
                        </div>
                        
                     @endforeach
					 <div class="row">
                     <div class="col-lg-12 mt-3">
					  <a href="{{url($return_url)}}"  class="submit mt-0 float-left " style="width:290px;"> return to summary</a>
                                       <input type="hidden" name="user_id" value ="{{$UserObj->id}}">
                                       <button type="submit" class="submit  float-right">submit</button>
                                 
                           </div>
                           </div>
                        </div>
                  @endif
               {!! Form::close() !!}
              
                  

                  </div>
                      
                        						
                      </div>

               
               
            </div>
         </div>


</section>
 <script type="text/javascript">

      function validatePaymentForm(){
       
         return true;
      }
      function deactive_allocation(alocation_id,checkboxid,balance){

         var alocation = "#"+alocation_id;
         var checkbox_id = "#"+checkboxid;
         if($(checkbox_id).is(":checked")){
            $(alocation). val(balance);
            $(alocation). attr('readonly',true);
         }
         else{
            $(alocation).val('');
            $(alocation). attr('readonly',false);
         }
      }

      function getfields(){
            $("#cheque_amount").prop('required',false);
            $("#cheque_no").prop('required',false);
            $("#datetext1").prop('required',false);
            $("#cheque_bank").prop('required',false);
            $(".waved").hide()
            $("#fromdate").prop('required',false);
            $("#bt_amount_received").prop('required',false);
            $(".alocation"). attr('disabled',false);
            $(".alocation"). val('');
            $("#cash_amount_received").prop('required',false);
            $("#datetext2").prop('required',false);
            //$("#receipt_no").prop('required',false);

         if($("#payment_option").val() ==1){
            $("#cheque").show(); 
            $("#receipt").show()
            $("#cash").hide(); 
            $("#bt").hide(); 
            $("#credit").hide();
            $("#cheque_amount").prop('required',true);
            $("#cheque_no").prop('required',true);
            $("#datetext1").prop('required',true);
            $("#cheque_bank").prop('required',true);
         }
         else if($("#payment_option").val() ==2){
            $("#bt").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#cash").hide(); 
            $("#credit").hide();
            $("#fromdate").prop('required',true);
            $("#bt_amount_received").prop('required',true);

         }
         else if($("#payment_option").val() ==3){
            $("#cash").show(); 
            $("#receipt").show()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#credit").hide();
            $("#cash_amount_received").prop('required',true);
            $("#datetext2").prop('required',true);
            //$("#receipt_no").prop('required',true);
         }
         else if($("#payment_option").val() ==6){
            $("#credit").show(); 
            $("#cash").hide(); 
            $("#receipt").hide()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#credit_amount").prop('required',true);
            //$("#receipt_no").prop('required',true);
            $(".waved").show()
         }
         else {
            $("#cash").hide(); 
            $("#receipt").hide()
            $("#cheque").hide(); 
            $("#bt").hide(); 
            $("#credit").hide(); 
         }

         $("#details").show(); 
      }

      
    </script>
@stop


