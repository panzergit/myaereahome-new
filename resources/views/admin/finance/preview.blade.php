@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>Invoice - Preview</h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     <li    class="activeul"><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
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
         @if($invoice->account_id ==53)
            {!! Form::model($invoice,['method' =>'PATCH','files' => true,'url' => url('opslogin/testinvoicesend/'.$invoice->id),'class'=>'forunit']) !!}
          @else
            {!! Form::model($invoice,['method' =>'PATCH','files' => true,'url' => url('opslogin/invoicesend/'.$invoice->id),'class'=>'forunit']) !!}
         @endif
               <style>
                  .utablechange{    width: 100%!important;}
                  .utablechange td {
                    width: 100%;
                }
                .utablechange th{    width: 100px;
                    display: block;
                    border: none!important;}

                .utablechange th:nth-child(1) {
                  background-color: #d6d3d3;
                    width: 100%;
                }
                .flex-container {
                  display: flex;
                }

                .flex-container .tabelnew {
               
                    border: 1px solid #b6afaf;
                    color: #495057;
                    background: #fff;
                    
                }
                .flex-container .tabelnew p{margin-bottom: 0px;
                    border-bottom: 1px solid #495057; padding-left: 10px; color:#495057;}
                  .flex-container .tabelnew p:nth-child(1) {
                  background-color: #d9d9d9!important;
                    width: 100%; color:#495057;
                    width: 100px;
                }
                .flex-container .tabelnew p:nth-of-type(even) {
                    background-color: #a9f1f9;
                }
                .flex-container .tabelnew p:nth-of-type(odd) {
                    background-color: #fff;
                }
                .flex-container .tabelnew input {    height: auto!important;
    margin-bottom: 0px!important;}
                .alltable td{    color: #495057;
                  font-size: 16px;}
                  .araepay{margin-top: 20px;
                  margin-bottom: 20px;}
                  .Themanage h1{    font-size: 20px;
    color: #495057;
    font-weight: 600;
    margin-bottom: 5px;
    margin-top: -5px;}
                  .Themanage h4{ font-size: 16px;
    text-transform: uppercase; font-weight: 600;
    color: #495057;}
                  .Themanage h6{color: #495057;
                  width: 250px;
                  font-size: 14px;
                  line-height: 22px;
                  font-weight: 600;
                  margin-top: 18px;
                  margin-bottom: 20px;}
                  .araeall p{font-size: 14px;
    line-height: 20px;
    text-align: justify;
    color: #495057;
    font-weight: 600;}
                  .accaddres{    width: 100%;
                  border: 1px solid #607d8b; font-weight: 600;}
                  .accaddres tr{}
                  .accaddres th{    background: #d9d9d9;
                  padding-left: 10px;}
                  .accaddres td{background: #fff;
                  padding-left: 10px;}
                  .araeall table{    width: 100%;
                  color: #495057;
                  font-size: 16px;}
                  .portcheck{     margin-bottom: 20px;
                  margin-top: 20px;   width: 100%;
                  text-align: center;
                  background: #d9d9d9;
                  padding-left: 10px;
                  font-size: 16px;}
                  .foraccount{}
                  .foraccount b{font-size: 16px;
                  color: #495057;}
                  .foraccount p{color: #495057;font-size: 16px;}
				  .thecreate{    background: #fff;
    border-radius: 20px;
    padding: 20px;}
	.usertable thead {
    background: transparent;
}

	.aprldate label{  font-size:16px;  color: #495057;      font-weight: 600;
    font-family: 'Lato', sans-serif !important;}
	.aprldate{    margin-bottom: 0px;}
   .submitnew2{ color: #495057;
        background: #a9f1f9;
    cursor: pointer;
}
   .submitnew2:hover{   background: #fff;  color: #495057;}
   .gapnew th{       font-weight: 600; text-align: left; background-color: #E9E9EA;}
   .gapnew td{color: #495057;
    font-weight: 600;}
   .gapnew tr{        border: 1px solid #607d8b;}
   .gapnew{font-size: 12pt;
    color: #A2A2A2;
    width: 100%;
    border-radius: 15px;
    padding-right: 0px;
    padding-left: 0px;
        margin-top: 10px;}
    </style>

<!--new-->
<div class="crearsection thecreate">
                     <div class="row">
                        <div class="col-lg-7 Themanage">
                           <h4>{{isset($invoice->comp_name)?$invoice->comp_name:''}}</h4>
                           <h6>@php echo isset($invoice->comp_address)?nl2br($invoice->comp_address):'' @endphp </h6>
                        </div>
                        <div class="col-lg-4 Themanage">
                           <h1>Invoice / Statement</h1>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5">
                                 <label><b>Invoice No:</b></label>
                              </div>
                              <div class="col-lg-7">
                                 <label>xxxxxxx-xxxxx</label>
                              </div>
                           </div>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5">
                                 <label><b>Invoice Date:</b></label>
                              </div>
                              <div class="col-lg-7">
                                 <label>{{isset($invoice->month)?date('d/m/Y',strtotime($invoice->month)):''}}</label>
                              </div>
                           </div>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5">
                                 <label><b>Due Date:</b></label>
                              </div>
                              <div class="col-lg-7">
                                 <label>{{isset($invoice->due_date)?date('d/m/Y',strtotime($invoice->due_date)):''}}</label>
                              </div>
                           </div>
                           <div class=" row dateform aprldate">
                              <div class="col-lg-5">
                                 <label><b>Account No:</b></label>
                              </div>
                              <div class="col-lg-7">
                                 <label>xxx#xx-xx</label>
                              </div>
                           </div>
						    <div class=" row dateform aprldate">
                              <div class="col-lg-5">
                                 <label><b>Share Value:</b></label>
                              </div>
                              <div class="col-lg-7">
                                 <label>xx</label>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <table class="accaddres">
                              <tbody>
                                
                                 <tr>
                                    <td colspan="2" class="addrespilt">
                                    Owner Name,<br />
                                    Address
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-6"></div>
                        <div class="col-lg-12">
                           <table class="gapnew ">
                              <thead>
                                 <tr>
                                    <th>Reference Type</th>
                                    <th >Reference No</th>
                                    <th>Description</th>
                                    <th>Amount S$</th>
                                 </tr>
                              </thead>
                              <tbody>
                              @php 
                                 $total_amt =0;
                                 $unit_share_tot_amt =0 ;
                                 $other_tot_amt =0 ;
                              @endphp
                                 @if(isset($invoice_details))
                                    @foreach($invoice_details as $detail)
                                    @php 
                                    if($detail['reference_type']==1 || $detail['reference_type']==2){
                                       $total_amt += $detail['amount']; 
                                       $unit_share_tot_amt += $detail['amount']; 
                                    }
                                    else{
                                       $total_amt += $detail['amount']; 
                                       $other_tot_amt += $detail['amount']; 
                                    }
                                    @endphp
                                    <tr>
                                       <td>{{isset($detail['reference_name'])?$detail['reference_name']:''}}</td>
                                       <td>{{isset($detail['reference'])?$detail['reference']:''}}</td>
                                       <td> {{isset($detail['description'])?$detail['description']:''}}</td>
                                       <td>${{isset($detail['amount'])?$detail['amount']:''}}</td>
                                       
                                    </tr>
                                    @endforeach
                                 @endif
                                 <tr>
                                    <td colspan="2"></td>
                                    <td><b>AMOUNT DUE</b></td>
                                    <td><b>${{number_format($total_amt,2)}}
                                    <input type="hidden" name="unit_share_tot_amt" value="{{$unit_share_tot_amt}}">
                                    <input type="hidden" name="other_tot_amt" value="{{$other_tot_amt}}">

                                    </b></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12 araeall"><p>@php echo nl2br($invoice->notes) @endphp </p>
                           
                        </div>
                     </div>
                     
                     
                  </div>
				  <div class="col-lg-12 pl-0">
						   <a class="submitnew submitnew2 float-left mt-3"  data-toggle="modal" data-target="#SelectUnit">Select Unit</a>
                     </div> 
                     <div class="clearfix"></div>
                     <div class="col-lg-12 ">
                     <a href='{{url("/opslogin/invoice/back/$invoice->id")}}' class="submitnew  float-left mt-3">Back</a>
                     <button class="submitnew float-right  mt-3" type="submit" name="action" value="send_invoice">SUBMIT</button>
                     </div> 
					 <!-- popup -->
					     <div class="modal fade bd-example-modal-lg" id="SelectUnit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Select Unit</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                        <div style=" overflow: scroll; width: auto;      border-bottom: 1px solid #b1a9a9;
    border-right: 1px solid #b1a9a9;   height: 400px;">

                                
                                  @if(isset($buildings))
                                    <div class="flex-container">
                                    @foreach($buildings as $k => $build)
                                      <div class="tabelnew">                    
                                        <p>
                                          <input type="checkbox" class="id{{$k+1}}"> {{$build->building}}
                                        </p>

                                        @if(isset($build->unites))
                                        @php
                                          $sorted_unites = $build->unites()->get()->sortBy(fn($i) => (!isset($i->unit) ? '0' : Crypt::decryptString($i->unit)))->values();
                                        @endphp
                                          @foreach($sorted_unites as $unit)
                                             @php
                                                $invObj = new \App\Models\v2\FinanceInvoice();
                                                $result = $invObj->checkUnit($unit->id,$invoice->month);
                                               //echo $unit->id. " ". $invoice->month;
                                             @endphp
                                            <p @if($result ==1) style="color:red" @endif>
                                              <input type="checkbox" name="units[]" value="{{$unit->id}}" class="class{{$k+1}}" @if($result ==1) disabled="disabled" @endif>&nbsp; #{{Crypt::decryptString($unit->unit)}}
                                            </p>
                                          @endforeach
                                        @endif 
                                      </div>
                                    @endforeach
                                    </div>
                                  @endif 
                              </div>
                              <div class="submitnew  float-right mt-3 " data-dismiss="modal" style="cursor: pointer;">Confirm</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  {!! Form::close() !!}   
               </div>   
               
            </div>
         </div>
      </section>


@stop


