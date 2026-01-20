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
                        <li><a href="{{url('/opslogin/docs-category/create#cd')}}">Upload new document</a></li>
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
<div class="overflowscroll2">
                  <table class="gap">
                  @if(isset($permission) && $permission->create==1)
                    <!--div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/docs-category/create")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + add new</a>
                           </div>
                       </div>
                    </div-->
                    @endif
                     <thead>
                        <tr>
                           <th class="width50">s/no</th>
                           <th>category </th>
                           <th class="widthtb">actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($types)
                      

                       @foreach($types as $k => $type)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           <td class="spacer">{{$type->docs_category}}</td>
                           
                           <td class="roundright">
						    <div class="dropdown">
                                    <div  class=" dropdown-toggle" data-toggle="dropdown">
                                       <div class="three-dots"></div>
                                    </div>
                                    <div class="dropdown-menu">
                                  @if(isset($permission) && $permission->edit==1)
                           <a class="dropdown-item" href="{{url("opslogin/docs-category/$type->id/edit")}}" >Edit</a>
                           @endif
                           @if(1==2)
                           <a class="dropdown-item" href="{{url("opslogin/docs-files/view/$type->id")}}" >View</a>
                           @endif

                           @if(isset($permission) && $permission->delete==1)
                           <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/docs-category/delete/$type->id")}}');" >Delete</a>
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
               </div>
@endsection

