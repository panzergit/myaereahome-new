@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1> CONDO DOCUMENT FILE - UPDATE  </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="containerwidth">
       {!! Form::model($fileObj,['method' =>'PATCH','class'=>"forunit",'files' => true,'url' => url('opslogin/docs-files/'.$fileObj->id)]) !!}

       <div class="pt-4">
					 <div class="row">
					 <div class="col-lg-8">
                           <div class="form-group row">
                              <label class="col-sm-4 col-form-label">
					<label> CATEGORY NAME:</label>
							  </label>
                              <div class="col-sm-6">
                              {{ Form::select('cat_id', $category, null, ['class'=>'form-control wauto','id'=>'cat' ]) }}

                              </div>
                           </div>
						    </div>
						    </div>
           
                              <div class="row" id="add_field" >
                                <div class="col-lg-8 " id="tbody">
                                
                                <div class="clbord clfet">
                                            <div class="form-group row ">
                                                <label class="col-sm-4 col-form-label">
                            <label>FILE UPLOAD: </label>
                                  </label>
                                                <div class="col-sm-1">
                            <div class="image-upload">
                                       <label for="file-input">
                                       <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
                                       </label>
                                       <input type="hidden" name="file_id" value="{{isset($fileObj->id)?$fileObj->id:''}}">
                                       <input id="file-input" name="docs_file" type="file" />
                                    </div>
                              </div>
                              
                            <div class="col-sm-7">
                            @if(isset($fileObj) &&  $fileObj->docs_file_name !='')
                            <a href="{{$img_full_path}}/{{$fileObj->docs_file}}" target="_blank"><img src="{{url('assets/admin/img/Files.png')}}" id="#preview-file-input"  class="viewimg"></a>
                            <input type="hidden" name="original_file_name" id="preview_input" value="{{isset($fileObj->original_file_name)?$fileObj->original_file_name:''}}" class="form-control">
                            @else
                            <input type="hidden" name="original_file_name" id="preview_input" value="" class="form-control">
                                       <img src="{{url('assets/admin/img/Files.png')}}" id="preview-file-input"  class="viewimg" style="display:none">
                            @endif
                            </div>
                           
                           </div>
                            <div class="form-group row">
                              <label class="col-sm-4 col-form-label">
                              <label>FILE NAME:</label>
                                    </label>
                              <div class="col-sm-8">
                              {{ Form::text("docs_file_name", isset($fileObj->docs_file_name)?$fileObj->docs_file_name:'', ['class'=>'form-control','placeholder' => 'Enter file name','id'=>"file_name"]) }}
                              </div>
                           </div>
                           </div>

						    </div>
							
						    </div>
             
            
                     <div class="row">
                     <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                           <button type="submit" class="submit mt-2 float-left">UPDATE</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
              
               
            </div>
         </div>
      </section>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">      
$(document).ready(function (e) {
  
   $('#file-input').change(function(){
      var fu1 = document.getElementById("file-input").value;
      var file_name = fu1.split("\\");
      $("#preview_input").val(file_name[2]); 
      $("#file_name").val(file_name[2]); 
      $("#preview-file-input").show();
      //console.log(file_name[2]);
      });
   
     
   
});
</script>
@stop