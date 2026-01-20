@extends('layouts.registration')
@section('content')
 <style>
         .prosel{    padding: 15px;}
         .prosel select{width: 100%;}
         .status h1 {
         margin-top: 0px;     margin-bottom: 25px;
         }
         .proaraimg{    width: 75%;
         float: right;
         }
         .file-55 {
         padding: 8px!important;
         }
         .forunit span {
         color: #5D5D5D;
         font-weight: 600;
         }
		 .Primaryc {
    text-transform: inherit!important;
}
a {
    color: #8F7F65;
    text-decoration: none;
    background-color: transparent;
}
.help-tip {
    position: absolute;
    top: 0px;
    right: 18px;
    text-align: center;
    background-color: #DFCFB5;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 14px;
    line-height: 26px;
    cursor: default;
    z-index: 9;
}
.help-tip:before{
    content:'?';
    font-weight: bold;
    color:#5D5D5D;
}

.help-tip:hover p{
    display:block;
    transform-origin: 100% 0%;

    -webkit-animation: fadeIn 0.3s ease-in-out;
    animation: fadeIn 0.3s ease-in-out;

}

.help-tip p {
    display: none;
    text-align: left;
    background-color: #DFCFB5;
    padding: 10px;
    width: 250px;
    position: absolute;
    border-radius: 3px;
    box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    right: -4px;
    color: #5D5D5D;
    font-size: 12px;
    line-height: 1.4;
    font-weight: 600;
}
.help-tip p:before{ /* The pointer of the tooltip */
    position: absolute;
    content: '';
    width:0;
    height: 0;
    border:6px solid transparent;
    border-bottom-color:#DFCFB5;
    right:10px;
    top:-12px;
}

.help-tip p:after{ /* Prevents the tooltip from being hidden */
    width:100%;
    height:40px;
    content:'';
    position: absolute;
    top:-40px;
    left:0;
}

/* CSS animation */

@-webkit-keyframes fadeIn {
    0% { 
        opacity:0; 
        transform: scale(0.6);
    }

    100% {
        opacity:100%;
        transform: scale(1);
    }
}

@keyframes fadeIn {
    0% { opacity:0; }
    100% { opacity:100%; }
}
   </style>
	       <section class="bgsec1">
         <div class="container">
		 <div class="row " >
               <div class="col-lg-9 col-6">
                  <div class="status">
                     <h1> User Registration</h1>
                  </div>
               </div>
               <div class="col-lg-3 col-6">
                  <div class="status">
                     <a href="{{url('/')}}"><img src="{{ asset('assets/img/Registered.png') }}" class="proaraimg"></a>
                  </div>
               </div>
            </div>
      <div class="row">
         <div class="col-lg-12 col-md-12">
            {!! Form::open(['method' => 'POST','class'=>'forunit', 'id' => "user-form", 'url' => url('opslogin/submit_registration'), 'files' => true]) !!}
			<div class="col-lg-3 asignbg  prosel">
                        <label>Select Property:</label>
                        <div class="clearfix"></div>
                         {{ Form::select('account_id', ['' => '--Select Property--'] + $properties, old('account_id'), ['class'=>'form-control wauto','required' => true,'id'=>'property','onchange'=>'getbuildings()']) }}
                       
                     </div>
                 <div class="row asignbg"  id="pro_form" style="margin:0 auto; display:none;">
					 <div class="col-lg-3">
                           <div class="form-group">
                              <label>Block <span>*</span>:</label>
                              {{ Form::select('building_no', ['' => '--Select Building--'], null, ['class'=>'form-control','id'=>'building','required' => true,'onchange'=>'getunits()' ]) }}
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="form-group">
                              <label>Unit <span>*</span>:</label>
                              {{ Form::select('unit_no', ['' => '--Select Unit--'], null, ['class'=>'form-control','id'=>'unit','required' => true ]) }}
                           </div>
                        </div>
                        <div class="col-lg-6"></div>
                     <!--div class="col-lg-3">
                        <div class="form-group ">
                           <label>building <span>*</span>: </label>
                           
                        </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group ">
                           <label>unit no <span>*</span>: </label>
                           
                        </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group">
                           <label> role:</label>
                           
                        </div>
                     </div-->
                     <div class="col-lg-3">
                        <div class="form-group ">
                           <label>first name <span>*</span>: </label>
                              {{ Form::text('first_name', old('name'), ['class'=>'form-control','required' => true]) }}
                        </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group ">
                           <label>last name <span>*</span>: </label>
                              {{ Form::text('last_name', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group ">
                           <label>contact No<span>*</span>: </label>
                              {{ Form::text('phone', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                     </div>
                    
                     <!--div class="col-lg-3">
                        <div class="form-group ">
                           <label>company: </label>
                              {{ Form::text('company_name', null, ['class'=>'form-control']) }}
                        </div>
                     </div-->
					  <div class="col-lg-3">
                        <div class="form-group ">
                           <label>country: </label>
                              {{ Form::select('country', $countries, old('country'), ['class'=>'form-control ']) }}
                        </div>
                     </div>
                     <div class="col-lg-3">
                        <div class="form-group ">
                           <label>mailing Address <span>*</span>: </label>
                              {{ Form::text('mailing_address', null, ['class'=>'form-control','required' => true,]) }}
                        </div>
                     </div>
                    
                     <div class="col-lg-3">
                        <div class="form-group">
                           <label>postal code: </label>
                              {{ Form::text('postal_code', null, ['class'=>'form-control']) }}
                        </div>
                     </div>
					  <div class="col-lg-3">
                        <div class="form-group ">
                           <label>email <span>*</span>: </label>
                              {{ Form::text('email', null, ['class'=>'form-control','required' => true]) }}
                        </div>
                     </div>
					 <div class="col-lg-3">
                           <div class="form-group">
                              <label>I am registering as:</label>
                              {{ Form::select('role_id', ['' => '--Select Role--'] + $roles, old('account_id'), ['class'=>'form-control wauto','required' => true,'id'=>'im_select']) }}
                           </div>
                        </div>
						 <div class="col-lg-3 " id="im_form" style="display:none;">
						 <div class="form-group">
                           <label>Upload tenancy agreement <span>*</span>:</label>
                           <input id="file-contract" type="file" name="contract">
                        </div>
                  </div>
                    
                  <div class="col-lg-3">
				    <div class="form-group">
                           <label>Upload Face recognition photo : </label>
						   <div class="help-tip">
    <p>Get a seamless entry experience by using facial recognition to unlock the devices. You may also upload the photo after your account has been approved.</p>
</div>
                           <input id="file-profile" type="file" name="profile">
                        </div>
                        </div>
						<div class="col-lg-3">
                           <div class="form-group">
                              <label>1st Vehicle Car plate: </label>
                              <input class="form-control" name="first_vehicle" type="text" placeholder="">
                           </div>
                        </div>
						<div class="col-lg-3">
                           <div class="form-group">
                              <label>2nd Vehicle Car plate: </label>
                              <input class="form-control" name="second_vehicle" type="text" placeholder="">
                           </div>
                        </div>
						<div class="col-lg-12">
                           <div class="form-group">
                              <label>&nbsp; </label>
                              <label class="Primaryc">I want to receive intercom call
                              <input type="checkbox" name="receive_intercom" value="1" required>
                              <span class="checkmarkpr"></span>
                              </label>
                           </div>
                        </div>
                        <div class="col-lg-12">
                           <div class="form-group">
                              <label>&nbsp; </label>
                              <label class="Primaryc">Please check the box to confirm your agreement to the <a href="{{url('/termsconditions')}}" target="blank">Terms of Use</a> and <a href="{{url('/privacypolicy')}}" target="_blank"> Privacy Policy </a>. By doing so, you acknowledge that the personal information provided will be used solely for the stated purposes and will be handled in accordance with the Personal Data Protection Act (PDPA) guidelines.
                              <input type="checkbox" name="accept_PDPA" value="1" required>
                              <span class="checkmarkpr"></span>
                              </label>
                           </div>
                        </div>

                     <div class="col-lg-12">
                        <div class="form-group">
                           <button type="submit" class="submit mt-3  float-left ">submit</button>
                        </div>
                     </div>
                 
               </div>
			
            {!! Form::close() !!}
         </div>
      </div>
 </div>
      </section>
@endsection
<script>
function getbuildings(){
   if ($("#pro_select").val() =='') {
      $("#pro_form").hide();
   } else {
      $("#pro_form").show();
      var property =  $("#property").val();
      $.ajax({
         url : "{!!URL:: route('getbuildings')!!}",
         dataType : "json",
         data:{
            property:property
         },
         success:function(data)
         {
            $('#building').empty();
            console.log(data);
            //$('select[name="user[]"]').append('<option value="a">All User</option>');
            $("#building").append('<option value="">--Select Building--</option>')
            $.each(data, function(id,rec){
               $("#building").append('<option value="'+ id +'">'+ rec +'</option>')
            });
         }
      });
   }
}
function getunits(){
 
 var property =  $("#property").val();
 var building =  $("#building").val();
$.ajax({
                     url : "{!!URL:: route('getunits')!!}",
                     dataType : "json",
                     data:{
                        property:property,
                        building:building
                     },
                     success:function(data)
                     {
                        $('#unit').empty();
                        console.log(data);
                        //$('select[name="user[]"]').append('<option value="a">All User</option>');
                        $.each(data, function(id,rec){
                           if(unit == id)
                              $("#unit").append('<option value="'+ id +'" selected="selected">'+ rec +'</option>')
                           else
                              $("#unit").append('<option value="'+ id +'">'+ rec +'</option>')
                           
                           

                        });
                     }
                  });
}
</script>