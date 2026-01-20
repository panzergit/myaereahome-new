@extends('layouts.app')

@section('content')
 <div class="container-scroller">
         <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
               <div class="row w-100">
                  <div class="col-lg-4 mx-auto">
                     <div class="auto-form-wrapper wellog">
                       
                        <form method="POST" action="{{ url('forgotloginotp') }}" aria-label="{{ __('Login') }}">
                        @csrf
                        <img src="{{url('assets/img/ærea.png')}}" class="areas">
                       

                  <div class="form-group row">
                             <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                             <div class="col-sm-12">
                                <h1>Forgot Password?</h1>
                             </div>
                          </div>
                          @if (session('status'))
                          
                              <div class="" style="color:#dc3545;font-size: 90%;text-align:center;">
                              {{ session('status') }}
                              </div>
                           
                           @endif

                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label pr-0">EMAIL :</label>
                              <div class="col-sm-10">
                                 <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="@if(Session::has('tmp_user')){{ Session::get('tmp_user') }}@else{{ old('email') }}@endif" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                              </div>
                           </div>
                          
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label"></label>
                              <div class="col-sm-10">
                                 <div class="form-group d-flex justify-content-between">
                                    <a href="{{url('login')}}" class="text-small forgot-password text-black">Click Here to Login</a>
                                    <div class="form-check form-check-flat mt-0">
                                       <div class="form-group">
                                          <button type="submit" class="retinfo">
                                             {{ __('Submit') }}
                                         </button>
                                          
                                       </div>
                                    </div>
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
