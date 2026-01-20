@extends('layouts.app')

@section('content')

<div class="container-scroller">
 <div class="container-fluid page-body-wrapper full-page-wrapper">
	<div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
	   <div class="row w-100">
		  <div class="col-lg-4 mx-auto">
			 <div class="auto-form-wrapper wellog">
				
				
				<div class="error" id="error"></div>
				
				<form action="{{ url('/updatepassword') }}" aria-label="{{ __('Login') }}" method="post" id="autologin">
					@csrf
					<img src="{{url('assets/img/ærea.png')}}" class="areas">
						
						<div class="form-group row">
							 <label for="staticEmail" class="col-sm-4 col-form-label"></label>
							 <div class="col-sm-8">
								<h1>Ops Portal</h1>
							 </div>
						  </div>
				   <div class="form-group">
					  <label class="label">SET NEW PASSWORD :</label>
					  <div class="input-group">
						 <input type="password" class="form-control" name="password" id="password" min="5" required>
					  </div>
				   </div>
				   <div class="form-group">
					  <label class="label">CONFIRM NEW PASSWORD :</label>
					  <div class="input-group">
						 <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
					  </div>
				   </div>
				   <div class="form-group">
					  <button class="retinfo" id="setpassword">SUBMIT</a>
				   </div>
				   <input type="hidden" name="email" id="email" value="@if(Session::has('tmp_user')){{ Session::get('tmp_user') }}@endif">
				</form>
				
				<div id="redirectdashboard" style="display:none;">
				   <div class="form-group row">
					  <label for="staticEmail" class="col-sm-2 col-form-label"></label>
					  <div class="col-sm-10">
						 <label class="label">PASSWORD SUCCESSFULLY SET!</label>
						 <label class="label">REDIRECTING TO DASHBOARD</label>
					  </div>
				   </div>
				  <img src="{{ url('/assets/img/loader.gif') }}" class="loadgif">
				</div>
			 </div>
		  </div>
	   </div>
	</div>
 </div>
</div>
@endsection
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {	

	$('#autologin').submit(function() {
		var email = $('#email').val();
		var password = $('#password').val();
		var confirm_password = $('#confirm_password').val();
		if(password != confirm_password) {
			$('#error').html('Password and confirm password mismatch!');
			$('#error').prop('color', 'red');
			return false;
		} else {
			$('#autologin').hide();
			$('#redirectdashboard').show();
			
			/*var token = "{!! csrf_token() !!}";
			$.ajax({
				type: "POST",
				url: '{{ url("/") }}/updatepassword',				
				data: {'email': email, 'password': password},
				headers: {'X-CSRF-TOKEN': token},		
				success: function(response) {
					console.log(response);
					if(response == 'success') {	
						window.location = "{{ url('/admin/home') }}";
					}
				},error: function(ts) {				
					console.log("Error:"+ts.responseText);  
				}
			});*/
				
		}
	});	
	
});
</script>
