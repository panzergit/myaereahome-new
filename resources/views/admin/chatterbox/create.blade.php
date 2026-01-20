@extends('layouts.adminnew')



@section('content')



<!-- Content Header (Page header) -->

   <div class="status">
        <h1>Announcement</h1>
    </div>

 @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<style>
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
	</style>
	<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/announcement')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/announcement/create')}}">Create new announcement</a></li>
                  </ul>
               </div>
               </div>
      <div class="">
                 {!! Form::open(['method' => 'POST', 'url' => url('opslogin/announcement'), 'files' => true]) !!}
                     <div class="row forunit tworow asignbg">
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label> user group :</label>
                                 {{ Form::select('role_array[]', $roles, null, ['class'=>'form-control selectpicker', 'title'=>'Please choose the group(s)', 'multiple'=>"multiple",'id'=>'role','required' => true]) }}
                             
                           </div>
                </div>
               <div class="col-lg-6">
                           <div class="form-group">
                              <label>subject:</label>
                                 <div id="sandbox">
            {{ Form::text('title', null, ['class'=>'form-control ','required' => true,'placeholder' => 'Enter  title']) }}
                                 
                              </div>
                           </div>
                           </div>
                        <div class="col-lg-6">
                           <div class="form-group">
						   <label>details:</label>
                            {{ Form::textarea('notes', null, ['class'=>'form-control','required' => 'yes','placeholder' => 'Enter details','rows'=>'6']) }}
                             
                           </div>
                        </div>
				
						      <!--div class="form-group col-lg-6">
                           <label>upload photo :</label><br>
                           <div class="image-upload uplodinline" data-toggle="tooltip" data-placement="bottom" title="upload Image" data-original-title="Add Image">
                              <label for="file-input" class="file-55" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="upimg" >
                              </label>
                              <input id="file-input" type="file" name="upload">
                        </div>
                     </div-->
                     <div class="col-lg-6 row pr-0">
					  <label class="col-lg-12">upload Image / PDF :</label>
						      <div class="form-group col-lg-6">
							  <div class="d-flex flex-sm-row flex-column">
  <div class="mr-auto p-2">
    <div class="image-upload uplodinline">
                              <label for="file-input" class="file-55 file100" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="announcementimg" >
                              </label>
                              <input id="file-input" type="file" name="upload">
                        </div>
  </div>
  <div class="p-2"> <img id="preview-file-input"  width="100px"></div>
  <div class="p-2"><img id="delete1" class="announcementdel"></div>
</div>
                         
                        
                     </div>
					   <div class="form-group col-lg-6">
					   <div class="d-flex flex-sm-row flex-column">
  <div class="mr-auto p-2"> 
   <div class="image-upload uplodinline">
                              <label for="file-input_2" class="file-55 file100" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="announcementimg" >
                              </label>
                              <input id="file-input_2" type="file" name="upload_2">
                        </div>
  </div>
  <div class="p-2">  <img id="preview-file-input_2"  width="100px"></div>
  <div class="p-2"><img id="delete2" class="announcementdel"></div>
</div>
                          
                       

                     </div>
					 <div class="form-group col-lg-6">
					 		   <div class="d-flex flex-sm-row flex-column">
  <div class="mr-auto p-2">   <div class="image-upload uplodinline">
                              <label for="file-input_3" class="file-55 file100" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="announcementimg" >
                              </label>
                              <input id="file-input_3" type="file" name="upload_3">
                        </div></div>
  <div class="p-2">  <img id="preview-file-input_3"  width="100px"></div>
   <div class="p-2"><img id="delete3" class="announcementdel"></div>
</div>
                         
                       
                     </div>
					 <div class="form-group col-lg-6">
					 		   <div class="d-flex flex-sm-row flex-column">
  <div class="mr-auto p-2">   <div class="image-upload uplodinline">
                              <label for="file-input_4" class="file-55 file100" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="announcementimg" >
                              </label>
                              <input id="file-input_4" type="file" name="upload_4">
                        </div></div>
  <div class="p-2">  <img id="preview-file-input_4"  width="100px"></div>
    <div class="p-2"><img id="delete4" class="announcementdel"></a></div>
</div>
                         
                       
                     </div>
					 <div class="form-group col-lg-6">
					 		   <div class="d-flex flex-sm-row flex-column">
  <div class="mr-auto p-2">    <div class="image-upload uplodinline">
                              <label for="file-input_5" class="file-55 file100" >
                                 <img src="{{url('assets/img/fileupload.png')}}" class="announcementimg" >
                              </label>
                              <input id="file-input_5" type="file" name="upload_5">
                        </div></div>
  <div class="p-2">  <img id="preview-file-input_5"  width="100px"></div>
   <div class="p-2"><img id="delete5" class="announcementdel"></div>
</div>
                        
                       
                     </div>
                     <div class="col-lg-12">
                     </div>
						   
						      <!-- <div class="col-lg-12 pr-0 annoimg">
                          <div class="form-group">
						       <a class="addrow   float-right"
                           id="addBtn" type="button" onclick="showmore()">
						    <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
<br>				
                      					   Add File
                        
                        </a> 
                           </div>
						  
                  </div>
-->
                     </div>

                      
                        <div class="col-lg-12 ">
                        <input type="hidden" name="pdf_icon_path" id="pdf_icon_path" value="{{$icon_path}}img/fileshw.png" />
                        <input type="hidden" name="delete_icon_path" id="delete_icon_path" value="{{$icon_path}}img/deleted.png" />
                        <button type="submit" href="#" class="submit mt-3 float-right ">submit</button>
                        </div>
                 

                    
                    {!! Form::close() !!}
               </div>

      
          <!-- /.box -->

    </section>  

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">      
$(document).ready(function (e) {
   var pdf_icon_path = $('#pdf_icon_path').val();
   var delete_icon_path = $('#delete_icon_path').val();
   $('#file-input').change(function(){
      let reader = new FileReader();
      reader.onload = (e) => { 
         var input_file = $("#file-input").val();
         var extension = input_file.split('.').pop().toLowerCase();

         if(extension=='pdf' || extension=='PDF'){
            $('#preview-file-input').show();
            $('#delete1').show(); 
            $('#preview-file-input').attr('src', pdf_icon_path); 
            $('#preview-file-input').attr('width', '100px'); 
	         $('#delete1').attr('src', delete_icon_path);
         }else if(extension=='jpeg' || extension=='JPEG'||extension=='jpg' || extension=='JPG'||extension=='PNG' || extension=='png'||extension=='SVG' || extension=='svg'){
            $('#preview-file-input').show();
            $('#delete1').show();
            $('#preview-file-input').attr('src', e.target.result); 
            $('#preview-file-input').attr('width', '100px'); 
	         $('#delete1').attr('src', delete_icon_path);
         }else{
            $("#file-input").val('');
            alert('Invalid file type!')
         }
    }
    reader.readAsDataURL(this.files[0]);  
   });
   $('#delete1').click(function(){
      $("#file-input").val('');
      $('#preview-file-input').attr('src', '');
      $('#preview-file-input').hide();
      $('#delete1').attr('src', ''); 
      $('#delete1').hide(); 
      
   });
   $('#file-input_2').change(function(){
      let reader = new FileReader();
      reader.onload = (e) => { 
         var input_file = $("#file-input_2").val();
         var extension = input_file.split('.').pop().toLowerCase();
         if(extension=='pdf' || extension=='PDF'){
            $('#preview-file-input_2').show();
            $('#delete2').show(); 
            $('#preview-file-input_2').attr('src', pdf_icon_path); 
            $('#preview-file-input_2').attr('width', '100px'); 
	         $('#delete2').attr('src', delete_icon_path);
         }else if(extension=='jpeg' || extension=='JPEG'||extension=='jpg' || extension=='JPG'||extension=='PNG' || extension=='png'||extension=='SVG' || extension=='svg'){
            $('#preview-file-input_2').show();
            $('#delete2').show();
            $('#preview-file-input_2').attr('src', e.target.result); 
            $('#preview-file-input_2').attr('width', '100px'); 
	         $('#delete2').attr('src', delete_icon_path);
         }else{
            $("#file-input_2").val('');
            alert('Invalid file type!')
         }
    }
    reader.readAsDataURL(this.files[0]);  
   });
   $('#delete2').click(function(){
      $("#file-input_2").val('');
      $('#preview-file-input_2').attr('src', '');
      $('#preview-file-input_2').hide();
      $('#delete2').attr('src', ''); 
      $('#delete2').hide(); 
      
   });


   $('#file-input_3').change(function(){
      let reader = new FileReader();
      reader.onload = (e) => { 
         var input_file = $("#file-input_3").val();
         var extension = input_file.split('.').pop().toLowerCase();
         if(extension=='pdf' || extension=='PDF'){
            $('#preview-file-input_3').show();
            $('#delete3').show(); 
            $('#preview-file-input_3').attr('src', pdf_icon_path); 
            $('#preview-file-input_3').attr('width', '100px'); 
	         $('#delete3').attr('src', delete_icon_path);
         }else if(extension=='jpeg' || extension=='JPEG'||extension=='jpg' || extension=='JPG'||extension=='PNG' || extension=='png'||extension=='SVG' || extension=='svg'){
            $('#preview-file-input_3').show();
            $('#delete3').show();
            $('#preview-file-input_3').attr('src', e.target.result); 
            $('#preview-file-input_3').attr('width', '100px'); 
	         $('#delete3').attr('src', delete_icon_path);
         }else{
            $("#file-input_3").val('');
            alert('Invalid file type!')
         }
    }
    reader.readAsDataURL(this.files[0]);  
   });
   $('#delete3').click(function(){
      $("#file-input_3").val('');
      $('#preview-file-input_3').attr('src', '');
      $('#preview-file-input_3').hide();
      $('#delete3').attr('src', ''); 
      $('#delete3').hide(); 
      
   });
   $('#file-input_4').change(function(){
      let reader = new FileReader();
      reader.onload = (e) => { 
         var input_file = $("#file-input_4").val();
         var extension = input_file.split('.').pop().toLowerCase();
         if(extension=='pdf' || extension=='PDF'){
            $('#preview-file-input_4').show();
            $('#delete4').show(); 
            $('#preview-file-input_4').attr('src', pdf_icon_path); 
            $('#preview-file-input_4').attr('width', '100px'); 
	         $('#delete4').attr('src', delete_icon_path);
         }else if(extension=='jpeg' || extension=='JPEG'||extension=='jpg' || extension=='JPG'||extension=='PNG' || extension=='png'||extension=='SVG' || extension=='svg'){
            $('#preview-file-input_4').show();
            $('#delete4').show();
            $('#preview-file-input_4').attr('src', e.target.result); 
            $('#preview-file-input_4').attr('width', '100px'); 
	         $('#delete4').attr('src', delete_icon_path);
         }else{
            $("#file-input_4").val('');
            alert('Invalid file type!')
         }
    }
    reader.readAsDataURL(this.files[0]);  
   });
   $('#delete4').click(function(){
      $("#file-input_4").val('');
      $('#preview-file-input_4').attr('src', '');
      $('#preview-file-input_4').hide();
      $('#delete4').attr('src', ''); 
      $('#delete4').hide(); 
      
   });
   $('#file-input_5').change(function(){
      let reader = new FileReader();
      reader.onload = (e) => { 
         var input_file = $("#file-input_5").val();
         var extension = input_file.split('.').pop().toLowerCase();
         if(extension=='pdf' || extension=='PDF'){
            $('#preview-file-input_5').show();
            $('#delete5').show(); 
            $('#preview-file-input_5').attr('src', pdf_icon_path); 
            $('#preview-file-input_5').attr('width', '100px'); 
	         $('#delete5').attr('src', delete_icon_path);
         }else if(extension=='jpeg' || extension=='JPEG'||extension=='jpg' || extension=='JPG'||extension=='PNG' || extension=='png'||extension=='SVG' || extension=='svg'){
            $('#preview-file-input_5').show();
            $('#delete5').show();
            $('#preview-file-input_5').attr('src', e.target.result); 
            $('#preview-file-input_5').attr('width', '100px'); 
	         $('#delete5').attr('src', delete_icon_path);
         }else{
            $("#file-input_5").val('');
            alert('Invalid file type!')
         }
    }
    reader.readAsDataURL(this.files[0]);  
   });
   $('#delete5').click(function(){
      $("#file-input_5").val('');
      $('#preview-file-input_5').attr('src', '');
      $('#preview-file-input_5').hide();
      $('#delete5').attr('src', ''); 
      $('#delete5').hide(); 
      
   });
});
</script>
@stop