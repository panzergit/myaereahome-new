<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorsFrontController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\FirebaseController;

Route::any('/', [VisitorsFrontController::class, 'index']);
Route::any('visitor-save', [VisitorsFrontController::class, 'visitorSave'])->name('visitor-save');

Route::any('/pre-registration_test/{ticket}',[VisitorsFrontController::class, 'pre_registration_test']);
Route::any('/pre-registration/{ticket}',[VisitorsFrontController::class, 'pre_registration']);
Route::any('/visitor-summary/{ticket}',[VisitorsFrontController::class, 'visitor_summary']);
Route::get('/consolidatedPrint', [VisitorsFrontController::class, 'consolidatedPrint']);
Route::get('/batchinvoices/{id}', [VisitorsFrontController::class, 'batchinvoices']);
Route::get('/invoice-pdf/{ticket}', [VisitorsFrontController::class, 'invoicePDF']);

Route::get('/testinvoice-pdf/{ticket}', [VisitorsFrontController::class, 'testinvoicePDF']);
Route::get('/set-pdf-rows', [VisitorsFrontController::class, 'setPdfRows']);
Route::get('/payment-pdf/{ticket}', [VisitorsFrontController::class, 'paymentPDF']);
Route::get('/generate-pdf/{ticket}', [VisitorsFrontController::class, 'generatePDF']);
Route::get('/moveinginout-pdf/{ticket}', [VisitorsFrontController::class, 'moveinginoutPDF']);
Route::get('/renovation-pdf/{ticket}', [VisitorsFrontController::class, 'renovationPDF']);
Route::get('/dooraccess-pdf/{ticket}', [VisitorsFrontController::class, 'dooraccessPDF']);
Route::get('/vehicleiu-pdf/{ticket}', [VisitorsFrontController::class, 'vehicleiuPDF']);
Route::get('/address-pdf/{ticket}', [VisitorsFrontController::class, 'addressPDF']);
Route::get('/particulars-pdf/{ticket}', [VisitorsFrontController::class, 'particularsPDF']);

Route::post('/generate', [QrCodeController::class, 'index']);
Route::get('/qrcodeview', [QrCodeController::class, 'Qrcodeview']);

Route::get('/firebase', [FirebaseController::class, 'index']);
Route::get('/load', [FirebaseController::class, 'load']);
?>