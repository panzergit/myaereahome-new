@extends('layouts.app')

@section('content')

<div class="container-scroller">
	 <div class="container-fluid page-body-wrapper full-page-wrapper">
		<div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
		   <div class="row w-100">
			  <div class="col-lg-4 mx-auto">
				 <div class="auto-form-wrapper wellog">
				
					@if(Session::has('error'))
						<div class="error">{{ Session::get('error') }}</div>
					@endif
					<form action="{{ url('verifyotp') }}" method="post">
						@csrf
						<img src="{{url('assets/img/ærea.png')}}" class="areas">
						
						<div class="form-group row">
							 <label for="staticEmail" class="col-sm-4 col-form-label"></label>
							 <div class="col-sm-8">
								<h1>Ops Portal</h1>
							 </div>
						  </div>
					   <div class="form-group">
						  <label class="label">ENTER YOUR REGISTERED EMAIL</label>
						  <div class="input-group">
							 <input type="email" class="form-control" name="email" id="email" required>
						  </div>
					   </div>
					   <div class="form-group">
						  <button type="submit" class="retinfo">RETRIEVE INFO</button>
					   </div>
					</form>
				 </div>
			  </div>
		   </div>
		</div>
	 </div>
  </div>
@endsection
