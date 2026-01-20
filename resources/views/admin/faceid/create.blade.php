@extends('layouts.adminnew')




@section('content')
@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $faceid_count = $permission->noOfFaceids($account_id);
   $permission = $permission->check_permission(50,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.btnpicture {
    background: transparent;
    border: none;
    height: 36px!important;
    margin-bottom: 0px!important;
    margin-left: -10px;
    color: #fff!important;
    box-shadow:none;
}
.btnpicture:focus {
    background: transparent;
    border: none;
    height: 36px!important;
    margin-bottom: 0px!important;
    margin-left: -10px;
    color: #fff!important;
    box-shadow:none;
}
input[type=file]::file-selector-button {
  border: 2px solid #efefef;
  border-radius: .9em;
  padding: 0px 10px!important;
  font-size: 1rem;
}

input[type=file]::file-selector-button:hover {
  border: 2px solid #efefef;
  border-radius: .9em;
}
.padtwo{padding:2px 20px!important}
.faclab1{
    padding-top: 8px!important;
}
.mmt5 {
    margin-top: -4px;
}
.faclab2{ padding-top: 10px!important;}
</style>

<!-- Content Header (Page header) -->

  <div class="status">
    <h1>facial recognition - upload </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li><a href="{{url('/opslogin/faceid#fi')}}">Summary</a></li>
					  <li><a href="{{url('/opslogin/faceid/new')}}">New Submission @if(isset($faceid_count) && $faceid_count >0 )
                  <span class="notification17">{{$faceid_count}}</span>
                  @endif</a></li>
                     <li   class="activeul"><a href="{{url('/opslogin/faceid/create')}}">Add new facial ID</a></li>
                  </ul>
               </div>
               </div>
      
      <div class="">
                {!! Form::open(['method' => 'POST','class'=>'forunitvisit ', 'id'=>"reg_form", 'url' => url('opslogin/faceid'), 'files' => true]) !!}
                     <div class="">
                        <div class=" row sectionone asignbg forunit">
                        @if(Auth::user()->role_id ==3 || 1==1)
                          
                              <div class="col-lg-6">
                                 <div class="form-group">
                                       <label>role:</label>
                                       {{ Form::select('role_id', ['' => '--Select Role--']+ $roles, null, ['class'=>'form-control wauto','id'=>'role' ,'onchange'=>'getmanagerlists()']) }}
                                 </div>
                              </div>
                         
                              <div class="col-lg-6" id="buildingfld" style="display:block">
                                 <div class="form-group ">
                                    <label>
                                     Building:
                                    </label>
                                    {{ Form::select('building_no', ['' => '--Select Building--']+$buildings, null, ['class'=>'form-control','id'=>'build_temp','onchange'=>'getunitlists()' ]) }}
                                 </div>
                              </div>
                              <div class="col-lg-6" id="unitfld" style="display:block">
                                 <div class="form-group ">
                                    <label>
                                     unit:
                                    </label>
                                    {{ Form::select('unit_no', ['' => '--Select Unit--']+$unites, null, ['class'=>'form-control','id'=>'unit_temp','onchange'=>'getunitusernewlists()' ]) }}
                                 </div>
                              </div>
                            

                              <div class="col-lg-6" style="display:block">
                                 <div class="form-group ">
                                    <label>
                                  user<span>*</span>:
                                    </label>
                                    <select class="form-control" id="user" required="" name="user_id">
                                       <option value="" selected="selected">--Choose User--</option>
                                       @if($users)
                                          @foreach($users as $user)
                                             <option value="{{$user->user_id}}" >{{Crypt::decryptString($user->first_name)}} {{Crypt::decryptString($user->last_name)}}</option>
                                          @endforeach
                                       @endif
                                    </select>
                                    
                                 </div>
                              </div>
                           @endif
                        
                              <div class="col-lg-6">
                                 <div class="form-group ">
                                    <label class="">
                                     facial picture:
                                    </label>
                                    <input id="picture" name="picture" class="form-control " type="file">

                                 </div>
                              </div>
                       
                          @if(Auth::user()->role_id ==3 || 1==1)
                        
                              <div class="col-lg-6">
                                 <div class="form-group ">
                                    <label class="faclab1">
                                     relationship:
                                    </label>
                                    {{ Form::select('option_id', ['' => '--Choose Relationship--']+$relationships, null, ['class'=>'form-control','required' => true,'id'=>'option','onchange'=>'getothers()']) }}
                                    
                                 </div>

                                 <div class="form-group" id="otherdiv" style="display:none;">
                                    <label class="faclab1">
                                     please specify:
                                    </label>
                                      {{ Form::text('others', null, ['class'=>'form-control']) }}
                                 </div>
                              </div>
                         
                         @endif

                       
                    
                  
               </div>
			    <div class="row">
			    <div class="col-lg-12 " id="submit_btn_div">
                        <input type="hidden" id="userroles" value="{{env('USER_APP_ROLE')}}" >
                        <input type="submit" class="submit mt-2 float-right " value="submit" style="    height: auto;">
                        <!--input type="submit" class="submit2 mt-3 ml-3 float-right mlres" value="Submit"-->
                        <!--<button type="submit" class="submit2 mt-3 ml-3 float-right mlres">SUBMIT</button>-->
                        </div>
                        </div>
			   </form>
         </div>
      </section>


@stop