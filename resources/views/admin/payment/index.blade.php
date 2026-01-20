@extends('layouts.adminnew')


@section('content')

<div class="status">
  <h1>MANAGE PROPERTY</h1>
</div>

  <div class="containerwidth">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
@endif
                  <table class="table usertable usertable2">
                     @if(Auth::user()->role_id ==1)
                    <div class="row">
                      <div class="col-lg-4">
                         <div class="form-group row">
                               <a href="{{url("/opslogin/configuration/property/create")}}"  class="submit ml-3 mt-2 float-left" style="width:auto"> + ADD NEW</a>
                           </div>
                       </div>
                    </div>
                     @endif
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>LOGO</th>
                           <th>PROPERTY</th>
                           <th>CONTACT NO</th>
                           <th>CONTACT EMAIL</th>
                           <th>ACTION</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if($properities)

                        @foreach($properities as $k => $property)
                        <tr>
                           <td>{{$k+1}}</td>
                           <td>@if(!empty($property->company_logo))
                                       <div class="image-upload">
                                          <label for="file-input">
                                          <img src="{{$file_path}}/{{$property->company_logo}}" class="viewimg"/>
                                          </label>
                                       </div>
                                    @endif</td>
                           <td>{{$property->company_name}}</td>
                           <td>{{$property->company_contact}}</td>
                           <td>{{$property->company_email}}</td>
                           <td class="swidth"><a href='{{url("opslogin/configuration/property/$property->id/edit")}}' alt="Edit" title="Edit"><img src="{{url('assets/admin/img/Edit.png')}}"></a>
                           @php if(Auth::user()->role_id ==1){ @endphp
                             
                           <a href='{{url("opslogin/configuration/property/access/$property->id")}}'><img src="{{url('assets/admin/img/setting.png')}}" alt="Modules Settings" title="Modules Settings" ></a>
                           @if($property->status ==0)
                              <a href="#" alt="Activate" title="Activate" onclick="activate_record('{{url("opslogin/configuration/property/activate/$property->id")}}');"><img src="{{url('assets/admin/img/deactive.png')}}"></a>
                              @else
                              <a href="#"  alt="De-Activate" title="De-Activate"  onclick="deactivate_record('{{url("opslogin/configuration/property/deactivate/$property->id")}}');"><img src="{{url('assets/admin/img/active.png')}}"></a>
                              @endif
                           @php } @endphp
                           </td>
                        </tr>
                       @endforeach

                      @endif   
                     </tbody>
                  </table>
               </div>
@endsection


