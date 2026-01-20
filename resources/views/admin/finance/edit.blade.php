@extends('layouts.adminnew')




@section('content')

 <div class="status">
    <h1>invoice - update remark</h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li class="activeul"><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                     <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                     <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
                 {!! Form::model($InvoiceObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/invoice/'.$InvoiceObj->id)]) !!}

                  <div class="row asignbg editbg">
                 
                <div class="col-lg-2">
                           <div class="form-group">
                              <label >invoice no :</label>
                            <h4>  {{$InvoiceObj->invoice_no}} </h4>
                                </div>
                           </div>
						   <div class="col-lg-2">
                           <div class="form-group">
                              <label>invoice date :</label>
                             <h4>  {{isset($InvoiceObj->invoice_date)?date('d/m/Y',strtotime($InvoiceObj->invoice_date)):''}}</h4>
                              
                           </div>
                           </div>
						   <div class="col-lg-2">
                           <div class="form-group ">
                              <label>due date :</label>
                              <h4> {{isset($InvoiceObj->due_date)?date('d/m/Y',strtotime($InvoiceObj->due_date)):''}}</h4>
                              
                           </div>
                           </div>
						   <div class="col-lg-2">
                           <div class="form-group">
                              <label>building :</label>
                              <h4> {{isset($InvoiceObj->getunit->buildinginfo->building)?$InvoiceObj->getunit->buildinginfo->building:''}}</h4>
                           </div>
                           </div>
						   <div class="col-lg-2">
                           <div class="form-group">
                              <label>unit :</label>
                             <h4>  #{{isset($InvoiceObj->getunit->unit)?Crypt::decryptString($InvoiceObj->getunit->unit):''}}</h4>
                           </div>
                           </div>
						   <div class="col-lg-2">
                           <div class="form-group ">
                              <label>total amount :</label>
                             <h4> ${{$InvoiceObj->payable_amount}}</h4>
                           </div>
                           </div>
<div class="col-lg-6">
                           <div class="form-group">
                              <label>purchaser :</label>
                              <h4> @if(isset($purchasers))
                                       @foreach($purchasers as $k => $purchaser)
                                          @php if($k >0)
                                             echo "<br /> "; 
                                          @endphp

                                          {{Crypt::decryptString($purchaser->name)}} {{isset($purchaser->userinfo->last_name)?Crypt::decryptString($purchaser->userinfo->last_name):''}} {{isset($purchaser->userinfo->phone)?'(Ph: '.Crypt::decryptString($purchaser->userinfo->phone).')':''}}
                                       @endforeach
                                    @endif
                              </h4>
                             
                           </div>
                           </div>
						   <div class="col-lg-6">
                           <div class="form-group ">
                              <label >Remarks :</label>
                          {{ Form::textarea('remarks', null, ['class'=>'form-control','rows' => 5]) }}
                           </div>
                           </div>
                
              
               
                     </div>
                     <div class="row">
                        <div class="col-lg-12">
                           <button type="submit" class="submit mt-2 float-right">submit</button>
                        </div>
                     </div>
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>

</section>
@stop


