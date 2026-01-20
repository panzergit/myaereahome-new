@extends('layouts.app')

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
                             <div class="col-sm-8">
                             <img src="{{url('assets/img/ærea.png')}}" class="areas">
                                <h1>Ops Portal</h1>
                             </div>
                          </div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-4 col-form-label">EMAIL :</label>
                              <div class="col-sm-8">
                                 <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-4 col-form-label">PASSWORD :</label>
                              <div class="col-sm-8">
                                 <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-4 col-form-label"></label>
                              <div class="col-sm-8">
                                 <div class="form-group d-flex justify-content-between">
                                    <a href="#" class="text-small forgot-password text-black">forgot password?</a>
                                    <div class="form-check form-check-flat mt-0">
                                       <div class="form-group">
                                          <button type="submit" class="bretinfo">
                                             {{ __('Login') }}
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
