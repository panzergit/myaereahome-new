@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Create Task


      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">Task Management</li>

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

              <h3 class="box-title">Please fill the form below to create a task!</h3>

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

              {!! Form::open(['method' => 'POST', 'action' => 'TaskController@store', 'files' => false]) !!}

              <div class = "row">

               

              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Task:') }}

              {{ Form::text('title', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Task Title']) }}

              </div>

              </div>

              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Assigned To:') }}

              {{ Form::select('assigned_to', ['' => '--Select Employee--'] + $users, null, ['class'=>'form-control', 'required' => true]) }}

              </div>

              </div>

              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Start Date:') }}

              {{ Form::date('start_on', null, ['class'=>'form-control', 'required' => true,'placeholder' => 'Select Start Date']) }} 

              </div>

              </div>

              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'End Date:') }}

              {{ Form::date('deadline', null, ['class'=>'form-control', 'required' => true,'placeholder' => 'Select Start Date']) }} 

              </div>

              </div>

             
              <div class = "col-xs-6">

              <div class = "form-group">

              {{ Form::label('name', 'Notes:') }}

              {{ Form::textarea('notes', null, ['class'=>'form-control','required' => false,'placeholder' => 'Enter Addition Notes']) }}

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