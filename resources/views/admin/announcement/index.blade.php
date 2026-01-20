@extends('layouts.adminnew')



@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(1,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>Announcement History</h1>
</div>
@if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/announcement')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/announcement/create')}}">Create new announcement</a></li>
                     @endif
                  </ul>
               </div>
               </div>
<div class="">
          <form action="{{url('/opslogin/announcement/search')}}" method="POST" role="search" class="forunit">
            {{ csrf_field() }}
                     <div class="row asignbg">
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>from date:</label>
                              <div id="sandbox">
                                <input type="text" class="form-control" id="fromdate"  name="startdate"
                    value="<?php echo(isset($startdate)?$startdate:'');?>">

                                   </div>
                           </div>
                </div>
              <div class="col-lg-3">
                           <div class="form-group">
                              <label>to date:</label>
                              <div id="sandbox2">
             <input type="text" class="form-control" id="todate"  name="enddate"
                    value="<?php echo(isset($enddate)?$enddate:'');?>">
                                   </div>
                           </div>
                </div>
               
               <div class="col-lg-3">
                           <div class="form-group ">
                              <label>assigned role:</label>
                               {{ Form::select('roles', ['a' => '--All User Group--'] + $roles, $role, ['class'=>'form-control','id'=>'role']) }}
                           </div>
                           </div>
                       <div class="col-lg-3">
                           <div class="form-group mt0-4">
                                  <a href="{{url("/opslogin/announcement")}}"  class="submit  ml-2  float-right">clear</a>
                                 <button type="submit" class="submit  float-right">search</button>
                           </div>
                           </div>
                     </div>
                  </form>

                  
                  <div class="">
                       @php
 
                      if($announcements){
                        $title='';
                         $content='';
                         $count =0;
                          foreach($announcements as $k => $news){

                            $link = 'link'.$k;
                            $count++;
                           
                           //print_r($news->role);
                           //$sent_to = isset($news->role->name)?$news->role->name:'All Roles';
                           $url = url("opslogin/announcement/delete/$news->id");
                           
                           $delete_function = "delete_record('".$url."');";

                           $announcment_obj = new \App\Models\v2\Announcement();
                           $results = $announcment_obj->roles($news->roles);
                           $role_string = ''; 
                           foreach($results as $result){
                              $role_string .= $result->name .", ";
                           }
                              $role_string = substr($role_string,0,-2);

                           $title .= '<li class="nav-item alert alert2">';
                           if(isset($permission) && $permission->delete==1 && $admin_id !=1){
                              $title .= '<a href="#" onclick="'.$delete_function.'" class="closbtn"  data-toggle="tooltip" data-placement="right" title="Delete"><img src="'.url('assets/admin/img/deleted.png').'" class="annondelt"></a>';
                           }
                            $title .= '<a class="nav-link" data-toggle="modal" data-target="#'.$link.'" >'.$count.'. '.$news->title.'<p>'.date('d/m/Y', strtotime($news->created_at)).'</p> <p>Assigned To : '. $role_string.'</p></a></li>';
                           
                            $content .='<div class="modal  fade bd-example-modal-lg" id="'.$link.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog modal-md" role="document">
					      
                        <div class="modal-content annonspopup">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span class="colserig" aria-hidden="true">&times;</span>
                              </button>
							  <div class="clearfix"></div>
                              <h5 class="modal-title text-center" id="exampleModalLabel">'.$count.'. '.$news->title.'</h5>
                         
                        
                           <div class="modal-body">';
                           
                          
                             
                              $content .='<div class="announce2">
                              '.nl2br($news->notes).'</div>';
 $content .='<div class="announce2p"><i>
                              '.'Assigned Group :'.$role_string.'</i></div>';
                           if(isset($news->upload) && $news->upload !=''){
                              $upload_1_file = explode(".",$news->upload);
                              if($upload_1_file[1] =="pdf" || $upload_1_file[1] =="PDF"){
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload.'" target="_blank"><img src="'.$icon_path.'img/pdf.png" class="pdficon"></a>
                              </div>';
                              } else{
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload.'" target="_blank"><img src="'.$file_path.'/'.$news->upload.'" class="viewimg1"></a>
                              </div>';
                              }
                           }
                           if(isset($news->upload_2) && $news->upload_2 !=''){
                              $upload_2_file = explode(".",$news->upload_2);
                              if($upload_2_file[1] =="pdf" || $upload_2_file[1] =="PDF"){
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_2.'" target="_blank"><img src="'.$icon_path.'img/pdf.png" class="pdficon"></a>
                              </div>';
                              } else{
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_2.'" target="_blank"><img src="'.$file_path.'/'.$news->upload_2.'" class="viewimg1"></a>
                              </div>';
                              }
                           }
                           if(isset($news->upload_3) && $news->upload_3 !=''){
                              $upload_3_file = explode(".",$news->upload_3);
                              if($upload_3_file[1] =="pdf" || $upload_3_file[1] =="PDF"){
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_3.'" target="_blank"><img src="'.$icon_path.'img/pdf.png" class="pdficon"></a>
                              </div>';
                              } else{
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_3.'" target="_blank"><img src="'.$file_path.'/'.$news->upload_3.'" class="viewimg1"></a>
                              </div>';
                              }
                           }
                           if(isset($news->upload_4) && $news->upload_4 !=''){
                              $upload_4_file = explode(".",$news->upload_4);
                              if($upload_4_file[1] =="pdf" || $upload_4_file[1] =="PDF"){
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_4.'" target="_blank"><img src="'.$icon_path.'img/pdf.png" class="pdficon"></a>
                              </div>';
                              } else{
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_4.'" target="_blank"><img src="'.$file_path.'/'.$news->upload_4.'" class="viewimg1"></a>
                              </div>';
                              }
                           }if(isset($news->upload_5) && $news->upload_5 !=''){
                              $upload_5_file = explode(".",$news->upload_5);
                              if($upload_5_file[1] =="pdf" || $upload_5_file[1] =="PDF"){
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_5.'" target="_blank"><img src="'.$icon_path.'img/pdf.png" class="pdficon"></a>
                              </div>';
                              } else{
                              $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload_5.'" target="_blank"><img src="'.$file_path.'/'.$news->upload_5.'" class="viewimg1"></a>
                              </div>';
                              }
                           }
                        
                           $content .='</div>
                           </div>
                     </div>
                  </div>';
                          }
                        }
                       @endphp
                       
					   
                       <ul class="nav nav-pills navover" role="tablist">
                        {!!$title!!}
                     </ul>
                     <!-- Tab panes -->
                     <div class="tab-content">
                        {!!$content!!}
                     </div>
                     
                     </div>
                  </div>
                  <!--button type="submit" class="submit mt-3 float-center text-center">print</button-->
               </div>

        


@endsection