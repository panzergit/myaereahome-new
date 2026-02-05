<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visitors\ApiController;
use App\Http\Controllers\Visitors\Apiv2Controller;
use App\Http\Controllers\Visitors\Apiv7Controller;
use App\Http\Controllers\Visitors\ApiV8Controller;

Route::controller(ApiController::class)->group(function () {
    Route::any('visitingPurpose', 'visitingPurpose');
    Route::any('visitorRegisitration', 'visitorRegisitration');
    Route::any('visitorRegSummary', 'visitorRegSummary');
    Route::any('visitorBookingInfo', 'visitorBookingInfo');
    Route::any('visitorBookingCancel', 'visitorBookingCancel');
    Route::any('visitorSendInvite', 'visitorSendInvite');
    Route::any('visitorRegValidation', 'visitorRegValidation');
    Route::any('testfirebase', 'testfirebase');
    Route::any('getqrstatus', 'getqrstatus');
});

Route::prefix('/v2')->controller(Apiv2Controller::class)->group(function(){
    Route::any('visitingPurpose', 'visitingPurpose');
    Route::any('visitorRegisitration', 'visitorRegisitration');
    Route::any('visitorRegSummary', 'visitorRegSummary');
    Route::any('visitorBookingInfo', 'visitorBookingInfo');
    Route::any('visitorBookingCancel', 'visitorBookingCancel');
    Route::any('visitorSendInvite', 'visitorSendInvite');
    Route::any('visitorRegValidation', 'visitorRegValidation');
    Route::any('testfirebase', 'testfirebase');
    Route::any('getqrstatus', 'getqrstatus');
    Route::any('trailgetqrstatus', 'trailgetqrstatus');
});

Route::prefix('/v7')->controller(Apiv7Controller::class)->group(function(){
    Route::any('visitingPurpose', 'visitingPurpose');
    Route::any('visitorRegisitration', 'visitorRegisitration');
    Route::any('visitorRegSummary', 'visitorRegSummary');
    Route::any('visitorBookingInfo', 'visitorBookingInfo');
    Route::any('visitorBookingCancel', 'visitorBookingCancel');
    Route::any('visitorSendInvite', 'visitorSendInvite');
    Route::any('visitorRegValidation', 'visitorRegValidation');
    Route::any('testfirebase', 'testfirebase');
    Route::any('getqrstatus', 'getqrstatus');
    Route::any('trailgetqrstatus', 'trailgetqrstatus');
});

Route::prefix('/v8')->controller(ApiV8Controller::class)->group(function(){

    Route::any('visitingPurpose', 'visitingPurpose');
    Route::any('visitorRegisitration', 'visitorRegisitration');
    Route::any('visitorRegSummary', 'visitorRegSummary');
    Route::any('visitorBookingInfo', 'visitorBookingInfo');
    Route::any('visitorBookingCancel', 'visitorBookingCancel');
    Route::any('visitorSendInvite', 'visitorSendInvite');
    Route::any('visitorRegValidation', 'visitorRegValidation');
    Route::any('testfirebase', 'testfirebase');
    Route::any('getqrstatus', 'getqrstatus');
    Route::any('trailgetqrstatus', 'trailgetqrstatus');

});