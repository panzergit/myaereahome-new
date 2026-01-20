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
  <h1>magazine</h1>
</div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li    class="activeul"><a href="{{url('/opslogin/magazine#cd')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/magazine/create#cd')}}">Upload new Magazine</a></li>
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
                               <a href="{{url("/opslogin/magazine/create")}}"  class="submit mt-2 ml-3 float-left" style="width:auto"> + add new</a>
                           </div>
                       </div>
                    </div-->
                    @endif
                     <thead>
                        <tr>
                           <th class="width50">s/no</th>
                           <th>Name </th>
                           <th class="widthtb">actions</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($files)
                      

                       @foreach($files as $k => $file)
                        <tr>
                           <td  class="roundleft">{{$k+1}}</td>
                           <td class="spacer">{{$file->docs_file_name}}</td>
                           
                           <td class="roundright">
						            <div class="dropdown">
                                 <div  class=" dropdown-toggle" data-toggle="dropdown">
                                    <div class="three-dots"></div>
                                 </div>
                                 <div class="dropdown-menu">
                                    @if($admin_id ==1)
                                       <a class="dropdown-item" href="{{url("opslogin/magazine/$file->id/edit")}}" >Edit</a>
                                    @endif
                                    @if(isset($permission) && $permission->delete==1)
                                       <a class="dropdown-item" href="#" onclick="delete_record('{{url("opslogin/magazine/delete/$file->id")}}');" >Delete</a>
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

