@extends('layouts.front')



@section('content')


<div class="status">
    <h1>DEFECTS Submission</h1>
</div>

 @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
            <div class="containerwidth bring">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/defect_save'), 'files' => 'true','class'=>"forunit"]) !!}
            {{ csrf_field() }} 
            
                     <div class="pt-4">
                        <table class="table">
                           <thead>
                              <tr >
                                 <th></th>
                                 <th>LOCATION</th>
                                 <th>DEFECT TYPE</th>
                                 <th>REMARKS</th>
                                 <th>PHOTO</th>
                              </tr>
                           </thead>
                           <tbody >
                              @for($i=1;$i<=10;$i++)
                              @php
                                 if($i ==1)
                                    $display_style = "";
                                 else
                                    $display_style = "display:none";
                              @endphp
                                 <tr id="add_field{{$i}}" style="{{$display_style}}">

                                 <td>1</td>
                                 <td>
                                 {{ Form::select("defect_location_$i",['' => '--Select Location--'] +$locations, null, ['class'=>'form-control','id'=>'unit' ]) }}

                                 </td>
                                 <td>
                                 {{ Form::select("defect_type_$i",['' => '--Select Type--'] + $types, null, ['class'=>'form-control','id'=>'unit' ]) }}

                                 </td>
                                 <td>
                                    <input type="text" name="notes_{{$i}}" class="form-control" id="" >
                                 </td>
                                 <td>
                                    <div class="image-upload">
                                       <label for="file-input_{{$i}}">
                                       <img src="{{url('assets/img/plus.png')}}" class="upimg">
                                       </label>
                                       <input id="file-input_{{$i}}" type="file"  name="upload_{{$i}}"/>
                                    </div>
                                 </td>
                              </tr>
                              @endfor
                           </tbody>
                        </table>
                        <a class="addrow"
                           id="addBtn" type="button" onclick="showmore()">
                        ADD ROW
                        </a>
                        <input type="hidden" id="rowcount" value="1">
                     </div>
                     <button type="submit" class="submit  mt-2">Submit</button>
                  </form>
               </div>

    </section>  

   


@stop