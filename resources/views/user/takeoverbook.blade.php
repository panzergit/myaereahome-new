@extends('layouts.front')



@section('content')


<div class="status">
    <h1>Appointment For Unit Take Over</h1>
</div>

 @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
  <div class="containerwidth">
                   {!! Form::open(['method' => 'POST', 'url' => url('opslogin/takeover_appt'), 'files' => false]) !!}
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label">SELECT DATE :</label>
                              <div class="col-sm-7">
                                <div id="sandbox">
                                    <input name="appt_date" id="appt_date" type="text" class="form-control" value="">
                                </div>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label">SELECT TIME SOLT :</label>
                              <div class="col-sm-7">
                                 <input name="appt_time" type="text" class="form-control" value="" id="appt_time" required="" readonly="">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label">PERSON 1 NRIC / FIN (Last 4 Digit):</label>
                              <div class="col-sm-7">
                                 <input name="nricid_1" type="text" class="form-control" value=""  required="" >
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label">PERSON 2 NRIC / FIN (Last 4 Digit):</label>
                              <div class="col-sm-7">
                                 <input name="nricid_2" type="text" class="form-control" value="" required="" >
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-6" id='timeslotstables'>
                           @foreach($times as $time)
                           <label class="containers " >
                           <input type="radio" name="timeslot" onclick="gettime(this.value)" value="{{$time}}">
                           <span class="checkmark">{{$time}}</span>
                           </label>
                           @endforeach
                        </div>
						<div class="col-lg-6"></div>
						<div class="col-lg-6">
						 <div class="taskselct">
                              <label class="container3">Available
                              <input type="checkbox">
                              <span class="checkmark3"></span>
                              </label>
                              <label class="container4">Taken
                              <input type="checkbox">
                              <span class="checkmark4"></span>
                              </label>
                           </div>
                           </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12">
                            {{ Form::submit('Create ', ['class'=>'bretinfo submit']) }}
                        </div>
                     </div>
                  </form>
               </div>

    </section>  

    <script type="text/javascript">
      function gettime($time){
        $("#appt_time").val($time);

      }
    </script>

@stop