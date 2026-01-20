@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        View Profile

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

             <div class="row"> 
      
      <div class="col-md-12 text-right">
     
      <a href="{{url("editmyprofile/$UserObj->name")}}" class="btn btn-flat bg-olive"><span class="fa fa-edit"> </span>  Edit My Profile</a>
      

     
       
      </div>
      
      
      </div>


              {!! Form::model($UserObj,['method' =>'PATCH', 'files' => true,'url' => url('opslogin/user/'.$UserObj->id)]) !!}

              <div class = "row">

            <div class = "col-ms-6 col-md-6" style="border:1px solid #b07cc6; margin: 15px;">
             <div class = "col-xs-12" style="padding: 10px; color: #b07cc6; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Personal Details') }}
             </div>

             <div class = "col-xs-4" style="padding: 10px">
             <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                 @php if(isset($UserObj->userinfo->profile_picture) && $UserObj->userinfo->profile_picture !='') {@endphp 
              <img src="{{$img_full_path}}{{$UserObj->userinfo->profile_picture}}"  style="width: 200px; height: 140px;">
              
               @php } @endphp 
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'First Name') }}
                </div>
                <div class = "col-xs-7">{{$UserObj->name}}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-5" >
                  {{ Form::label('name', 'Last Name') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->last_name}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Date of Birth') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->date_of_birth}} 
                </div>
              </div>
            </div>

             <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('email', 'Gender') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->gender}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Nationality') }}
                </div>
                <div class = "col-xs-7">
                   {{ $UserObj->userinfo->nationality}} 
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Identity Card No') }}
                </div>
                <div class = "col-xs-7">
                   {{ $UserObj->userinfo->identification_no}} 
                </div>
              </div>
            </div>
           
            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Passport No') }}
                </div>
                <div class = "col-xs-7">
                 {{ $UserObj->userinfo->passport}} 
                </div>
              </div>
            </div>    
            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Mobile Phone') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->phone}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Home Tel No') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->homephone}}
                </div>
              </div>
            </div>

            

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Emergency Contact') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->emergency_contact}}
                </div>
              </div>
            </div>

             <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Personal Email') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->email}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Residential Address') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->local_address}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class = "form-group">
                <div class = "col-xs-5">
                  {{ Form::label('phone', 'Permanent Address') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->permanent_address}}
                </div>
              </div>
            </div>


            
           
           

            </div>

             <div class = "col-ms-6 col-md-5" style="border:1px solid #2ae0bb;margin: 15px;">

            <div class = "col-xs-12" style="padding: 10px; color: #2ae0bb; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Employee Details') }}
             </div>

             

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-6">
                  {{ Form::label('name', 'Employee ID') }}
                </div>
                <div class = "col-xs-6">
                  {{$UserObj->userinfo->emp_id}}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Department') }}
                </div>
                <div class = "col-xs-6">
                  @if(isset($UserObj->userinfo->departments->department))
                    {{$UserObj->userinfo->departments->department}}
                  @endif
                  @if($UserObj->userinfo->manager ==1)
                   <br>(Manager / HOD)
                  @endif
                </div>
              </div>
            </div>


            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Designation') }}
                </div>
                <div class = "col-xs-6">
                  {{$UserObj->userinfo->designation}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-6">
                  {{ Form::label('name', 'Date Of Joining') }}
                </div>
                <div class = "col-xs-6">
                  @if(!empty($UserObj->userinfo->date_of_joining))
                  {{date('d/m/Y',strtotime($UserObj->userinfo->date_of_joining))}} 
                  @endif
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-6">
                  {{ Form::label('name', 'Probation End Date') }}
                </div>
                <div class = "col-xs-6">
                  @if(!empty($UserObj->userinfo->probation_end))
                  {{date('d/m/Y',strtotime($UserObj->userinfo->probation_end))}} 
                  @endif
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Basic Salary ') }}
                </div>
                <div class = "col-xs-6">
                  {{$UserObj->userinfo->probation_salary}}
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Confirmation Salary ') }}
                </div>
                <div class = "col-xs-6">
                  {{$UserObj->userinfo->salary}}
                </div>
              </div>
            </div>

            @php if(isset($UserObj->inc_salary[0]->salary)) { @endphp
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Increament ') }}
                </div>
                <div class = "col-xs-6">
                  {{isset($UserObj->inc_salary[0]->salary)?$UserObj->inc_salary[0]->salary:''}}
                </div>
              </div>
            </div>

            @php } @endphp

             @php if(isset($UserObj->inc_salary[0]->start_date)) { @endphp
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Salary Increasement from ') }}
                </div>
                <div class = "col-xs-6">
                  @php
                    $hikeDate=$UserObj->salaryLogs()->latest()->get();
                      if(count($hikeDate)>0)
                      {
                        echo date('d/m/Y',strtotime($hikeDate[0]['increament_date']));
                      }
                  @endphp
                  
                </div>
              </div>
            </div>
            @php } @endphp

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Working Hours / Day') }}
                </div>
                <div class = "col-xs-6">
                  {{$UserObj->userinfo->working_hrs }} hrs
                </div>
              </div>
            </div>

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-6" >
                  {{ Form::label('name', 'Employement Type') }}
                </div>
                <div class = "col-xs-6">
                   @if($UserObj->userinfo->employement_status==1)
                    Permanent
                   @elseif($UserObj->userinfo->employement_status==2)
                    Probation
                   @elseif($UserObj->userinfo->employement_status==3)
                    Full Time
                   @else
                    Part Time
                   @endif
                </div>
              </div>
            </div>

             
            </div>

             <div class = "col-ms-6 col-md-5" style="border:1px solid #E26A6A;margin: 15px;">

            <div class = "col-xs-12" style="padding: 10px; color: #E26A6A; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Bank Account Details') }}
             </div>

             

            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Account Holder Name') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->account_holder_name}}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-5" >
                  {{ Form::label('name', 'Account Number') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->account_number}}
                </div>
              </div>
            </div>


            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group" >
                <div class = "col-xs-5" >
                  {{ Form::label('name', 'Bank Name') }}
                </div>
                <div class = "col-xs-7">
                  {{$UserObj->userinfo->bank_name }}
                </div>
              </div>
            </div>
            <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Branch') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->branch}}
                </div>
              </div>
            </div> 

             <div class = "col-xs-12" style="padding: 10px">
              <div class="form-group">
                <div class = "col-xs-5">
                  {{ Form::label('name', 'Swift Code') }}
                </div>
                <div class = "col-xs-7">
                  {{ $UserObj->userinfo->swift_code}}
                </div>
              </div>
            </div> 
      
            </div>

            <div class = "col-ms-6 col-md-6" style="border:1px solid #2ae0bb;margin: 15px;">

            <div class = "col-xs-12" style="padding: 10px; color: #2ae0bb; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Entitled  Leave') }}
             </div>

            @if($leave_types)
              <div class = "col-xs-12" style="padding: 10px">
                  <div class="form-group">
                    <div class = "col-xs-4">
                      {{ Form::label('name', 'Leave Type') }}
                    </div>
                    <div class = "col-xs-3 text-center">
                      {{ Form::label('name', 'Total Days') }}           
                    </div>
                    <div class = "col-xs-2 text-center">
                      {{ Form::label('name', 'Taken') }}           
                    </div>
                    <div class = "col-xs-3 text-center">
                      {{ Form::label('name', 'Balance Days') }}                   
                    </div>
                  </div>
                </div>
              @foreach($leave_types as $k => $leave)  

                <div class = "col-xs-12" style="padding: 10px">
                  <div class="form-group">
                    <div class = "col-xs-4">
                      {{ Form::label('name', $leave->leave_type) }}
                    </div>
                    <div class = "col-xs-3 text-center" >
                      {{isset($user_total_leaves[$leave->id])?$user_total_leaves[$leave->id]:''}}                    
                    </div>
                    <div class = "col-xs-2 text-center" >
                      {{isset($user_taken_leaves[$leave->id])?$user_taken_leaves[$leave->id]:''}}                    
                    </div>
                    <div class = "col-xs-3 text-center">
                      {{ isset($user_balance_leaves[$leave->id])?$user_balance_leaves[$leave->id]:''}}                    
                    </div>
                  </div>
                </div>
           
              @endforeach

            @endif

        
            </div>

            <div class = "col-ms-6 col-md-5" style="border:1px solid red; margin: 15px;">

             <div class = "col-xs-12" style="padding: 10px; color: red; font-size: 18px">
             <i class="fa fa-calendar"></i> {{ Form::label('name', 'Documents') }}
             </div>

             <div class = "col-xs-12" style="padding: 10px">
                  <div class="form-group">
                    <div class = "col-xs-5">
                      {{ Form::label('name', 'File Title') }}
                    </div>
                    <div class = "col-xs-5">
                      {{ Form::label('name', 'Attachment') }}           
                    </div>                   
                  </div>
                </div>
              @for($file=1; $file <=8; $file++)  

              @php if(isset($user_documents[$file]['file_name']) && $user_documents[$file]['file_name'] !='') {
                        $filename = $user_documents[$file]['file_name'];
                        $id = $user_documents[$file]['id'];
                       @endphp 
                <div class = "col-xs-12" style="padding: 10px">
                  <div class="form-group">
                    <div class = "col-xs-5">
                       {{ isset($user_documents[$file]['file_title'])?$user_documents[$file]['file_title']:''}}
                    </div>
                    <div class = "col-xs-5">                       
                       <a href="{{$img_full_path}}{{$user_documents[$file]['file_name']}}" target="_blank"><i class="fa fa-download"></i> View </a>                         
                        
                    </div>                   
                  </div>
                </div>
                 @php } @endphp        
              @endfor
            
            </div>

            

            <div class = "col-ms-6 col-md-5" style="border:1px solid Blue; margin: 15px;">

             <div class = "col-xs-12" style="padding: 10px; color: Blue; font-size: 18px">
             <i class="fa fa-money"></i> {{ Form::label('name', 'Allowances') }}
             </div>
            
            @if($allowance_types)

              @foreach($allowance_types as $k => $allowance)  
                @if(isset($user_allowanzes[$allowance->id]))
                <div class = "col-xs-12" style="padding: 10px">
                  <div class="form-group">
                    <div class = "col-xs-5">
                      {{ Form::label('name', $allowance->allowance_type) }}
                    </div>
                    <div class = "col-xs-7">
                      ${{ isset($user_allowanzes[$allowance->id])?$user_allowanzes[$allowance->id]:''}}
                    
                    </div>
                  </div>
                </div>
                @endif
           
              @endforeach

            @endif

           
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