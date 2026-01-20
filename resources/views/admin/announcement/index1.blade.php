@extends('layouts.adminnew')



@section('content')

@php 
$permission = Auth::user();
@endphp

<div class="status">
  <h1>Announcement History</h1>
</div>
@if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<div class="containerwidth">
          <form action="{{url('/opslogin/announcement/search')}}" method="POST" role="search" class="forunit">
            {{ csrf_field() }}
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-3  col-6 col-form-label">FROM DATE:</label>
                              <div class="col-sm-5 col-6">
                              <div id="sandbox">
                                <input type="text" class="form-control" id="fromdate"  name="startdate"
                    value="<?php echo(isset($startdate)?$startdate:'');?>">

                                   </div>
                              </div>
                           </div>
                </div>
              <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-6 col-form-label">TO DATE:</label>
                              <div class="col-sm-5 col-6">
                              <div id="sandbox2">
             <input type="text" class="form-control" id="todate"  name="enddate"
                    value="<?php echo(isset($enddate)?$enddate:'');?>">
                                   </div>
                              </div>
                           </div>
                </div>
               
               <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-3 pr-0  col-6 col-form-label">ASSIGNED ROLE:</label>
                              <div class="col-sm-8 col-6">
                               {{ Form::select('roles', ['a' => '--All User Group--'] + $roles, $role, ['class'=>'form-control','id'=>'role']) }}
                              </div>
                           </div>
                           </div>
               <div class="col-lg-6"></div>
                       <div class="col-lg-6">
                           <div class="form-group row">
                              <div  class="col-sm-3 "></div>
                              <div class="col-sm-8 col-12">
                                  <a href="{{url("/opslogin/announcement")}}"  class="submit ml-0 ancler mt-2 float-left">CLEAR</a>
                                 <button type="submit" class="submit mt-2 float-right">SEARCH</button>
                              </div>
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

                            $title .= '<li class="nav-item alert alert2"> <a href="#" onclick="'.$delete_function.'" class="closbtn"  data-toggle="tooltip" data-placement="right" title="Delete"><img src="'.url('assets/admin/img/delete.png').'"></a><a class="nav-link" data-toggle="modal" data-target="#'.$link.'" >'.$count.'. '.$news->title.'<p>'.date('d/m/Y', strtotime($news->created_at)).'</p></a></li>';
                           
                            $content .='<div class="modal fade bd-example-modal-lg" id="'.$link.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">'.$count.'. '.$news->title.'</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                           </div>
                           <div class="modal-body">

                              <div class="announce2">
                              '.$news->notes.'</div>';
                          
                           $content .='<div class="announce-img"><a href="'.$file_path.'/'.$news->upload.'" target="_blank"><img src="'.$file_path.'/'.$news->upload.'" class="viewimg"></a>
									</div>';
                        
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
                  <button type="submit" class="submit mt-3 float-center text-center">print</button>
               </div>

        


@endsection