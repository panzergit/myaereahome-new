@extends('layouts.adminnew')
@section('content')
<div class="row">
   <div class="col-lg-12">
        <div class="status">
          <h1>Defect Update</h1>
        </div>
        <ul class="summarytab">
            <li><a href="{{url('/opslogin/defects')}}">Dashboard</a></li>
            <li class="activeul"><a href="{{url('/opslogin/defects').'?view=summary'}}">Summary</a></li>
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

</style>
<div class="">
    {!! Form::model($defectObj,['method' =>'PATCH','class'=>"forunit",'url' => url('opslogin/defects/submission-update',$defectObj->id)]) !!}
    <div class="col-lg-12 asignFace">
        <h2>defect Update</h2>
    </div>
    <div class="row asignbg editbg">
		<div class="col-lg-3 col-6">
            <div class="form-group">
                <label>ticket : </label>
                <h4>{{$defectObj->ticket}}</h4>
            </div>
        </div>
		<div class="col-lg-3 col-6">
            <div class="form-group  ">
                <label>ticket status:</label>
                 <h4>@php
                              if(isset($defectObj->status)){
                              if($defectObj->status==0)
                                 echo "OPEN";
                              else if($defectObj->status==1)
                                 echo "CLOSED";
                              else if($defectObj->status==3)
                                 echo "ON SCHEDULE";
                              else if($defectObj->status==5)
                                 echo "COMPLETED-PENDING RESIDENT UPDATE";
                              else if($defectObj->status==6)
                                 echo "COMPLETED-FINAL INSPECTION SCHEDULED";
                              else if($defectObj->status==2)
                                 echo "IN PROGRESS";
                              }
                          
                                
                            
                /*{{ Form::select('status', $defectStatus, null, ['class'=>'form-control selwidth', 'disabled' => $isUserSignatureEmpty,'readonly'=>'readonly']) }} */
                @endphp</h4>
            </div>   
        </div> 
        <div class="col-lg-2 col-6">						   
            <div class="form-group">
                <label>unit no : </label>
                <h4>  {{isset($defectObj->getunit->unit)?Crypt::decryptString($defectObj->getunit->unit):''}}</h4>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="form-group ">
                <label>submitted by : </label>
                <h4> {{isset($defectObj->user->name)?Crypt::decryptString($defectObj->user->name):''}}</h4>
            </div>
        </div>
		<div class="col-lg-2 col-6">
            <div class="form-group">
                <label>submitted date :</label>
                <h4> {{date('d/m/y',strtotime($defectObj->created_at))}}</h4>
            </div>
        </div>
    </div>
    
    @if($isUserSignatureEmpty)
    <div class="row defectlist">
        <div class="col-lg-12">
            <div class="alert alert-warning messagecls" role="alert">Your signature has not been updated yet. Please add your signature before proceeding update.</div>
        </div>
        <div class="col-lg-12">
            <canvas id="signature-pad" width="400" height="250" class="" style="border:1px solid #000; border-radius:15px;"></canvas>
        </div>
        <div class="col-lg-12 mt-3">
            <a href="javascript:void(0);" class="btn submit saveCanvas float-left mr-3">Save</a>
            <a href="javascript:void(0);" class="btn clearCanvas Delete float-left mr-3">Clear</a>
            <a href="javascript:void(0);" id="uploadSignature" class="btn clearCanvas Delete float-left mr-3 w-auto">Upload Signature</a>
        </div>
        <input type="file" style="display:none;" id="fileInput" />
    </div>
    @endif
                    
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
                        <h6>User remarks</h6>
                        <p>{{$defect->notes}}</p>
                        <label>Defect remark (Defect Team)</label>
                        {{ Form::select("defect_status[$defect->id]", ['' => '',2=>'Need to rectify','3'=>'Not a issue'],  $defect->status, ['class'=>'form-control w30', 'disabled' => $isUserSignatureEmpty]) }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

	<div class="row">
        <div class="col-lg-12">
            <a href="{{url('/opslogin/defects')}}?view=summary"  class="Delete   float-right">cancel</a>
            @if(!$isUserSignatureEmpty)
            <button type="submit" class="submit  float-right mr-3">update</button>
            @endif
            
        </div>
    </div>
    
    {!! Form::close() !!}
</section>
@stop
@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
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
                $(this).html('Saving...');
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
                      $(this).html('Save');
                      alert('Done');
                      window.location.reload()
                    })
                  .catch(err => console.error(err));
            }
        });
    });
</script>
@endsection


