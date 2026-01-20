@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

      Update User Access Permissions

      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">User Access Permissions </li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="box">

            <div class="box-header">

              <h3 class="box-title">Please fill the form below to update permissions!</h3>

            </div>

            <!-- /.box-header -->

            <div class="box-body">

              @if ($errors->any())

                  <div class="alert alert-danger">

                      <ul>

                          @foreach ($errors->all() as $error)

                              <li>{{ $error }}</li>

                          @endforeach

                      </ul>

                  </div>

              @endif

              @if (session('status'))

                <div class="alert alert-info">

                    {{ session('status') }}

                </div>
              @endif
              
          @if(Session::has('message'))
                <div class="alert alert-{{ Session::get('message-type') }} alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <i class="glyphicon glyphicon-{{ Session::get('message-type') == 'success' ? 'ok' : 'remove'}}"></i> {{ Session::get('message') }}
                </div>
            @endif
             
              {!! Form::model($UserObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/user/rights/'.$UserObj->id)]) !!}

              <div class = "row">

               
                <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'User:') }}

              {{ Form::text('name', $UserObj->name, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Role Title','readonly'=>'readonly']) }}

              </div>

              </div>

              <div class = "col-xs-10">

              <div class = "form-group">
             
              {{ Form::label('name', 'Modules Access Level:') }} 
              <table class="table table-striped " >
                <thead>
                  <tr>    
                    <th>Module <input type="checkbox" id="checkAll" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;">Check All</th>
                    <th style="text-align: center;">View <input type="checkbox" id="checkAllView" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;"></th>
                    <th style="text-align: center;">Add / Create <input type="checkbox" id="checkAllAdd" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;"></th>
                    <th style="text-align: center;">Edit <input type="checkbox" id="checkAllEdit" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;"></th>
                    <th style="text-align: center;">Delete <input type="checkbox" id="checkAllDelete" name="check_all" value="1" class ='form-check-input' style="width: 15px;height: 15px;"></th>
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
              <tr style="background-color: #fff">
                
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
              

              </div>

              </div>


            </div>




            <div class = "form-group">

              {{ Form::submit('Update ', ['class'=>'btn btn-primary']) }}

            </div>  

              {!! Form::close() !!}

            </div>

            <!-- /.box-body -->

          </div>

          <!-- /.box -->

    </section>  

@stop