@extends('layouts.adminnew')
@section('content')
<style>
    .signature-pad {
  cursor: url(pen.png) 1 26, pointer;
  border: 2px solid var(--primary-color);
  border-radius: 4px;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <ul class="summarytab">
            <li   class="activeul"><a href="{{url('/opslogin/defects')}}?view=summary">Summary</a></li>
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
				.digitalup tr{color:#5D5D5D;     font-weight: 600;}
				.digitalup b{color:#5D5D5D;}
				.digitalup input{width:200px;}
				.digitalup p{margin-top: 5px;
    position: absolute; }
    .w30{width:300px;}
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
                 {!! Form::model($defectObj,['method' =>'PATCH','class'=>"forunit", 'files' => true,'url' => url('opslogin/defects/handoverupdate/'.$defectObj->id)]) !!}
<div class="col-lg-12 asignFace">
                  <h2>Defect Handover Update</h2>
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
                              <h4>
                              @php
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
                             /*Form::select('status', $defectStatus, null, ['class'=>'form-control selwidth', 'disabled' => $isUserSignatureEmpty])*/
                             @endphp
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
                  <h2>Joint Inspection</h2>
               </div>    
 
                  @if($defectObj->submissions)
                     @php $i = 0; @endphp
                     @foreach($defectObj->submissions as $k => $defect)
                        @if($defect->status ==2) 
                                 @php $i++ @endphp 
                        <div class="col-lg-6 editbg">
                            <div class="defectlist">
                                <div class="row">
                                    <div class="col-lg-1">{{$i}}.</div>
                                    <div class="col-lg-2">
                                       @if(!empty($defect->upload))
                                          <a href="{{$file_path}}/{{$defect->upload}}" target="_blank"><img src="{{$file_path}}/{{$defect->upload}}" width="50" height="50"></a>
                                       @endif
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="conDivdefect">
                                          <span class="icon">
                                          <label>Location</label>
                                          <p> : {{isset($defect->getlocation->defect_location)?$defect->getlocation->defect_location:''}}</p>
                                          </span>
                                          <span>
                                          <label>Defect type</label>
                                          <p> : {{isset($defect->gettype->defect_type)?$defect->gettype->defect_type:''}}</p>
                                          </span>
                                          </div>
                                       <h6>User remarks</h6>
                                       <p>{{$defect->notes}}</p>
                                       @if(!$isUserSignatureEmpty)
                                       <label>Rectified Image</label>
                                       <div >
                                          {{$defect->rectieid_image}}
                                          @if(!empty($defect->rectieid_image))
                                             <a href="{{$file_path}}/{{$defect->rectieid_image}}" target="_blank"><img src="{{$file_path}}/{{$defect->rectieid_image}}" width="50" height="50"></a>
                                          @endif
                                          <input id="rectieid_image_{{$defect->id}}" name="rectieid_image_{{$defect->id}}" type="file" />
                                       </div>
                                       <label>Defect Status</label>
                                          {{ Form::select("defect_status[$defect->id]", [0=>'Pending','1'=>'Fixed','2'=>'Other'],  $defect->defect_status, ['id' =>"defect_status_$defect->id", 'class'=>'form-control w30','onchange'=>"openaddtext('$defect->id')"]) }}
                                          @php
                                          $display_status = ($defect->defect_status ==2)?"display:block":"display:none";
                                          @endphp
                                          <div id="others_{{$defect->id}}" style={{$display_status}}>
                                             <b> 
                                                <textarea type="text" name="handover_message[{{$defect->id}}]" class="form-control w30 mt-3"> : {{isset($defect->handover_message)?$defect->handover_message:''}}</textarea>
                                             </b>
                                          </div>
                                        @else
                                        <div class="col-lg-12">
                                            <div class="alert alert-warning messagecls" role="alert">Your signature has not been updated yet. Please add your signature before proceeding with the Handover Inspection.</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                     @endif 
                  @endforeach
               @endif 
            
            @if($isUserSignatureEmpty)
                @include('admin.defect.defect_submission_update')
            @endif
            
            <div class="row">
                <div class="col-lg-7">
                   <input type="hidden" name="id" value="{{$defectObj->id}}">
                </div>
                <div class="col-lg-5">
                    @if(!$isUserSignatureEmpty)
                    <button type="submit" class="submit mt-2 2 ml-3 mr-0 float-right">Update</button>
                    @endif
                    <a href="{{url('/opslogin/defects')}}?view=summary"  class="submit mt-2 float-right">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}
        </div>
    </div>
</section>
@stop

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
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

    function openaddtext($row){
        var status_fld = "#defect_status_"+$row;
        var others_fld = "#others_"+$row;
    
        if($(status_fld).val() ==2){
            $(others_fld).show(); 
        }
        else{
            $(others_fld).hide(); 
            $(others_fld).val="";
        }
    }
</script>
@endsection