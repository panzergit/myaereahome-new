
@extends('layouts.adminnew')




@section('content')

  <div class="status">
    <h1>Edit Key Collection appointment</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
			  <div class="row">
               <div class="col-lg-12">
                  <ul class="summarytab">
                     <li   class="activeul"><a href="{{url('/opslogin/takeover_appt/lists#kc')}}">Summary</a></li>
                     <li><a href="{{url('/opslogin/takeover_appt#kc')}}">New appointments</a></li>
                  </ul>
               </div>
               </div>
<div class="">
 <div class="">
                   {!! Form::model($takeoverObj,['method' =>'PATCH','url' => url('opslogin/takeover_appt/'.$takeoverObj->id),'class'=>'forunit']) !!}
				   <div class="col-lg-12 asignFace">
                  <h2>Status Update</h2>
               </div>
                     <div class="row asignbg editbg">
                        <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>unit no:</label>
                              <h4> #{{isset($takeoverObj->getunit->unit)?Crypt::decryptString($takeoverObj->getunit->unit):''}}<h4>
                             
                           </div>
						   </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>booked by:</label>
                               <h4>  {{isset($takeoverObj->getname->name)?Crypt::decryptString($takeoverObj->getname->name):''}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>date booked:</label>
							 <h4> {{date('d/m/y',strtotime($takeoverObj->appt_date))}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>time booked:</label>
							<h4>  {{$takeoverObj->appt_time}}</h4>
                           </div>
                           </div>
						    <div class="col-lg-3 col-6">
                           <div class="form-group">
                              <label>NRIC / FIN 1:</label>
							  <h4>{{$takeoverObj->nricid_1}}</h4>
                           </div>
                           </div>
						    
                           @if($takeoverObj->nricid_2 !='')
							   <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>NRIC / FIN 2:</label>
                             <h4> {{$takeoverObj->nricid_2}}</h4>
                           </div>
                           </div>
                           @endif
						   <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>status:</label>
                              @if($takeoverObj->status ==1)
                              <label >Cancelled</label>
                              @else 

                                   {{ Form::select('status', ['0' => 'New','2'=>'On Schedule','3'=>'Done'], null, ['class'=>'form-control','id'=>'status' ]) }}
                              @endif
                              </div>
                              </div>
                           
                           @if($takeoverObj->reason !='')
							   <div class="col-lg-3 col-6">
                           <div class="form-group ">
                              <label>reason:</label>
                             <h4> {{$takeoverObj->reason}}</h4>
                           </div>
                           </div>
                           @endif
						   <div class="col-lg-6"></div>
						    <div class="col-lg-6  mt0-2">
                        @if($takeoverObj->status !=1)
                           <div class="caneclbook mt-1">
                              <a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$takeoverObj->id}}" class="open-dialog float-left"><span>cancel booking</span></a>
                           </div>
                        @endif
                           
                           @if($takeoverObj->nricid_2 !='')
                           <!--div class="form-group row mobilres">
                              <label class="col-sm-5 col-form-label col-6">&nbsp; </label>
                              <div class="col-sm-7 col-6">
                              <label class="col-form-label"> &nbsp;&nbsp; </label>
                              </div>
                           </div-->
                           @endif
                           
                           @if($takeoverObj->status !=1)
                           <button type="submit" class="submit  float-right ">update</button>
                           @endif
                          
                        </div>
                        </div>
						  <div class="col-lg-12 asignFace">
                  <h2>edit date / time slot</h2>
               </div>
                       <div class="row asignbg editbg">
                        @if($takeoverObj->status !=1)
                        <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label">select new date :</label>
                              <div class="col-sm-7">
                                 <div id="sandbox">
                                   {{ Form::text('appt_date', null, ['class'=>'form-control','required' => true,'id'=>'appt_date']) }}
                                    
                                 </div>
                              </div>
                           </div>
                           <div class="form-group row mt20">
                              <label  class="col-sm-5 col-form-label pr-0">select new tim slot :</label>
                              <div class="col-sm-7">
                                 {{ Form::text('appt_time', null, ['class'=>'form-control','required' => true,'id'=>'appt_time','readonly'=>'readonly']) }}
                              </div>
                           </div>
                           <button type="submit" class="submit mt182 float-right">update</button>
                        </div>

                        <div class="col-lg-6" id="timeslotstables">
                           @foreach($times as $time)
                           <label class="containers ">
                           <input type="radio" name="timeslot" onclick="gettime(this.value)" value="{{$time}}">
                           <span class="checkmark">{{$time}}</span>
                           </label>
                           @endforeach
                           <div class="clearfix"></div>
                           <!--div class="taskselct">
                              <label class="container3">Available
                              <input type="checkbox">
                              <span class="checkmark3"></span>
                              </label>
                              <label class="container4">Taken
                              <input type="checkbox">
                              <span class="checkmark4"></span>
                              </label>
                           </div-->
                          </div>
                          </div>
                        @endif
                     </div>
                  </form>
               </div>



               <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/book_appt/updatecancelstatus'), 'files' => false]) !!}
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


 <script type="text/javascript">

      window.onload = function() {
         getTakeoverTimeslots();
      };

      function gettime($time){
        $("#appt_time").val($time);

      }
    </script>

@stop

