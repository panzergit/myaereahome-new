
@extends('layouts.adminnew')


@section('content')

@php 
$permission = Auth::user();
@endphp
<div class="status">
  <h1>MANAGE Roles Access Permissions</h1>
</div>

  <div class="containerwidth">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
                  <table class="table usertable ">
                   <!-- <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url('admin/configuration/module/create')}}"  class="submit mt-2 float-left" style="width:200px"> + ADD NEW</a>
                           </div>
                       </div>
                    </div>
                  -->
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>UNIT NO</th>
                           <th>ACTIONS</th>
                        </tr>
                     </thead>
                     <tbody>
                     @if($roles)

                        @foreach($roles as $k => $role)
                        
                        <tr>
                           <td>{{$k+1}}</td>
                           <td>{{$role->name}}</td>
                           
                           <td>
                             @php if($permission->role_id ==1 ){    @endphp
                            <a href="{{url("admin/configuration/menu/$role->id/edit")}}"><img src="{{url('assets/admin/img/Edit.png')}}"></a>

                            <a href="#" onclick="delete_record('{{url("admin/configuration/menu/delete/$role->id")}}');"><img src="{{url('assets/admin/img/delete.png')}}"></a>
                             @php } @endphp
                            </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
@endsection


