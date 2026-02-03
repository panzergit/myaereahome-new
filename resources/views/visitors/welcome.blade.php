<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>QR Scanner Code Generate</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
		 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
  body {
    background: #eee
}

#regForm {
    background-color: #ffffff;
    margin: 0px auto;
    font-family: Raleway;
    padding: 40px;
    border-radius: 10px
}
h2 {
    text-align: center
}


  </style>
    </head>
    <body>
<div class="container mt-5">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-md-8">
	  
	  
	   
	  
	  <form id="regForm" action="{{ url('/generate') }}" enctype="multipart/form-data" method = "post">
	  {{ csrf_field() }}
		 <div class="card">
		  <div class="card-body">
			<h4 class="card-title"><h2>QR Scanner Code Generate</h2></h4>
			<div class="text-right"><a href = "{{ url('/Qrcodeview') }}" class="btn btn-primary pull-right">View QRScanner</a></div>
				<div class="form-group">
					<label for="companyname">Website Name</label>
					<input type="text" class="form-control" id="companyname" placeholder="Enter Company name" name="companyname">
				</div>
				<div class="form-group">
					<label for="url">URL</label>
					<input type="url" class="form-control" id="url" placeholder="Enter URL" name="geturl">
				</div>		
				<button type="submit" class="btn btn-primary">Submit</button>
		  </div>
		</div>
	  
	  
	
	  </form>
	</div>
	</div>
</div>
    </body>
</html>
