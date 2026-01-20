@extends('layouts.adminnew')



@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

       Update Announcement 

      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">HR Announcement </li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="box">

          

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
             
              {!! Form::model($newsObj,['method' =>'PATCH','files' => true,'url' => url('opslogin/announcement/'.$newsObj->id)]) !!}

              <div class = "row">
              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Title') }}

              {{ Form::text('title', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter File Title']) }}

              </div>

              </div>

            
               <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('role', 'Roles') }}

              {{ Form::select('roles', ['0' => '--All Roles--'] + $roles, null, ['class'=>'form-control']) }}

              </div>

              </div>
               <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Notes') }}

              {{ Form::textarea('notes', null, ['class'=>'form-control','required' => false,'placeholder' => 'Enter Addition Notes']) }}

              </div>

              </div>

            </div>



            <div class = "form-group">

              {{ Form::submit('Update ', ['class'=>'btn btn-flat bg-olive']) }}

            </div>  

              {!! Form::close() !!}

            </div>

            <!-- /.box-body -->

          </div>

          <!-- /.box -->

    </section>  

@stop