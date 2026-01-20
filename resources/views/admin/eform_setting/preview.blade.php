@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>{{$eformObj->gettype->name}} - preview </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li  class="activeul"><a href="{{url('/opslogin/configuration/eform_setting#eformsettings')}}">E-Form Settings </a></li>
                     <li ><a href="{{url('/opslogin/configuration/payment_setting#paymentsettings')}}">Payment Settings </a></li>
                     <li ><a href="{{url('/opslogin/configuration/holiday_setting#holidayssettings')}}">Public Holidays Settings  </a></li>
                  </ul>
               </div>
               </div>
       <div class="">
       <div class="row forunit">
                     <div class="col-lg-4"></div>
                     <div class="col-lg-4 emobile mt-4">
                        <form>
                           <div class="col-lg-12">
                              <h3>OFFICIAL USE</h3>
                              {!!$eformObj->official_notes!!}
                           </div>
                            @if($eformObj->payment_mode_cheque ==1)
                           <div class="col-lg-12">
                              <label class="col-form-label">CHEQUE NUMBER : 
                              </label>
                              <input  type="text" name="" class="form-control" placeholder="Enter cheque number" id="">
                           </div>

                           <div class="col-lg-12">
                              <label class="col-form-label">BANK: 
                              </label>
                              <input  type="text" name="" class="form-control" placeholder="Enter bank" id="">
                           </div>
                           @endif

                           <div class="col-lg-12">
                              <label class="col-form-label">OFFICIAL RECEIPT NO : 
                              </label>
                              <input  type="text" name="" class="form-control" placeholder="Enter official receipt no" id="">
                           </div>
                           <div class="col-lg-12">
                              <label class="col-form-label">ACKNOWLEDGE BY RESIDENT : 
                              </label>
                              <input  type="text" name="" class="form-control" placeholder="Enter acknowledge by resident" id="">
                           </div>
                           <div class="col-lg-12">
                              <label class="col-form-label">NAME OF MANAGER RECEIVED : 
                              </label>
                              <input  type="text" name="" class="form-control" placeholder="Enter name of manager received" id="">
                           </div>
                           <div class="col-lg-12">
                              <label class="col-form-label">DATE: 
                              </label>
                             <div id="sandbox2">
                                    <input id="datepicker2" type="text" class="form-control" value="">
                                 </div>
                           </div>
                           <div class="col-lg-12">
                              <label class="col-form-label">SIGNATURE: 
                              </label>
                              <textarea  type="text" class="form-control">Signature</textarea>
                           </div>
                           
                        </form>
                     </div>
                  </div>
               </div>
         </div>   
         

</section>
@stop


