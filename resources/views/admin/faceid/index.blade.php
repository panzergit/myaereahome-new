@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $faceid_count = $permission->noOfFaceids($account_id);
   $permission = $permission->check_permission(50,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.noimg img{    width: 40px;}
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
<div class="status">
  <h1>facial recognition summary </h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/faceid#fi')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/faceid/new')}}">New Submission @if(isset($faceid_count) && $faceid_count >0 )
                  <span class="notification17">{{$faceid_count}}</span>
                  @endif</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                     <li><a href="{{url('/opslogin/faceid/create')}}">Add new facial ID</a></li>
                     @endif
                  </ul>
               </div>
               </div>
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
<form action="{{url('/opslogin/faceid/summarysearch')}}" method="get" role="search" class="forunit">
           
                     <div class="row asignbg">
                        <div class="col-lg-3">
                     <div class="form-group">
                              <label class="">Block : 
                              </label>
                     {{ Form::select('building', ['' => '--Block--'] + $buildings, $building, ['class'=>'form-control','id'=>'building']) }}
                           </div>
                           </div>
                        <div class="col-lg-3">
                        
                           <div class="form-group">
                              <label class="">unit: 
                             
                              </label>
                                 <input  type="text" name="unit" class="form-control" value="<?php echo(isset($unit)?$unit:'');?>" id="unit_list">
                           </div>
						    </div>
							 <div class="col-lg-3">
                           <div class="form-group">
                              <label class="">name: 
                              </label>
                                 <input  type="text" class="form-control" name="name" id="name" value="<?php echo(isset($name)?$name:'');?>">
                            
                           </div>
                           </div>
						    <div class="col-lg-3">
                           <div class="form-group ">
                              <label class="">relationship: 
                              </label>
                              {{ Form::select('relationship', [''=>'Select']+$relationships, $relationship, ['class'=>'form-control','id'=>'role']) }}
                           </div>
                           </div>
                       
                        <div class="col-lg-12">
                           <div class="form-group mt-2">
						 
                            
							   <a href="{{url("/opslogin/faceid")}}"  class="submit mtop4 ml-2  float-right">clear</a>
                        <button type="submit" class="submit  float-right mtop4">search</button>
                           </div>
                           </div>
                           <!--div class="form-group row">
                           <div class="col-sm-12">
                              
                           </div> </div-->
                         

                     </div>
                  </form>

 <div class="overflowscroll2">
                  <table class="gap">
                      <!--div class="col-lg-12">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/faceid/create")}}"  class="submit mt-0 float-left" style="width:190px;     margin: 0 0px;"> + add new facial id</a>
							   <a href="{{url("/opslogin/faceid/new")}}"  class="submit mt-0 float-left" style="width:190px;     margin: 0 12px;"> New Submission</a>
                           </div>
                    </div--> 
                     <thead>
                        <tr>
                          
                           <th>date</th>
                           <th>block</th>
                           <th>unit</th>
                           <th>name</th>
                          <!-- <th>photo</th>-->
                           <th>relationship</th>
                           <th>approved date</th>
                           <th>actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($faceids)

                       @foreach($faceids as $k => $faceid)
                        <tr>
                           <td class="roundleft">{{date('d/m/y',strtotime($faceid->created_at))}}</td>
                           <td class="spacer">{{isset($faceid->getunit->buildinginfo->building)?$faceid->getunit->buildinginfo->building:''}}</td>

                           <td class="spacer">{{isset($faceid->getunit->unit)?Crypt::decryptString($faceid->getunit->unit):''}}</td>
                           <td class="spacer">{{isset($faceid->user->name)?Crypt::decryptString($faceid->user->name):''}}</td>
                           <!--<td class="spacer noimg">
                              <a data-toggle="modal" data-target="#exampleModalCenter2" data-id="{{$faceid->id}}" class="open-dialog-access">
									<img src="{{url('assets/admin/img/scenery.png')}}">
						         </td>-->
                           <!--td class="spacer"> @if(isset($faceid->face_picture))
                                 <a href="{{$file_path}}/{{$faceid->face_picture}}" target="_blank">
                                    <img src="{{$file_path}}/{{$faceid->face_picture}}" class="viewimg phvert">
                                 </a>
                              @endif</td-->
                           <td class="spacer">{{isset($faceid->optionname->option)?$faceid->optionname->option:''}}</td>
                           <td class="spacer">{{date('d/m/y',strtotime($faceid->updated_at))}}</td>

                           <td class="roundright">
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                        @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/faceid/$faceid->id/edit")}}">Edit</a>
                           @endif
                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/faceid/delete/$faceid->id")}}');">Delete</a>
                           @endif
                                    </div>
                                 </div>
                         
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
				  </div>
                  <div class="col-lg-12">
					<div  class="form-group ">
						@if ($faceids->hasPages())
							<ul class="paginationul">
								{{-- Previous Page Link --}}
								@if ($faceids->onFirstPage())
									<li class="disabled"><span><i class="fa fa-angle-left" aria-hidden="true"></i></span></li>
								@else
									<li><a href="{{ $faceids->appends($_GET)->previousPageUrl() }}" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
								@endif

								@if($faceids->currentPage() > 3)
									<li class="hidden-xs"><a href="{{ $faceids->appends($_GET)->url(1) }}">1</a></li>
								@endif
								@if($faceids->currentPage() > 4)
									<li><span>...</span></li>
								@endif
								@foreach(range(1, $faceids->lastPage()) as $i)
									@if($i >= $faceids->currentPage() - 2 && $i <= $faceids->currentPage() + 2)
										@if ($i == $faceids->currentPage())
											<li class="active"><span>{{ $i }}</span></li>
										@else
											<li><a href="{{ $faceids->appends($_GET)->url($i) }}">{{ $i }}</a></li>
										@endif
									@endif
								@endforeach
								@if($faceids->currentPage() < $faceids->lastPage() - 3)
									<li><span>...</span></li>
								@endif
								@if($faceids->currentPage() < $faceids->lastPage() - 2)
									<li class="hidden-xs"><a href="{{ $faceids->appends($_GET)->url($faceids->lastPage()) }}">{{ $faceids->lastPage() }}</a></li>
								@endif

								{{-- Next Page Link --}}
								@if ($faceids->hasMorePages())
									<li><a href="{{ $faceids->appends($_GET)->nextPageUrl() }}" rel="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
								@else
									<li class="disabled"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span></li>
								@endif
							</ul>
						@endif
					</div>
				</div>	
               </div>
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
                 <input type="hidden" name="Id" id="Id" value="">
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
	                  var image = $('<img></img>');
                     image.attr('src',data['img']);
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

