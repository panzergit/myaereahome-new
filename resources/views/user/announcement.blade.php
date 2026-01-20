@extends('layouts.front')



@section('content')

@php 
$permission = Auth::user();
@endphp

<!-- Content Header (Page header) -->

 @php
 
  if($announcements){
  $title='';
   $content='';
   $count=0;
    foreach($announcements as $k => $news){
      $count++;
      $link = 'link'.$k;
     
      $title .= '<li class="nav-item"><a class="nav-link" data-toggle="pill" href="#'.$link.'">'.$count.'. '.$news->title.'<p>'.date('d/m/Y', strtotime($news->created_at)).'</p></a></li>';

      $content .='<div id="'.$link.'" class="tab-pane ">
                           <br>
                           <div class="announce2">'.$news->notes
                           .'</div>
                        </div>';
     
    }
  }
 @endphp
<div class="">
                     <ul class="nav nav-pills navover" role="tablist">
                        {!!$title!!}
                     </ul>
                     <!-- Tab panes -->
                     <div class="tab-content">
                        {!!$content!!}
                     </div>
                  </div>



@endsection