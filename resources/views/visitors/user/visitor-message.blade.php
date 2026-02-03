@extends('visitors.layouts.visitors')
@section('content')
<style>
.expired {list-style-type: none;}
</style>
<div class="col-lg-12 expired">
   <h2>oops! <br>The max number of visitors allow per unit has been met. </h2>
   <p>Please contact the property management or the resident who has sent you the invitation for further assistance.</p>
</div>

@endsection
