@extends('layouts.front')



@section('content')


   <div class="status">
                  <h1>Appointment For Unit Take Over</h1>
               </div>
              
                @if($units)
               <div class="containerwidth bring">
               	  <p><strong> Status : @php
                  if(isset($units->status)){
                    if($units->status==0)
                      echo "New";
                    else  if($units->status==1)
                      echo "Cancelled";
                     else  if($units->status==2)
                      echo "On Schedule";
                       else  if($units->status==3)
                      echo "Done";
                  }
                  @endphp </strong></p>

                  <p> Your appointment is set on {{date('d M Y, D',strtotime($units->appt_date))}} at {{$units->appt_time}}.</p>
                  <p>Please bring along the xxxxx.</p>
                  <p> You are advised to etc etc etc. </p>
               </div>
               @else
               <div class="containerwidth bring">
                  <p> Sorry, You have no active booking.</p>
               </div>
               @endif

@stop