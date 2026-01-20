
@extends('layouts.adminnew')




@section('content')
<style>
.f14{
    font-size: 12px!important;
}
.face14{    margin-top: -6px;}

.checkmarkuser1 {
    position: absolute;
    top: 5px;
    left: 0px;
    height: 19px;
    width: 19px;
    background-color: #D0D0D0;
}
.containeruser1 .checkmarkuser1:after {
    left: 7px;
    top: 3px;
    width: 6px;
    height: 12px;
    border: solid #8F7F65;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
.containeruser1 {
    padding-left: 28px;
}
</style>
  <div class="status">
    <h1>User Management - Update User</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/user')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/user/access')}}">User Access</a></li>
                     <li><a href="{{url('/opslogin/user/create')}}">Create new user</a></li>
                     <li ><a href="{{url('/opslogin/unitlist')}}">Unit information</a></li>
                  </ul>
               </div>
               </div>
<div class="">
                       {!! Form::model($UserObj,['method' =>'PATCH','url' => url('opslogin/user/'.$UserObj->id),'class'=>'forunit']) !!}

                     <div class="row asignbg editbg p-3">
                     @if(@Auth::user()->role_id ==1)
                <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">
          <label>Property:</label>
                </label>
                              <div class="col-sm-5">
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                              </div>
                           </div>
                </div>
                @else
                <input type="hidden" id="property" name="account_id" value="{{ Auth::user()->account_id }}">
                @endif
                
                <div class="col-lg-12" >
                  <div class="" id="primary_div" style="display:none">
                     <div class="form-group">
					 <label class="containeruser1 f14">
                <input type="checkbox"  id="primary" name="primary_contact" value="1" style="margin-bottom: 0px; height: auto;" @if(isset($userPurchaseRec) && $userPurchaseRec->primary_contact ==1) checked="checked" @endif> primary contact
               <span class="checkmarkuser1"></span>
               </label>

                     </div>
                  </div>
               </div> 
             
                <div class="col-lg-3">
                           <div class="form-group ">
          <label>assign role :</label>
          <select class="form-control wauto" id="role" onchange="getunits()" name="role_id">
            @foreach($roles as $role)
               <option value="{{$role->id}}" @if($UserObj->role_id ==$role->id) selected="selected" @endif>{{($role->type ==1)?"AH":"AM"}} - {{$role->name}}</option>
            @endforeach
            </select>
            
                           </div> </div>
                           <div class="col-lg-3">
                           <div class="form-group ">
          <label>first name <span>*</span>: </label>
            {{ Form::text('name', Crypt::decryptString($UserObj->name), ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
						    <div class="form-group ">
          <label>last name <span>*</span>: </label>
            {{ Form::text('last_name', isset($UserMoreInfoObj->last_name)?Crypt::decryptString($UserMoreInfoObj->last_name):'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
						     <div class="form-group ">
          <label>email <span>*</span>: </label>
             {{ Form::text('email', null, ['class'=>'form-control','required' => true]) }}
                           
                           </div>
                           </div>
               

                
						   <div class="col-lg-3">
						      <div class="form-group ">
          <label>contact <span>*</span>: </label>
             {{ Form::text('phone', isset($UserMoreInfoObj->phone)?Crypt::decryptString($UserMoreInfoObj->phone):'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
						      <div class="form-group ">
          <label>company: </label>
            {{ Form::text('company_name', isset($UserMoreInfoObj->company_name)?$UserMoreInfoObj->company_name:'', ['class'=>'form-control']) }}
                           </div>
                           </div>
             
         
  <div class="col-lg-3">
				                           <div class="form-group ">
          <label>Mailing add <span>*</span>: </label>
                       {{ Form::textarea('mailing_address', isset($UserMoreInfoObj->mailing_address)?$UserMoreInfoObj->mailing_address:'', ['class'=>'form-control','required' => true,'rows'=>4]) }}

                           </div>
                           </div>
						    
                           <!--<div class="form-group row">
                              <label  class="col-sm-4 col-6 col-form-label">
          <label>CARD NO : </label>
                </label>
                              <div class="col-sm-8 col-6">
             {{ Form::select('card_nos[]', ['' => '--Select Card--'], null, ['class'=>'form-control','id'=>'card','multiple'=>'multiple','rows'=>4, 'style'=>'height:110px !important','disabled' => 'disabled']) }}
             <input type="hidden" id="card_temp" name="card_temp" value="{{isset($UserObj->userinfo->card_no)?$UserObj->userinfo->card_no:''}}">
                              </div>
                </div> -->
                <div class="col-lg-3">
                           <div class="form-group">
                              <label>password:</label>
                              {{ Form::input('password', 'password','', ['class'=>'form-control','placeholder' => 'Enter Password']) }}
                        
                        </div>
                        </div>
               

           <div class="col-lg-3">
                           <div class="form-group">
          <label>country <span>*</span>: </label>
                              {{ Form::select('country', $countries, isset($UserMoreInfoObj->country)?$UserMoreInfoObj->country:'', ['class'=>'form-control','required' => true]) }}
                           </div>
                           </div>
						   <div class="col-lg-3">
                           <div class="form-group">
          <label>postal code <span>*</span>: </label>
                              {{ Form::text('postal_code', isset($UserMoreInfoObj->postal_code)?$UserMoreInfoObj->postal_code:'', ['class'=>'form-control']) }}
                           </div>
                           </div>
                           <div class="col-lg-3">
                  <div class="" id="faceid_access_div" style="display:none">
                     <div class="form-group face14">
                        <label class="containeruser1 f14">
                              <input type="checkbox" name="faceid_access_permission" id="myCheck" @if($UserMoreInfoObj->faceid_access_permission ==1) checked="checked" @endif>Allow Face ID Access 
                              <span class="checkmarkuser1"></span>
                        </label>
                     </div>
                     <div class="form-group hide" id="area">
                     {{ Form::text('faceid_access_code', isset($UserMoreInfoObj->faceid_access_code)?$UserMoreInfoObj->faceid_access_code:'', ['class'=>'form-control','placeholder' => 'Access Code']) }}
                     </div>
                  </div>
               </div>
               <!--<div class="col-lg-6" id="unit_div">
               
                           <div class="form-group row">
                              <label  class="col-sm-4 col-5 col-form-label">
          <label>BUILDING *: </label>
                </label>
                              <div class="col-sm-8 col-7">
             {{ Form::select('building_no', ['' => '--Select Building--'] + $buildings, null, ['class'=>'form-control','id'=>'building','onchange'=>'getunits()'  ]) }}
                              <input type="hidden" id="building_no" name="building_temp" value="{{isset($UserObj->building_no)?$UserObj->building_no:''}}"></div>
                           </div>
                
               
               <div class="form-group row">
                  <label  class="col-sm-4 col-5 col-form-label">
<label>UNIT NO *: </label>
    </label>
                  <div class="col-sm-8 col-7">
 {{ Form::select('unit_no', ['' => '--Select Unit--'] + $unites, null, ['class'=>'form-control','id'=>'unit','onchange'=>'getcards()'  ]) }}
                  <input type="hidden" id="unit_temp" name="unit_temp" value="{{isset($UserObj->unit_no)?$UserObj->unit_no:''}}"></div>
               </div>
    </div>
    </div> 
    -->
           
	</div>
	
   
              
                
              

            
               @php 
               $env_roles 	= explode(",",env('USER_APP_ROLE'));

               @endphp
               @if(@Auth::user()->role_id ==1 || !in_array($UserObj->role_id,$env_roles))
               <div class="col-lg-12 asignFace">
                  <h2>assign property</h2>
               </div>
                 
                   <div class="overflowscroll">
					 <table class="table usertable1">
                     <thead>
                        <tr>
                           <th>Property Name</th>
                           <th>
						   <label class="containeruser1">
              <input type="checkbox" class="id1" name="property_13" value="1"> Assign
               <span class="checkmarkuser1"></span>
               </label> </th>
                        </tr>
                     </thead>
                     <tbody>
                     @foreach($agent_properties as $property)
                        <tr>
                           <td> {{$property->company_name}}</td>
                          <td>
						  	   <label class="containeruser1" style="    margin-top: -12px;">
              <input type="checkbox" class="class1"  name="property_{{$property->id}}"  value="1" class ='viewCheckBox' {{(in_array($property->id,$assigned_property)) ?'checked':'' }}>
               <span class="checkmarkuser1"></span>
               </label> 
						  </td>
                        </tr>
                     @endforeach
					
                     </tbody>
                  </table>	
					
                    </div>
               @endif
               
               @if(isset($UserMoreInfoObj->status) && $UserMoreInfoObj->status !=1)
                  <div class="row">
                     <div class="col-lg-12">
                        <a href="{{url($return_url)}}"><button type="button" class="submit mt-3 float-right">Back</button></a>
                     </div>
                  </div>
               @else
                  <div class="row">
                     <div class="col-lg-12">
                        <button type="submit" class="submit mt-3 float-right">update</button>
                     </div>
                  </div>
               @endif
         </form>
         {!! Form::close() !!}
               </div>
               </div>
</section>


@stop

