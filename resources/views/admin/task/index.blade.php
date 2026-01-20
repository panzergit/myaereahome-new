@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Task Management

      </h1>

      <ol class="breadcrumb">

        <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">Task Management</li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="box">
            <div class="box-header">
              <p> <a href = "{{url('admin/task/create')}}" class="btn btn-success"> <i class="fa fa-plus"></i> Create Task </a></p>
            </div>
            <div class="box-body table-responsive no-padding">
            @if (session('status'))
            <div class="alert alert-info">
                {{ session('status') }}
            </div>
            @endif

          <div class="col-sm-12">
            
            <div class = "row" >
              <div class = "col-xs-3" style="float: right;">
                <div class = "form-group" >
                  <input type="text" class="form-control" id="nameInput" name="nameInput"
                    placeholder="Search here" onkeyup="searchKeyword()" <span class="input-group-btn">
                </div>
              </div>
              
            </div>            
       
        </div>
        <br>
         <div class = "col-xs-12">
          <table class="table table-striped "  id="myTable" style="border:1px solid #2ae0bb">

            <thead>
              <tr >
                <th>#</th>
                <th>Task</th>
                <th>Assigned To</th>
                <th>Start On</th>
                <th>Deadline</th>
                <th colspan = "2" width="4%">Actions</th>
              </tr>
            </thead>
            <tbody>
              @if($tasks)
              @foreach($tasks as $k => $task)
              <tr>
                <td>{{$k+1}}</td>
                <td>{{$task->title}}</td>
                <td>{{isset($task->assignedto->name)?$task->assignedto->name:''}}</td>
                <td>{{$task->start_on}}</td>
                <td>{{$task->deadline}}</td>
                <td><a href="{{url("admin/task/$task->id/edit")}}" title = "Edit" class="btn btn-primary"> <i class="fa fa-edit"></i> View / Edit</a>
                </td>
                <td><a href="#" onclick="delete_record('{{url("admin/task/delete/$task->id")}}');" title = "Delete" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</a></td>
              </tr>
              @endforeach
              @endif
            </tbody>

        </table>

      </div>

        {{$tasks->links() }}

            </div>

        </div>

    </section>        



@endsection

<script>

function searchKeyword() {
 var input, filter, table, tr, td,tds,i,td2,td3;
  input = document.getElementById("nameInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  tds = table.getElementsByTagName("td");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[3];

    if (td || td2|| td3) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1 || td2.innerHTML.toUpperCase().indexOf(filter) > -1 || td3.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}


</script>