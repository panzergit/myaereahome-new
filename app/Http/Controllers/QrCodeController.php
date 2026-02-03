<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\v7\Qrdetail;
use QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {	
		$qrdetails = new Qrdetail;
		$qrdetails->companyname = $request->companyname;
		$qrdetails->websiteurl = $request->geturl;
		$qrtext = $request->geturl;
		$qrsize = 300;
        $randnum = rand(10000, 99999) . '-' . now()->format('YmdHis');
        $filename = "companyqrcodes/{$randnum}.png";

		// $img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(0)->generate($qrtext));
		// $data = base64_decode($img);
		// $file = image_storage_domain().'/companyqrcodes/'.$randnum.'.png';	
		// file_put_contents($file, $data);

		$qrPng = QrCode::format('png')
                ->size($qrsize)
                ->errorCorrection('H')
                ->margin(0)
                ->generate($qrtext);

		Storage::put(upload_path($filename), $qrPng);

		$qrdetails->imagepath = $filename;
		$qrdetails->save();

		return redirect('/qrcodeview')->with('success', ' QRCode Successfully Created!');
    }

	public function Qrcodeview()
	{
		$qrdetails = Qrdetail::all();      
        return view('visitors.qrcode',compact('qrdetails'));
	}
}
