@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>moving in & out : inspection</h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li class="activeul"><a href="{{url('/opslogin/eform/moveinout#ef')}}">Moving In & Out</a></li>
                     <li><a href="{{url('/opslogin/eform/renovation#ef')}}">Renovation</a></li>
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
	   <div class="col-lg-12 asignFace">
                  <h2>official use only </h2>
               </div>
                
                  
                 {!! Form::model($eformObj,['method' =>'PATCH','class'=>"forunit", 'autocomplete'=>"off", 'files' => true,'url' => url('opslogin/eform/moveinout/inspectionsave/'.$eformObj->id)]) !!}
                       <div class="row asignbg">
                          <div class="col-lg-4">
                           <div class="form-group ">
                              <label >actual date of completion : 
                              </label>
                                 <div id="sandbox4">
                                    {{ Form::text('date_of_completion', isset($eformObj->inspection->date_of_completion)?$eformObj->inspection->date_of_completion:'', ['id'=>'datetext2','class'=>'form-control','required' => true,'placeholder' => 'Enter date of completion']) }}
                                 </div>
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label>Unit inspected by mangement : 
                              </label>
                                 {{ Form::text('inspected_by', isset($eformObj->inspection->inspected_by)?$eformObj->inspection->inspected_by:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter name of unit inspector name']) }}
                             
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                                 <label>unit status : 
                                 </label>
                                 <div class="form-group checkbook unitstate row">
                                 {{ Form::radio('unit_in_order_or_not', '1', (isset($eformObj->inspection->unit_in_order_or_not) && ($eformObj->inspection->unit_in_order_or_not==1))?$eformObj->inspection->unit_in_order_or_not:'',['class'=>'form-control']) }}<label>Unit in order & full amount refunded</label>
                                 </div>
                                 <div class="form-group checkbook unitstate row">
                                    {{ Form::radio('unit_in_order_or_not', '2',(isset($eformObj->inspection->unit_in_order_or_not) && ($eformObj->inspection->unit_in_order_or_not==2))?$eformObj->inspection->unit_in_order_or_not:'',['class'=>'form-control']) }}<label>Unit not in order</label>
                                 </div>            
                                 
                           </div>
                           </div>
                         <div class="col-lg-4">
                           <div class="form-group ">
                              <label >amount deducted from deposit : 
                              </label>
                                 {{ Form::text('amount_deducted', isset($eformObj->inspection->amount_deducted)?$eformObj->inspection->amount_deducted:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter amount deducted from deposit']) }}
                           
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label>amount balance to be refunded : 
                              </label>
                                 {{ Form::text('refunded_amount', isset($eformObj->inspection->refunded_amount)?$eformObj->inspection->refunded_amount:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter amount balance to be refunded']) }}
                            
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label >amount claimable : 
                              </label>
                                 {{ Form::text('amount_claimable', isset($eformObj->inspection->amount_claimable)?$eformObj->inspection->amount_claimable:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter amount claimable:']) }}
                             
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label>acutal amount received : 
                              </label>
                                 {{ Form::text('actual_amount_received', isset($eformObj->inspection->actual_amount_received)?$eformObj->inspection->actual_amount_received:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter actual amount received']) }}
                           </div>
                           </div>
                            <div class="col-lg-4">
                           <div class="form-group ">
                              <label> received by name of resident : 
                              </label>
                                 {{ Form::text('acknowledged_by', isset($eformObj->inspection->acknowledged_by)?$eformObj->inspection->acknowledged_by:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter  received By Name of Resident:']) }}
                             
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label>pp / nric no. of resident : 
                              </label>
                                 {{ Form::text('resident_nric', isset($eformObj->inspection->resident_nric)?$eformObj->inspection->resident_nric:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter PP / NRIC No of Resident']) }}
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label >date of resident signature : 
                              </label>
                                 <div id="sandbox">
                                 {{ Form::text('resident_signature_date', isset($eformObj->inspection->resident_signature_date)?$eformObj->inspection->resident_signature_date:'', ['class'=>'form-control','required' => true,"id"=>"fromdate",'placeholder' => 'Enter date of resident signature']) }}
                                 </div>
                              
                           </div>
                           </div>
						    <div class="col-lg-4">
                           <div class="form-group ">
                              <label >name of 
                                 management received : 
                              </label>
                                 {{ Form::text('manager_received', isset($eformObj->inspection->manager_received)?$eformObj->inspection->manager_received:'', ['class'=>'form-control','required' => true,'placeholder' => 'Enter name of management received: ']) }}
                           </div>
                           </div>
                           @if(isset($eformObj->inspection->manager_signature))
							    <div class="col-lg-4">
                           <div class="form-group ">
                              <label>name of management received : 
                              </label>
                                 <a href="#" target="_blank"><img src="data:image/png;base64, {{$eformObj->inspection->manager_signature}}" class="viewsig"/></a>
                           </div>
                           </div>
                           @endif
						    <div class="col-lg-4">
                           <div class="form-group">
                              <label>date of management signature : 
                              </label>
                                 <div id="sandbox2">
                                    {{ Form::text('date_of_signature', isset($eformObj->inspection->date_of_signature)?$eformObj->inspection->date_of_signature:'', ['class'=>'form-control','required' => true,'id' =>'todate','placeholder' => 'Enter date of management signature']) }}
                                 </div>
                           </div>
                           </div>
                        </div> 
                      
						 <div class="col-lg-12 asignFace">
                  <h2>defects </h2>
               </div>
                        <div class="row">
                        @for($i=1;$i<=5;$i++)
                        @php
                           if($i ==1)
                              {
                                  $display_style = "";
                                  //$rowcount = 1;
                              }
                           else if(isset($defect_files[$i]['key']) && $defect_files[$i]['key'] ==$i)
                              {
                                    $display_style = "";
                                    $rowcount = $i;
                              }
                           else
                              {
                                    $display_style = "display:none";
                                    //$rowcount = 1;
                              }
                        @endphp
                               

                     <div class="col-lg-6" id="add_field{{$i}}" style="{{$display_style}}">
							<div  id="tbody">
							
							<div class="clbord clfet">
                              <div class="form-group row ">
                                 <label class="col-sm-4 col-4 col-form-label">
                                    <label>upload image: </label>
                                 </label>
                                 <div class="col-sm-1 col-4 ">
                                          <div class="image-upload">
                                             <label for="file-input_{{$i}}">
                                                <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
                                             </label>
                                          <input id="file-input_{{$i}}" name="file_{{$i}}" type="file" />
                                       </div>
                                    </div>
                                    <div class="col-sm-7 col-4">
                                       
                                    @if(isset($defect_files[$i]['image_file']))
                                       <a href="#" target="_blank"><img src="data:image/png;base64, {{$defect_files[$i]['image_file']}}" class="viewimg"/></a>
                                    @endif
                                 </div>              
                              
                              </div>
                              <div class="form-group row">
                                 <label class="col-sm-4 col-4 col-form-label">
                                    <label>description :</label>
                                       </label>
                                    <div class="col-sm-8 col-8">
                                    <input type="hidden" name="file_id_{{$i}}" value="{{isset($defect_files[$i]['id'])?$defect_files[$i]['id']:''}}">

                                       <textarea type="text" name="description_{{$i}}" id="description_{{$i}}" class="form-control" placeholder="">{{(isset($defect_files[$i]['notes'])?$defect_files[$i]['notes']:'')}}</textarea>
                                    </div>
                              </div>
                           </div>
						    </div>
							
						    </div>
                              @endfor
							  </div>
                              <div class="row" >
                           <div class="col-lg-2" id="buttonsection">   <a class="addrow  mt-4 mb-4"
                           id="addBtn" type="button" onclick="showmore()">
                         <img src="{{url('assets/img/plus.png')}}" class="upimg"/><br>
						 Add File
                        </a></div>
						</div>
                        <input type="hidden" id="rowcount" value="1">
                        <input type="hidden" id="maxcount" value="10">

              
              <div class="row">
                       <div class="col-lg-8"></div>
                       <div class="col-lg-4">
                           <div class="form-group ">
                           <a href="{{url("/opslogin/eform/moveinout")}}"  class="Delete ml-3 mr-0  mt-2 float-right">cancel</a>
                             
                              <input type="hidden" name="inspection_id" value="{{isset($eformObj->inspection->id)?$eformObj->inspection->id:''}}">
                              <button type="submit" class="submit mt-2  float-right">update</button>
                              </div>
                           </div>
                           </div>
                     
                  

                    

                    {!! Form::close() !!}
               
               
            </div>
      


</section>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">      
$(document).ready(function (e) {
  
   $('#file-input_1').change(function(){
      var fu1 = document.getElementById("file-input_1").value;
      var file_name = fu1.split("\\");
      $("#preview_input_1").val(file_name[2]); 
      $("#file_name_1").val(file_name[2]); 
      $("#preview-file-input_1").show();
      //console.log(file_name[2]);
      });
   
      $('#file-input_2').change(function(){
      var fu1 = document.getElementById("file-input_2").value;
      var file_name = fu1.split("\\");
      $("#preview_input_2").val(file_name[2]); 
      $("#file_name_2").val(file_name[2]);
      $("#preview-file-input_2").show();
      //console.log(file_name[2]);
      });

      $('#file-input_3').change(function(){
      var fu1 = document.getElementById("file-input_3").value;
      var file_name = fu1.split("\\");
      $("#preview_input_3").val(file_name[2]); 
      $("#file_name_3").val(file_name[2]);
      $("#preview-file-input_3").show();
      //console.log(file_name[2]);
      });

      $('#file-input_4').change(function(){
      var fu1 = document.getElementById("file-input_4").value;
      var file_name = fu1.split("\\");
      $("#preview_input_4").val(file_name[2]); 
      $("#file_name_4").val(file_name[2]);
      $("#preview-file-input_4").show();
      //console.log(file_name[2]);
      });

      $('#file-input_5').change(function(){
      var fu1 = document.getElementById("file-input_5").value;
      var file_name = fu1.split("\\");
      $("#preview_input_5").val(file_name[2]); 
      $("#file_name_5").val(file_name[2]);
      $("#preview-file-input_5").show();
      //console.log(file_name[2]);
      });

      $('#file-input_6').change(function(){
      var fu1 = document.getElementById("file-input_6").value;
      var file_name = fu1.split("\\");
      $("#preview_input_6").val(file_name[2]); 
      $("#file_name_6").val(file_name[2]);
      $("#preview-file-input_6").show();
      //console.log(file_name[2]);
      });

      $('#file-input_7').change(function(){
      var fu1 = document.getElementById("file-input_7").value;
      var file_name = fu1.split("\\");
      $("#preview_input_7").val(file_name[2]); 
      $("#file_name_7").val(file_name[2]);
      $("#preview-file-input_7").show();
      //console.log(file_name[2]);
      });

      $('#file-input_8').change(function(){
      var fu1 = document.getElementById("file-input_8").value;
      var file_name = fu1.split("\\");
      $("#preview_input_8").val(file_name[2]);
      $("#file_name_8").val(file_name[2]); 
      $("#preview-file-input_8").show();
      //console.log(file_name[2]);
      });

      $('#file-input_9').change(function(){
      var fu1 = document.getElementById("file-input_9").value;
      var file_name = fu1.split("\\");
      $("#preview_input_9").val(file_name[2]); 
      $("#file_name_9").val(file_name[2]);
      $("#preview-file-input_9").show();
      //console.log(file_name[2]);
      });

      $('#file-input_10').change(function(){
      var fu1 = document.getElementById("file-input_10").value;
      var file_name = fu1.split("\\");
      $("#preview_input_10").val(file_name[2]); 
      $("#file_name_10").val(file_name[2]);
      $("#preview-file-input_10").show();
      //console.log(file_name[2]);
      });
   
});
</script>
@stop


