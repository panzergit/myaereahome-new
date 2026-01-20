@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
   $module =  $permission->check_menu_permission(22,$permission->role_id,1);
   $role =  $permission->check_menu_permission(23,$permission->role_id,1);
   $building =  $permission->check_menu_permission(49,$permission->role_id,1);
   $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
   $sharesetting =  $permission->check_menu_permission(63,$permission->role_id,1);
   $permission = $permission->check_permission(23,$permission->role_id); 
@endphp

<!-- Content Header (Page header) -->

  <div class="status">
    <h1>add new block </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
                     @if(isset($role->view) && $role->view==1 )
                        <li ><a href="{{url('/opslogin/configuration/role#rolesettings')}}">Manage Role </a></li>
                     @endif

                     @if(isset($building->view) && $building->view==1 )
                        <li  class="activeul"><a href="{{url('/opslogin/configuration/building#buildingsettings')}}">Manage Block </a></li>
                     @endif

                     @if(isset($unit->view) && $unit->view==1 )
                        <li ><a href="{{url('/opslogin/configuration/unit#unitsettings')}}">Manage Unit </a></li>
                     @endif

                     @if(isset($sharesetting->view) && $sharesetting->view==1 )
                        <li><a href="{{url('/opslogin/configuration/sharesettings#unitsettings')}}">Manage Mgmt/Sinking Fund </a></li>
                     @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/configuration/building'), 'files' => false]) !!}

                  <div class="row">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-4">
                           <div class="form-group ">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                
                 <!--<div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-3 col-form-label">BLOCK :</label>
                              <div class="col-sm-5 pl-0">
                                {{ Form::text('building', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Building Name']) }}
                              </div>
                           </div>
               
                </div> -->
                </div>
                <div class="row">

                @for($i=1;$i<=5;$i++)
                  @php
                     if($i ==1){
                        $display_style = "";
                        $required = "true";
                        }
                     else
                        $display_style = "display:none";
                        $required = "false";
                  @endphp
                               

                  <div class=" col-lg-4" id="add_field{{$i}}" style="{{$display_style}}">
							<div class=" " id="tbody">
							
							   <div class="clbord clfet">
                          
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">block {{$i}}:</label>
                              <div class="col-sm-8">
                                {{ Form::text('building_'.$i, null, ['class'=>'form-control',($i ==1)?"required":"",'placeholder' => 'Enter Block No']) }}
                              </div>
                           </div>
                        </div>
						   </div>
							
						</div>
                  @endfor
				  </div> 
                              <div class="row" id="buttonsection">
                              <div class="col-lg-4">
                              <a class="addrow"
                           id="addBtn" type="button" onclick="showmore()">
                                       <img src="{{url('assets/img/plus.png')}}" class="upimg"/>
<br>		 Add Block
                        </a></div>
						</div>
                        <input type="hidden" id="rowcount" value="1">
                        <input type="hidden" id="maxcount" value="6">

              
			      <div class="row">
              <div class="col-lg-12">
              <div class="" id="add_field">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
</div></div>
              
 
                <!--<div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">BLOCK NUMBER:<br>
                              Only supports numbers</label>
                              <div class="col-sm-5">
                                {{ Form::text('building_no', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Building Number']) }}
                              </div>
                           </div>
               
                </div> -->
               
                     </div>

          
                    
                     <!--div class="row">
                        <div class="col-lg-6">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div-->
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop