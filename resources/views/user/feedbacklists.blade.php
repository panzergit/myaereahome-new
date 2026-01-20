@extends('layouts.front')



@section('content')


<div class="status">
    <h1>Feedback Status</h1>
</div>

 @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
            <div class="containerwidth">
                  <table class="table tablefbor">
                     <thead>
                        <tr>
                           <th scope="col"></th>
                           <th scope="col">DATE</th>
                           <th scope="col">TIME</th>
                           <th scope="col">CATEGORY</th>
                           <th scope="col">STATUS</th>
                           <th scope="col">REMARKS</th>
                        </tr>
                     </thead>
                     <tbody>
                     
                     @if($feedbacks)

                        @foreach($feedbacks as $k => $feedback)
                        
                        <tr>
                           <td>{{$k+1}}</td>
                           <td>{{date('d/m/y',strtotime($feedback->created_at))}}</td>
                           <td>{{date('H:i A',strtotime($feedback->created_at))}}</td>
                           <td>{{isset($feedback->getoption->feedback_option)?$feedback->getoption->feedback_option:''}}</td>
                           <td>@php
                              if($feedback->status ==2)
                                 echo "Completed";
                              else if($feedback->status ==1)
                                 echo "In Progress";
                              else
                                 echo "New";
                              @endphp
                           </td>
                           <td>{{$feedback->notes}}</td>
                        </tr>
                        @endforeach

                      @endif 
                     </tbody>
                  </table>
               </div>
               </div>

    </section>  

    

@stop