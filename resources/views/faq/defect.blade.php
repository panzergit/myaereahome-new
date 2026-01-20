@extends('layouts.faq')

@section('content')

               <div class="status">
                  <h1>Defects List</h1>
               </div>
               <div id="main">
                  <div class="container">
                     <div class="accordion" id="faq">
                        <div class="card">
                           <div class="card-header" id="faqhead1">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq1"
                                 aria-expanded="true" aria-controls="faq1">How many defects list can I submit?</a>
                           </div>
                           <div id="faq1" class="collapse" aria-labelledby="faqhead1" data-parent="#faq">
                              <div class="card-body">
                                 We understand that not all defects are identified on a single visit, thus there are currently no restrictions on how many defects list a unit can submit.
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead2">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                                 aria-expanded="true" aria-controls="faq2">How do I view my submitted defect list?</a>
                           </div>
                           <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                              <div class="card-body">
                                 <p>Once you have submitted a defect list, you will receive a notification in your INBOX. Clicking on that notification will bring you to the submitted defect list. </p>
                                 <p>You may also click on the Defects List icon   on your dashboard which will bring you to the summary page where all the previously submitted defects list will be shown there. </p>
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead3">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                                 aria-expanded="true" aria-controls="faq3">How do I differentiate the defects lists that I have submitted?</a>
                           </div>
                           <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#faq">
                              <div class="card-body">
                                 Each submitted defects list comes with a Ticket No and the date that the list was submitted for easy reference.
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead4">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq4"
                                 aria-expanded="true" aria-controls="faq4">Can I amend a list that was previously submitted?</a>
                           </div>
                           <div id="faq4" class="collapse" aria-labelledby="faqhead4" data-parent="#faq">
                              <div class="card-body">
                                 Unfortunately, you can’t. We recommend that you submit a new list so that the defects can be tracked more efficiently. 
                              </div>
                           </div>
                        </div>
                        <div class="card">
                           <div class="card-header" id="faqhead5">
                              <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq5"
                                 aria-expanded="true" aria-controls="faq5">I have submitted a defects list by mistake. How do I delete it?</a>
                           </div>
                           <div id="faq5" class="collapse" aria-labelledby="faqhead5" data-parent="#faq">
                              <div class="card-body">
                                 Kindly contact the managing agent to do so.  
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
           
@endsection
