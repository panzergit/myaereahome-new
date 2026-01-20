@extends('layouts.adminnew')

@section('content')
<style>
.forunit h3 {font-style: italic;}
.forunit h2 {
    font-size: 18px;
    font-weight: 900;
    color: #fff;
	    margin-bottom: -35px;
}
</style>
 <div class="status">
    <h1>manage device(S) :{{Crypt::decryptString($UserMoreInfoObj->first_name)}}  {{isset($UserObj->userinfo_fromadmin->last_name)?Crypt::decryptString($UserObj->userinfo_fromadmin->last_name):''}}</h1>
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
			  <h2>Assign Devices</h2>
            {!! Form::model($UserObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/user/assigndevice/'.$UserObj->id)]) !!}

                 @php $devices_count =0 @endphp
                 
                  @if(!empty($data))  
                     
                     @foreach($data as $k => $record)
                     @php $record_id = $record['id'];  @endphp
                        <h3>{{$record['building']}}</h3>
                  
                        <div class="overflowscroll">
                           <table class="table usertable1 mb-4">
                              <thead>
                                 <tr>
                                    <th>Device Name </th>
                                    <th>Serial No. </th>
                                    <th>Location</th>
                                    <th>
												<label class="containeruser1" style="margin-top: -20px;">
              <input type="checkbox" class="uid{{$k+1}}" name="device_13" value="1" >Bluetooth Door Open
               <span class="checkmarkuser1"></span>
               </label>  </th>
                                    <th>
									<label class="containeruser1" style="margin-top: -20px;">
              <input type="checkbox" class="id{{$k+1}}" name="device_13" value="1"> Remote Door Open
               <span class="checkmarkuser1"></span>
               </label></th>
                                 </tr>
                              </thead>
                              <tbody>
                              
                                 @foreach($record['devices'] as $device)
                                    @if(isset($device['id']))
                                    @php
                                    $devices_count =1;
                                    $diviceid = $device['id'];
                                    $bluetooth_status = $device['user_bluethooth_checked_status'];
                                    $remote_status = $device['user_remote_checked_status'];
                                    @endphp
                                    <tr>
                                       <td> {{isset($device['device_name'])?$device['device_name']:''}}</td>
                                       <td>{{isset($device['device_serial_no'])?$device['device_serial_no']:''}}</td>
                                       <td><?php echo isset($device['location'])?$device['location']:''; ?></td>
                                    <td>
									<label class="containeruser1" style="margin-top: -15px;">
              <input type="checkbox"   name="building_{{$record_id}}_device_{{$diviceid}}"  value="1" class ='uclass{{$k+1}} viewCheckBox' {{($bluetooth_status ==1) ?'checked':'' }}>
               <span class="checkmarkuser1"></span>
               </label>
									</td>
                                       <td>
									   			<label class="containeruser1" style="margin-top: -15px;">
               <input type="checkbox"  name="building_{{$record_id}}_device_remote_{{$diviceid}}"  value="1" class ='class{{$k+1}} viewCheckBox' {{($remote_status  ==1)?'checked':'' }}>
               <span class="checkmarkuser1"></span>
               </label>
									  </td>
                                    </tr>
                                    @endif
                                 @endforeach
                              </tbody>
                           </table>	
                        </div>
                       
                     @endforeach
                     <div class="row">
                     <div class="col-lg-12">
                                       <input type="hidden" name="user_id" value ="{{$UserObj->id}}">
                                       <input type="hidden" name="user_info_id" value ="{{$UserMoreInfoObj->id}}">

									   <a href="{{url($return_url)}}"  class="submit  float-left " style="width:290px;"> return to summary</a>
                                       @if($devices_count ==1)
                                       <button type="submit" class="submit  float-right ">submit</button>
                                       @endif
                                   
                           </div>
                           </div>
                        </div>
                  @endif
               {!! Form::close() !!}
              
                  

                  <!--div class="col-lg-12">
                         <div class="form-group row">
                               
                           </div>
                    </div--> 
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


