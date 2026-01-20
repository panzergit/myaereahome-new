@extends('layouts.adminnew')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <ul class="summarytab">
            <li class="activeul"><a href="{{url('/opslogin/defects#defect')}}">Summary</a></li>
        </ul>
    </div>
</div>

@if (session('status'))
    <div class="alert alert-info">
        {{ session('status') }}
    </div>
@endif
<style>
   .forunit input {
    margin-bottom: 0px;
}
.digitalup{       margin-bottom: 20px; border: 1px solid white;}
				.digitalup td{padding:6px}
				.digitalup tr{color:#fff;}
				.digitalup b{color:#fff;}
				.digitalup input{width:200px;}
				.digitalup p{margin-top: 20px;
    position: absolute; }
    .w30{width:300px;}
	.messagecls{    font-weight: 600;}
	.alert-warning {
    color: #6c6161;
    background-color: #dfcfb5;
    border-color: #dfcfb5;
    font-size: 13px;
}
</style>
    <div class="">
        <form method="POST" class="forunit" action="{{ url('opslogin/defects/final-inspection-update/'.$defectObj->id) }}">
            {{ csrf_field() }}
            <div class="col-lg-12 asignFace">
                <h2>defect Update</h2>
            </div>
            <input type="hidden" name="def_id" value="{{$defectObj->id}}">
                <div class="row asignbg editbg">
				    <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>ticket : 
                              </label>
                             <h4>{{$defectObj->ticket}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group  ">
                         
                              <label>ticket status 
                              </label>
                              <h4 style="font-size:14px;">
                              {{ $defectStatus[$defectObj->status] ?? '' }}
                             </h4>
                           </div>   
                           </div> 
 <div class="col-lg-2 col-6">						   
                           <div class="form-group ">
                             
                              <label>unit no : 
                              </label>
                            <h4>  {{isset($defectObj->getunit->unit)?Crypt::decryptString($defectObj->getunit->unit):''}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-2 col-6">
                           <div class="form-group ">
                              <label>submitted by : 
                              </label>
                             <h4> {{isset($defectObj->user->name)?Crypt::decryptString($defectObj->user->name):''}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-2 col-6">
                           <div class="form-group">
                             
                              <label>submitted date : 
                              </label>
                              
                             <h4> {{date('d/m/y',strtotime($defectObj->created_at))}}</h4>
                             
                           </div>
                           </div>
						  					
                </div>
                    
                <div class="col-lg-12 asignFace">
                    <h2>Final Inspection</h2>
                </div>
                 
                <div class="row asignbg editbg">
                    
                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>appt date : 
                            </label><h4>{{isset($defectObj->inspection->appt_date)?date('d/m/y',strtotime($defectObj->inspection->appt_date)):''}}</h4>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>appt time : </label>
                            <h4> {{isset($defectObj->inspection->appt_time)?$defectObj->inspection->appt_time:''}}</h4>
                        </div>
                    </div>
					<div class="col-lg-3">
                        <div class="form-group">
                            <label>appt status:</label>
                            @if($inspectionObj->status ==1)
                            <h4 class="col-form-label">Cancelled</h4>
                            @else
                            {{ Form::select('inspection_status', ['0' => 'New','2'=>'On Schedule','4'=>'In Progress','3'=>'Done'], $inspectionObj->status, ['class'=>'form-control','id'=>'status','onchange'=>'getfields()']) }}
                            @endif
                            @php
                                if(1==2) {
                                 if(isset($inspectionObj->status) && $inspectionObj->status==0)
                                    echo "New";
                                 else  if(isset($inspectionObj->status) && $inspectionObj->status==1)
                                    echo "Cancelled";
                                 else  if(isset($inspectionObj->status) && $inspectionObj->status==2)
                                    echo "On Schedule";
                                 else  if(isset($inspectionObj->status) && $inspectionObj->status==3)
                                    echo "Done";
                                 else  if(isset($inspectionObj->status) && $inspectionObj->status==4)
                                    echo "In Progress";
                              }
                              @endphp
                        </div>
                    </div>
                    @if($inspectionObj->status ==1)
					<div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label class="">reason</label>
                            <h4>{{$inspectionObj->reason}}</h4>
                        </div>
                    </div>
                    @endif
                    @if($inspectionObj->status !=1)
                    <div class="col-lg-3 mt0-2">
                        <div class="caneclbook mt-3 ml-3" style="width:100%">
                            <a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$inspectionObj->id}}" class="open-dialog"><span>cancel appointment</span></a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-lg-12 col-6" id="reminderflds" class="sendremark" @if($inspectionObj->status!=4) style="display:none" @endif>
                        <div class="row">
						    <div class="col-lg-3 col-6">
                                <div class="form-group" id="sandbox">
                                    <label>key handover date:</label>
                                    {{ Form::text('progress_date', $inspectionObj->progress_date!='0000-00-00' ? $inspectionObj->progress_date : '', ['class'=>'form-control','id'=>'progress_date']) }}
                                </div>
                            </div>
							<div class="col-lg-3 col-6">
                                <div class="form-group ">
                                    <label>send reminder in :</label>
                                    {{ Form::text('reminder_in_days', isset($inspectionObj->reminder_in_days)?$inspectionObj->reminder_in_days:'', ['class'=>'form-control']) }} 
                                    <label class="col-form-label">Days(s)</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="form-group ">
                                    <label >email addresses:<br><p class="pcp">(Separate email by comma) </p></label>
                                    {{ Form::textarea('reminder_emails', isset($inspectionObj->reminder_emails)?$inspectionObj->reminder_emails:'', ['class'=>'form-control','rows'=>1,'required' => false,'placeholder' => 'Enter Reminder Email(s)']) }}
                                </div>
                            </div>
							<div class="col-lg-3 col-6">
                                <div class="form-group ">
                                    <label>  email message:</label>
                                    {{ Form::textarea('email_message', isset($inspectionObj->email_message)?$inspectionObj->email_message:'', ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Reminder Email Message']) }}
                                </div>
                            </div>
                        </div>						   
                    </div>
                    
                    @if($inspectionObj->status !=1)
                    <div class="col-lg-6 mt-2">
                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label">select new date :</label>
						    <div class="col-sm-6">
                                <div id="sandbox11">
                                {{ Form::text('appt_date', isset($defectObj->inspection->appt_date)?$defectObj->inspection->appt_date:'', ['class'=>'form-control','required' => true,'id'=>'inspection_date']) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt20">
                            <label  class="col-sm-6 col-form-label">select new tim slot :</label>
						    <div class="col-sm-6">
                            {{ Form::text('appt_time', isset($defectObj->inspection->appt_time)?$defectObj->inspection->appt_time:'', ['class'=>'form-control','required' => true,'id'=>'appt_time','readonly'=>'readonly']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-2" id="timeslotstables"></div>
                    @endif
                    
                </div>
              
                <div class="row">
					<div class="col-lg-12">
                        <a href="{{url('/opslogin/defects')}}?view=summary"  class="Delete float-right">cancel</a>
                        @if($inspectionObj->status !=1)
                        <button type="submit" class="submit float-right mr-3">update</button>
                        @endif
                    </div>
                </div>
            </div>
        {!! Form::close() !!}

         <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/defects/final-inspection-cancel'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <input type="hidden" name="defect" value="{{$defectObj->id}}" />
                <h5 class="modal-title" id="exampleModalCenterTitle">Booking - Cancel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>REASON:</label>
                {{ Form::textarea('reason', null, ['class'=>'form-control','rows'=>4,'required'=>true]) }}
              </div>
              <div class="modal-body">
                 <input type="hidden" name="return_url" value="list">
               <input type="hidden" name="bookId" id="bookId" value="">
               <input type="hidden" name="status"value="1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
             {!! Form::close() !!}
          </div>
        </div>
</section>
 <script type="text/javascript">

      window.onload = function() {
         getInspectionTimeslots();
      };

      function gettime($time,inputId){
        $("#"+inputId).val($time);
      }

      function getfields(){
         if($("#status").val() ==4){
            $("#reminderflds").show(); 
         }else{
            $("#reminderflds").hide(); 
         }
      }
    </script>
@stop


