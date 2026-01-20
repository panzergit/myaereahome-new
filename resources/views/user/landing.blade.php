@extends('layouts.landing')
@section('title', 'Page Title')
@section('content')
<style>
@media screen and (max-width: 768px)
{
   .login100-form {
      width: 100%;
      min-height: 100vh;
   }
   .login100-more {
      width: 100%;
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      position: relative;
      z-index: 1;
      order: 2;
   }
   .landingection .ignup {
      background: #8F7F65;
      color: #fff;
      text-decoration: none;
      padding: 6px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 600;
      margin-left: 3px;
      margin-right: 3px;
   }
   .landingection .ignup img {
      width: 25px;
      margin-right: 6px;
      margin-top: -5px;
   }
}
</style>
<div class="limiter">
   <div class="container-login100">
      <div class="wrap-login100">
         <div class="login100-form validate-form ">
            <div class="row h-100 landingection">
               <div class="my-auto">
                  <img src="{{ asset('assets/landing/img/Registered.png')}}" class="llogo" />
                  <h2>HOME</h2>
                  <h3>Simplifying Condo Living Experiences</h3>
                  <h4>For Residents, Managing Agents,</h4>
                  <h4>Council Members and Developers</h4>
                  <div class="clearfix"></div>
                  <div class="iconimg">
                     <a href="/opslogin" class="ignup">
                        <img src="{{ asset('assets/landing/img/icons8lock.png')}}" class="" />
                        OPS PORTAL LOGIN
                     </a>
                  </div>
                  <h5>Now Available on</h5>
                  <div class="qrimg">
                     <a href="https://apps.apple.com/sg/app/aerea-home/id1582950240">
                        <img src="{{ asset('assets/landing/img/Apple-l.png')}}" class="">
                     </a>
                     <a href="https://play.google.com/store/apps/details?id=com.aerea.home">
                        <img src="{{ asset('assets/landing/img/Andriod-l.png')}}" class="">
                     </a>
                  </div>
                  <div class="appimg">
                     <img src="{{ asset('assets/landing/img/AppleStoreQR.png')}}" class="vertop">
                     <img src="{{ asset('assets/landing/img/GooglePlayQR.png')}}" class="vertop2">
                  </div>
					   <ul class="landimg2">
						   <li><a href="{{url('/termsconditions')}}">App Terms of Use</a></li>
						   <li><a href="{{url('/privacypolicy')}}">App Privacy Policy</a></li>
                     <li><a href="{{url('/faq/profile')}}">FAQ </a></li>
                     <li><a href="{{url('/contact-us')}}">Contact Us </a></li>
						</ul>
               </div>
            </div>
         </div>
         <div class="login100-more" style="background-image: url({{ asset('assets/landing/img/iStockl.jpg')}});"></div>
      </div>
   </div>
</div>
@endsection