@extends('layouts.adminnew')


@section('content')

@php 
   $permission = Auth::user();
   $permission = $permission->check_permission(50,$permission->role_id); 
@endphp

<div class="status">
  <h1>digital access </h1>
</div>

<div class="containerwidth">
                  <div class="row">
                     <div class="col-lg-6 countp coubg">
                        <div class="dgred h-100">
                           <div class="my-auto">
                              <div class="col-lg-12">
                                 <p>Total number of people</p>
                                 <div class="number-diy">
                                    <div class="data" data-number="{{$household_count}}"></div>
                                 </div>
                              </div>
                              <div class="row h-100">
                                 <div class="col-lg-6">
                                    <p>Room</p>
                                    <div class="number-diy2">
                                       <div class="data2" data-number="{{$room_count}}"></div>
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <p>Device</p>
                                    <div class="number-diy3">
                                       <div class="data3" data-number="{{$device_count}}"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-6 coubg">
                        <div id="chartContainer3" style="height: 300px; width: 100%;"></div>
                        <div class="removewatermark"></div>
                     </div>
                     <div class="col-lg-6 coubg">
                        <div id="chartContainer1" style="height: 300px; width: 100%;"></div>
                        <div class="removewatermark"></div>
                     </div>
                     <div class="col-lg-6 coubg">
                        <div id="chartContainer2" style="height: 300px; width: 100%;"></div>
                        <div class="removewatermark"></div>
                     </div>
                  </div>
               </div>
@endsection

      <!--<script src=" {{ asset('assets/admin/js/jquery.min.js') }}" src="assets/js/jquery.min.js"></script>
      <script src=" {{ asset('assets/admin/js/bootstrap.min.js') }}" src="assets/js/bootstrap.min.js"></script>
      <script src=" {{ asset('assets/admin/js/jquery.rollNumber.js') }}" src="assets/js/jquery.rollNumber.js"></script>
      <script src=" {{ asset('assets/admin/js/canvasjs.min.js') }}" src="assets/js/canvasjs.min.js"></script-->
	   



