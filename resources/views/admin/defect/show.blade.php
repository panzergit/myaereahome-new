@extends('layouts.adminnew')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="status">
          <h1>Defect Details</h1>
        </div>
        <ul class="summarytab">
            <li><a href="{{url('/opslogin/defects')}}">Dashboard</a></li>
            <li class="activeul"><a href="{{url('/opslogin/defects').'?view=summary'}}">Summary</a></li>
        </ul>
    </div>
</div>
<style>
    .forunit input  
    {
        margin-bottom: 0px;
    }
	.messagecls{    font-weight: 600;}
	.alert-warning {
    color: #6c6161;
    background-color: #dfcfb5;
    border-color: #dfcfb5;
    font-size: 13px;
}
</style>
<div class="">
    <form class="forunit">
    <div class="col-lg-12 asignFace">
    <h2>Defect Details</h2>
    </div>
    <div class="row asignbg editbg">
	    <div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Ticket : </label>
                <h4>{{$defectObj->ticket}}</h4>
            </div>
        </div>
		<div class="col-lg-4 col-6">
            <div class="form-group">
                <label>Ticket status</label>
                <h4 style="font-size:14px !important;">{{ $defectStatus[$defectObj->status] ?? '' }}</h4>
            </div>   
        </div> 
        <div class="col-lg-2 col-6">						   
            <div class="form-group">
                <label>Unit No :</label>
                <h4>  {{isset($defectObj->getunit->unit)?Crypt::decryptString($defectObj->getunit->unit):''}}</h4>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="form-group ">
                <label>Submitted By :</label>
                <h4> {{isset($defectObj->user->name)?Crypt::decryptString($defectObj->user->name):''}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>Submitted Date :</label>
                <h4> {{date('d/m/y h:i:s A',strtotime($defectObj->created_at))}}</h4>
            </div>
        </div>
    </div>
                    
    <div class="col-lg-12 asignFace">
        <h2>Joint Inspection</h2>
    </div>
    
    <div class="row asignbg editbg">
        @if(empty($inspectionObj->id))
        <div class="col-lg-12">
            <div class="alert alert-warning messagecls" role="alert">Not yet scheduled.</div>
        </div>
        @else
		<div class="col-lg-3 col-6">
            <div class="form-group">
                <label>Appt Date :</label>
                <h4>{{date('d/m/y',strtotime($defectObj->inspection->appt_date))}}</h4>
            </div>
        </div>
		<div class="col-lg-3 col-6">
            <div class="form-group">
                <label>Appt Time :</label>
                <h4> {{isset($defectObj->inspection->appt_time)?$defectObj->inspection->appt_time:''}}</h4>
            </div>
        </div>
		<div class="col-lg-3">
            <div class="form-group">
                <label>Appt Status:</label>
                <h4>{{$inspectionStatus[$inspectionObj->status] ?? ''}}</h4>
            </div>
        </div>
        @if($inspectionObj->status ==1)
		<div class="col-lg-3 col-6">
            <div class="form-group">
                <label class="">Reason :</label>
                <h4>{{$inspectionObj->reason}}</h4>
            </div>
        </div>
        @endif

        @if($inspectionObj->status==4)
        <div class="col-lg-12 col-6" id="reminderflds" class="sendremark" >
            <div class="row">
			    <div class="col-lg-3 col-6">
                    <div class="form-group ">
                        <label>key handover date:</label>
                        {{ Form::text('progress_date', $inspectionObj->progress_date, ['class'=>'form-control','id'=>'progress_date']) }}
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
        @endif
        @endif
    </div>
              
    <div class="col-lg-12 asignFace">
        <h2>Final Inspection</h2>
    </div>
    <div class="row asignbg editbg">
		@if(!$defectObj->finalInspection()->exists())
		<div class="col-lg-12">
		    <div class="alert alert-warning messagecls" role="alert">Not yet scheduled.</div>
		</div>
		@else
		<div class="col-lg-3 col-6">
            <div class="form-group">
                <label>Appt date : </label>
                <h4>{{ \Carbon\Carbon::parse($defectObj->finalInspection->appt_date)->format('d/m/y') }}</h4>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="form-group">
                <label>Appt Time : </label>
                <h4> {{ $defectObj->finalInspection->appt_time}}</h4>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Appt Status:</label>
                <h4>{{$inspectionStatus[$defectObj->finalInspection->status] ?? ''}}</h4>
            </div>
        </div>
        @if($defectObj->finalInspection->status==1)
		<div class="col-lg-3 col-6">
		    <div class="form-group">
		        <label class="">Reason :</label>
                <h4>{{$inspectionObj->reason}}</h4>
            </div>
        </div>
        @endif
        @endif
    </div>
				  
    @if($defectObj->submissions)
    <div class="row">
    @foreach($defectObj->submissions as $k => $defect)
        <div class="col-lg-6 editbg">
            <div class="defectlist">
                <div class="row">
                    <div class="col-lg-1">{{$k+1}}.</div>
                    <div class="col-lg-2">
    			    @if(!empty($defect->upload))
                        <a href="{{$file_path}}/{{$defect->upload}}" target="_blank"><img src="{{$file_path}}/{{$defect->upload}}" width="50" height="50"></a>
                    @endif
    			    </div>
                    <div class="col-lg-9">
                        <div class="conDivdefect">
                            <span class="icon">
                                <label>Location</label>
                                <p> {{isset($defect->getlocation->defect_location)?$defect->getlocation->defect_location:''}}</p>
                            </span>
                            <span>
                                <label>Defect type</label>
                                <p> {{isset($defect->gettype->defect_type)?$defect->gettype->defect_type:''}}</p>
                            </span>
                        </div>
                        <h6>User remarks</h6><br>
                        <p>{{$defect->notes}}</p>
                        <label>Defect remark (Defect Team)</label>
                        @if($defect->status==2)<b> Need to rectify</b> @endif
                        @if($defect->status==3) Not a issue @endif
                        @if(!empty($defect->rectified_image))
                        <h6>Rectified Image</h6>
					<br>
                        <a href="{{$file_path}}/{{$defect->rectified_image}}" target="_blank"><img src="{{$file_path}}/{{$defect->rectified_image}}" width="50" height="50"></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
    @endif 
    
            <div class="row asignbg">
            @if($defectObj->signature !='')
                <div class="col-lg-3">
                    <div class="form-group text-center">
                        <label>Submission Signature</label>
                        <br>
                        <img src="{{$file_path}}/{{$defectObj->signature}}" class="viewsig" width="100px">
                        @if($signatureUserName!='')
                        <br>
                        <label>Submitted by {{$signatureUserName}}</label>
                        <br>
                        <label>{{$signatureUserTime}}</label>
                        @endif
                    </div>
                </div>
            @endif    
            
            @if($defectObj->inspection_team_signature !='') 
        	    <div class="col-lg-3">
                    <div class="form-group text-center">
                        <label>Inspection Team Signature</label>
                        <br>
                        <img src="{{$file_path}}/{{$defectObj->inspection_team_signature}}" class="viewsig" width="100px">
                        @if($signatureUserName!='')
                        <br>
                        <label>Inspected by {{$signatureUserName}}</label>
                        <br>
                        <label>{{$inspectedTeamTime}}</label>
                        @endif
                    </div>
                </div>
            @endif       
        
            @if($defectObj->inspection_owner_signature !='') 
                <div class="col-lg-3">
                    <div class="form-group text-center">
                        <label>Inspection Owner Signature</label>
                        <br>
                        <img src="{{$file_path}}/{{$defectObj->inspection_owner_signature}}" class="viewsig"/>
                        @if($inspectedOwnerName!='')
                        <br>
                        <label>Inspected by {{$inspectedOwnerName}}</label>
                        <br>
                        <label>{{$inspectedOwnerTime}}</label>
                        @endif
                    </div>
                </div>
            @endif
            
            @if($defectObj->handover_team_signature !='') 
        	    <div class="col-lg-3">
                    <div class="form-group text-center">
                        <label>Handover Team Signature</label>
                        <br>
                        <img src="{{$file_path}}/{{$defectObj->handover_team_signature}}" class="viewsig" width="100px">
                        @if($handOverTeamName!='')
                        <br>
                        <label>Handovered by {{$handOverTeamName}}</label>
                        <br>
                        <label>{{$handOverTeamTime}}</label>
                        @endif
                    </div>
                </div>
            @endif       
        
            @if($defectObj->handover_owner_signature !='') 
                <div class="col-lg-3">
                    <div class="form-group text-center">
                        <label>Handover Owner Signature</label>
                        <br>
                        <img src="{{$file_path}}/{{$defectObj->handover_owner_signature}}" class="viewsig"/>
                        @if($handOverOwnerName!='')
                        <br>
                        <label>Handovered by {{$handOverOwnerName}}</label>
                        <br>
                        <label>{{$handOverOwnerTime}}</label>
                        @endif
                    </div>
                </div>
            @endif
            
            </div>
        	<div class="row">
                <div class="col-lg-12">
                    <a href="{{ url('opslogin/defects') }}?view=summary"  class="Delete float-right">Back</a>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</section>
@stop