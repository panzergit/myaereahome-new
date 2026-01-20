@extends('layouts.faq')

@section('content')

               <div class="status">
                  <h1>Profile</h1>
               </div>
               <div id="main">
                  <div class="container">
                     <div class="accordion" id="faq">
                        <div class="card">
                           <div class="card-header" id="faqhead1">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq1"
                                 aria-expanded="true" aria-controls="faq1">How do I create an account?</a>
                           </div>
                           <div id="faq1" class="collapse" aria-labelledby="faqhead1" data-parent="#faq">
                              <div class="card-body">
                                 Accounts are created by the Managing Agent of the property. Once your account has been created, you will then be informed by the Managing Agent.
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead2">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                                 aria-expanded="true" aria-controls="faq2">I’ve received an invitation from my property’s managing agent to download Aerea Home App. How do I login?</a>
                           </div>
                           <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                              <div class="card-body">
                                Once you have launched the app and accepted the terms & conditions, enter your registered email address to receive the OTP for verification. Once OTP has been verified, you will be asked to set your login password, which will be used for subsequent logins. 
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead3">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                                 aria-expanded="true" aria-controls="faq3">I’ve lost my password, how do I reset it?</a>
                           </div>
                           <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#faq">
                              <div class="card-body">
                                 You can reset your password by tapping on “Forget Password” on the login page. You will be asked to enter your registered email to receive an OTP for verification. Once verified, you will then be able to set a new password.
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead4">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq4"
                                 aria-expanded="true" aria-controls="faq4">Can I change my password after setting it for the first time?</a>
                           </div>
                           <div id="faq4" class="collapse" aria-labelledby="faqhead4" data-parent="#faq">
                              <div class="card-body">
                                 Yes, you can change your password under Settings. 
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead5">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq5"
                                 aria-expanded="true" aria-controls="faq5">Can I change my mailing address?</a>
                           </div>
                           <div id="faq5" class="collapse" aria-labelledby="faqhead5" data-parent="#faq">
                              <div class="card-body">
                                 Yes, you can change your mailing address under Settings.
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
           
@endsection
