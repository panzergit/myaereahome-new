@extends('layouts.app')

@section('content')

<div class="container-scroller">
         <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
               <div class="row w-100">
                  <div class="col-lg-4 mx-auto">
                     <div class="auto-form-wrapper wellog wellog2">
                				
						
						<div id="error" class="error"></div>
						<div class="success" id="resendalert"></div>
                        <form action="{{ url('/setpassword') }}" method="post" id="verifyotp">
						@csrf
						<img src="{{url('assets/img/ærea.png')}}" class="areas">
						
						<div class="form-group row">
							 <label for="staticEmail" class="col-sm-4 col-form-label"></label>
							 <div class="col-sm-8">
								<h1>Ops Portal</h1>
							 </div>
						  </div>
                           <label class="label log2">OTP SENT TO YOUR REGISTERED EMAIL</label>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label">OTP:</label>
                              <div class="col-sm-10">
                                 <input type="text" class="form-control" name="verification_code" id="verification_code" value="{{ old('verification_code') }}">
                              </div>
                           </div>
                           <div class="clearfix"></div>
                           <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label"></label>
                              <div class="col-sm-10">
                                 <div class="form-group d-flex justify-content-between">
                                    <a href="javascript:void(0);" id="resend" class="text-small forgot-password text-black">Resend OTP</a>
                                    <div class="form-check form-check-flat mt-0">
                                       <div class="form-group">
                                          <button class="retinfo" type="button" id="checkverification">SUBMIT</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>						   
						   <input type="hidden" name="email" id="email" value="{{ $email }}">						   
                        </form>
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
	
	$('#checkverification').click(function() {
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
					$('#verifyotp').submit();
				}
			},error: function(ts) {				
				console.log("Error:"+ts.responseText);  
			}
		});
		/*var otp = $('#otp').val();
		if(otp != verificationcode) {
			$('#error').html('Invalid verification code!');
			$('#error').prop('color', 'red');
			return false;
		} else {
		}*/
	});
});
</script>	
