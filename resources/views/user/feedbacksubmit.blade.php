@extends('layouts.front')



@section('content')


<div class="status">
    <h1>Feedback </h1>
</div>

 @if (session('status'))

            <div class="alert alert-info">

                {{ session('status') }}

            </div>
            @endif
            <div class="containerwidth">
            {!! Form::open(['method' => 'POST', 'url' => url('opslogin/feedback_save'), 'files' => 'true','class'=>"forunit"]) !!}
            {{ csrf_field() }} 
                     <div class="row">
                        <div class="col-lg-7">
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">SELECT CATEGORY :</label>
                              <div class="col-sm-8">
                              {{ Form::select('fb_option',$feedbacks, null, ['class'=>'form-control','id'=>'unit' ]) }}

                                
                              </div>
                           </div>
                           <div class="form-group row">
                              <label  class="col-sm-4 col-form-label">UPLOAD:</label>
                              <div class="col-sm-8">
                                 <div class="image-upload uplodinline">
                                    <label for="file-input">
                                    <img src="{{url('assets/img/plus.png')}}" class="upimg">
                                    </label>
                                    <input id="file-input" type="file" name="upload" class="form-control">
                                 </div>
                                 
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-12 mt-4">
                           <div class="form-group">
                           {{ Form::textarea('notes', null, ['class'=>'form-control','required' => true,'rows'=>9,'id'=>'notes']) }}
                              <b>0 / 1000 WORDS</b>
                           </div>
                        </div>
                     </div>
                     <button type="submit" class="submit  mt-2">Submit</button>
                  </form>
               </div>

    </section>  

    

@stop