<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visitors\FrontController;
use App\Http\Controllers\Visitors\QrCodeController;
use App\Http\Controllers\FirebaseController;

Route::any('/', [FrontController::class, 'index']);
Route::any('visitor-save', [FrontController::class, 'visitorSave'])->name('visitor-save');

Route::any('pre-registration_test/{ticket}',[FrontController::class, 'pre_registration_test']);
Route::any('pre-registration/{ticket}',[FrontController::class, 'pre_registration']);
Route::any('visitor-summary/{ticket}',[FrontController::class, 'visitor_summary']);
Route::get('consolidatedPrint', [FrontController::class, 'consolidatedPrint']);
Route::get('batchinvoices/{id}', [FrontController::class, 'batchinvoices']);
Route::get('invoice-pdf/{ticket}', [FrontController::class, 'invoicePDF']);

Route::get('testinvoice-pdf/{ticket}', [FrontController::class, 'testinvoicePDF']);
Route::get('set-pdf-rows', [FrontController::class, 'setPdfRows']);
Route::get('payment-pdf/{ticket}', [FrontController::class, 'paymentPDF']);
Route::get('generate-pdf/{ticket}', [FrontController::class, 'generatePDF']);
Route::get('moveinginout-pdf/{ticket}', [FrontController::class, 'moveinginoutPDF']);
Route::get('renovation-pdf/{ticket}', [FrontController::class, 'renovationPDF']);
Route::get('dooraccess-pdf/{ticket}', [FrontController::class, 'dooraccessPDF']);
Route::get('vehicleiu-pdf/{ticket}', [FrontController::class, 'vehicleiuPDF']);
Route::get('address-pdf/{ticket}', [FrontController::class, 'addressPDF']);
Route::get('particulars-pdf/{ticket}', [FrontController::class, 'particularsPDF']);

Route::post('generate', [QrCodeController::class, 'index']);
Route::get('qrcodeview', [QrCodeController::class, 'Qrcodeview']);

Route::get('firebase', [FirebaseController::class, 'index']);
Route::get('load', [FirebaseController::class, 'load']);
?>