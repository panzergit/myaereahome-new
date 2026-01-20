@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1> User Guide - update  </h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/userguide#cd')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/userguide/create#cd')}}">Upload new Guide</a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
       {!! Form::model($fileObj,['method' =>'PATCH','class'=>"forunit",'files' => true,'url' => url('opslogin/userguide/'.$fileObj->id)]) !!}

       <!--<div class="">
					 <div class="row">
					 <div class="col-lg-4 pl-4">
                           <div class="form-group  mt-2">
					<label> category name:</label>
                              {{ Form::text('docs_category', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Category Name']) }}
                           </div>
						    </div>
							 <div class="col-lg-8">
						    </div>
-->
                              <div class="col-lg-6">
                                <div class=" " id="tbody">
                                <input type="hidden" name="file_id" value="{{isset($fileObj->id)?$fileObj->id:''}}">
                                <div class="clbord editbg clfet alert">
                                            <div class="form-group row ">
                                                <label class="col-sm-4 col-5 col-form-label">
                            <label>file upload: </label>
                                  </label>
                                                <div class="col-sm-1 col-3">
                            <div class="image-upload">
                                       <label for="file-input">
                                       <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
                                       </label>
                                      
                                       <input id="file-input" name="docs_file" type="file" />
                                    </div>
                              </div>
                           
                            <div class="col-sm-7 col-4">
                            
                            @if(isset($fileObj->docs_file) &&  $fileObj->docs_file !='')
                            <a href="#" class="closopity"  onclick='deleteUserguideFile("{{$fileObj->id}}")'><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg phvert"></a>

                            <a href="{{$img_full_path}}/{{$fileObj->docs_file}}" target="_blank"><img src="{{url('assets/admin/img/Condo.png')}}" id="#preview-file-input"  class="viewimgfile"></a>
                            <!--<img src="{{$img_full_path}}/{{$fileObj->docs_file}}" class="viewimg">-->
                            <input type="hidden" name="original_file_name" id="preview_input" value="{{isset($fileObj->original_file_name)?$fileObj->original_file_name:''}}" class="form-control">
                            @else
                            <input type="hidden" name="original_file_name" id="preview_input" value="" class="form-control">
                                       <img src="{{url('assets/admin/img/Condo.png')}}" id="preview-file-input"  class="viewimgfile" style="display:none">
									   <a href="#" class="closopity" ><img src="{{url('assets/admin/img/deleted.png')}}" class="viewimg phvert"  style="display:none"></a>
                            @endif
                            </div>
                           
                           </div>
                            <div class="form-group row">
                              <label class="col-sm-4 col-form-label">
                              <label>Category:</label>
                                    </label>
                              <div class="col-sm-8">
                              {{ Form::text("file_name", isset($fileObj->docs_file_name)?$fileObj->docs_file_name:'', ['class'=>'form-control','placeholder' => 'Enter file name','id'=>"file_name"]) }}
                              </div>
                           </div>
                          
                           </div>

						    </div>
							
						    </div>
                            
                              
              </div>

                     <div class="row">
                     <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
              
               
            </div>
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