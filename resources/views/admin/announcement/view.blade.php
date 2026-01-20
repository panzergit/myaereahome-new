@extends('layouts.adminnew')



@section('content')

@php
$permission = Auth::user();
@endphp

<!-- Content Header (Page header) -->

   <section class="content-header">

     

      <ol class="breadcrumb">

        <li><a href="{{url('opslogin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active"> <a href="{{url("opslogin/")}}">Announcement Management</a></li>

      </ol>

    </section>
   

    <!-- Main content -->

    <section class="content">


      <div class="box col-xs-9">


            <div class="box-body">
                 <div class = "col-xs-9">
                    <div class = "col-xs-6" >
                      {{ Form::label('name', 'Title') }}<br/>
                      {{isset($announcement->title)?$announcement->title:''}}
                    </div>

                     <div class = "col-xs-3" >
                      {{ Form::label('name', 'Send To') }}<br/>
                     {{isset($announcement->role->name)?$announcement->role->name:'All Roles'}}

                    </div>
                     <div class = "col-xs-3" >
                      {{ Form::label('name', 'Send On') }}<br/>
                      {{date('d M Y', strtotime($announcement->created_at))}}
                    </div>
                   
                </div>

                <div class = "col-xs-9 " style="margin-top:30px;">
                     <div class = "col-xs-9">
                     {{$announcement->notes}}
                    </div>
                    
                </div>

           </div>
      </div>  

      <div class="box">
         <div class = "col-xs-9">
         <div class="box-body table-responsive no-padding">

      

          <table class="table table-striped " style="border:1px solid #2ae0bb">

            <thead>

              <tr>
                <th>#</th>                
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th colspan = "2" width="4%">Actions</th>
              </tr>

            </thead>

            <tbody>

              @if($announcement->details)

              @foreach($announcement->details as $key => $detail)             
              @php

              if($detail->status ==1)
              {
                $status = "New";
                $fontcolor="#00c0ef";
              }
              else 
              {
                $status = "Opened";
                $fontcolor="green";
              }
             
              

              @endphp
              <tr>
                <td>{{$key+1}}</td>
                <td>{{isset($detail->name)?$detail->name:''}} </td>
               
                <td>{{isset($detail->email)?$detail->email:''}} </td>
                <td style="color:{{$fontcolor}}; font-weight: bold;">{{$status}}</td>
                <td> <a href="{{url("admin/payroll/view/$detail->id")}}" title = "Edit" class="btn btn-info btn-flat"><i class="fa fa-edit"></i> View / Edit</a> </td>

                <td>  <a href="#" onclick="delete_record('{{url("admin/payroll/delete/$detail->id")}}');" title = "Delete" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</a> </td>
              </tr>

              @endforeach

              @endif

              

            </tbody>

        </table>

      </div>
    </div>
    
    </div>
  </div>
    </section>        



@endsection