@extends('layouts.faq')

@section('content')

               <div class="status">
                  <h1>Feedback</h1>
               </div>
               <div id="main">
                  <div class="container">
                     <div class="accordion" id="faq">
                        <div class="card">
                           <div class="card-header" id="faqhead1">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq1"
                                 aria-expanded="true" aria-controls="faq1">How do I book a facility?</a>
                           </div>
                           <div id="faq1" class="collapse" aria-labelledby="faqhead1" data-parent="#faq">
                              <div class="card-body">
                                 <p>You can do so by tapping on the Facilities Booking icon    on your dashboard and follow the steps below to book a facility.</p>
                                 <p>Step 1: Tap on “+ NEW BOOKING” button</p>
                                 <p>Step 2: Choose the facility that you want to book</p>
                                 <p>Step 3: Select a date for your booking.</p>
                                 <p>Step 4: Choose from the available timeslot (The timeslot will be White if it’s available and Grey if it’s taken.</p>
                                 <p>Step 5: Tap on Submit to send your booking.</p>
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead2">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                                 aria-expanded="true" aria-controls="faq2">How do I reschedule / cancel my booking?</a>
                           </div>
                           <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                              <div class="card-body">
                                 Kindly contact the managing agent to reschedule / cancel your booking. Do note that to rescheduling of booking is subject to the availability of the facility. 
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
           
@endsection
