<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td style="background:#efefef;font-size:14px;padding-top:15px;padding-bottom:15px">
				<table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#ffffff;margin:0 auto;max-width:900px;width:100%;">
					<tr>
						<td>
							<table border="0" align="center" style="margin:0 auto; ma-width:400px; width:100%; background:#ffffff;">
							<tr>
									<td style="padding:15px 30px;background:#ffffff;text-align:left;font-size:10px;color:#454545;">
									<p><i>This is a system generated email. Please do not reply to this email. Should you have any enquiries, kindly contact the property management or the resident who had sent you the invitation.</i></p>
									</td>
								</tr>
								<tr>
									<td style="background:#ffffff;color:#454545;text-align:center; padding:20px 15px 0px 15px;font-size:24px;">
									<img src="{{url('assets/img/aerea-logo.png') }}" width="120">
									</td>
								</tr>
								
								<tr>
									<td style="background:#ffffff;color:#454545;text-align:left; padding:0px 15px;font-size:14px;">
									Hi {{$name}}
									</td>
								</tr>								
								<tr>
									<td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;font-size:12px;">
									<p style="text-align:left">{{$invited_by}} has registered you for your visit on {{$date_of_visit}}.<br> For a seamless entry experience. Kindly display the QR code below on your day of visit. 
									</p>
									</td>
								</tr>

								<tr>
                           <td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;font-size:12px;">
                              <table border="0" align="center" cellpadding="4" cellspacing="0" style="width:100%;">
                                 <tr>
                                    <td style="font-size:14px;color:#454545;" width="39%">
                                       <table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%;">
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%; padding-left: 0px;">
                                                <b>Booking ID :</b>
                                             </td>
                                             <td style="font-size:14px;color:#454545;">
                                                <i>{{$ticket}}</i>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%; padding-left: 0px;">
                                                <b>Date Of Visit  :</b>
                                             </td>
                                             <td style="font-size:14px;color:#454545; ">
                                                <i>{{$date_of_visit}}</i>
                                             </td>
                                          </tr>
                                       </table>
                                    </td>
                                    <td style="font-size:14px;color:#454545;" width="29%">
                                       <table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%;">
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%; padding-left: 0px;" >
                                                <b>Property :</b>
                                             </td>
                                             <td style="font-size:14px;color:#454545;">
                                                <i>{{$property}}</i>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%; padding-left: 0px;">
                                                <b>Unit No :</b>
                                             </td>
                                             <td style="font-size:14px;color:#454545; ">
                                                <i>{{$unit}}</i>
                                             </td>
                                          </tr>
                                       </table>
                                    </td>
                                    <td style="font-size:14px;color:#454545;" width="33%">
                                       <table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%;">
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%;">
                                                <b>Invited By :</b>
                                             </td>
                                             <td style="font-size:14px;color:#454545;">
                                                <i>{{$invited_by}}</i>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="font-size:14px;color:#454545; width:40%;">
                                                <b>Purpose : </b>
                                             </td>
                                             <td style="font-size:14px;color:#454545; ">
                                                <i>{{$purpose}}</i>
                                             </td>
                                          </tr>
                                       </table>
                                    </td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
									
								<tr>
									<td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;font-size:12px;"> 
										<table border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;">
											<tr>
											
												<td style="font-size:14px;color:#454545;" width="39%">
												<b style="text-align:left; padding-bottom: 14px;
    display: block;">Visitor Details.</b>
													<table border="0" align="center" cellpadding="6" cellspacing="0" style="width:100%;">
													
														<tr>
															<td style="font-size:14px;color:#454545; width:40%;">
																Name :
															</td>
															<td style="font-size:14px;color:#454545;">
																 {{$name}}
															</td>
														</tr>
														<tr>
															<td style="font-size:14px;color:#454545; width:40%;">
																Mobile : 
															</td>
															<td style="font-size:14px;color:#454545; ">
																 {{$mobile}}
															</td>
														</tr>
														<tr>
															<td style="font-size:14px;color:#454545; width:40%;">
															Vehicle No :
															</td>
															<td style="font-size:14px;color:#454545;">
															 {{$vehicle_no}}
															</td>
														</tr>
														<tr>
															<td style="font-size:14px;color:#454545; width:40%;">
																Email : 
															</td>
															<td style="font-size:14px;color:#454545;">
																 <span style="color: #566ff7; text-decoration: underline;"> {{$email}} </span>
															</td>
														</tr>
														@if($id_number !='')
														<tr>
															<td style="font-size:14px;color:#454545; width:40%;">
																ID Number : 
															</td>
															<td style="font-size:14px;color:#454545;">
																 <span style="color: #566ff7; text-decoration: underline;"> {{$id_number}} </span>
															</td>
														</tr>
														@endif
													
													</table>
												</td>
												<td style="font-size:14px;color:#454545;" width="27%">
												<img src="{{$qrcode_eurl}}" width="80px" height="80px" style="    margin-top: 50px;">
                                          		<p style=" margin-top: 0px;">{{$name}}</p>
												</td>
												<td style="font-size:14px;color:#454545;" width="33%">
												</td>
												
											</tr>
											
										</table>
									</td>
								</tr>

								<tr>
									<td style="background:#ffffff; color:#454545;text-align:left;padding:10px 15px;font-size:14px;">
									<p style="text-align:left">Thank You.</p>
									</td>
								</tr>								
								<tr>
									<td style="padding:15px 30px;background:#ffffff;text-align:center;font-size:10px;color:#454545;">
									<p>This email is sent by {{$companyname}}.<br> &copy; 2021, {{$companyname}}. All rights reserved.</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
