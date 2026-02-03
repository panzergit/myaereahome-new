<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorsApiV8Controller;

Route::any('visitingPurpose', [VisitorsApiV8Controller::class, 'visitingPurpose']);
Route::any('visitorRegisitration', [VisitorsApiV8Controller::class, 'visitorRegisitration']);
Route::any('visitorRegSummary', [VisitorsApiV8Controller::class, 'visitorRegSummary']);
Route::any('visitorBookingInfo', [VisitorsApiV8Controller::class, 'visitorBookingInfo']);
Route::any('visitorBookingCancel', [VisitorsApiV8Controller::class, 'visitorBookingCancel']);
Route::any('visitorSendInvite', [VisitorsApiV8Controller::class, 'visitorSendInvite']);
Route::any('visitorRegValidation', [VisitorsApiV8Controller::class, 'visitorRegValidation']);
Route::any('testfirebase', [VisitorsApiV8Controller::class, 'testfirebase']);
Route::any('getqrstatus', [VisitorsApiV8Controller::class, 'getqrstatus']);
Route::any('trailgetqrstatus', [VisitorsApiV8Controller::class, 'trailgetqrstatus']);