@if(!empty(session('status')))

<div class="row" style="padding:15px 15px 0px 15px; margin-bottom:-15px;">
    <div class="col-sm-12">
    <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Success</h4> {{session('status')}}
              </div>
              </div>
              </div>
@endif