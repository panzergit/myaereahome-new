@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Edit My Profile

      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">My Profile</li>

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


          @if(Session::has('message'))
                <div class="alert alert-{{ Session::get('message-type') }} alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <i class="glyphicon glyphicon-{{ Session::get('message-type') == 'success' ? 'ok' : 'remove'}}"></i> {{ Session::get('message') }}
                </div>
            @endif

              
              {!! Form::model($UserObj,['method' =>'PATCH', 'files' => true,'action' => ['UserMoreInfoController@updateprofile']]) !!}

              <div class = "row">

            <div class = "col-ms-6 col-md-6" style="border:1px solid #04a75e; margin: 15px;">
             <div class = "col-xs-12" style="padding: 10px; color: #04a75e; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Personal Details') }}
             </div>

             <div class = "col-xs-4" style="padding: 10px">
             <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                 @php if(isset($UserObj->userinfo->profile_picture) && $UserObj->userinfo->profile_picture !='') {@endphp 
              <img src="{{$img_full_path}}{{$UserObj->userinfo->profile_picture}}"  style="width: 200px; height: 140px;">
              
               @php } @endphp 
              </div>
            </div>
             <div class = "col-xs-8" style="padding: 10px">
             <div class="fileinput-preview thumbnail"  style="width: 50px; height: 50px;">
                 @php if(isset($UserObj->userinfo->profile_picture) && $UserObj->userinfo->profile_picture !='') {@endphp 
              <a href="#" onclick="deleteUserInfo({{$UserObj->id}},{{$UserObj->userinfo->id}},'profile_picture','{{$UserObj->userinfo->profile_picture}}')" title = "Delete" class="btn btn-danger"> <i class="fa fa-trash"></i> </a>
               @php } @endphp 
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Profile Picture') }}
                </div>
                <div class = "col-xs-7">
                  <input type="file" name="profile_picture"  />
                  
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'First Name') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('name', null, ['class'=>'form-control','placeholder' => 'Enter Name']) }}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-5" >
                  {{ Form::label('name', 'Last Name') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('last_name', $UserObj->userinfo->last_name, ['class'=>'form-control','placeholder' => 'Enter Name']) }}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Date of Birth') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('date_of_birth', date('d-m-Y',strtotime($UserObj->userinfo->date_of_birth)), ['class'=>'form-control', 'id' => 'date_of_birth', 'required' => true,'placeholder' => 'Select Date']) }}                   
                </div>
              </div>
            </div>

             <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('email', 'Gender') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::select('gender', ['male' => 'Male', 'female'=>"Female"], $UserObj->userinfo->gender, ['class'=>'form-control']) }}
                </div>
              </div>
            </div>

             <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Nationality') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('nationality',  $UserObj->userinfo->nationality, ['class'=>'form-control','placeholder' => 'Enter Nationality','required' => true]) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Identity Card No') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('identification_no', $UserObj->userinfo->identification_no, ['class'=>'form-control','placeholder' => 'Enter National ID / FIN / Passport NO','required' => true]) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Passport No') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('passport', $UserObj->userinfo->passport, ['class'=>'form-control','placeholder' => 'Enter Passport No.']) }}
                </div>
              </div>
            </div>      

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Mobile Phone') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('phone', $UserObj->userinfo->phone, ['class'=>'form-control','placeholder' => 'Enter Mobile Phone','required' => true]) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Home Tel No') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('homephone', $UserObj->userinfo->homephone, ['class'=>'form-control','placeholder' => 'Enter Homr Tel No.']) }}
                </div>
              </div>
            </div>

             <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Emergency Contact') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('emergency_contact', $UserObj->userinfo->emergency_contact, ['class'=>'form-control','placeholder' => 'Enter Emergency Contact','required' => true]) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Personal Email') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('personal_email', $UserObj->userinfo->personal_email, ['class'=>'form-control','placeholder' => 'Enter Personal Email','required' => true]) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Residential Address') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('local_address', $UserObj->userinfo->local_address, ['class'=>'form-control','placeholder' => 'Enter Local Address']) }}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Permanent Address') }}
                </div>
                <div class = "col-xs-7">
                  {{ Form::text('permanent_address', $UserObj->userinfo->permanent_address, ['class'=>'form-control','placeholder' => 'Enter Permanent Address']) }}
                </div>
              </div>
            </div>

            
            <div class = "col-xs-12" style="padding: 10px">
            <div class = "form-group">

              {{ Form::submit(' + Save', ['class'=>'btn btn-flat bg-olive','style'=>'']) }}

            </div>  
          </div>

            </div>

             

          </div>

          </div>


              {!! Form::close() !!}

            </div>

            <!-- /.box-body -->

          </div>

          <!-- /.box -->

    </section>  

@stop

@section('customJS')
<script>
  $(document).ready(function(e)
  {
    $('#date_of_birth').datepicker({
            autoclose : true,
            format:'dd-mm-yyyy',
            endDate: "0d"
          });
  });
</script>
@endsection