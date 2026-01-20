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
    top: 26px;
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
            {!! Form::model($UserObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'url' => url('opslogin/user/assignproperty/'.$UserObj->id)]) !!}
               <h2 class="mt-3">Assign Property</h2>
                  <div class="row">
				         
                     <div class="col-lg-3">
                           <div class="form-group moreselect01">
                              <label>property(multiple selection) :</label>
                              <input type="hidden" id="user_id" name="user_id" value="{{ $UserObj->id }}">
                              <input type="hidden" id="user_more_info_id" name="user_more_info_id" value="{{ $UserMoreInfoObj->id }}">
                             
                              {{ Form::select('properties[]', $not_assigned_properties, null, ['class'=>'form-control selectpicker','required' => 'required','id'=>'locations','title'=>'--Select Location--','multiple'=>'multiple']) }} 
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
               {!! Form::close() !!}
               <div>
               <div class="">

               <div class="devicehead">
               <h2>Assigned Properties</h2>
                        
                   <div class="overflowscroll">
                           <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>Property</th>
                           <th>assigned date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                     @if($assigned_props)
                        @foreach($assigned_props as $k => $prop)
                           <tr>
                              <td>
                                 {{isset($prop->propinfo->company_name)?$prop->propinfo->company_name:''}}
                              </td>
                              
                              <td>
                                 @php 
                                    echo date('d/m/y',strtotime($prop->created_at));
                                 @endphp
                              </td>
                              <td>
                                
                                    <a href="#"  onclick="delete_record('{{url("opslogin/user/deleteproperty/$prop->id")}}');" data-toggle="tooltip" data-placement="top" title="Delete"><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg phvert"></a>
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


