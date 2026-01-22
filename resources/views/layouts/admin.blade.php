<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">
  <title>{{ config('app.name', 'Jui Residence | Portal') }}</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="{{asset('public/css/jquery-ui.min.css')}}" rel="stylesheet" type="text/css"/> 
  <link href="{{ asset('public/css/datepicker.css') }}" rel="stylesheet" type="text/css" />
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
    <body>
      <section>
         <div class="row">
            <p></p>
         </div>
      </section>

@php 
  $permission = Auth::user();
@endphp

      <section>
         <div class="row">
            <div class="col-lg-2 col3">
               <a href="{{url('/admin/home')}}"> <img src="{{url('assets/img/logo.png')}}" class="logo"></a>
               <ul class="annonce">
                  @php
                    $announcement =  $permission->check_menu_permission(1,$permission->role_id,1);
                    if(isset( $announcement) && $announcement->view==1){
                  @endphp
                  <li class="active">
                     <a href="{{url('/admin/announcement')}}" class="anone">Announcements</a>
                  </li>
                   @php 
                    }
                    $user =  $permission->check_menu_permission(7,$permission->role_id,1);
                    if(isset($user) && $user->view==1){
                  @endphp

                   <li class="active">
                     <a href="{{url('admin/employee')}}" class="anone">User Management</a>
                  </li>

                   @php 
                      }
                      $takeover =  $permission->check_menu_permission(2,$permission->role_id,1);
                      if($takeover->view==1){
                    @endphp

                  <li>
                     <p>Appointment For</p>
                     <p>
                        Unit Take Over
                     </p>
                     <a href="{{url('admin/takeover_appt')}}">- New Appointment</a><br />
                     <a href="{{url('admin/takeover_appt/lists')}}">- Appointment Full List</a><br />
                  </li>
                   @php 
                    }
                    $feedback =  $permission->check_menu_permission(6,$permission->role_id,1);
                    if($feedback->view==1){
                  @endphp
                 <li>
                     <p>Feedback</p>
                     <a href="#">- Feedback Status</a>
                  </li>
                   @php 
                    }
                    $facility =  $permission->check_menu_permission(5,$permission->role_id,1);
                    if($facility->view==1){
                  @endphp
                  
                  <li>
                     <p>Facillties Booking</p>
                     <a href="#">- Check Booking</a>
                  </li>
                   @php 
                     } 
                      
                      $settings =  $permission->check_menu_permission(9,$permission->role_id,1);
                      $module =  $permission->check_menu_permission(22,$permission->role_id,1);
                      $role =  $permission->check_menu_permission(23,$permission->role_id,1);
                      $unit =  $permission->check_menu_permission(24,$permission->role_id,1);
                      $menu =  $permission->check_menu_permission(25,$permission->role_id,1);

                      if(isset($settings->view) && $settings->view ==1 || isset($module->view) && $module->view ==1 ||  isset($role->view) && $role->view ==1 || isset($unit->view) && $unit->view ==1 || isset($menu->view) &&  $menu->view ==1){

                     @endphp

                      <li>
                     <p>Manage Password</p>
                      @php if(isset($settings->view) && $settings->view==1 ) {@endphp 
                        <a href="{{url('admin/configuration/setting')}}">- General Settings</a><br />
                      @php } if(isset($role->view) && $role->view==1 ) {@endphp 
                        <a href="{{url('admin/configuration/role')}}">- Manage Role</a><br />
                      @php } if(isset($unit->view) && $unit->view==1 ) {@endphp 
                        <a href="{{url('admin/configuration/unit')}}">- Manage Unit</a><br />
                      @php } if(isset($menu->view) && $menu->view==1 ) {@endphp 
                        <a href="{{url('admin/configuration/menu')}}">- Menu</a><br />
                      @php } @endphp 
                  </li>
                   @php 
                     }
                    @endphp

                  
                  <li class="userlog">
                     <img src="{{url('assets/img/user.png')}}">
                     <h3>{{ Auth::user()->name }}</h3>
                     <h4>#15-01</h4>
                     <a href="{{url('logout')}}" class="logicon">logout <img src="{{url('assets/img/logout.png')}}"></a>
                  </li>
               </ul>
            </div>
            <div class="col-lg-10 col9">
               <table class="table tablefbor tophead">
                  <thead>
                     <tr>
                        <th scope="col">Good Morning</th>

                        <th scope="col">10:30 AM</th>
                        <th scope="col" >{{date('d M Y')}}</th>
                     </tr>
                  </thead>
               </table>
               <div class="">
                  <div class="">
                    @yield('content')
                  </div>
               </div>
            </div>
         </div>
      </section>
  <!-- /.content-wrapper -->

   <section>
         <div class="row">
            <p></p>
         </div>
      </section>

  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->



 <script src=" {{ asset('assets/js/jquery.min.js') }}"></script>
      <script src=" {{ asset('assets/js/bootstrap.min.js') }}"></script>


<script src="{{asset('public/js/jquery-ui.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/bower_components/validator/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/bower_components/validator/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/bower_components/jquery-steps/jquery.steps.js')}}" type="text/javascript"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/locales.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

@yield('customJS')

<script >
$(document).ready(function() {

  

   // Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementById("close");

// When the user clicks the button, open the modal 
/*btn.onclick = function() {
    $('#action_status').val();
    modal.style.display = "block";
}*/


// When the user clicks on <span> (x), close the modal
/*span.onclick = function() {
    modal.style.display = "none";
}*/

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
        }); 

function opendialog(status) {
  var modal = document.getElementById('myModal');
  var span = document.getElementById("close");
    $('#action_status').val(status);
    modal.style.display = "block";
}

function adjustment(status) {
  
    $('#type').val(status);
    $("#adjustment").submit();
}
</script>

<script>
 
  

 function deleteUserInfo(uid,infoid,fieldname,img){
  if (confirm('Do you want to remove?')) {
     var token = '{{csrf_token()}}';
     console.log(infoid+ " , "+ fieldname + " , "+ img+ ' , '+token)
      $.ajax({
        url: "{{URL::TO('admin/delete_user_file')}}",
        type: 'post',
        data: {
          id: infoid,
          field_name:fieldname,
          file_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
            <?php if($permission->role->name =="Employee"){ ?>
            var name = <?php echo $permission->name ?>;
            window.location.href ='../editmyprofile/'+ name;
          <?php } else { ?>
            window.location.href ='../'+uid+'/edit';
          <?php } ?>
        },

        error: function (response) {
          console.log(response);
        }
      })
    }

    }


function deleteMediaPicture(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_media_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteRecruitmentInfo(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/recruitment_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteCompanyLogo(mid,img){
  if (confirm('Do you want to remove logo?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_company_logo')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           //window.location.href ='../'+mid+'/edit';
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function deleteSharefileInfo(mid,img){
  if (confirm('Do you want to remove attachment?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/sharedoc_file')}}",
        type: 'post',
        data: {
          id: mid,
          img_path:img,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
           window.location.href ='../'+mid+'/edit';
          //location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function getuserslist(){

 var roleId = $("#role").val();
              
               if(roleId)
               {
                  $.ajax({
                     url : "{!!URL:: route('getuserlist')!!}",
                     dataType : "json",
                     data:{
                      role:roleId
                     },
                     success:function(data)
                     {
                        console.log(data);
                        $('select[name="user[]"]').empty();
                        $('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,value){
                           $('select[name="user[]"]').append('<option value="'+ id +'">'+ value +'</option>');
                        });
                     }
                  });
               }
               else
               {
                  $('select[name="user[]"]').empty();
               }
}

$("#SearchEmpName").autocomplete({
       
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocomplete')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchEmpName").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 3,
        delay: 300
    });

$("#SearchEmpId").autocomplete({
       
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocompleteid')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchEmpId").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 3,
        delay: 300
    });



$("#SearchByFileNo").autocomplete({
       
        source: function(request, response) {
            $.ajax({
                url: "{!!URL:: route('autocompletefileno')!!}",
                dataType: "json",
                data: {
                    term : $("#SearchByFileNo").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        min_length: 1,
        delay: 300
    });


function payrollAdjustment(status){

  var note = $("#note").val();
  var amount = $("#amount").val();
  //var type = $("#type").val();
  var type = status;
  var id = $("#payroll_id").val();
  console.log(note+" "+amount+" "+type);
  
  if(note !='' && amount !='' && type !=''){
    
  
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/add_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: id,
          note: note,
          amount:amount,
          type:type,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          console.log(response);
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }
  else {
    if(note =='')
      alert('Please enter your note!');
    else if(amount =='')
      alert('Please enter your amount!');
    else if(type =='')
      alert('Please select type!');
  }

}    


function deleteAdditional(mid){
  if (confirm('Do you want to delete this record?')) {
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/delete_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: mid,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      })
  }

}

function addClaimAddition(mid){
     var payroll = $("#payroll_id").val();
     var token = '{{csrf_token()}}';
      $.ajax({
        url: "{{URL::TO('admin/claim_payroll_adjustment')}}",
        type: 'post',
        data: {
          id: mid,
          payroll: payroll,
           _token: token,
        },
         cache:false,
         datatype: "JSON",
        success: function (response) {
          location.reload();
        },

        error: function (response) {
          console.log(response);
        }
      }) 
}

   
function delete_record(url)
{ 
 doyou = confirm("Do you want to delete this record? All related data will be deleted (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

function cancel_record(url)
{ 
 doyou = confirm("Do you want to cancel this claim? you can't revert (OK = Yes   Cancel = No)");
 if (doyou == true)
 {
    window.location.href= url;
 }
}

$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
$("#checkAllView").click(function(){
    $('.viewCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllAdd").click(function(){
    $('.addCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllEdit").click(function(){
    $('.editCheckBox').not(this).prop('checked', this.checked);
});

$("#checkAllDelete").click(function(){
    $('.deleteCheckBox').not(this).prop('checked', this.checked);
});

//Confirmation Due Date  Start //

function confirmationDuebyProbation(){


       var date = new Date($("#hire_date").val()),
           days = parseInt($("#probation_month").val(), 10);
        
        if(!isNaN(date.getTime())){
            date.setMonth(date.getMonth() + days);
            
            $("#confirmation_due").val(date.toInputFormat());
        } else {
            alert("Invalid Date");  
        }


}
  ;(function($, window, document, undefined){
  
    
    
    //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
    };
})(jQuery, this, document);

function confirmationDuebyDate(){


       var date = new Date($("#hire_date").val()),
           days = parseInt($("#probation_month").val(), 10);
        
        if(!isNaN(date.getTime()) && days !=''){
            date.setMonth(date.getMonth() + days);
            
            $("#confirmation_due").val(date.toInputFormat());
        } else {
            alert("Invalid Date");  
        }


}
  

  ;(function($, window, document, undefined){
  
    
    
    //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
    };
})(jQuery, this, document);



//Confirmation Due Date  End //
  
</script>
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>
