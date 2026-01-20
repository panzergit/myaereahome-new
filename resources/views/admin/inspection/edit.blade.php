
@extends('layouts.adminnew')




@section('content')

  <div class="status">
    <h1>Edit Defect Inspection APPOINTMENT</h1>
  </div>
@if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
<div class="containerwidth">
 <div class="">
                   {!! Form::model($inspectionObj,['method' =>'PATCH','url' => url('opslogin/inspection_appt/'.$inspectionObj->id),'class'=>'forunit']) !!}
                     <div class="row">
                        <div class="col-lg-8">
                           <h3>STATUS UPDATE</h3>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">UNIT NO:</label>
                              <div class="col-sm-7 col-6">
                                 <label class="col-form-label"> #{{isset($inspectionObj->getunit->unit)?$inspectionObj->getunit->unit:''}}</label>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">BOOKED BY:</label>
                              <div class="col-sm-7 col-6">
                                 <label class="col-form-label">{{isset($inspectionObj->getname->name)?$inspectionObj->getname->name:''}}</label>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">DATE BOOKED:</label>
                              <div class="col-sm-7 col-6">
                                 <label class="col-form-label">{{date('d/m/y',strtotime($inspectionObj->appt_date))}}</label>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">TIME BOOKED:</label>
                              <div class="col-sm-7 col-6">
                                 <label class="col-form-label">{{$inspectionObj->appt_time}}</label>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">STATUS:</label>
                              <div class="col-sm-4 col-6">
                              @if($inspectionObj->status ==1)
                              <label class="col-form-label">Cancelled</label>
                              @else 
                              
                                   {{ Form::select('status', ['0' => 'New','2'=>'On Schedule','4'=>'In Progress','3'=>'Done'], null, ['class'=>'form-control','id'=>'status','onchange'=>'getfields()']) }}
                              @endif
                              </div>
                              

                           </div>
                           @php
                              if($inspectionObj->status ==4){
                                 $display_style = "display:block";
                              }
                              else{
                                 $display_style = "display:none";
                              }
                           @endphp
                           <div id="reminderflds" class="sendremark" style="{{$display_style}}">
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">KEY HANDOVER DATE:</label>
                                 <div class="col-sm-4 col-6">
                                    <div id="sandbox">
                                    {{ Form::text('progress_date', null, ['class'=>'form-control','id'=>'progress_date']) }}
                                       
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">SEND REMINDER IN :</label>
                                 <div class="col-sm-2">
                                    {{ Form::text('reminder_in_days', null, ['class'=>'form-control']) }} 
                                 </div>
                                 <div class="col-sm-3">
                                       <label class="col-form-label">Days(s)</label>
                                 </div>
                              </div>

                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">EMAIL ADDRESSES:<br><p class="pcp">(Separate email by comma) </p></label>
                                 <div class="col-sm-7">
                                 {{ Form::textarea('reminder_emails', null, ['class'=>'form-control','rows'=>1,'required' => false,'placeholder' => 'Enter Reminder Email(s)']) }}
                                 
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">EMAIL MESSAGE:</label>
                                 <div class="col-sm-7">
                                 
                                    {{ Form::textarea('email_message', null, ['class'=>'form-control','rows'=>3,'required' => false,'placeholder' => 'Enter Reminder Email Message']) }}
                                 </div>
                              </div>
                           </div>
                           @if($inspectionObj->reason !='' && $inspectionObj->status ==1)
                           <div class="form-group row">
                              <label  class="col-sm-5 col-form-label col-6">REASON:</label>
                              <div class="col-sm-7 col-6 col-form-label">
                                   <label class="col-form-label">{{$inspectionObj->reason}}</label>
                              </div>
                           </div>
                           @endif

                        </div>
                        <div class="col-lg-4">
                        @if($inspectionObj->status !=1)
                           <div class="caneclbook mt-4">
                              <a href="#"  data-toggle="modal" data-target="#exampleModalCenter" data-id="{{$inspectionObj->id}}" class="open-dialog"><span>CANCEL BOOKING</span></a>
                           </div>
                        @endif
                           <div class="form-group row mobilres">
                              <label class="col-sm-5 col-form-label col-6">&nbsp; </label>
                              <div class="col-sm-7 col-6">
                              <label class="col-form-label"> &nbsp;&nbsp; </label>
                              </div>
                           </div>
                         
                           <div class="form-group row mobilres">
                              <label class="col-sm-5 col-form-label col-6">&nbsp; </label>
                              <div class="col-sm-7 col-6">
                              <label class="col-form-label"> &nbsp;&nbsp; </label>
                              </div>
                           </div>
                         
                           <div class="form-group row mobilres">
                              <label class="col-sm-5 col-form-label col-6">&nbsp; </label>
                              <div class="col-sm-7 col-6">
                              <label class="col-form-label"> &nbsp;&nbsp; </label>
                              </div>
                           </div>
                           @php
                              if($inspectionObj->status ==4){
                                 $display_style = "display:block";
                              }
                              else{
                                 $display_style = "display:none";
                              }
                           @endphp
                           <div id="reminderflds" class="sendremark mobilres" style="{{$display_style}}">
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">   &nbsp;</label>
                                 <div class="col-sm-4 col-6">
                                    <div id="sandbox">
                                      &nbsp;
                                       
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">   &nbsp;</label>
                                 <div class="col-sm-2">
                                      &nbsp;
                                 </div>
                                 <div class="col-sm-3">
                                       <label class="col-form-label">   &nbsp;</label>
                                 </div>
                              </div>

                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">   &nbsp;<br><p class="pcp">   &nbsp;</p></label>
                                 <div class="col-sm-7">
                                   &nbsp;
                                 
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label  class="col-sm-5 col-form-label pr-0">   &nbsp;</label>
                                 <div class="col-sm-7">
                                 
                                   &nbsp;
                                 </div>
                              </div>
                              <div class="form-group row m17">
                                 <label  class="col-sm-5 col-form-label pr-0">   &nbsp;</label>
                                 <div class="col-sm-7">
                                 
                                   &nbsp;
                                 </div>
                              </div>
                           </div>
                           @if($inspectionObj->status !=1)
                           <button type="submit" class="submit3  float-left">UPDATE</button>
                           @endif
                        </div>
                        @if($inspectionObj->status !=1)
                        <div class="timesdiv row col-lg-12 p-0 m-0 paddres">
                           <div class="col-lg-6 mt-5">
                              <h3>EDIT DATE / TIME SLOT</h3>
                              <div class="form-group row mt28">
                                 <label  class="col-sm-7 col-form-label">SELECT NEW DATE :</label>
                                 <div class="col-sm-5 p-0">
                                    <div id="sandbox">
                                    {{ Form::text('appt_date', null, ['class'=>'form-control','required' => true,'id'=>'inspection_date']) }}
                                       
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group row mt35">
                                 <label  class="col-sm-7 col-form-label pr-0">SELECT NEW TIM SLOT :</label>
                                 <div class="col-sm-5 p-0 ">
                                    {{ Form::text('appt_time', null, ['class'=>'form-control','required' => true,'id'=>'appt_time','readonly'=>'readonly']) }}
                                 </div>
                              </div>

                              <div class="form-group row mt20">
                              <div class="col-sm-7"></div>
                              <div class="col-sm-5 p-0 "><button type="submit" class="submit mt18 mt-0 float-right">UPDATE</button><div class="col-sm-5 p-0"></div></div></div>
                        
                           </div>

                           <div class="col-lg-6 mt-5" id="timeslotstables brr">
                           <br><br>
                           @foreach($times as $time)
                           <label class="containers ">
                           <input type="radio" name="timeslot" onclick="gettime(this.value)" value="{{$time}}">
                           <span class="checkmark">{{$time}}</span>
                           </label>
                           @endforeach
                           <div class="clearfix"></div>
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
                           <div class="col-lg-6"></div>
                           <div class="col-lg-6">
                           
                        </div>

                        @endif
                        </div>
                        
                     </div>
                  </form>
               </div>

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


 <script type="text/javascript">

      window.onload = function() {
         getInspectionTimeslots();
      };

      function gettime($time){
        $("#appt_time").val($time);

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

