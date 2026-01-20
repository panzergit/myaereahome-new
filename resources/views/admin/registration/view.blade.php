
@extends('layouts.adminnew')

@section('content')
@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(7,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.faceimg img{
    vertical-align: middle;
    border-style: none;
    width: 100px;
    height: 20px;
    object-fit: contain;
}
</style>
  <div class="status">
    <h1>Registration - Info</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                   <ul class="summarytab">
                     <li   ><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     @endif
                     @if(isset($permission) && $permission->create==1 )
                        <li><a href="#myModalcnf"  data-toggle="modal" >Import from CSV</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                     @endif
                     @if(isset($permission) && $permission->edit==1 )
                        <li class="activeul"><a href="{{url('/opslogin/registrations')}}">Registrations</a></li>
                     @endif
                  </ul>
               </div>
               </div>
              
                
              
               <div class="container forunit ">
                <!--div class="col-lg-3">
                  <div class="form-group ">
                     <label>Assign role:</label>
                  </div> 
               </div-->
	<div class="row asignbg editbg">
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Block  : </label>
                <h4>{{$regObj->buildinginfo->building}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Unit  : </label>
                <h4>{{isset($regObj->getunit->unit)?"#".Crypt::decryptString($regObj->getunit->unit):''}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>first name : </label>
                <h4>{{$regObj->first_name}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>last name  : </label>
                <h4>{{$regObj->last_name}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>contact No : </label>
                <h4>{{$regObj->phone}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>country : </label>
                <h4>{{isset($regObj->getcountry->country_name)?$regObj->getcountry->country_name:null}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>mailing Address : </label>
                <h4>{{$regObj->mailing_address}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>postal code : </label>
                <h4>{{$regObj->postal_code}}</h4>
            </div>
        </div>
		<div class="col-lg-4 col-6">
            <div class="form-group">
                <label>email  : </label>
                <h4>{{$regObj->email}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>I am registering as : </label>
                <h4>{{isset($regObj->role->name)?$regObj->role->name:''}}</h4>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Created By : </label>
                <h4>{{isset($regObj->user->name)?Crypt::decryptString($regObj->user->name):''}}</h4>
            </div>
        </div>
      @if($regObj->role_id ==29)
		<div class="col-lg-2 col-6 faceimg">
            <div class="form-group">
                <label> Tenancy agreement </label>
                @if($regObj->contract_file !='')
                <h4><a href="{{$img_full_path}}/{{$regObj->contract_file}}" target="_blank"><img src="{{url('assets/admin/img/Condo.png')}}" id="#preview-file-input"  class="viewimg"></a></h4>
                @endif
            </div>
        </div>
      @endif
		<div class="col-lg-2 col-6 faceimg">
            <div class="form-group">
                <label> Face recognition photo: </label>
                <h4><a href="{{$img_full_path}}/{{$regObj->profile_picture}}" target="_blank"><img src="{{$img_full_path}}/{{$regObj->profile_picture}}" class="proaraimg"></a></h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>1st Vehicle Car plate : </label>
                <h4>{{$regObj->first_vehicle}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>2nd Vehicle Car plate: </label>
                <h4>{{$regObj->second_vehicle}}</h4>
            </div>
        </div>
         <div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Receive intercom call : </label>
                <h4>{{($regObj->receive_intercom==1)?"Yes":"No"}}</h4>
            </div>
        </div>
        @if($regObj->status ==3)
        <div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Rejected Reason : </label>
                <h4>{{$regObj->reason}}</h4>
            </div>
        </div>
        @endif
        @if($ownersObj)
            @foreach($ownersObj as $k => $Owner)
                <div class="col-lg-4 col-6">
                    <div class="form-group">
                        <label>Unit Owner Name {{$k+1}}: </label>
                        <h4>{{$Owner->first_name}} {{$Owner->last_name}} - Ph: {{$Owner->phone}}</h4>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    
    
             <div class="row">
                     <div class="col-lg-12">
                        <div class="form-group">
                            @if(isset($permission) && $permission->edit==1 && $regObj->status !=2)
                                <a  href="{{url("opslogin/registrations/approve/$regObj->id")}}"><button type="submit" class="submit mt-1 mb-5 float-left">Approve</button></a>
                            @endif
                            @if(isset($permission) && $permission->edit==1 && $regObj->status ==1)
                                <button type="submit" class="Delete open-dialog mt-1 mb-5 float-left ml-5" data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$regObj->id}}" >Reject</button>
                            @endif
                        </div>
                     </div>
                  </div>   
      </div>
	  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	   <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/registrations/cancelregistration'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Registration  - Reject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>REASON:</label>
                {{ Form::textarea('reason', null, ['class'=>'form-control', 'required' => true,'rows'=>4]) }}
              </div>
              <div class="modal-body">
               <input type="hidden" name="bookId" id="bookId" value="">
               <input type="hidden" name="status"value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
          </div>
   </div>
</section>


@stop

