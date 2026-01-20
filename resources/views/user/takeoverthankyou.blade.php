@extends('layouts.front')



@section('content')


   <div class="status">
                  <h1>Appointment For Unit Take Over</h1>
               </div>
               <div class="containerwidth bring">
                  <p> Your appointment is set on {{date('d M Y, D',strtotime($units->appt_date))}} at {{$units->appt_time}}.</p>
                  <p>Please bring along the xxxxx.</p>
                  <p> You are advised to etc etc etc. </p>
               </div>

@stop