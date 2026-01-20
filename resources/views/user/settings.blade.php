@extends('layouts.front')



@section('content')


<div class="status">
    <h1>Settings</h1>
</div>


  <div class="containerwidth">
     @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
    <form action="{{ url('/admin/user/settingpassword') }}" aria-label="{{ __('Login') }}" method="post" id="autologin", class="forunit">
               {{ csrf_field() }}  
         <div class="row">
                        <div class="col-lg-7 setings">
            <h3>CHANGE PASSWORD</h3>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">OLD PASSWORD:</label>
                              <div class="col-sm-8">
                                  {{ Form::input('password', 'old_password','', ['class'=>'form-control','required' => true]) }}
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">NEW PASSWORD:</label>
                              <div class="col-sm-8">
                                     {{ Form::input('password', 'new_password','', ['class'=>'form-control','required' => true,'id'=>'Password']) }}
                              </div>
                           </div>
                <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">CONFIRM PASSWORD:</label>
                              <div class="col-sm-8">
                                    {{ Form::input('password', 'confirm_password','', ['class'=>'form-control','required' => true,'id'=>'ConfirmPassword']) }}
                              </div>
                           </div>

                           <div>
                            <div class="error" id="error"></div>
                          </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit  mt-2">Update</button>
                        </div>
                     </div>
                  </form>
           {!! Form::open(['method' => 'POST', 'url' => ('opslogin/user/settingprofilepic'), 'files' => 'true','class'=>"forunit"]) !!}
            {{ csrf_field() }}  
                     <div class="row">
                        <div class="col-lg-3 setings">
            <h3>PHOTO</h3>
                        
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">UPLOAD:</label>
                              <div class="col-sm-8">
                                 <div class="image-upload uplodinline">
                                    <label for="file-input">
                                    <img src="{{url('assets/img/plus.png')}}" class="upimg">
                                    </label>
                                    <input id="file-input" type="file" name="profile_picture" class="form-control">
                                 </div>
                                 
                              </div>
                           </div>
                             <button type="submit" class="submit  mt-2">Update</button>
                        
                        </div>
                     </div>
                     <div class="row">
                       
                     </div>
                  </form>
               </div>

    </section>  

    <script>
      function checkPasswordMatch() {
        var password = $("#Password").val();
        var confirmPassword = $("#ConfirmPassword").val();
        if (password != confirmPassword)
             $("#error").html("Password does not match !").css("color","red");
        else
           $("#error").html("");
       
    }
    window.onload = function() {
       $("#ConfirmPassword").keyup(checkPasswordMatch);
    };


 
</script>

@stop