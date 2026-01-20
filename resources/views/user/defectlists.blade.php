@extends('layouts.front')



@section('content')


<div class="status">
    <h1>Submitted Defects Lists</h1>
</div>

 @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
            <div class="containerwidth bring">
                  <form action="">
                     <div class="pt-4">
                        <table class="table">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>LOCATION</th>
                                 <th>DEFECT TYPE</th>
                                 <th>REMARKS</th>
                                 <th>PHOTO</th>
                              </tr>
                           </thead>
                           <tbody id="tbody">
                           @if($defects)

                              @foreach($defects as $k => $defect)

                              <tr>
                                 <td>{{$k+1}}</td>
                                 <td>
                                 {{isset($defect->getlocation->defect_location)?$defect->getlocation->defect_location:''}}
                                 </td>
                                 <td>
                                 {{isset($defect->gettype->defect_type)?$defect->gettype->defect_type:''}}
                                 </td>
                                 <td>
                                    {{$defect->notes}}
                                 </td>
                                 <td>
                                    @if(!empty($defect->upload))
                                       <div class="image-upload">
                                          <label for="file-input">
                                          <img src="{{$file_path}}/{{$defect->upload}}" class="viewimg"/>
                                          </label>
                                          <input id="file-input" type="file" />
                                       </div>
                                    @endif
                                 </td>
                              </tr>

                              @endforeach

                      @endif 
                             
                           </tbody>
                        </table>
                     </div>
                  </form>
               </div>
               </div>

    </section>  

   

@stop