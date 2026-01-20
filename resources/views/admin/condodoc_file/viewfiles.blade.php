@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(27,$permission->role_id); 
@endphp

<div class="status">
  <h1>CONDO DOCUMENT - FILES</h1>
</div>

  <div class="containerwidth">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif

<form action="" class="forunit">

<div class="row">
					 <div class="col-lg-8">
                           <div class="form-group row">
                              <label class="col-sm-4 col-form-label">
					<label>DOCUMENT FILE CATEGORY:</label>
							  </label>
                              <div class="col-sm-6"><input type="text" class="form-control" value="{{$category->docs_category}}">

                              </div>
                           </div>
						    </div>
                      </div>
                      
                  <table class="table usertable ">
                  @if(isset($permission) && $permission->create==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/docs-files/addfile/$category->id")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + ADD NEW</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <thead>
                        <tr>
                           <th>S/NO</th>
                           <th>FILE NAME </th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($files)
                      

                       @foreach($files as $k => $file)
                        <tr>
                           <td>{{$k+1}}</td>
                           <td>{{$file->docs_file_name}}</td>
                           
                           <td>
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/docs-files/$file->id/edit")}}"><img src="{{url('assets/admin/img/Edit.png')}}"></a>
                           @endif

                           @if(isset($permission) && $permission->delete==1)
                           <a href="#" onclick="delete_record('{{url("opslogin/docs-files/delete/$file->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                           @endif
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>


               </div>
</form>
@endsection

