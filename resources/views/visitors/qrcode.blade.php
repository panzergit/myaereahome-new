<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Generate QR Code Examples</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
         <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
		 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container mt-4">
	<div class="row d-flex justify-content-center align-items-center">
        <div class="col-md-12">
        
		<div class="card">
            <div class="card-header">
                <h2>QR Scanner Code Generated for given URL</h2>
            </div>
			<div class="text-right"><a href = "{{ url('/') }}" class="btn btn-primary">Add QRScanner</a></div>
            <div class="card-body">
			
			  <div class="table-responsive">

                        <table class="table table-striped dataex-html5-selectors" id = "subscriber">

                            <thead>

                                <tr style="background:#CCC;">                                   

                                    <th>ID</th>   

									<th>Website Name</th>
									<th>URL</th>
									<th>QR Code</th>
									
									<th>Created Date</th>

						

                                </tr>

                            </thead>

                            <tbody>

							@foreach($qrdetails as $qr)

                            <tr>
							<tr>

								<td>{{ $qr->id }}</td>

								<td>{{ $qr->companyname }}</td>

								<td>{{ $qr->websiteurl  }}</td>					
								
								<td>
								<?PHP $url = URL ('/').'/assets/companyqrcodes/'.$qr->imagepath; ?>
								
								<img src="<?PHP echo $url; ?>" width="100" ></td>
								

								<td>{{ $qr->created_at }}</td>


							</tr>

                            @endforeach

                            </tbody>

                            </table>
			
			
				
				
				<?PHP 
						/*$gimifylogo ='http://localhost/qrscanner/assets/images/logo-success.png';
							$logosize= 2;	
						$img = base64_encode(QrCode::format('png')->merge($gimifylogo, '0.'.$logosize, true)->size($qrsize)->errorCorrection('H')->margin(0)->generate($qrtext)); */
						
						//echo '<img src = "http://localhost/qrscanner/public/assets/companyqrcodes/'.$randnum.'.png" >';
						?>
					 
					 
			
				

            </div>
        </div>

    </div>
	</div>
</div>
</body>
</html>