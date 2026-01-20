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
                                 aria-expanded="true" aria-controls="faq1">How do I submit a feedback?</a>
                           </div>
                           <div id="faq1" class="collapse " aria-labelledby="faqhead1" data-parent="#faq">
                              <div class="card-body">
                                 <p>You can do so by tapping on the Feedback icon   on your dashboard and follow the steps below to submit your feedback.</p>
                                 <p>Step 1: Tap on “+ NEW FEEDBACK” button</p>
                                 <p>Step 2: Choose the feedback category</p>
                                 <p>Step 3: Upload a photo (if any)</p>
                                 <p>Step 4: Enter the subject and your feedback</p>
                                 <p>Step 5: Tap on Submit to send your feedback.</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
           
@endsection
