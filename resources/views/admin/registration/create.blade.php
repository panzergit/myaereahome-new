@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>CONDO DOCUMENT - NEW FILE </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

       <div class="containerwidth">
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/docs-files'), 'files' => true]) !!}

                 <div class="pt-4">
                  <div class="row">
                     <div class="col-lg-8">
                        <div class="form-group row">
                           <label class="col-sm-4 col-form-label">
                              <label> CATEGORY NAME:</label>
                           </label>
                           <div class="col-sm-6">
                              {{ Form::select('cat_id', $category, $id, ['class'=>'form-control','id'=>'cat' ]) }}
                           </div>
                           
                        </div>
                     </div>
                  </div>

                  @for($i=1;$i<=10;$i++)
                              @php
                                 if($i ==1)
                                    $display_style = "";
                                 else
                                    $display_style = "display:none";
                              @endphp
                               

                              <div class="row" id="add_field{{$i}}" style="{{$display_style}}">
							<div class="col-lg-8 " id="tbody">
							
							<div class="clbord clfet">
                           <div class="form-group row ">
                              <label class="col-sm-4 col-form-label">
                              <label>FILE UPLOAD: </label>
                                    </label>
                                    <div class="col-sm-1">
                                          <div class="image-upload">
                                             <label for="file-input_{{$i}}">
                                                <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
                                             </label>
                                          <input id="file-input_{{$i}}" name="file_{{$i}}" type="file" />
                                       </div>
                                    </div>
                                    <div class="col-sm-7">
                                    <input type="hidden" name="original_file_name_{{$i}}" id="preview_input_{{$i}}" value="" class="form-control">
                                       <img src="{{url('assets/admin/img/Files.png')}}" id="preview-file-input_{{$i}}"  class="viewimg" style="display:none">
                                    </div>              
                              
                           </div>
                            <div class="form-group row">
                              <label class="col-sm-4 col-form-label">
					<label>FILE NAME:</label>
							  </label>
                              <div class="col-sm-8">
						<input type="text" name="file_name_{{$i}}" id="file_name_{{$i}}" class="form-control" placeholder="Define File Name">
                              </div>
                           </div>
                           </div>
						    </div>
							
						    </div>
                              @endfor
                              <div class="row">
                              <a class="addrow submit2 mt-4 mb-4"
                           id="addBtn" type="button" onclick="showmore()">
                        + ADD FILE
                        </a>
						</div>
                        <input type="hidden" id="rowcount" value="1">
              </div>
 
            
                     <div class="row">
                     <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                           <button type="submit" class="submit mt-2 float-left">SUBMIT</button>
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
