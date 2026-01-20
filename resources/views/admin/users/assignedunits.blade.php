@extends('layouts.adminnew')

@section('content')

 <div class="status">
    <h1>manage unit(S) :{{Crypt::decryptString($UserMoreInfoObj->first_name)}} {{Crypt::decryptString($UserMoreInfoObj->last_name)}}</h1>
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
            <div class=" forunit forchange devicehead">
            {!! Form::model($UserObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/user/assignunit/'.$UserObj->id)]) !!}

                   <h2 class="mt-3">Assign Unit</h2>
                  <div id="unit_div" style="display:block">
				  <div class="row asignbg" >
                  <div class="col-lg-3" >
                        <div class="form-group">
                              <label>primary contact:</label>
						   <label class="containeruser1">
               <input type="checkbox"  id="primary" name="primary_contact" value="1">Yes
               <span class="checkmarkuser1"></span>
               </label>
                        </div>
                     </div>
                      <div class="col-lg-3" >
                           <div class="form-group">
                                 <label>assign role <span>*</span>:</label>
                                 {{ Form::select('role_id', $roles,'', ['class'=>'form-control','id'=>'role','required' => true]) }}
                           </div>
                     </div>
                      <div class="col-lg-3" >
                        <div class="form-group">
                              <label>building <span>*</span>: </label>
                              <input type="hidden" id="user_id" name="user_id" value="{{ $UserObj->id }}">
                              <input type="hidden" id="user_more_info_id" name="user_more_info_id" value="{{ $UserMoreInfoObj->id }}">
                              <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                           {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control','id'=>'building','onchange'=>'getunits()' ]) }}
                         
                        </div>
                     </div>
                     <div class="col-lg-3" >
                        <div class="form-group">
                              <label>unit no <span>*</span>: </label>
                              {{ Form::select('unit_no', ['' => '--Select Unit--'], null, ['class'=>'form-control','id'=>'unit','onchange'=>'getcards()' ]) }}
                        </div>
                     </div>
                     <!-- <div class="col-lg-3" >
                        <div class="form-group">
                              <label>card no : </label>
                              {{ Form::select('card_nos[]', ['' => '--Select Card--'], null, ['class'=>'form-control','id'=>'card','multiple'=>'multiple','rows'=>2, 'style'=>'height:60px !important']) }}
                       
                        </div>
                     </div> -->
					 <div class="col-lg-9">
                           <div class="form-group ">
                                       <button type="submit" class="submit mt0-4 ml-3 float-right ">submit</button>
                                  
                                 </div>
                           </div>
                  </div>

                        
                        
                        </div>
                        </div>
               
               {!! Form::close() !!}
               <div>
               <div class="">

               <div class="devicehead">
               <h2>Assigned Units</h2>
                        
                   <div class="overflowscroll">
                           <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>block</th>
                           <th>unit</th>
                           <th>role</th>
                           <th>primary contact</th>
                           <th>assigned date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                     @if($PurchaserUnits)
                        @php  $count =count($PurchaserUnits);@endphp
                        @foreach($PurchaserUnits as $k => $PurchaserUnit)
                           <tr>
                              <td>
                                 {{isset($PurchaserUnit->addubuildinginfo)?$PurchaserUnit->addubuildinginfo->building:''}}
                              </td>
                              <td > 
                                 #{{isset($PurchaserUnit->addunitinfo)?Crypt::decryptString($PurchaserUnit->addunitinfo->unit):''}}
                              </td>
                              <td > 
                                 {{isset($PurchaserUnit->role->name)?Str::limit($PurchaserUnit->role->name,20):''}}
                              </td>
                              <td > {{($PurchaserUnit->primary_contact==1)?"Yes":"No"}}
                              </td>
                              <td>
                                 @php 
                                    echo date('d/m/y',strtotime($PurchaserUnit->created_at));
                                 @endphp
                              </td>
                              <td>
                                 @php
                                    $userunits = new \App\Models\v2\UserPurchaserUnit();
                                    $activeunit = $userunits->activeunit($PurchaserUnit->id);
                                 @endphp
                                 @if($count >1)
                                    <a href="#"  onclick="delete_record('{{url("opslogin/user/deleteunit/$PurchaserUnit->id")}}');" data-toggle="tooltip" data-placement="top" title="Delete"><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg phvert"></a>
                                 @endif
                                    <a href="{{url("opslogin/user/assignunitupdate/$PurchaserUnit->id")}}" title="Delete">Edit</a>

                              </td>
                        @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
               </div>

                  

                  <div class="row">
                  <div class="col-lg-12">
                         <div class="form-group ">
                               <a href="{{url($return_url)}}"  class="submit mt-3 float-left " style="width:290px;"> return to summary</a>
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


