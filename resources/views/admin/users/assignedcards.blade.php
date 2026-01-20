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
            {!! Form::model($UserObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/user/assigncard/'.$UserObj->id)]) !!}

                   <h2 class="mt-3">Assign Card</h2>
                  <div >
				  <div class="row asignbg" >
                  
                      <div class="col-lg-3" >
                        <div class="form-group">
                              <label>building <span>*</span>: </label>
                              <input type="hidden" id="user_id" name="user_id" value="{{ $UserObj->id }}">
                              <input type="hidden" id="user_more_info_id" name="user_more_info_id" value="{{ $UserMoreInfoObj->id }}">
                              <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                           {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, '', ['class'=>'form-control','id'=>'building','onchange'=>'getblockunits()' ]) }}
                         
                        </div>
                     </div>
                     <div class="col-lg-3" >
                        <div class="form-group">
                              <label>unit no <span>*</span>: </label>
                              {{ Form::select('unit_no', ['' => '--Select Unit--'], null, ['class'=>'form-control','id'=>'unit' ]) }}
                        </div>
                     </div>
                     <div class="col-lg-4" >
                        <div class="form-group">
                              <label>card no : (Separate Multiple cards by comma ",")</label>
                              {{ Form::textarea('card_no', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Card(s)']) }}
                        </div>
                     </div>
					 <div class="col-lg-2">
                           <div class="form-group ">
						    <label>&nbsp; </label>
                                       <button type="submit" class="submit mt0-4 ml-3 ">submit</button>
                                  
                                 </div>
                           </div>
                  </div>

                        
                        
                        </div>
                        </div>
               
               {!! Form::close() !!}
               <div>
               <div class="">

               <div class="devicehead">
               <h2>Assigned Cards</h2>
                        
                   <div class="overflowscroll">
                           <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>Block</th>
                           <th>unit</th>
                           <th>Card No</th>
                           <!-- <th>assigned date</th> -->
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                     @if($assignedCards)
                        @php  $count =count($assignedCards);@endphp
                        @foreach($assignedCards as $k => $assignedCard)
                           <tr>
                              <td>
                                 {{isset($assignedCard->addubuildinginfo)?$assignedCard->addubuildinginfo->building:''}}
                              </td>
                              <td > 
                                 #{{isset($assignedCard->addunitinfo)?Crypt::decryptString($assignedCard->addunitinfo->unit):''}}
                              </td>
                              <td > 
                                {{$assignedCard->card_no}}
                              </td>
                              <td>
                                 @php 
                                    #echo date('d/m/y',strtotime($assignedCard->created_at));
                                 @endphp
                              </td>
                              <td>
                                
                                    <a href="#"  onclick="delete_record('{{url("opslogin/user/deletecard/$assignedCard->id")}}');" data-toggle="tooltip" data-placement="top" title="Delete"><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg phvert"></a>
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

      

      
    </script>
@stop


