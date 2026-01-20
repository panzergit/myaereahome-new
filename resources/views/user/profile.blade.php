@extends('layouts.adminnew')
@section('content')
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
.messagecls {
    display: none;
}
</style>
<div class="">
    <form class="forunit">
    <div class="col-lg-12 asignFace">
        <h2>Profile</h2>
    </div>
    <div class="row asignbg editbg">
        <div class="col-6">
            <h6>Signature:</h6>
            @include('admin.defect.signature_canvas')
        </div>
        @if(auth()->user()->signature!=null)
        <div class="col-6">
            <img src="{{Storage::disk('public')->url('app/'.auth()->user()->signature)}}" width="250" height="250">
        </div>
        @endif
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
</script>
@endsection