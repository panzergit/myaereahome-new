@extends('layouts.faq')

@section('content')

                @if (session('status'))
                    <div class="status">
                        <div class="alert alert-info">
                        <p>{{ session('status') }}</p>
                        </div>
                    </div>
                
                @endif

                <div class="status">
                  <h1>Contact Us</h1>
                  <p>If you have any enquiries, you may contact us though the form below or email us at hello@myaereahome.com</p>
               </div>
               <div id="main">
                  <div class="row">
                     <div class="col-lg-3 col-md-3"></div>
                     <div class="col-lg-6 col-md-6">
                        
                        <form action="{{url('/enquiry')}}" method="post" class="contactus">

                        @csrf
						
						<div class="row">
                              <div class="col-lg-6">
                                 <input type="text" required="true" class="form-control" id="" placeholder="First Name" name="first_name">
                              </div>
                              <div class="col-lg-6">
                                 <input type="text" required="true" class="form-control" placeholder="Last Name" name="last_name">
                              </div>
                          
                              <div class="col-lg-6">
                                 <input type="text" required="true" class="form-control" id="" placeholder="Contact No" name="phone">
                              </div>
                              <div class="col-lg-6">
                                 <input type="email" required="true" class="form-control" placeholder="Email " name="email">
                              </div>
                         
                              <div class="col-lg-12">
                                 <textarea class="form-control" required="true" rows="10" name="enquiry" id="comment" placeholder="Message"></textarea>
                              </div>
                        <div class="col-lg-12">
                           <button type="submit" class="submit float-right">Submit</button>   </div>
                        </form>
                     </div>
                     </div>
                     <div class="col-lg-3 col-md-3"></div>
                  </div>
               </div>
           
@endsection
