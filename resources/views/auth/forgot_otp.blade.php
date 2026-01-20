@extends('layouts.app')

@section('content')

<div class="container-scroller">
         <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
               <div class="row w-100">
                  <div class="col-lg-4 mx-auto">
                     <div class="auto-form-wrapper wellog wellog2">
                     					
						
						
						<div class="success" id="resendalert"></div>
                        <form method="POST" action="{{ url('resetpassword') }}" aria-label="{{ __('OTP') }}">
                        @csrf
						<img src="{{url('assets/img/ærea.png')}}" class="areas">
						
						<div class="form-group row">
							 <label for="staticEmail" class="col-sm-4 col-form-label"></label>
							 <div class="col-sm-12">
								<h1>OTP</h1>
							 </div>
						  </div>
						  <div class="form-group row mb-0">
						  <label class="col-sm-2 col-form-label"></label>
                           <label class="col-sm-10">OTP SENT TO YOUR REGISTERED CONTACT</label>
</div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label">OTP:</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control" name="verification_code" id="verification_code" value="{{ old('verification_code') }}">
								 <div id="error" class="error"></div>
                              </div>
							  
                           </div>
                           <div class="clearfix"></div>
                           <div class="form-group row welform">
                              <label for="staticEmail" class="col-sm-2 col-form-label"></label>
                              <div class="col-sm-10">
                                 <div class="form-group d-flex justify-content-between">
                                    <a href="javascript:void(0);" id="resend" class="text-small forgot-password text-black">Resend OTP</a>
                                    <div class="form-check form-check-flat mt-0">
                                        <button type="submit" class="retinfo">
                                             {{ __('Submit') }}
                                         </button>
                                    </div>
                                 </div>
                              </div>
                           </div>						   
						   <input type="hidden" name="email" id="email" value="{{ $email }}">						   
						   <!--<input type="hidden" name="otp" id="otp" value="{{ $otp }}">	-->					   
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
 
@endsection
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#resend').click(function() {
		$('#error').html('');
		var email = $('#email').val();
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/resendotp',				
			data: {'email': email},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				console.log(response);
				if(response != '') {				
					$('#resendalert').html('OTP successfully sent!');					
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});	
	});
	
	/*$('#checkverification').click(function() {
		var verificationcode = $('#verification_code').val();
		var email = $('#email').val();
		var token = "{!! csrf_token() !!}";
		$.ajax({
			type: "POST",
			url: '{{ url("/") }}/checkotp',				
			data: {'email': email, 'verificationcode': verificationcode},
			headers: {'X-CSRF-TOKEN': token},		
			success: function(response) {
				console.log(response);
				if(response == 'invalid') {				
					$('#error').html('Invalid verification code!');														
				} else {
					window.location = "{{ url('/opslogin/home') }}";
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
		
	}); */
});
</script>	
