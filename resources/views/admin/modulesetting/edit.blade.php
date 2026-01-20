@extends('layouts.adminnew')


@section('content')


<div class="status">
    <h1> Roll Access Permissions - Update
  </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


        <div class="containerwidth">
                 {!! Form::model($RoleObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/configuration/menu/'.$RoleObj->id),'class'=>'forunit']) !!}

                  <div class="row">
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">Role :</label>
                              <div class="col-sm-5">
                                {{ Form::text('name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Role Title']) }}
                              </div>
                           </div>
                </div>
               
                     </div>


                     <div class="row">
                       
                        <h3>SYSTEM ACCESS</h3>
              <table class="table usertable ">
                <thead>
                  <tr>    
                    <th>Module <div class="form-group form-check"><input type="checkbox" id="checkAll" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Check All</div></th>
                    <th style="text-align: center;"> <div class="form-group form-check"><input type="checkbox" id="checkAllView" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">View
                    </div></th>
                    <th style="text-align: center;"><div class="form-group form-check"><input type="checkbox" id="checkAllAdd" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Add / Create </div></th>
                    <th style="text-align: center;"><div class="form-group form-check"><input type="checkbox" id="checkAllEdit" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Edit</div></th>
                    <th style="text-align: center;"><div class="form-group form-check"><input type="checkbox" id="checkAllDelete" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Delete </div></th>
                  </tr>
                </thead>

                <tbody>
              @if($modules)

              @foreach($modules as $module)
              @php
              $view =false;
              $create=false;
              $edit =false;
              $delete =false;
              $array_exist=false;
                if(isset($role_access[$module->id])) {
                  $array_exist = true;
                  if($role_access[$module->id][0] ==1)
                    $view=1;
                  if($role_access[$module->id][1] ==1)
                    $create=1;
                  if($role_access[$module->id][2] ==1)
                    $edit=1;
                  if($role_access[$module->id][3] ==1)
                    $delete=1;
                }
              @endphp   
              <tr >
                
                <td>{{$module->name}}</td>
                <td style="text-align: center;"><input type="checkbox" name="mod_view_{{$module->id}}"  value="1" class ='viewCheckBox' {{ ($view ==1) ?'checked':'' }} style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_add_{{$module->id}}" value="1" class ='addCheckBox' {{ ($create ==1) ?'checked':'' }} style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_edit_{{$module->id}}" value="1" class ='editCheckBox' {{ ($edit ==1) ?'checked':'' }} style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_delete_{{$module->id}}" value="1" class ='deleteCheckBox' {{ ($delete ==1) ?'checked':'' }} style="width: 17px;height: 17px;"></td>
                
              </tr>

              @endforeach

              @endif

              

            </tbody>

        </table>
            
                     <div class="row">
                        <div class="col-lg-11">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


