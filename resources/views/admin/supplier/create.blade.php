@extends('layouts.adminnew')




@section('content')

@php 
   $permission = Auth::user();
   $admin_id = Auth::user()->id;
   $account_id = Auth::user()->account_id;
   $permission = $permission->check_permission(79,$permission->role_id); 
@endphp
      <style>
#main {
  margin: 50px 0;
}

#main #faq .card {
  margin-bottom: 20px;
  border: 0;
}
.accordion>.card:(:last-of-type) {
    border-bottom-right-radius: 20px;
    border-bottom-left-radius: 20px;
	    border-top-right-radius: 20px;
    border-top-left-radius: 20px;
}
.accordion>.card {
    border-bottom-right-radius: 20px;
    border-bottom-left-radius: 20px;
	    border-top-right-radius: 20px;
    border-top-left-radius: 20px;
}
#main #faq .card .card-header {
  border: 0;
  -webkit-box-shadow: 0 0 20px 0 rgba(213, 213, 213, 0.5);
          box-shadow: 0 0 20px 0 rgba(213, 213, 213, 0.5);
  border-radius: 2px;
  padding: 0;
}

#main #faq .card .card-header .btn-header-link {
    display: block;
    text-align: left;
    background: #DFCFB5;
    color: #495057;
    padding: 20px;
    font-weight: 600;
}

#main #faq .card .card-header .btn-header-link:after {
  content: "\f107";
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  float: right;
}

#main #faq .card .card-header .btn-header-link.collapsed {
  color: #495057;
}
.btn.focus, .btn:focus {
    outline: 0;
    box-shadow: none!important;
}
#main #faq .card .card-header .btn-header-link.collapsed:after {
  content: "\f106";
}


#main #faq .card .collapse {
  border: 0;
}

#main #faq .card .collapse.show {
    background: transparent;
    line-height: 24px;

  color: #222;
}
.asignaccord{
border-radius: 0px!important;
    padding: 0px 0px!important;
}
      </style>
<!-- Content Header (Page header) -->

  <div class="status">
    <h1>supplier management - add  </h1>
  </div>
<div class="row">
               <div class="col-lg-12">
              <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/supplier')}}">Summary</a></li>
                    
                     @if(isset($permission) && $permission->create==1 )
                        <li   class="activeul"><a href="{{url('/opslogin/supplier/create')}}">Create new supplier</a></li>
                     @endif
                    
                  </ul>
               </div>
               </div>
  <div class="">
    @if (session('status'))
  <div class="alert alert-info">
  {{ session('status') }}
  </div>
  @endif
  {!! Form::open(['method' => 'POST','class'=>'forunit ', 'id'=>"user-form", 'url' => url('opslogin/supplier'), 'files' => true]) !!}

					 <div id="main">
					 <div class="accordion" id="faq">
                    <div class="card">
                        <div class="card-header" id="faqhead1">
                            <a href="#" class="btn btn-header-link" data-toggle="collapse" data-target="#faq1"
                            aria-expanded="true" aria-controls="faq1">Name And Contact</a>
                        </div>

                        <div id="faq1" class="collapse show" aria-labelledby="faqhead1" data-parent="#faq">
                            <div class="">
                              <div class="row asignbg asignaccord">
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Title: </label>
                              {{ Form::select('title', ['Mr'=>'Mr','Mrs'=>'Mrs','Miss'=>'Miss','Ms'=>'Ms','Dr'=>'Dr'], '', ['class'=>'form-control ']) }}
                              </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>First name: <span>*</span></label>
                              {{ Form::text('first_name', null, ['class'=>'form-control','required' => true]) }}
                              </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Middle name: </label>
                              {{ Form::text('middle_name', null, ['class'=>'form-control']) }}
                              </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Last name: <span>*</span></label>
                              {{ Form::text('last_name', null, ['class'=>'form-control','required' => true]) }}
                              </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Suffix: </label>
                              {{ Form::text('suffix', null, ['class'=>'form-control','required' => true]) }}
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>Supplier display name :</label>
                              {{ Form::text('supplier_display_name', null, ['class'=>'form-control']) }}

                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Company name: </label>
                              {{ Form::text('company_name', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						   <div class="col-lg-3">
                           <div class="form-group">
                              <label>Email <span>*</span>: </label>
                              {{ Form::email('email', null, ['class'=>'form-control','required' => true]) }}
                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Phone number: <span>*</span></label>
                              {{ Form::text('phone_number', null, ['class'=>'form-control','required' => true]) }}
                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Mobile number: </label>
                              {{ Form::text('mobile_number', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
                      <div class="col-lg-3">
                           <div class="form-group">
                              <label>Fax: </label>
                              {{ Form::text('fax', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						 <!--<div class="col-lg-3">
                           <div class="form-group">
                              <label>Other: </label>
                              {{ Form::text('others', null, ['class'=>'form-control']) }}
                           </div>
                        </div> -->
						 <div class="col-lg-3">
                           <div class="form-group">
                              <label>Website: </label>
                              {{ Form::text('website', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="faqhead2">
                            <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                            aria-expanded="true" aria-controls="faq2">Address</a>
                        </div>

                        <div id="faq2" class="collapse show" aria-labelledby="faqhead2" data-parent="#faq">
                            <div class="">
							 <div class="row asignbg asignaccord">
                                <div class="col-lg-3">
                           <div class="form-group">
                              <label>address line 1: </label>
                              {{ Form::text('address1', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>address line 2: </label>
                              {{ Form::text('address2', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>City: </label>
                              {{ Form::text('city', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						  <!--<div class="col-lg-3">
                           <div class="form-group">
                              <label>Province: </label>
                              {{ Form::text('province', null, ['class'=>'form-control']) }}
                           </div>
                        </div> -->
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Postcode: </label>
                              {{ Form::text('postal_code', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Country: </label>
                              {{ Form::select('country', $countries, '', ['class'=>'form-control ']) }}
                           </div>
                        </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="faqhead3">
                            <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                            aria-expanded="true" aria-controls="faq3">Notes And Attachments</a>
                        </div>

                        <div id="faq3" class="collapse show" aria-labelledby="faqhead3" data-parent="#faq">
                            <div class="">
							 <div class="row asignbg asignaccord">
                               <div class="col-lg-3">
                           <div class="form-group">
                              <label>Notes: </label>
                              {{ Form::textarea('notes', null, ['class'=>'form-control','rows'=>4]) }}
                              </div>
                        </div>
                        <div class="col-lg-5 file-5">
                           <div class="form-group">
                              <label class="fcid">Attachments</label>
                              <input id="picture" name="attachement" class="form-control " type="file">
                              <label for="file-5" class="file-55"><span>Max file size: 2 MB</span></label>
                           </div>
                        </div>
                            </div>
                        </div>
                        </div>
                    </div>
					<div class="card">
                        <div class="card-header" id="faqhead4">
                            <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq4"
                            aria-expanded="true" aria-controls="faq4">Additional info</a>
                        </div>

                        <div id="faq4" class="collapse show" aria-labelledby="faqhead4" data-parent="#faq">
                            <div class="">
							 <div class="row asignbg asignaccord">
                            <div class="col-lg-12 mt-3 mb-2">
						<b>Taxes</b>
						</div>
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>Business ID No. / National Insurance No : </label>
                              {{ Form::text('business_id', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						<div class="col-lg-12 mt-3 mb-2">
						 <b>Expense rates</b>
						</div>
						  <div class="col-lg-3">
                           <div class="form-group">
                              <label>Billing rate (/hr) : </label>
                              {{ Form::text('billing_rate', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						<div class="col-lg-12 mt-3 mb-2">
						<b>Payments</b>
						</div>
						 <div class="col-lg-3">
                           <div class="form-group">
                              <label>Terms : </label>
                              {{ Form::select('payment_term', ['1'=>'Due on Receipt','10'=>'Net 10','15'=>'Net 15','30'=>'Net 30','60'=>'Net 60'], '', ['class'=>'form-control ']) }}
                           </div>
                        </div>
						 <div class="col-lg-3">
                           <div class="form-group">
                              <label>Account no. : </label>
                              {{ Form::text('account_no', null, ['class'=>'form-control','required' => true]) }}
                           </div>
                        </div> 
						<div class="col-lg-12 mt-3 mb-2">
						   <b>Accounting</b>
						</div>
						<div class="col-lg-3">
                           <div class="form-group">
						
                              <label>Default expense category : </label>
                              {{ Form::select('expense_category', ['1'=>'Category 1'], '', ['class'=>'form-control ']) }}
                           </div>
                        </div>
					<div class="col-lg-12 mt-3 mb-2">
						<b>Opening balance</b>
						</div>
						<div class="col-lg-3">
                           <div class="form-group">
                              <label>Opening balance : </label>
                              {{ Form::text('opening_balance', null, ['class'=>'form-control']) }}
                           </div>
                        </div>
						<div class="col-lg-3">
                           <div class="form-group">
                              <label>As of: </label>
                  <div id="sandbox6">
                     {{ Form::text('opening_balance_date', null, ['class'=>'form-control','id'=>"datetext6",'required' => true]) }}

                              </div>
                           </div>
                        </div>
								  <div class="col-lg-9"></div>
               <div class="col-lg-3">
               <button type="submit" class="submit mt-3 float-right ">Save</button>
               </div>
                            </div>
                        </div>
                        </div>
                    </div>
				
                </div>
                </div>
               </div>
             
               </form>
                       
               
            </div>
         </div>
      </section>


@stop