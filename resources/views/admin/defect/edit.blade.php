@extends('layouts.adminnew')
@section('content')
<div class="row">
               <div class="col-lg-12">
                  <div class="status">
          <h1>Defect Joint Inspection</h1>
        </div>
        <ul class="summarytab">
            <li><a href="{{url('/opslogin/defects')}}">Dashboard</a></li>
            <li class="activeul"><a href="{{url('/opslogin/defects').'?view=summary'}}">Summary</a></li>
        </ul>
               </div>
               </div>
 <!--div class="status">
    <h1>defect  - update </h1>
  </div-->

      @if (session('status'))
        <div class="alert alert-info">
          {{ session('status') }}
        </div>
      @endif
      <!--div class="show">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div-->
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

@keyframes pulseBorder {
  0% {
    border-color: red;
  }
  50% {
    border-color: #ff9999;
  }
  100% {
    border-color: red;
  }
}

.canvas-error {
  border: 2px solid red !important;
  animation: pulseBorder 1s infinite;
}
.comma{    width: 110%;}
</style>
       <div class="">
       
                 {!! Form::model($defectObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/defects/'.$defectObj->id)]) !!}
  <div class="col-lg-12 asignFace">
                  <h2>defect Update</h2>
               </div>
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
                         
                              <label>ticket status: 
                              </label>
                              {{ Form::select('status', $defectStatus, null, ['class'=>'form-control selwidth', 'readonly'=>'true']) }}
                             
                           </div>   
                           </div> 
                            <div class="col-lg-2 col-6">						   
                                <div class="form-group ">
                                    <label>unit no :</label>
                                    <h4>{{isset($defectObj->getunit->unit)?Crypt::decryptString($defectObj->getunit->unit):''}}</h4>
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
                  <h2>Joint Inspection</h2>
               </div>
                <div class="row asignbg editbg">
                    @if(empty($inspectionObj))
                    <div class="col-lg-12">
                        <div class="alert alert-warning messagecls" role="alert"> Joint inspection is not yet scheduled.</div>
                    </div>
                    @endif
                    @if(isset($inspectionObj->id))
					<div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>appt date :</label>
                            <h4>{{date('d/m/y',strtotime($defectObj->inspection->appt_date))}}</h4>
                        </div>
                    </div>
					<div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>appt time :</label>
                            <h4>{{$defectObj->inspection->appt_time}}</h4>
                        </div>
                    </div>
					<div class="col-lg-3">
                        <div class="form-group">
                            <label>appt status:</label>
                             @if($inspectionObj->status ==1)
                            <label class="col-form-label">Cancelled</label>
                            @elseif(isset($inspectionObj->status))
                                {{ Form::select('inspection_status', ['0' => 'New','2'=>'On Schedule','4'=>'In Progress','3'=>'Done'], isset($inspectionObj->status)?$inspectionObj->status:null, ['class'=>'form-control','id'=>'status','onchange'=>'getfields()']) }}
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        @if($inspectionObj->status!=1)
                        <div class="caneclbook mt-3 ml-3 w-100">
                            <a data-toggle="modal" href="#exampleModalCenter" data-id="{{$inspectionObj->id}}" class="open-dialog"><span>cancel appointment</span></a>
                        </div>
                        @endif
                    </div>
                    @if(isset($inspectionObj->status) && $inspectionObj->status ==1)
				    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label class="">reason</label>
                            <h4>{{$inspectionObj->reason}}</h4>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-lg-12 col-6" id="reminderflds" class="sendremark" @if($inspectionObj->status!=4) style="display:none;" @endif>
                        <div class="row">
					        <div class="col-lg-3 col-6">
                                <div class="form-group " id="sandbox">
                                    <label>key handover date:</label>
                                    {{ Form::text('progress_date', ($inspectionObj->progress_date!='0000-00-00')?$inspectionObj->progress_date:'', ['class'=>'form-control','id'=>'progress_date']) }}
                                </div>
                            </div>
							<div class="col-lg-3 col-6">
                                <div class="form-group row">
									<div class="col-lg-8">
                                    <label>send reminder in :</label>
                                    {{ Form::text('reminder_in_days', $reminder_in_days, ['class'=>'form-control']) }} 
									</div>
									<div class="col-lg-4 pl-0">
									<label>&nbsp;</label>
									<div class="clearfix"></div>
                                    <label class="col-form-label">Days(s)</label>
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="form-group ">
                                    <label class="comma">email addresses : (Separate email by comma) </label>
                                    {{ Form::textarea('reminder_emails',$reminder_emails, ['class'=>'form-control','rows'=>2,'required' => false,'placeholder' => 'Enter Reminder Email(s)']) }}
                                </div>
                            </div>
							<div class="col-lg-3 col-6">
                                <div class="form-group ">
                                    <label>  email message:</label>
                                    {{ Form::textarea('email_message',$email_message, ['class'=>'form-control','rows'=>2,'required' => false,'placeholder' => 'Enter Reminder Email Message']) }}
                                </div>
                            </div>
                        </div>						   
                    </div>
                     <div class="col-lg-6 mt-2">
                           <div class="form-group row">
                              <label class="col-sm-6 col-form-label">select new date :</label>
							   <div class="col-sm-6">
                                 <div id="sandbox11">
                               {{ Form::text('appt_date', $defectObj->inspection->appt_date, ['class'=>'form-control','required' => true,'id'=>'inspection_date']) }}
                            
                              </div>
                              </div>
                           </div>
                           <div class="form-group row mt20">
                              <label  class="col-sm-6 col-form-label">select new tim slot :</label>
							   <div class="col-sm-6">
                              {{ Form::text('appt_time', $defectObj->inspection->appt_time, ['class'=>'form-control','required' => true,'id'=>'appt_time','readonly'=>'readonly']) }}
                            </div>
                        </div>
                    </div>
                        <div class="col-lg-6 mt-2" id="timeslotstables"></div>
                        @endif
                    </div>
              
				<div class="row">
					<div class="col-lg-12">
                        <a href="{{url('/opslogin/defects')}}?view=summary"  class="Delete float-right">cancel</a>
                        <button type="submit" class="submit float-right mr-3">update</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
              

         <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/book_inspection/updatecancelstatus'), 'files' => false]) !!}
            {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Booking - Cancel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <label>REASON:</label>
                {{ Form::textarea('reason', null, ['class'=>'form-control','rows'=>4]) }}
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
@stop
@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
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

    const canvas = document.querySelector("canvas");
    const signaturePad = new SignaturePad(canvas);
    $(document).ready(function(e){
        
        $('#uploadSignature').on('click',function(){
            $('#fileInput').trigger('click');
        });
        
        $('#fileInput').on('change',function(){
            if($('#fileInput').val()!=''){
                const input = document.getElementById('fileInput');
                const file = input.files[0];
                const formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'file');
                
              fetch('{{ url("opslogin/user/save-signature") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // for Laravel blade
                },
                // Laravel expects CSRF token only for web routes, not API routes (by default)
              }).then(res => res.json())
              .then(data => {
                  alert('Done');
                window.location.reload();
              })
              .catch(err => {
                console.error('Error:', err);
              });
            }
        });
        
        $('.clearCanvas').on('click',function(){
            signaturePad.clear();
        });
        
        $('.saveCanvas').on('click',function(){
            if(signaturePad.isEmpty()){
                $('#signature-pad').addClass('canvas-error');
                setTimeout(function(){
                    $('#signature-pad').removeClass('canvas-error');
                },2000);
            }else{
                $('#signature-pad').removeClass('canvas-error');
                const dataURL = canvas.toDataURL();
                fetch('{{ url("opslogin/user/save-signature") }}', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}' // for Laravel blade
                    },
                    body: JSON.stringify({ image: dataURL, action:'draw_signature' })
                  })
                  .then(res => res.json())
                  .then(data => {
                      alert('Done');
                      window.location.reload()
                    })
                  .catch(err => console.error(err));
            }
        });
    });
</script>
@endsection


