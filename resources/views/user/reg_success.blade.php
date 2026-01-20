@extends('layouts.registration')

@section('content')
<style>
         .prosel{    padding: 15px;}
         .prosel select{width: 100%;}
         .status h1 {
         margin-top: 0px;     margin-bottom: 25px;
         }
         .proaraimg{    width: 75%;
         float: right;
         }
         .file-55 {
         padding: 8px!important;
         }
         .forunit span {
         color: #5D5D5D;
         font-weight: 600;
         }
		 .h-100 {
    height: 100% !important;
}
.my-auto {
    margin-top: auto !important;
    margin-bottom: auto !important;
}
.Reqsuccess {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    margin-top: 8%!important;
    margin: 0 auto;
    width: 50%;
}
.Reqsuccess .reqimg {
    width: 100px;
    margin: 0 auto;
    text-align: center;
    display: block;
    padding-top: 20px;
}
.Reqsuccess h2 {
    color: #8F7F65;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    margin-top: 20px;
    margin-bottom: 15px;
}
.Reqsuccess p {
    color: #5D5D5D;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
}
      </style>
<section class="bgsec1">
         <div class="container">
             <div class="row " >
               <div class="col-lg-9 col-6">
                  <div class="status">
                     <h1> User Registration</h1>
                  </div>
               </div>
               <div class="col-lg-3 col-6">
                  <div class="status">
                     <a href="{{url('/')}}"><img src="{{ asset('assets/img/Registered.png') }}" class="proaraimg"></a>
                  </div>
               </div>
            </div>
			<div class="row h-100 ">
                @if($message ==1)
                    <div class="my-auto Reqsuccess">
					 <img src="{{ asset('assets/img/save.png') }}" class="reqimg">
                      <h2>Request Sucessfull!</h2>
					  <p>Your registration request has been submitted successfully. Managing agent will revert back to you shortly. Thank you</p>
                     </div>
                @elseif($message ==2)
                     <div class="my-auto Reqsuccess">
					 <img src="{{ asset('assets/img/userclose.png') }}" class="reqimg">
                      <h2>Email Already Registered!</h2>
					  <p>Your email has been registered already. Please cntact managing agent. Thank You</p>
                     </div>
                @elseif($message ==4)
                     <div class="my-auto Reqsuccess">
					 <img src="{{ asset('assets/img/userclose.png') }}" class="reqimg">
                      <h2>Vehicle Reached Limit!</h2>
					  <p>Vehicle registration reached maximum limit. Please cntact managing agent. Thank you</p>
                     </div>
                @else
                     <div class="my-auto Reqsuccess">
					 <img src="{{ asset('assets/img/userclose.png') }}" class="reqimg">
                      <h2>Request Pending for Action!</h2>
					  <p>You email already registered and waiting for managing agent action. they will revert back to you shortly. Thank you</p>
                     </div>
                  </div>
                @endif
            </div>
</section>           
        
@endsection
