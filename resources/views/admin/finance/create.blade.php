@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $rm =  $permission->check_menu_permission(61,$permission->role_id,1);
   $batch =  $permission->check_menu_permission(71,$permission->role_id,1);
   $individual =  $permission->check_menu_permission(72,$permission->role_id,1);

   $permission = $permission->check_permission(61,$permission->role_id); 
   //print_r($permission);
@endphp
<style>
.maxch p{    color: #495057;
    font-size: 14px;
    font-weight: 600;    margin-bottom: 0px;}
.maxch span{
	
	}
.maxch{}
</style>
<!-- Content Header (Page header) -->

  <div class="status">
    <h1>Create Invoice </h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
               <ul class="summarytab">
               @if(isset($rm) && $rm->view==1 && $admin_id !=1)
                    <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
					   @endif
                    @if(isset($rm) && $rm->create==1 && $admin_id !=1)
                    <li   class="activeul"><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                    @endif
                    @if(isset($batch) && $batch->view==1 && $admin_id !=1)
                    <li  ><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                    @endif
                    @if(isset($individual) && $individual->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                    @endif
                    @if(isset($permission) && $permission->view==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                    @endif
                    @if(isset($permission) && $permission->create==1 && $admin_id !=1)
                    <li><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                    @endif
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif


       <div class="">
       {!! Form::open(['method' => 'POST','class'=>'forunit', 'url' => url('opslogin/invoice'), 'files' => true]) !!}

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
                    width: 100%;
                    border: 1px solid #b6afaf;
                    color: #000;
                    background: #fff;
                    
                }
                .flex-container .tabelnew p{margin-bottom: 0px;
                    border-bottom: 1px solid #000; padding-left: 10px; color:#000;}
                  .flex-container .tabelnew p:nth-child(1) {
                  background-color: #d9d9d9!important;
                    width: 100%; color:#000;
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
    .mticon{    margin-top: -10px;}
    .mticon2{    margin-top: -15px;}
    .wthclass2 {
    width: 205px;
}
               </style>

<!--new-->
<div class="">
                   
                     <div class="row asignbg">
                        <div class="col-lg-7 Themanage">
							   <h5>Company Info</h5>
                        <div class=" row dateform">
                              <div class="col-lg-4">
                                 <label>Company Name:</label>
                              </div>
                              <div class="col-lg-8">
                                 <input type="text" name="comp_name" class="form-control" value="{{isset($invoice->comp_name)?$invoice->comp_name:$property->management_company_name}}">
                                 <input type="hidden" id="accountid" value="{{$property->id}}">
                              </div>
                           </div>
							     
						        <div class="row dateform">
                              <div class="col-lg-4">
                                 <label>Address:</label>
                              </div>
                              <div class="col-lg-8">
                                 <textarea name="comp_address" class="form-control" rows="3">{{isset($invoice->comp_address)?$invoice->comp_address:$property->management_company_addr}}
                              </textarea>
                              </div>
                           </div>
						   
                     @if(1==2)
						   <h5>Account  Info</h5>
						    <div class=" row dateform">
                              <div class="col-lg-4">
                                 <label>Account Name:</label>
                              </div>
                              <div class="col-lg-4">
                                 <input name="bank_name" type="text" class="form-control" value="{{isset($invoice->bank_name)?$invoice->bank_name:''}}">
                              </div>
                           </div>
						   
						      <div class=" row dateform">
                              <div class="col-lg-4">
                                 <label>Account No:</label>
                              </div>
                              <div class="col-lg-4">
                                 <input name="account_no" type="text" class="form-control" value="{{isset($invoice->account_no)?$invoice->account_no:''}}">
                              </div>
                           </div>
						    <div class=" row dateform">
                              <div class="col-lg-4">
                                 <label>Bank Code:</label>
                              </div>
                              <div class="col-lg-4">
                                 <input name="bank_code" type="text" class="form-control" value="{{isset($invoice->bank_code)?$invoice->bank_code:''}}">
                              </div>
                           </div>
						        <div class="row dateform">
                              <div class="col-lg-4">
                                 <label>Address:</label>
                              </div>
                              <div class="col-lg-8">
                                 <textarea name="bank_address" class="form-control" rows="3"> {{isset($invoice->bank_address)?$invoice->bank_address:''}}
                              </textarea>
                              </div>
                           </div>                        
                        @endif
                     </div>
                        <div class="col-lg-4 Themanage2">
                           <h5>Invoice Info</h5>
                           <div class="form-inline row dateform">
                              <div class="col-lg-6">
                                 <label>Invoice Date:</label>
                              </div>
                              <div class="col-lg-6">
                              <div id="">
                                 <input name="month" type="text" class="form-control datetext9" required="true"  value="{{isset($invoice->month)?date('Y-m-d',strtotime($invoice->month)):''}}">
                              </div></div>
                           </div>
						    
                          
                        </div>
						</div>
                        <div class="row">
                        <div class="col-lg-12">
						 <div class="overflowscroll2">
                           <table class="gap">
                              <thead>
                                 <tr>
                                    <th class="wthclass2">Reference Type</th>
                                    <th class="wthclass">Reference No</th>
                                    <th>Description</th>
                                    <th class="wthclass">Amount S$</th>
                                    <th style="width:50px;">Actions</th>
                                 </tr>
                              </thead>
                              <tbody id="myTable">
                              @php
                              $count =0;
                              @endphp
                              @for($i=1;$i<=8;$i++)
                              @php
                                if($i ==1){
                                   $count++;
                                    $display_style = "";
                                    $required = 'required="true"';
                                    }
                                 else if(isset($invoice_details[$i]['id']) && $invoice_details[$i]['id'] >=1){
                                    $count++;
                                    $display_style = "";
                                    $required = '';
                                    }
                                else{
                                    $display_style = "display:none";
                                    $required = '';
                                 }

                                 //echo $required;
                              @endphp
                                 <tr id="add_field{{$i}}" style="{{$display_style}}">
                                    <td class="roundleft">{{ Form::select("reference_type[$i]", ['' => '--Select Type--'] + $types,isset($invoice_details[$i]['reference_type'])?$invoice_details[$i]['reference_type']:'', ['class'=>'form-control mticon','onchange'=>"getinvoicetypeamount(\"type$i\",\"amount$i\")",'id'=>"type$i", "width"=>"80px", $required]) }}
                                    </td>
                                    <td class="spacer"><input type="text" class="form-control" id="reference{{$i}}" name="reference[{{$i}}]" value="{{isset($invoice_details[$i]['reference'])?$invoice_details[$i]['reference']:''}}" {{$required}}></td>
                                    <td class="spacer"><input class="form-control" name="description[{{$i}}]" id="description{{$i}}" value="{{isset($invoice_details[$i]['description'])?$invoice_details[$i]['description']:''}}" {{$required}} style="    height: 45px;"> </input></td>
                                    <td class="spacer"><input type="text" class="form-control" id="amount{{$i}}" name="amount[{{$i}}]" value="{{isset($invoice_details[$i]['amount'])?$invoice_details[$i]['amount']:''}}" {{$required}}></td>
                                    <td class="roundright">
                                    @if($i >1)
                                       <a href="#"  title="Delete" onclick='hiderow("add_field{{$i}}","{{$i}}")' ><img src="{{url('assets/admin/img/deleted.png')}}" class="mticon2"></a>
                                    @endif
                                    </td>
                                 </tr>
                                 @endfor
                              </tbody>
                              
                           </table>
						   </div>
                           <a class="addrow addrowbut mt-2 mb-2 float-right" type="button" onclick="showmore()" style="width: auto; text-decoration: none;" id="buttonsection">
                        <img src="{{url('assets/img/plus.png')}}" class="upimg"/> <br> Add Row
                        </a>
                        <input type="hidden" id="rowcount" value="{{$count}}">
                        <input type="hidden" id="maxcount" value="8">
                        </div>
                        </div>
                        <!--<div class="col-lg-12">
						   <a class="submitnew float-left"  data-toggle="modal" data-target="#SelectUnit">Select Unit</a>
                     </div>  -->
                     

                      <div class="row asignbg">
                     <div class="col-lg-12">
                                 <label>Notes:</label>
                              </div>
                        <div class="col-lg-12 araepay">
                        <textarea name="notes" class="form-control" rows="12" id="create_notes"  onkeyup="countChar(this)">{{isset($invoice->notes)?$invoice->notes:''}}</textarea>
                        </div>
						 <div class="col-lg-12 maxch">
                       <p class="float-right"><span id="charNum"> </span></p>
						</div>
                     </div>
                     <div class="row">
                     <div class="col-lg-8">
                     @if(isset($invoice->status)  && $invoice->status==1)
                        <input type="hidden" name="invoice_info_id" value="{{isset($invoice->id)?$invoice->id:''}}">
                     @endif                  
                        <button class="submitnew float-left" type="submit" name="action" value="save_as_draft">SAVE AS DRAFT</button>
                        <br><br>
                     </div>
                     <div class="col-lg-4">
                        <button class="submitnew float-right" type="submit" name="action" value="submit_preview">SUBMIT & PREVIEW</button>
                     </div></div>
                  </div>
<!--new-->
@if(1==2)
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
                                          @foreach($build->unites as $unit)
                                            <p>
                                              <input type="checkbox" name="units[]" value="{{$unit->id}}" class="class{{$k+1}}">&nbsp; #{{$unit->unit}}
                                            </p>
                                          @endforeach
                                        @endif 
                                      </div>
                                    @endforeach
                                    </div>
                                  @endif 
                              </div>
                           <!--<button class="submitnew float-right mt-3 " data-dismiss="modal">Close</button-->
                        </div>
                      </div>
                    </div>
                  @endif
                  </div> 
                  {!! Form::close() !!}   
               </div>   
               
            </div>
         </div>
      </section>


@stop




