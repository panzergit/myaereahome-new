@extends('layouts.adminnew')

@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

      Key Collection Appointment


      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active"> Appointment</li>

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

              {!! Form::open(['method' => 'POST', 'url' => url('opslogin/takeover_appt'), 'files' => false]) !!}

              <div class = "row">

               

              <div class = "col-xs-3">

              <div class = "form-group">

              {{ Form::label('name', 'Select Date') }}

               {{ Form::date('appt_date', null, ['class'=>'form-control','placeholder' => 'Leave Start Date', 'required' => true]) }}

              </div>

             


              </div>

               <div class = "col-xs-3">

              <div class = "form-group">

              <label for="emp_id">Select Time <sup class="label-red">*</sup></label> 

              {{ Form::select('appt_time', ['' => '--Select Time--']+$times, null, ['class'=>'form-control', 'required' => true]) }}

              </div>

              </div>


              
              

            </div>



            <div class = "form-group">

              {{ Form::submit('Create ', ['class'=>'btn btn-flat bg-olive']) }}

            </div>  

              {!! Form::close() !!}

            </div>

            <!-- /.box-body -->

          </div>

          <!-- /.box -->

    </section>  

@stop