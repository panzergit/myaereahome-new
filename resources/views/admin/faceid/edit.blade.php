@extends('layouts.adminnew')




@section('content')

<style>
.btnpicture {
    background: transparent;
    border: none;
    height: 36px!important;
    margin-bottom: 0px!important;
    margin-left: -10px;
    color: #fff!important;
    box-shadow:none;
}
.btnpicture:focus {
    background: transparent;
    border: none;
    height: 36px!important;
    margin-bottom: 0px!important;
    margin-left: -10px;
    color: #fff!important;
    box-shadow:none;
}
input[type=file]::file-selector-button {
  border: 2px solid #efefef;
  border-radius: .9em;
  padding: 0px 10px!important;
  font-size: 1rem;
}

input[type=file]::file-selector-button:hover {
  border: 2px solid #efefef;
  border-radius: .9em;
}
.padtwo{padding:2px 20px!important}
.faclab1{
    padding-top: 8px!important;
}
.mmt5 {
    margin-top: -4px;
}
.faclab2{ padding-top: 10px!important;}
.noimg img{    width: 80px;}
.facidimg img{width: 100%;
    height: 250px;
    object-fit: contain;}
	.facidimg {width: 100%;
    height: 250px;
    object-fit: contain;}
	.facidimg b{      margin: 0 auto;
    display: block;  text-align: center;}
	.modal-body input{    font-weight: 600;}
</style>

<!-- Content Header (Page header) -->

  <div class="status">
    <h1>facial recognition - edit </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif

  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/faceid#fi')}}">Summary</a></li>
					   <li><a href="{{url('/opslogin/faceid/new')}}">New Submission</a></li>
                     <li><a href="{{url('/opslogin/faceid/create')}}">Add new facial ID</a></li>
                  </ul>
               </div>
               </div>
      <div class="">
      {!! Form::model($UserFaceObj,['method' =>'PATCH','class'=>"",'url' => url('opslogin/faceid/'.$UserFaceObj->id)]) !!}
                     <div class="row asignbg forunit editbg">
                              <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                    <label>
                                     unit :
                                    </label>
									<h4>{{isset($UserFaceObj->user->getunit->unit)?"#".Crypt::decryptString($UserFaceObj->user->getunit->unit):''}}</h4>
                               
                                 </div>
                              </div>
                           

                        
                              <div class="col-lg-3 col-6">
                                 <div class="form-group">
                                <label>
                                  user<span>*</span>:
                                    </label>
									<h4>{{isset($UserFaceObj->user->name)?Crypt::decryptString($UserFaceObj->user->name):''}}</h4>
                                 </div>
                              </div>
                          
                            
                         
                             <div class="col-lg-3 col-6">
                                 <div class="form-group ">
                                    <label class=" ">
                                     facial picture:
                                    </label>
									  <h4 class="noimg">
									   <a data-toggle="modal" data-target="#exampleModalCenter2" data-id="" class="open-dialog">
									<img src="{{url('assets/admin/img/scenery.png')}}">
									</a>
									<h4>
                                   <!-- <h4 class=" ">
                                    @if(isset($UserFaceObj->face_picture))
                                       <a href="{{$file_path}}/{{$UserFaceObj->face_picture}}" target="_blank">
                                          <img src="{{$file_path}}/{{$UserFaceObj->face_picture}}" class="viewimg phvert">
                                       </a>
                                    @endif
                                    </h4>-->
                                 </div>
                              </div> 
                      
                         
                              <div class="col-lg-3 col-6">
                                 <div class="form-group ">
                                    <label class="">
                                     relationship:
                                    </label>
                                    {{ Form::select('option_id', ['' => '--Choose Relationship--']+$relationships, null, ['class'=>'form-control','required' => true,'id'=>'option','onchange'=>'getothers()']) }}
                                    
                                 </div>

                                 <div class="form-group row" id="otherdiv" style="display:none;">
                                    <label class="">
                                     please specify:
                                    </label>
                                     {{ Form::text('others', null, ['class'=>'form-control']) }} 
                                 </div>
                              </div>
                  
         </div>
		 <div class="row">
		                         <div class="col-lg-12 " id="submit_btn_div">
                        <input type="submit" class="submit mt-2 float-right " value="submit">
                        <!--input type="submit" class="submit2 mt-3 ml-3 float-right mlres" value="Submit"-->
                        <!--<button type="submit" class="submit2 mt-3 ml-3 float-right mlres">SUBMIT</button>-->
                        </div>
		 </div>
		 </form>
		  <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <form  class="userpopup" action="#">
            {{ csrf_field() }}          
            <div class="modal-dialog modal-dialog-centered" role="document">
                
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Access Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>Text:</label>
                 <input class="form-control" id="access_code" name="access_code" type="text" placeholder="">
                 <input type="hidden" name="Id" id="Id" value="{{$UserFaceObj->id}}">
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="checkinpopup_new();" class="open-dialog">Submit</button>
				<!--button type="submit" class="btn btn-primary" onclick="facidPopup()">Submit</button-->
              </div>
            </div>
            
          </div>
          {!! Form::close() !!}
        </div>
		<!--Model2-->
			  <div class="modal fade" id="facidmodalSuccess" >
		  <form method="" action="#"  autocomplete="off">  
          <div class="modal-dialog modal-dialog-centered" role="document">
                
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Facial Picture</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
         
			  <div id="image-popup" class="facidimg"></div>
              </div>
              
          
            </div>
            
          </div>
		  </form>
        </div>
		<!--Model2-->
@endsection

<script>

function checkinpopup_new()
        {
            var code = $("#access_code").val();
            var id = $("#Id").val();
            console.log(id);
            
            $.ajax({
               url : "{!!URL:: route('accessfaceid')!!}",
               dataType : "json",
               data:{
                  code:code,
                  id:id
                },
                success:function(data)
                {
                  console.log(data);
                  if(data.status ==1){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     });
                     console.log(data['img']);
	                  //var image = $('<img></img>');
                     //image.attr('src',data['img']);
                     var html = '<b>For security reasons, Face ID photo has been deleted after the facial template has been obtained and sent to the devices.</b>';
                     $('#image-popup').append(html);
                     $('#image-popup').append(image);
                  }
                  else if(data.status ==2){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Login!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else if(data.status ==3){
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Access Code!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else {
                     $("#facidmodalSuccess").show();
                     $("#exampleModalCenter2").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#facidmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b>No Record Found!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                    
               },
               error: function (textStatus, errorThrown) {
                  //alert("NRIC in blacklist!")
               }
            });
            
         
        }
function checkinpopup2()
        {
            var code = $("#access_code").val();
            var id = $("#Id").val();
            console.log(id);
            
            $.ajax({
               url : "{!!URL:: route('accessfaceid')!!}",
               dataType : "json",
               data:{
                  code:code,
                  id:id
                },
                success:function(data)
                {
                  console.log(data);
                  if(data.status ==1){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     });
                    // alert("hi");
	                  var image = $('<img></img>');
                     image.attr('src', "data:image/png;base64, "+data['64img']);
                     $('#image-popup').append(image);
                  }
                  else if(data.status ==2){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Login!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else if(data.status ==3){
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b> Invalid Access Code!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                  else {
                     $("#digitalmodalSuccess").show();
                     $("#digitalfaceid").hide();
					      $(".fade").css("opacity", "1");
					      $(".close").click(function()  {				
                     $("#digitalmodalSuccess").fadeOut(200);
						      location.reload();
                     })
                     var html = '<b>No Record Found!</b>';
                     $('#image-popup').append(html);
                     //alert("helloo");

                  }
                    
               },
               error: function (textStatus, errorThrown) {
                  //alert("NRIC in blacklist!")
               }
            });
            
         
        }

</script>

