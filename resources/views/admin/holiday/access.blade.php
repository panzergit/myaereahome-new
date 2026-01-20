@extends('layouts.adminnew')


@section('content')


<div class="status">
    <h1> Property Modules - Update
  </h1>
  </div>

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


        <div class="containerwidth">
                 {!! Form::model($PropertyObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/configuration/property/accessupdate/'.$PropertyObj->id),'class'=>'forunit']) !!}

                  <div class="row">
                 <div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-7 col-form-label pl-0">Property  : <span class="comspan">  {{$PropertyObj->company_name}}</span></label>
                              <div class="col-sm-5">
                               
                              </div>
                           </div>
                </div>
               
                     </div>


                     <div class="row">
                       
                        <h3>MODULES</h3>
              <table class="table usertable ">
                <thead>
                  <tr>    
                    <th>Module <div class="form-group form-check"><input type="checkbox" id="checkAll" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Check All</div></th>
                    <th style="text-align: center;"> <div class="form-group form-check"><input type="checkbox" id="checkAllView" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Status
                    </div></th>
                    
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
                
              </tr>

              @endforeach

              @endif

              

            </tbody>

        </table>
            
                     <div class="row">
                        <div class="col-lg-11">
                           <button type="submit" class="submit ml-3 mt-2 float-right">SUBMIT</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


