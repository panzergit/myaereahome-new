@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row ">
    <div class="col-md-3"></div>
        <div class="col-md-6 lomain">
            <div class="card">
            <div class="login-logo">
    <!--a ><b>Lumiere<XX/b> </a-->
    <a><img src="{{url('public/img/logo.png')}}" class="logoimg " alt="Lumire Admin"> </a>
  </div>

                <div class="card-body">
                    <h3 class="frg">{{ __('Reset Password') }}</h3>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                         

                            <div class="col-md-12">
                            <label for="email" class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 ">
                                <button type="submit" class="btn btn-log">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
