@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(32,$permission->role_id); 
   //print_r($permission);
@endphp

<div class="status">
  <h1>condo document</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/docs-category#cd')}}">Summary</a></li>
                     @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                     <li><a href="{{url('/opslogin/docs-category/create#cd')}}">Add new document</a></li>
                     @endif
                  </ul>
               </div>
               </div>
  <div class="containerwidth">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
                  <table class="table usertable ">
                  @if(isset($permission) && $permission->create==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/docs-category/create")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + ADD NEW dd</a>
                           </div>
                       </div>
                    </div>
                    @endif
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>LOCATION </th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($types)
                      

                       @foreach($types as $k => $type)
                        <tr>
                           <td>{{$k+1}}</td>
                           <td>{{$type->docs_category}}</td>
                           
                           <td>
                           @if(isset($permission) && $permission->edit==1)
                           <a href="{{url("opslogin/docs-category/$type->id/edit")}}"><img src="{{url('assets/admin/img/Edit.png')}}"></a>
                           @endif
                           @if(isset($permission) && $permission->view==1)
                           <a href="{{url("opslogin/docs-files/$type->id")}}"><img src="{{url('assets/admin/img/view-icon.png')}}"></a>
                           @endif

                           @if(isset($permission) && $permission->delete==1)
                           <a href="#" onclick="delete_record('{{url("opslogin/docs-category/delete/$type->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                           @endif
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
@endsection

