@extends('layouts.adminnew')


@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

       Create New Role


      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active"> Role Access Permissions</li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">


             @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif

      <div class="box">

            <div class="box-header">

              <h3 class="box-title">Please fill the form below to new role and access permissions!</h3>

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

              {!! Form::open(['method' => 'POST', 'url' => url('opslogin/configuration/menu'), 'files' => true]) !!}

              <div class = "row">

               

              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Role Title:') }}

              {{ Form::text('name', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Role Title']) }}

              </div>

              </div>

              <div class = "col-xs-10">

              <div class = "form-group">

              {{ Form::label('name', 'Modules Access Level:') }} <input type="checkbox" id="checkAll" name="check_all" value="1" class ='form-check-input' style="width: 17px;height: 17px;"> <b>Check All<b>
              <table class="table table-striped " >
                <thead>
                  <tr>    
                    <th>Module </th>
                    <th></th>
                    <th style="text-align: center;">View</th>
                    <th style="text-align: center;">Add / Create</th>
                    <th style="text-align: center;">Edit</th>
                    <th style="text-align: center;">Delete</th>
                  </tr>
                </thead>

                <tbody>
              @if($modules)

              @foreach($modules as $module)   
              <tr style="background-color: #fff">
                
                <td colspan="2">{{$module->name}}</td>
                <td style="text-align: center;"><input type="checkbox" name="mod_view_{{$module->id}}" value="1" class ='form-check-input' style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_add_{{$module->id}}" value="1" class ='form-check-input' style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_edit_{{$module->id}}" value="1" class ='form-check-input' style="width: 17px;height: 17px;"></td>
                <td style="text-align: center;"><input type="checkbox" name="mod_delete_{{$module->id}}" value="1" class ='form-check-input' style="width: 17px;height: 17px;"></td>
                
              </tr>

              @endforeach

              @endif

              

            </tbody>

        </table>
              

              </div>

              </div>


            </div>



            <div class = "form-group">

              {{ Form::submit('Create ', ['class'=>'btn btn-primary']) }}

            </div>  

              {!! Form::close() !!}

            </div>

            <!-- /.box-body -->

          </div>

          <!-- /.box -->

    </section>  

@stop