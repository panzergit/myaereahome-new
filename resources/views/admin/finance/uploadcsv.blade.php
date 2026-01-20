@extends('layouts.adminnew')




@section('content')



<!-- Content Header (Page header) -->

  <div class="status">
    <h1>import from csv </h1>
  </div>
  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li ><a href="{{url('/opslogin/paymentoverview#vm')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/invoice/create')}}">Create MF/SF Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice#vm')}}">Manage Batch Invoice</a></li>
                     <li><a href="{{url('/opslogin/invoice_report#vm')}}">Manage Individual Invoices</a></li>
                     <li><a href="{{url('/opslogin/paidlists#vm')}}">Filter Payments</a></li>
                     <li   class="activeul"><a href="{{url('/opslogin/invoice/uploadcsv#vm')}}">Import Invoices</a></li>
                  </ul>
               </div>
               </div>
      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
       <div class="fileuplod">
                 {!! Form::open(['method' => 'POST','class'=>"forunit", 'url' => url('opslogin/invoice/importcsv'), 'files' => true]) !!}

                  <div class="row asignbg">
                  @if(@Auth::user()->role_id ==1)
                <div class="col-lg-4">
                           <div class="form-group">
          <label>Property:</label>
             {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, null, ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getroles()']) }}
                           </div>
                </div>
                @endif
                <div class="col-lg-4">
                           <div class="form-group ">
          <label>upload csv file:</label>
              {{ Form::file('csv_file', null, ['class'=>'form-control picture2','required' => false]) }}

                           </div>
                </div>
                 <div class="col-lg-12">
                           <button type="submit" class="submit mt0-3 float-right">submit</button>
                        </div>
                </div>
            
 
                <!--<div class="col-lg-8">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">BUILDING NUMBER:<br>
                              Only supports numbers</label>
                              <div class="col-sm-5">
                                {{ Form::text('building_no', null, ['class'=>'form-control','required' => true,'placeholder' => 'Enter Building Number']) }}
                              </div>
                           </div>
               
                </div> -->
               
                     </div>

          
                    
                     <!--div class="row">
                        <div class="col-lg-6">
                           <button type="submit" class="submit mt-2 float-right">SUBMIT</button>
                        </div>
                     </div-->
                    {!! Form::close() !!}
               </div>   
               
            </div>
         </div>
      </section>


@stop