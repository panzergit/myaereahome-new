@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
   $menu =  $permission->check_menu_permission(25,$permission->role_id,1);
   $feedback =  $permission->check_menu_permission(26,$permission->role_id,1);
   $defect =  $permission->check_menu_permission(27,$permission->role_id,1);
   $property =  $permission->check_menu_permission(28,$permission->role_id,1);
   $facility =  $permission->check_menu_permission(29,$permission->role_id,1);
   $vm =  $permission->check_menu_permission(37,$permission->role_id,1);
   $eforms =  $permission->check_menu_permission(39,$permission->role_id,1);
   $payment =  $permission->check_menu_permission(46,$permission->role_id,1);
   $holiday =  $permission->check_menu_permission(53,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
   $dashmenu =  $permission->check_menu_permission(55,$permission->role_id,1);
   $key_setting =  $permission->check_menu_permission(9,$permission->role_id,1);
   $inspection_setting =  $permission->check_menu_permission(57,$permission->role_id,1);
   $sharesetting =  $permission->check_menu_permission(63,$permission->role_id,1);
   $permission = $permission->check_permission(29,$permission->role_id); 
@endphp
<div class="status">
    <h1> Mobile App Dashboard Settings - Update 
  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if( isset($property->view) && $property->view==1 )
                        <li><a href="{{url('/opslogin/configuration/property#settings')}}">Property <br> Settings</a></li>
                     @endif

                     @if(Auth::user()->role_id ==1)
                        <li  ><a href="{{url('/opslogin/configuration/banner#settings')}}">Home Banner  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/ads#settings')}}">Ads  <br>Management</a></li>
                        <li><a href="{{url('/opslogin/configuration/econcierge#settings')}}">E-Cconcierge  <br>Management</a></li>
                     @endif

                     @if((isset($key_setting) && $key_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/collectionappoinment')}}">Key Collection  <br>Appointment Settings</a></li>
                     @endif

                     @if((isset($inspection_setting) && $inspection_setting->view>=1) && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/inspectionappoinment')}}">Defects Inspection  <br>Appointment Settings</a></li>
                     @endif

                     @if(2 ==1)
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/dashboard#settings')}}">Mobile App  <br>Dashboard Settings</a></li>
                     @endif

                     @if(isset($menu->view) && $menu->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/menu#menusettings')}}">Menu  <br>Management</a></li>
                     @endif

                     @if(isset($feedback->view) && $feedback->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/feedback#feedbacksettings')}}">Feedback  <br>Options</a></li>
                     @endif
                     
                     @if(isset($defect->view) && $defect->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/defect#defectsettings')}}">Defects  <br>Location</a></li>
                     @endif
                     
                     @if(isset($facility->view) && $facility->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/facility#facilitysettings')}}">Facility  <br>Type</a></li>
                     @endif
                     
                     @if(isset($vm->view) && $vm->view==1 && $admin_id !=1)
                        <li><a href="{{url('/opslogin/configuration/purpose#visitingsettings')}}">Visiting  <br> Purpose</a></li>
                     @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


        <div class="col-lg-12">
                 {!! Form::model($PropertyObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/configuration/dashboardupdate/'.$PropertyObj->id),'class'=>'forunit','onSubmit'=>"return count_validation()","id"=>"unitform"]) !!}

                  <div class="row">
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-7 col-form-label "> property : <span class="comspan">  {{$PropertyObj->company_name}}</span></label>
                              <div class="col-sm-5">
                               
                              </div>
                           </div>
                </div>
               
                     </div>


                     <div class="row">
                        <div class="col-lg-8">
                        <h3>modules</h3>
						<div class="overflowscroll">
              <table class="table usertable1 ">
                <thead>
                  <tr> 
                     
                    <th> <span style="text-align: left;     padding-left: 0px;" class="">module </th>
                    <th style="text-align: center;"> <div class="">display on home screen
                    </div></th>
                    <th style="text-align: center;"> <div class="">display position
                    </div></th>
                    
                  </tr>
                </thead>

                <tbody>
                @if($modules)
                  @foreach($modules as $module)

                    @php
                      $view =false;
                      $position='';
                      $array_exist=false;
                        if(isset($role_access[$module->id])) {
                          $array_exist = true;
                          if($role_access[$module->id][0] ==1){
                            $view=1;
                            $position=$role_access[$module->id][1];
                            }
                        }
                      @endphp   
                      <tr>
                        
                        <td>{{$module->name}}</td>
                        <td style="text-align: center;">
						<label class="containeruser" style="    margin-bottom: 25px!important;">
                                   <input type="checkbox" name="mod_view_{{$module->id}}"  value="1" class ='viewCheckBox class1' {{ (isset($view)&& $view ==1) ?'checked':'' }}>
                                    <span class="checkmarkuser"></span>
                                    </label>
						
						</td>  
                        <td style="text-align: center;"><input type="number" id="mod_position_{{$module->id}}" name="mod_position_{{$module->id}}" value="{{ (isset($position))?$position:'' }}" class ='class1 clapp' onBlur="check_validation(this.value,'mod_position_{{$module->id}}')"></td>                       
                      </tr>
                  @endforeach
                @endif
            </tbody>

        </table>
       </div>
                           <button type="submit" class="submit float-left mt-3">submit</button>
       
        </div>
        <div class="col-lg-4">
        <img src="{{url('assets/img/mobile2.png')}}" class="appdash">
		 <div class="numbertop">
		 <div class="row">
		 <div class="col-lg-4 col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>1</span></span>
</label>
		 </div>
		  <div class="col-lg-4  col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>2</span></span>
</label>
		 </div>
		  <div class="col-lg-4  col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>3</span></span>
</label>
		 </div>
		  <div class="col-lg-4  col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>4</span></span>
</label>
		 </div>
		  <div class="col-lg-4  col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>5</span></span>
</label>
		 </div>
		  <div class="col-lg-4  col-4 pr52">
		 		 <label class="containerapp">
  <input type="checkbox">
  <span class="checkmarkapp"><span>6</span></span>
</label>
		 </div>
		 </div>


		 </div>
        </div>
        </div>
                     <div class="row">
                        <!--div class="col-lg-8">
                           <button type="submit" class="submit float-left mt-0">SUBMIT</button>
                        </div-->
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
<script>
function count_validation(){

  var numberOfChecked = $('input:checkbox:checked').length;
  if(numberOfChecked <=6){
    return true;
  }
  else{
    var exceed = numberOfChecked - 6;
    alert("Exceeded module seelction, Allowed maximum 6");
    return false;
  }
}

function check_validation(check_val, field_id){
  $("form#unitform :input").each(function(){
    var arr = new Array();
    var input = $(this);
    if(input.attr('type') =='number' && input.val() !=''){
      if(check_val ==input.val() && input.attr('id') != field_id){
        //alert("ID :"+field_id+" Check Val "+check_val+ " Array Val :"+input.val());
        alert("Display position already exist!");
        return false;
       
      }
      if(check_val <=0 || check_val > 6){
        //alert(check_val);
        //alert("ID :"+field_id+" Check Val "+check_val+ " Array Val :"+input.val());
        alert("Display position not valid (Position range between 1-6)");
        return false;
      }
    }
    
  });
  
}

</script>
@stop


