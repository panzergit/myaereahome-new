@extends('layouts.faq')

@section('content')

               <div class="status">
                  <h1>Appointment for Joint Inspection</h1>
               </div>
               <div id="main">
                  <div class="container">
                     <div class="accordion" id="faq">
                        <div class="card">
                           <div class="card-header" id="faqhead1">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq1"
                                 aria-expanded="true" aria-controls="faq1">How many appointments can I book?</a>
                           </div>
                           <div id="faq1" class="collapse" aria-labelledby="faqhead1" data-parent="#faq">
                              <div class="card-body">
                                 You are allowed to book only 1 appointment. 
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead2">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                                 aria-expanded="true" aria-controls="faq2">How do I reschedule / cancel an appointment?</a>
                           </div>
                           <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                              <div class="card-body">
                                 Kindly contact the managing agent to do so. You can book a new appointment only after the managing agent has cancelled your appointment from their system.  
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
           
@endsection
