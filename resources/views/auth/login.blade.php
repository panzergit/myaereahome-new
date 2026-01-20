@extends('layouts.app')
@section('styles')
<style>
.containeruser1 {
    display: block;
    position: relative;
    padding-left: 20px;
    margin-bottom: 0px;
    cursor: pointer;
    font-size: 12px!important;
    font-weight: 400!important;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.containeruser1 input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmarkuser1 {
  position: absolute;
  top: 0;
  left: 0;
  height: 15px;
  width: 15px;
  background-color: #D0D0D0;
}

.containeruser1:hover input ~ .checkmarkuser1 {
  background-color: #ccc;
}

.containeruser1 input:checked ~ .checkmarkuser1 {
  background-color: #c2c2c2;
}

.checkmarkuser1:after {
  content: "";
  position: absolute;
  display: none;
}

.containeruser1 input:checked ~ .checkmarkuser1:after {
  display: block;
}

.containeruser1 .checkmarkuser1:after {
    left: 4px;
    top: 2px;
    width: 6px;
    height: 9px;
    border: solid #8F7F65;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
.containeruser1 input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.forgot-password:hover {color: #fff;}
.forgot-password {
    color: #fff;
    text-align: right;
    float: right;
    text-transform: capitalize;
    font-size: 12px;
}
</style>
@endsection
@section('content')
 <div class="container-scroller">
         <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
               <div class="row w-100">
                  <div class="col-lg-4 mx-auto">
                     <div class="auto-form-wrapper wellog">
                       
                        <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
                      
                       

                  <div class="form-group row">
                             <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                             <div class="col-sm-12 plogin">
                             <img src="{{url('assets/img/ærea.png')}}" class="areas">
                                <h1>Ops Portal</h1>
                             </div>
                          </div>
                          @if (session('status'))
                          <div class="form-group row">
                          <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                          <div class="col-sm-8 plogin">
                              <div class="" style="color:#dc3545;font-size: 90%;">
                              {{ session('status') }}
                              </div>
                              </div>
                              </div>
                           @endif

                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-4 col-form-label">EMAIL :</label>
                              <div class="col-sm-8 plogin">
                                 <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="@if(Session::has('tmp_user')){{ Session::get('tmp_user') }}@else{{ old('email') }}@endif" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                              </div>
                           </div>
                           <div class="row">
                              <label for="staticEmail" class="col-sm-4 col-form-label">PASSWORD :</label>
                              <div class="col-sm-8 plogin">
                                 <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                              </div>
                           </div>
                           <div class=" row mb-2 mt-2">
                              <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                              <div class="col-sm-3 plogin pr-0">
							    <label class="containeruser1">
                                    <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    <span class="checkmarkuser1"></span>
                                </label>
							  </div>
							  <div class="col-sm-5 plogin pr-0">
							     <a href="{{url('forgotpassword')}}" class="text-small forgot-password text-black">forgot password?</a>
							  </div>
						    </div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                              <div class="col-sm-8 plogin">
                                 <div class="form-group">
                                    <!--<a href="{{url('forgotpassword')}}" class="text-small forgot-password text-black">forgot password?</a>-->
                                    <button type="submit" class="retinfo mb-4">
                                             {{ __('Submit') }}
                                         </button>
                                 </div>
                              </div>
                           </div>
                       </form>
                     </div>
                  </div>
               </div>
            </div>
            <!-- content-wrapper ends -->
         </div>
         <!-- page-body-wrapper ends -->
      </div>
@endsection