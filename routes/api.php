<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Apiv7Controller;
use App\Http\Controllers\Apiv8Controller;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OpnController;
use App\Http\Controllers\OpsApiv4Controller;
use App\Http\Controllers\CronController;
use App\Http\Controllers\Apiv2Controller;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SyncController;

Route::any('twiliosms', [Apiv7Controller::class, 'twiliosms']);

// Sync API
Route::post('/sync/apply', [SyncController::class, 'apply']);
Route::post('/sync/fetch', [SyncController::class, 'fetch']);
Route::post('/sync/mark-synced', [SyncController::class, 'markSynced']);

Route::controller(ApiController::class)->group(function () {
    Route::any('retrieveInfoApi', 'retrieveInfoApi');
    Route::any('verifyOtpApi', 'verifyOtpApi');
    Route::any('setPasswordApi', 'setPasswordApi');
    Route::any('verifyLoginApi', 'verifyLoginApi');
    Route::any('resendOtpApi', 'resendOtpApi');
    Route::any('updatePassword', 'updatePassword');
    Route::any('updatePicture', 'updatePicture');
    Route::any('updateProfile', 'updateProfile');
    Route::any('getUserinfo', 'userinfo');
    Route::any('getUserthinmooinfo', 'user_thinmoo_info');

    Route::any('forgotPassword', 'forgotPassword');
    Route::any('FacialRegPicOption', 'FacialRegPicOption');
    Route::any('FacialRegPic', 'FacialRegPic');
    Route::any('FacialRegPicAdd', 'FacialRegPicAdd');
    Route::any('FacialRegPicDelete', 'FacialRegPicDelete');
    Route::any('FacialRegPicUpdate', 'FacialRegPicUpdate');
    Route::any('PushCallAction', 'call_from_thinmoo');
    Route::any('CallPushAppNotification', 'call_push_notification');
    Route::any('HackingworkEnddate', 'hackingwork_enddate');

    Route::any('BluetoothDevices', 'bluetooth_device_info');
    Route::any('UserBluetoothDevices', 'user_bluetooth_device_list');
    Route::any('UserRemoteDevices', 'user_remote_device_list');
    Route::any('InsertBluetoothOpenRecord', 'BluetoothDoorOpenRecord');

    Route::any('FailOpenDoorRecordPush', 'FailOpenDoorRecordPush');
    Route::any('CallUnitRecordPush', 'CallUnitRecordPush');
    Route::any('FailOpenDoorRecordPush2', 'FailOpenDoorRecordPush2');
    Route::any('CallUnitRecordPush2', 'CallUnitRecordPush2');
    Route::any('ChecktimeInterval', 'ChecktimeInterval');
    Route::any('ParkingRecordPush', 'ParkingRecordPush');

    Route::any('getAnnouncement', 'announcement');
    Route::any('getDefectslist', 'defectslist');
    Route::any('getfeedbacklist', 'feedbacklist');

    Route::any('getFeedbackoption', 'feedbackoption');
    Route::any('getDefectslocation', 'defectslocation');
    Route::any('getDefectstype', 'defectstype');

    Route::any('checkUnitTakeover', 'checkunittakeover');
    Route::any('checkJointInspection', 'checkjointinspection');
    Route::any('gettakeovertimeslots', 'gettakeovertimeslots');

    Route::any('bookunittakeover', 'bookunittakeover');
    Route::any('bookjointinspection', 'bookjointinspection');
    Route::any('getinspectiontimeslots', 'getinspectiontimeslots');

    Route::any('submitdefects', 'submitdefects');
    Route::any('getdefects', 'getdefects');
    Route::any('submitfeedbacks', 'submitfeedbacks');
    Route::any('updatesingnature', 'updatesingnature');
    Route::any('submitdefectreview', 'submitdefectreview');
    Route::any('inspectionsingnature', 'inspectionsingnature');
    Route::any('handoversingnature', 'handoversingnature');

    Route::any('bookfacility', 'bookfacility');
    Route::any('cancelfacilitybooking', 'cancelfacilitybooking');
    Route::any('getfacilitiestype', 'facilitiestype');
    Route::any('getfacilitytimeslots', 'getfacilitytimeslots');
    Route::any('getfacilitybooking', 'getfacilitybooking');

    Route::any('facilitydetail', 'facilityDetail');
    Route::any('feedbackdetail', 'feedbackDetail');
    Route::any('defectdetail', 'defectDetail');
    Route::any('announcementdetail', 'announcementDetail');

    Route::any('announcementstatusupdated', 'announcementStatusUpdate');

    Route::any('takeoverapptdetails', 'takeoverapptdetails');
    Route::any('inspectionapptdetails', 'inspectionapptdetails');

    Route::any('inboxMessage', 'inboxMessage');
    Route::any('upcomingEvents', 'upcomingEvents');

    Route::any('enquiry', 'enquiry');

    Route::any('getandroidversion', 'getandroidversion');
    Route::any('getiosversion', 'getiosversion');
    Route::any('getaccesstoken', 'getaccesstoken');

    Route::any('getdocumentcategories', 'documentCategories');
    Route::any('getcategoryfiles', 'categoryFiles');
    Route::any('faciltybookingvalidation', 'validateFacilityBoooking');

    Route::any('getDocumentType', 'getDocumentType');
    Route::any('residentFileUpload', 'residentFileUpload');
    Route::any('residentFileUploadDetail', 'residentFileUploadDetail');
    Route::any('getUploadedlist', 'uploadedlist');
    Route::any('getUploadedFilelist', 'uploadedFilelist');

    Route::any('password_reset_mannual', 'password_reset_mannual');

    Route::any('loginHistoryLogs', 'loginHistoryLogs');
    Route::any('logoutHistoryLogs', 'logoutHistoryLogs');

    Route::any('visitingPurpose', 'visitingPurpose');
    Route::any('visitorRegisitration', 'visitorRegisitration');
    Route::any('visitorRegSummary', 'visitorRegSummary');
    Route::any('visitorBookingInfo', 'visitorBookingInfo');
    Route::any('visitorBookingCancel', 'visitorBookingCancel');
    Route::any('visitorSendInvite', 'visitorSendInvite');

    Route::any('dooropenmenulists', 'dooropenmenulists');

    Route::any('eformslists', 'eformslists');
    Route::any('eformsettingdetail', 'eformsettingdetail');
    Route::any('eformMovingIO', 'eform_movinginout');
    Route::any('eformMovingIOInfo', 'eform_movinginout_info');
    Route::any('eformRenovation', 'eform_renovation');
    Route::any('eformRenovationInfo', 'eform_renovation_info');
    Route::any('eformDooraccess', 'eform_dooraccess');
    Route::any('eformDooraccessInfo', 'eform_dooraccess_info');
    Route::any('eformRegVehicleIU', 'eform_reg_vehicle');
    Route::any('eformRegVehicleIUInfo', 'eform_reg_vehicle_info');
    Route::any('eformRegVehicleFileCat', 'eform_reg_vehicle_file_category');
    Route::any('eformChangeAddress', 'eform_change_address');
    Route::any('eformAddressInfo', 'eform_address_info');
    Route::any('eformUpdateParticulars', 'eform_update_particulars');
    Route::any('eformParticularsInfo', 'eform_reg_particulars_info');
    Route::any('eformsubmittedlists', 'eformsubmittedlists');
    Route::any('eformsubmittedsearchlists', 'eformsubmittedsearchlists');
});

// Visitor APIs
Route::prefix('visitors')->group(function () {
    require base_path('routes/visitors_api.php');
});

Route::prefix('/v2')->group(function () {
    Route::any('PushCallAction', [Apiv2Controller::class, 'call_from_thinmoo']);
    Route::any('FailOpenDoorRecordPush', [Apiv2Controller::class, 'FailOpenDoorRecordPush']);
    Route::any('CallUnitRecordPush', [Apiv2Controller::class, 'CallUnitRecordPush']);
    Route::any('ParkingRecordPush', [Apiv2Controller::class, 'ParkingRecordPush']);
});

Route::prefix('/v7')->group(function () {
    Route::any('test_firebase', [Apiv7Controller::class, 'test_firebase']);
    Route::any('release_notification', [Apiv7Controller::class, 'release_notification']);
    Route::any('retrieveInfoApi', [Apiv7Controller::class, 'retrieveInfoApi']);
    Route::any('verifyOtpApi', [Apiv7Controller::class, 'verifyOtpApi']);
    Route::any('setPasswordApi', [Apiv7Controller::class, 'setPasswordApi']);
    Route::any('verifyLoginApi', [Apiv7Controller::class, 'verifyLoginApi']);
    Route::any('resendOtpApi', [Apiv7Controller::class, 'resendOtpApi']);
    Route::any('updatePassword', [Apiv7Controller::class, 'updatePassword']);
    Route::any('updatePicture', [Apiv7Controller::class, 'updatePicture']);
    Route::any('updateProfile', [Apiv7Controller::class, 'updateProfile']);
    Route::any('getTermAndCondion', [Apiv7Controller::class, 'getTermAndCondion']);
    Route::any('getPushNotificationSettings', [Apiv7Controller::class, 'getPushNotificationSettings']);
    Route::any('UpdatePushNotificationSettings', [Apiv7Controller::class, 'UpdatePushNotificationSettings']);
    Route::any('encryptstring', [Apiv7Controller::class, 'encryptstring']);
    Route::any('decryptstring', [Apiv7Controller::class, 'decryptstring']);

    Route::any('getUserinfo', [Apiv7Controller::class, 'userinfo']);
    Route::any('getUserthinmooinfo', [Apiv7Controller::class, 'user_thinmoo_info']);
    Route::any('dashboardmenu', [Apiv7Controller::class, 'dashboardmenu']);
    Route::any('add_favmenu', [Apiv7Controller::class, 'add_favmenu']);
    Route::any('delete_favmenu', [Apiv7Controller::class, 'delete_favmenu']);
    Route::any('invoices', [Apiv7Controller::class, 'invoices']);
    Route::any('viewinvoice', [Apiv7Controller::class, 'viewinvoice']);
    Route::any('viewinvoice', [Apiv7Controller::class, 'viewinvoice']);
    Route::any('filter_invoice', [Apiv7Controller::class, 'filter_invoice']);
    Route::any('getqrcode', [Apiv7Controller::class, 'getqrcode']);
    Route::any('payment_screenshot', [Apiv7Controller::class, 'payment_screenshot']);
    Route::any('bookinglists', [Apiv7Controller::class, 'bookinglists']);
    Route::any('notifications', [Apiv7Controller::class, 'user_notifications']);
    Route::any('update_notification', [Apiv7Controller::class, 'update_notification']);
    Route::any('switchunit', [Apiv7Controller::class, 'switchunit']);
    Route::any('getunitlist', [Apiv7Controller::class, 'getunitlist']);
    Route::any('getSwitchId', [Apiv7Controller::class, 'getswitchid']);

    Route::any('forgotPassword', [Apiv7Controller::class, 'forgotPassword']);
    Route::any('FacialRegPicOption', [Apiv7Controller::class, 'FacialRegPicOption']);
    Route::any('FacialRegPic', [Apiv7Controller::class, 'FacialRegPic']);
    Route::any('FacialRegPicAdd', [Apiv7Controller::class, 'FacialRegPicAdd']);
    Route::any('FacialRegPicDelete', [Apiv7Controller::class, 'FacialRegPicDelete']);
    Route::any('FacialRegPicUpdate', [Apiv7Controller::class, 'FacialRegPicUpdate']);
    Route::any('PushCallAction', [Apiv7Controller::class, 'call_from_thinmoo']);
    Route::any('CallPushAppNotification', [Apiv7Controller::class, 'call_push_notification']);
    Route::any('HackingworkEnddate', [Apiv7Controller::class, 'hackingwork_enddate']);

    Route::any('BluetoothDevices', [Apiv7Controller::class, 'bluetooth_device_info']);
    Route::any('UserBluetoothDevices', [Apiv7Controller::class, 'user_bluetooth_device_list']);
    Route::any('UserRemoteDevices', [Apiv7Controller::class, 'user_remote_device_list']);
    Route::any('InsertBluetoothOpenRecord', [Apiv7Controller::class, 'BluetoothDoorOpenRecord']);

    Route::any('FailOpenDoorRecordPush', [Apiv7Controller::class, 'FailOpenDoorRecordPush']);
    Route::any('CallUnitRecordPush', [Apiv7Controller::class, 'CallUnitRecordPush']);
    Route::any('FailOpenDoorRecordPush2', [Apiv7Controller::class, 'FailOpenDoorRecordPush2']);
    Route::any('CallUnitRecordPush2', [Apiv7Controller::class, 'CallUnitRecordPush2']);
    Route::any('ChecktimeInterval', [Apiv7Controller::class, 'ChecktimeInterval']);
    Route::any('getAnnouncement', [Apiv7Controller::class, 'announcement']);
    Route::any('getDefectslist', [Apiv7Controller::class, 'defectslist']);
    Route::any('getfeedbacklist', [Apiv7Controller::class, 'feedbacklist']);

    Route::any('getFeedbackoption', [Apiv7Controller::class, 'feedbackoption']);
    Route::any('getDefectslocation', [Apiv7Controller::class, 'defectslocation']);
    Route::any('getDefectstype', [Apiv7Controller::class, 'defectstype']);

    Route::any('checkUnitTakeover', [Apiv7Controller::class, 'checkunittakeover']);
    Route::any('checkJointInspection', [Apiv7Controller::class, 'checkjointinspection']);
    Route::any('gettakeovertimeslots', [Apiv7Controller::class, 'gettakeovertimeslots']);
    Route::any('checkfinalinspection', [Apiv7Controller::class, 'checkfinalinspection']);

    Route::any('ContactLists', [Apiv7Controller::class, 'ContactLists']);
    Route::any('InfoContact', [Apiv7Controller::class, 'InfoContact']);
    Route::any('AddContact', [Apiv7Controller::class, 'AddContact']);
    Route::any('EditContact', [Apiv7Controller::class, 'EditContact']);
    Route::any('DeleteContact', [Apiv7Controller::class, 'DeleteContact']);
    Route::any('SearchContact', [Apiv7Controller::class, 'SearchContact']);
    Route::any('bookunittakeover', [Apiv7Controller::class, 'bookunittakeover']);
    Route::any('bookjointinspection', [Apiv7Controller::class, 'bookjointinspection']);
    Route::any('getinspectiontimeslots', [Apiv7Controller::class, 'getinspectiontimeslots']);
    Route::any('bookfinalinspection', [Apiv7Controller::class, 'bookfinalinspection']);
    Route::any('chatterbox_category', [Apiv7Controller::class, 'chatterbox_category']);
    Route::any('chatterboxlist', [Apiv7Controller::class, 'chatterboxlist']);
    Route::any('chatterboxsubmit', [Apiv7Controller::class, 'chatterboxsubmit']);
    Route::any('chatterboxdetail', [Apiv7Controller::class, 'chatterboxdetail']);
    Route::any('chatterboxcomment', [Apiv7Controller::class, 'chatterboxcomment']);
    Route::any('chatterboxreport', [Apiv7Controller::class, 'chatterboxreport']);
    Route::any('chatterboxdelete', [Apiv7Controller::class, 'chatterboxdelete']);
    Route::any('chattercommentdelete', [Apiv7Controller::class, 'chattercommentdelete']);
    Route::any('chatterboxcommentreport', [Apiv7Controller::class, 'chatterboxcommentreport']);
    Route::any('chatterreportrevert', [Apiv7Controller::class, 'chatterreportrevert']);
    Route::any('chatteruserlists', [Apiv7Controller::class, 'chatteruserlists']);
    Route::any('chatterblockuser', [Apiv7Controller::class, 'chatterblockuser']);
    Route::any('chatterunblockuser', [Apiv7Controller::class, 'chatterunblockuser']);
    Route::any('chatterbox_tnc', [Apiv7Controller::class, 'chatterbox_tnc']);
    Route::any('chatterbox_accept_tnc', [Apiv7Controller::class, 'chatterbox_accept_tnc']);
    Route::any('chatterbox_tnc_status', [Apiv7Controller::class, 'chatterbox_tnc_status']);

    Route::any('chatReplyNotification', [Apiv7Controller::class, 'chatReplyNotification']);
    Route::any('chatAttachmentAPI', [Apiv7Controller::class, 'chatAttachmentAPI']);
    Route::any('submitdefects', [Apiv7Controller::class, 'submitdefects']);
    Route::any('getdefects', [Apiv7Controller::class, 'getdefects']);
    Route::any('submitfeedbacks', [Apiv7Controller::class, 'submitfeedbacks']);
    Route::any('updatesingnature', [Apiv7Controller::class, 'updatesingnature']);
    Route::any('submitdefectreview', [Apiv7Controller::class, 'submitdefectreview']);
    Route::any('inspectionsingnature', [Apiv7Controller::class, 'inspectionsingnature']);
    Route::any('handoversingnature', [Apiv7Controller::class, 'handoversingnature']);

    Route::any('bookfacility', [Apiv7Controller::class, 'bookfacility']);
    Route::any('cancelfacilitybooking', [Apiv7Controller::class, 'cancelfacilitybooking']);
    Route::any('getfacilitiestype', [Apiv7Controller::class, 'facilitiestype']);
    Route::any('getfacilitytimeslots', [Apiv7Controller::class, 'getfacilitytimeslots']);
    Route::any('getfacilitybooking', [Apiv7Controller::class, 'getfacilitybooking']);

    Route::any('facilitydetail', [Apiv7Controller::class, 'facilityDetail']);
    Route::any('feedbackdetail', [Apiv7Controller::class, 'feedbackDetail']);
    Route::any('defectdetail', [Apiv7Controller::class, 'defectDetail']);
    Route::any('announcementdetail', [Apiv7Controller::class, 'announcementDetail']);

    Route::any('announcementstatusupdated', [Apiv7Controller::class, 'announcementStatusUpdate']);

    Route::any('takeoverapptdetails', [Apiv7Controller::class, 'takeoverapptdetails']);
    Route::any('inspectionapptdetails', [Apiv7Controller::class, 'inspectionapptdetails']);

    Route::any('inboxMessage', [Apiv7Controller::class, 'inboxMessage']);
    Route::any('update_inbox', [Apiv7Controller::class, 'update_inboxmessage']);
    Route::any('upcomingEvents', [Apiv7Controller::class, 'upcomingEvents']);

    Route::any('enquiry', [Apiv7Controller::class, 'enquiry']);
    Route::any('getandroidversion', [Apiv7Controller::class, 'getandroidversion']);
    Route::any('getiosversion', [Apiv7Controller::class, 'getiosversion']);
    Route::any('getaccesstoken', [Apiv7Controller::class, 'getaccesstoken']);

    Route::any('getdocumentcategories', [Apiv7Controller::class, 'documentCategories']);
    Route::any('getcategoryfiles', [Apiv7Controller::class, 'categoryFiles']);
    Route::any('faciltybookingvalidation', [Apiv7Controller::class, 'validateFacilityBoooking']);

    Route::any('getDocumentType', [Apiv7Controller::class, 'getDocumentType']);
    Route::any('residentFileUpload', [Apiv7Controller::class, 'residentFileUpload']);
    Route::any('residentFileUploadDetail', [Apiv7Controller::class, 'residentFileUploadDetail']);
    Route::any('getUploadedlist', [Apiv7Controller::class, 'uploadedlist']);
    Route::any('getUploadedFilelist', [Apiv7Controller::class, 'uploadedFilelist']);

    Route::any('password_reset_mannual', [Apiv7Controller::class, 'password_reset_mannual']);

    Route::any('loginHistoryLogs', [Apiv7Controller::class, 'loginHistoryLogs']);
    Route::any('logoutHistoryLogs', [Apiv7Controller::class, 'logoutHistoryLogs']);

    Route::any('visitingPurpose', [Apiv7Controller::class, 'visitingPurpose']);
    Route::any('visitorRegisitration', [Apiv7Controller::class, 'visitorRegisitration']);
    Route::any('visitorRegSummary', [Apiv7Controller::class, 'visitorRegSummary']);
    Route::any('visitorBookingInfo', [Apiv7Controller::class, 'visitorBookingInfo']);
    Route::any('visitorBookingCancel', [Apiv7Controller::class, 'visitorBookingCancel']);
    Route::any('visitorSendInvite', [Apiv7Controller::class, 'visitorSendInvite']);

    Route::any('submenulists', [Apiv7Controller::class, 'submenulists']);

    Route::any('eformslists', [Apiv7Controller::class, 'eformslists']);
    Route::any('eformsettingdetail', [Apiv7Controller::class, 'eformsettingdetail']);
    Route::any('eformMovingIO', [Apiv7Controller::class, 'eform_movinginout']);
    Route::any('eformMovingIOInfo', [Apiv7Controller::class, 'eform_movinginout_info']);
    Route::any('eformRenovation', [Apiv7Controller::class, 'eform_renovation']);
    Route::any('eformRenovationInfo', [Apiv7Controller::class, 'eform_renovation_info']);
    Route::any('eformDooraccess', [Apiv7Controller::class, 'eform_dooraccess']);
    Route::any('eformDooraccessInfo', [Apiv7Controller::class, 'eform_dooraccess_info']);
    Route::any('eformRegVehicleIU', [Apiv7Controller::class, 'eform_reg_vehicle']);
    Route::any('eformRegVehicleIUInfo', [Apiv7Controller::class, 'eform_reg_vehicle_info']);
    Route::any('eformRegVehicleFileCat', [Apiv7Controller::class, 'eform_reg_vehicle_file_category']);
    Route::any('eformChangeAddress', [Apiv7Controller::class, 'eform_change_address']);
    Route::any('eformAddressInfo', [Apiv7Controller::class, 'eform_address_info']);
    Route::any('eformUpdateParticulars', [Apiv7Controller::class, 'eform_update_particulars']);
    Route::any('eformParticularsInfo', [Apiv7Controller::class, 'eform_reg_particulars_info']);
    Route::any('eformsubmittedlists', [Apiv7Controller::class, 'eformsubmittedlists']);
    Route::any('eformsubmittedsearchlists', [Apiv7Controller::class, 'eformsubmittedsearchlists']);

    Route::any('MpAdsConditions', [Apiv7Controller::class, 'MpAdsConditions']);
    Route::any('MpAdsTypes', [Apiv7Controller::class, 'MpAdsTypes']);
    Route::any('MpAdsSubmit', [Apiv7Controller::class, 'MpAdsSubmit']);
    Route::any('MpAdsUpdate', [Apiv7Controller::class, 'MpAdsUpdate']);
    Route::any('MpAdsAddImage', [Apiv7Controller::class, 'MpAdsAddImage']);
    Route::any('MpAdsUpdateImage', [Apiv7Controller::class, 'MpAdsUpdateImage']);
    Route::any('MpAdsDeleteImage', [Apiv7Controller::class, 'MpAdsDeleteImage']);
    Route::any('MpAdsMyList', [Apiv7Controller::class, 'MpAdsMyList']);
    Route::any('MpAdsList', [Apiv7Controller::class, 'MpAdsList']);
    Route::any('MpGroupList', [Apiv7Controller::class, 'MpGroupList']);
    Route::any('MpAdsLikeList', [Apiv7Controller::class, 'MpAdsLikeList']);
    Route::any('MpAdsSearch', [Apiv7Controller::class, 'MpAdsSearch']);
    Route::any('MpAdsItemDetail', [Apiv7Controller::class, 'MpAdsItemDetail']);
    Route::any('MpAdsReportRevert', [Apiv7Controller::class, 'MpAdsReportRevert']);
    Route::any('MpAdsReport', [Apiv7Controller::class, 'MpAdsReport']);
    Route::any('MpAdsMarkAsSold', [Apiv7Controller::class, 'MpAdsMarkAsSold']);
    Route::any('MpAdsMarkAsUnsold', [Apiv7Controller::class, 'MpAdsMarkAsUnsold']);
    Route::any('MpAdsDelete', [Apiv7Controller::class, 'MpAdsDelete']);
    Route::any('MpAdsBlockedUserLists', [Apiv7Controller::class, 'MpAdsBlockedUserLists']);
    Route::any('MpAdsBlockUser', [Apiv7Controller::class, 'MpAdsBlockUser']);
    Route::any('MpAdsUnblockUser', [Apiv7Controller::class, 'MpAdsUnblockUser']);
    Route::any('MpAdsLike', [Apiv7Controller::class, 'MpAdsLike']);
    Route::any('MpAdsUnLike', [Apiv7Controller::class, 'MpAdsUnLike']);
    Route::any('MpGroupRegister', [Apiv7Controller::class, 'MpGroupRegister']);
    Route::any('MpGroupUnregister', [Apiv7Controller::class, 'MpGroupUnregister']);

    Route::any('MagazineLists', [Apiv7Controller::class, 'MagazineLists']);
    Route::any('MagazineDetail', [Apiv7Controller::class, 'MagazineDetail']);
    Route::any('UserGuideLists', [Apiv7Controller::class, 'UserGuideLists']);
    Route::any('UserGuideDetail', [Apiv7Controller::class, 'UserGuideDetail']);

    Route::any('charges', [PaymentController::class, 'charges']);

    Route::any('opn_retrive_customer_info', [OpnController::class, 'opn_retrive_customer_info']);
    Route::any('opn_account_creation', [OpnController::class, 'opn_account_creation']);
    Route::any('opn_create_token', [OpnController::class, 'opn_create_token']);
    Route::any('opn_attach_card_customer', [OpnController::class, 'opn_attach_card_customer']);
    Route::any('opn_update_default_card', [OpnController::class, 'opn_update_default_card']);
    Route::any('opn_destroy_card', [OpnController::class, 'opn_destroy_card']);
    Route::any('opn_create_charge', [OpnController::class, 'opn_create_charge']);
    Route::any('non_3ds_create_charge', [OpnController::class, 'non_3ds_create_charge']);

    Route::any('opn_deposit_call', [OpnController::class, 'opn_deposit_call']);
    Route::any('opn_3ds_charger_info', [OpnController::class, 'opn_3ds_charger_info']);
    Route::any('opn_capture_charge', [OpnController::class, 'opn_capture_charge']);
    Route::any('opn_reverse_amount', [OpnController::class, 'opn_reverse_amount']);
    //https://aerea.panzerplayground.com/api/payment/testwebhook
    Route::any('opn_payment_webhook', [OpnController::class, 'opn_payment_webhook']); // OPN Webhook
    Route::any('opn_facility_deposit_capture', [OpnController::class, 'opn_facility_deposit_capture']);


    Route::any('visitorRegValidation', [Apiv7Controller::class, 'visitorRegValidation']);
});

Route::prefix('/v8')->group(function () {

    Route::any('userRegistration', [Apiv8Controller::class, 'user_registration']);
    Route::any('getProperties', [Apiv8Controller::class, 'property_lists']);
    Route::any('propertyBlock', [Apiv8Controller::class, 'property_block']);
    Route::any('blockUnit', [Apiv8Controller::class, 'block_unit']);
    Route::any('userRoles', [Apiv8Controller::class, 'user_roles']);
    Route::any('getCountries', [Apiv8Controller::class, 'countries']);
    Route::any('getPropertiesArray', [Apiv8Controller::class, 'property_array']);
    Route::any('propertyBlockArray', [Apiv8Controller::class, 'property_block_array']);
    Route::any('blockUnitArray', [Apiv8Controller::class, 'block_unit_array']);
    Route::any('userRolesArray', [Apiv8Controller::class, 'user_roles_array']);
    Route::any('getCountriesArray', [Apiv8Controller::class, 'countries_array']);
    Route::any('regRolesArray', [Apiv8Controller::class, 'reg_roles_array']);

    Route::any('twiliosms', [Apiv8Controller::class, 'twiliosms']);
    Route::any('verifyLoginApi', [Apiv8Controller::class, 'verifyLoginApi']);
    Route::any('verifyOtpApi', [Apiv8Controller::class, 'verifyOtpApi']);

    Route::any('retrieveInfoApi', [Apiv8Controller::class, 'retrieveInfoApi']);
    Route::any('setPasswordApi', [Apiv8Controller::class, 'setPasswordApi']);
    Route::any('resendOtpApi', [Apiv8Controller::class, 'resendOtpApi']);
    Route::any('test_firebase', [Apiv8Controller::class, 'test_firebase']);
    Route::any('forgotPassword', [Apiv8Controller::class, 'forgotPassword']);
    Route::any('PushCallAction', [Apiv8Controller::class, 'call_from_thinmoo']);
    Route::any('CallPushAppNotification', [Apiv8Controller::class, 'call_push_notification']);
    Route::any('HackingworkEnddate', [Apiv8Controller::class, 'hackingwork_enddate']);
    Route::any('BluetoothDevices', [Apiv8Controller::class, 'bluetooth_device_info']);
    Route::any('FailOpenDoorRecordPush', [Apiv8Controller::class, 'FailOpenDoorRecordPush']);
    Route::any('CallUnitRecordPush', [Apiv8Controller::class, 'CallUnitRecordPush']);
    Route::any('ChecktimeInterval', [Apiv8Controller::class, 'ChecktimeInterval']);
    Route::any('getFeedbackoption', [Apiv8Controller::class, 'feedbackoption']);
    Route::any('getDefectslocation', [Apiv8Controller::class, 'defectslocation']);
    Route::any('getDefectstype', [Apiv8Controller::class, 'defectstype']);
    Route::any('gettakeovertimeslots', [Apiv8Controller::class, 'gettakeovertimeslots']);
    Route::any('getinspectiontimeslots', [Apiv8Controller::class, 'getinspectiontimeslots']);
    Route::any('chatterbox_category', [Apiv8Controller::class, 'chatterbox_category']);
    Route::any('chatReplyNotification', [Apiv8Controller::class, 'chatReplyNotification']);
    Route::any('getfacilitiestype', [Apiv8Controller::class, 'facilitiestype']);
    Route::any('getandroidversion', [Apiv8Controller::class, 'getandroidversion']);
    Route::any('getiosversion', [Apiv8Controller::class, 'getiosversion']);
    Route::any('getaccesstoken', [Apiv8Controller::class, 'getaccesstoken']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('account-delete')->group(function () {
            Route::get('reasons', [Apiv8Controller::class, 'accountDeleteReasons']);
            Route::post('request', [Apiv8Controller::class, 'accountDeleteRequest']);
        });

        Route::any('release_notification', [Apiv8Controller::class, 'release_notification']);
        Route::any('updatePassword', [Apiv8Controller::class, 'updatePassword']);
        Route::any('updatePicture', [Apiv8Controller::class, 'updatePicture']);
        Route::any('updateProfile', [Apiv8Controller::class, 'updateProfile']);
        Route::any('getTermAndCondion', [Apiv8Controller::class, 'getTermAndCondion']);
        Route::any('getPushNotificationSettings', [Apiv8Controller::class, 'getPushNotificationSettings']);
        Route::any('UpdatePushNotificationSettings', [Apiv8Controller::class, 'UpdatePushNotificationSettings']);
        Route::any('encryptstring', [Apiv8Controller::class, 'encryptstring']);
        Route::any('decryptstring', [Apiv8Controller::class, 'decryptstring']);
        Route::any('getUserinfo', [Apiv8Controller::class, 'userinfo']);
        Route::any('getUserthinmooinfo', [Apiv8Controller::class, 'user_thinmoo_info']);
        Route::any('dashboardmenu', [Apiv8Controller::class, 'dashboardmenu']);
        Route::any('add_favmenu', [Apiv8Controller::class, 'add_favmenu']);
        Route::any('delete_favmenu', [Apiv8Controller::class, 'delete_favmenu']);
        Route::any('invoices', [Apiv8Controller::class, 'invoices']);
        Route::any('viewinvoice', [Apiv8Controller::class, 'viewinvoice']);
        Route::any('filter_invoice', [Apiv8Controller::class, 'filter_invoice']);
        Route::any('getqrcode', [Apiv8Controller::class, 'getqrcode']);
        Route::any('payment_screenshot', [Apiv8Controller::class, 'payment_screenshot']);
        Route::any('bookinglists', [Apiv8Controller::class, 'bookinglists']);
        Route::any('notifications', [Apiv8Controller::class, 'user_notifications']);
        Route::any('update_notification', [Apiv8Controller::class, 'update_notification']);
        Route::any('switchunit', [Apiv8Controller::class, 'switchunit']);
        Route::any('getunitlist', [Apiv8Controller::class, 'getunitlist']);
        Route::any('getSwitchId', [Apiv8Controller::class, 'getswitchid']);
        Route::any('FacialRegPicOption', [Apiv8Controller::class, 'FacialRegPicOption']);
        Route::any('FacialRegPic', [Apiv8Controller::class, 'FacialRegPic']);
        Route::any('FacialRegPicAdd', [Apiv8Controller::class, 'FacialRegPicAdd']);
        Route::any('FacialRegPicDelete', [Apiv8Controller::class, 'FacialRegPicDelete']);
        Route::any('FacialRegPicUpdate', [Apiv8Controller::class, 'FacialRegPicUpdate']);
        Route::any('UserBluetoothDevices', [Apiv8Controller::class, 'user_bluetooth_device_list']);
        Route::any('RemoteDoorOpen', [Apiv8Controller::class, 'remote_door_open']);
        Route::any('UserRemoteDevices', [Apiv8Controller::class, 'user_remote_device_list']);
        Route::any('InsertBluetoothOpenRecord', [Apiv8Controller::class, 'BluetoothDoorOpenRecord']);
        Route::any('getAnnouncement', [Apiv8Controller::class, 'announcement']);
        Route::any('getDefectslist', [Apiv8Controller::class, 'defectslist']);
        Route::any('getfeedbacklist', [Apiv8Controller::class, 'feedbacklist']);
        Route::any('checkUnitTakeover', [Apiv8Controller::class, 'checkunittakeover']);
        Route::any('checkJointInspection', [Apiv8Controller::class, 'checkjointinspection']);
        Route::any('checkfinalinspection', [Apiv8Controller::class, 'checkfinalinspection']);
        Route::any('UserAllDevices', [Apiv8Controller::class, 'user_all_device_list']);
        Route::any('InsertCallUnitRecord', [Apiv8Controller::class, 'CallUnitRecord']);
        Route::any('ContactLists', [Apiv8Controller::class, 'ContactLists']);
        Route::any('InfoContact', [Apiv8Controller::class, 'InfoContact']);
        Route::any('AddContact', [Apiv8Controller::class, 'AddContact']);
        Route::any('EditContact', [Apiv8Controller::class, 'EditContact']);
        Route::any('DeleteContact', [Apiv8Controller::class, 'DeleteContact']);
        Route::any('SearchContact', [Apiv8Controller::class, 'SearchContact']);

        Route::any('bookunittakeover', [Apiv8Controller::class, 'bookunittakeover']);
        Route::any('bookjointinspection', [Apiv8Controller::class, 'bookjointinspection']);
        Route::any('bookfinalinspection', [Apiv8Controller::class, 'bookfinalinspection']);

        Route::any('chatterboxlist', [Apiv8Controller::class, 'chatterboxlist']);
        Route::any('chatterboxsubmit', [Apiv8Controller::class, 'chatterboxsubmit']);
        Route::any('chatterboxdetail', [Apiv8Controller::class, 'chatterboxdetail']);
        Route::any('chatterboxcomment', [Apiv8Controller::class, 'chatterboxcomment']);
        Route::any('chatterboxreport', [Apiv8Controller::class, 'chatterboxreport']);
        Route::any('chatterboxdelete', [Apiv8Controller::class, 'chatterboxdelete']);
        Route::any('chattercommentdelete', [Apiv8Controller::class, 'chattercommentdelete']);
        Route::any('chatterboxcommentreport', [Apiv8Controller::class, 'chatterboxcommentreport']);
        Route::any('chatterreportrevert', [Apiv8Controller::class, 'chatterreportrevert']);
        Route::any('chatteruserlists', [Apiv8Controller::class, 'chatteruserlists']);
        Route::any('chatterblockuser', [Apiv8Controller::class, 'chatterblockuser']);
        Route::any('chatterunblockuser', [Apiv8Controller::class, 'chatterunblockuser']);
        Route::any('chatterbox_tnc', [Apiv8Controller::class, 'chatterbox_tnc']);
        Route::any('chatterbox_accept_tnc', [Apiv8Controller::class, 'chatterbox_accept_tnc']);
        Route::any('chatterbox_tnc_status', [Apiv8Controller::class, 'chatterbox_tnc_status']);
        Route::any('chatAttachmentAPI', [Apiv8Controller::class, 'chatAttachmentAPI']);

        Route::any('submitdefects', [Apiv8Controller::class, 'submitdefects']);
        Route::any('defectupdate', [Apiv8Controller::class, 'defectupdate']);
        Route::any('submitfeedbacks', [Apiv8Controller::class, 'submitfeedbacks']);
        Route::any('updatesingnature', [Apiv8Controller::class, 'updatesingnature']);
        Route::any('submitdefectreview', [Apiv8Controller::class, 'submitdefectreview']);
        Route::any('inspectionsingnature', [Apiv8Controller::class, 'inspectionsingnature']);
        Route::any('handoversingnature', [Apiv8Controller::class, 'handoversingnature']);

        Route::any('bookfacility', [Apiv8Controller::class, 'bookfacility']);
        Route::any('cancelfacilitybooking', [Apiv8Controller::class, 'cancelfacilitybooking']);
        Route::any('getfacilitytimeslots', [Apiv8Controller::class, 'getfacilitytimeslots']);
        Route::any('getfacilitybooking', [Apiv8Controller::class, 'getfacilitybooking']);
        Route::any('getfacilitytimeslotstest', [Apiv8Controller::class, 'getfacilitytimeslotsTest']);

        Route::any('facilitydetail', [Apiv8Controller::class, 'facilityDetail']);
        Route::any('feedbackdetail', [Apiv8Controller::class, 'feedbackDetail']);
        Route::any('defectdetail', [Apiv8Controller::class, 'defectDetail']);
        Route::any('announcementdetail', [Apiv8Controller::class, 'announcementDetail']);

        Route::any('announcementstatusupdated', [Apiv8Controller::class, 'announcementStatusUpdate']);
        Route::any('takeoverapptdetails', [Apiv8Controller::class, 'takeoverapptdetails']);
        Route::any('inspectionapptdetails', [Apiv8Controller::class, 'inspectionapptdetails']);
        Route::any('inboxMessage', [Apiv8Controller::class, 'inboxMessage']);
        Route::any('update_inbox', [Apiv8Controller::class, 'update_inboxmessage']);
        Route::any('upcomingEvents', [Apiv8Controller::class, 'upcomingEvents']);
        Route::any('enquiry', [Apiv8Controller::class, 'enquiry']);
        Route::any('getdocumentcategories', [Apiv8Controller::class, 'documentCategories']);
        Route::any('getcategoryfiles', [Apiv8Controller::class, 'categoryFiles']);
        Route::any('faciltybookingvalidation', [Apiv8Controller::class, 'validateFacilityBoooking']);

        Route::any('getDocumentType', [Apiv8Controller::class, 'getDocumentType']);
        Route::any('residentFileUpload', [Apiv8Controller::class, 'residentFileUpload']);
        Route::any('residentFileUploadDetail', [Apiv8Controller::class, 'residentFileUploadDetail']);
        Route::any('getUploadedlist', [Apiv8Controller::class, 'uploadedlist']);

        Route::any('password_reset_mannual', [Apiv8Controller::class, 'password_reset_mannual']);
        Route::any('loginHistoryLogs', [Apiv8Controller::class, 'loginHistoryLogs']);
        Route::any('logoutHistoryLogs', [Apiv8Controller::class, 'logoutHistoryLogs']);

        Route::any('visitingPurpose', [Apiv8Controller::class, 'visitingPurpose']);
        Route::any('visitorRegisitration', [Apiv8Controller::class, 'visitorRegisitration']);
        Route::any('visitorRegSummary', [Apiv8Controller::class, 'visitorRegSummary']);
        Route::any('visitorBookingInfo', [Apiv8Controller::class, 'visitorBookingInfo']);
        Route::any('visitorBookingCancel', [Apiv8Controller::class, 'visitorBookingCancel']);
        Route::any('visitorSendInvite', [Apiv8Controller::class, 'visitorSendInvite']);
        Route::any('submenulists', [Apiv8Controller::class, 'submenulists']);
        Route::any('eformslists', [Apiv8Controller::class, 'eformslists']);

        Route::any('eformsettingdetail', [Apiv8Controller::class, 'eformsettingdetail']);
        Route::any('eformMovingIO', [Apiv8Controller::class, 'eform_movinginout']);
        Route::any('eformMovingIOInfo', [Apiv8Controller::class, 'eform_movinginout_info']);
        Route::any('eformRenovation', [Apiv8Controller::class, 'eform_renovation']);
        Route::any('eformRenovationInfo', [Apiv8Controller::class, 'eform_renovation_info']);
        Route::any('eformDooraccess', [Apiv8Controller::class, 'eform_dooraccess']);
        Route::any('eformDooraccessInfo', [Apiv8Controller::class, 'eform_dooraccess_info']);
        Route::any('eformRegVehicleIU', [Apiv8Controller::class, 'eform_reg_vehicle']);
        Route::any('eformRegVehicleIUInfo', [Apiv8Controller::class, 'eform_reg_vehicle_info']);
        Route::any('eformRegVehicleFileCat', [Apiv8Controller::class, 'eform_reg_vehicle_file_category']);
        Route::any('eformChangeAddress', [Apiv8Controller::class, 'eform_change_address']);
        Route::any('eformAddressInfo', [Apiv8Controller::class, 'eform_address_info']);
        Route::any('eformUpdateParticulars', [Apiv8Controller::class, 'eform_update_particulars']);
        Route::any('eformParticularsInfo', [Apiv8Controller::class, 'eform_reg_particulars_info']);
        Route::any('eformsubmittedlists', [Apiv8Controller::class, 'eformsubmittedlists']);
        Route::any('eformsubmittedsearchlists', [Apiv8Controller::class, 'eformsubmittedsearchlists']);

        Route::any('MpAdsConditions', [Apiv8Controller::class, 'MpAdsConditions']);
        Route::any('MpAdsTypes', [Apiv8Controller::class, 'MpAdsTypes']);
        Route::any('MpAdsSubmit', [Apiv8Controller::class, 'MpAdsSubmit']);
        Route::any('MpAdsUpdate', [Apiv8Controller::class, 'MpAdsUpdate']);
        Route::any('MpAdsAddImage', [Apiv8Controller::class, 'MpAdsAddImage']);
        Route::any('MpAdsUpdateImage', [Apiv8Controller::class, 'MpAdsUpdateImage']);
        Route::any('MpAdsDeleteImage', [Apiv8Controller::class, 'MpAdsDeleteImage']);
        Route::any('MpAdsMyList', [Apiv8Controller::class, 'MpAdsMyList']);
        Route::any('MpAdsList', [Apiv8Controller::class, 'MpAdsList']);
        Route::any('MpGroupList', [Apiv8Controller::class, 'MpGroupList']);
        Route::any('MpAdsLikeList', [Apiv8Controller::class, 'MpAdsLikeList']);
        Route::any('MpAdsItemDetail', [Apiv8Controller::class, 'MpAdsItemDetail']);
        Route::any('MpAdsReportRevert', [Apiv8Controller::class, 'MpAdsReportRevert']);
        Route::any('MpAdsReport', [Apiv8Controller::class, 'MpAdsReport']);
        Route::any('MpAdsMarkAsSold', [Apiv8Controller::class, 'MpAdsMarkAsSold']);
        Route::any('MpAdsMarkAsUnsold', [Apiv8Controller::class, 'MpAdsMarkAsUnsold']);
        Route::any('MpAdsDelete', [Apiv8Controller::class, 'MpAdsDelete']);
        Route::any('MpAdsBlockedUserLists', [Apiv8Controller::class, 'MpAdsBlockedUserLists']);
        Route::any('MpAdsBlockUser', [Apiv8Controller::class, 'MpAdsBlockUser']);
        Route::any('MpAdsUnblockUser', [Apiv8Controller::class, 'MpAdsUnblockUser']);
        Route::any('MpAdsLike', [Apiv8Controller::class, 'MpAdsLike']);
        Route::any('MpAdsUnLike', [Apiv8Controller::class, 'MpAdsUnLike']);
        Route::any('MpGroupRegister', [Apiv8Controller::class, 'MpGroupRegister']);
        Route::any('MpGroupUnregister', [Apiv8Controller::class, 'MpGroupUnregister']);

        Route::any('MagazineLists', [Apiv8Controller::class, 'MagazineLists']);
        Route::any('UserGuideLists', [Apiv8Controller::class, 'UserGuideLists']);

        Route::any('opn_retrive_customer_info', [OpnController::class, 'opn_retrive_customer_info']);
        Route::any('opn_account_creation', [OpnController::class, 'opn_account_creation']);
        Route::any('opn_create_token', [OpnController::class, 'opn_create_token']);
        Route::any('opn_attach_card_customer', [OpnController::class, 'opn_attach_card_customer']);
        Route::any('opn_update_default_card', [OpnController::class, 'opn_update_default_card']);
        Route::any('opn_destroy_card', [OpnController::class, 'opn_destroy_card']);
        Route::any('opn_create_charge', [OpnController::class, 'opn_create_charge']);
        Route::any('non_3ds_create_charge', [OpnController::class, 'non_3ds_create_charge']);
    });

    Route::any('opn_payment_webhook', [OpnController::class, 'opn_payment_webhook']); // OPN Webhook
    Route::any('opn_facility_deposit_capture', [OpnController::class, 'opn_facility_deposit_capture']);
    Route::any('visitorRegValidation', [Apiv8Controller::class, 'visitorRegValidation']);
    Route::any('charges', [PaymentController::class, 'charges']);
});

Route::prefix('/ops/v4')->group(function () {

    Route::any('getiosversion', [OpsApiv4Controller::class, 'getiosversion']);
    Route::any('getandroidversion', [OpsApiv4Controller::class, 'getandroidversion']);

    //Auth
    Route::any('login', [OpsApiv4Controller::class, 'login']);
    Route::any('verifyotp', [OpsApiv4Controller::class, 'verifyotp']);
    Route::any('resendotp', [OpsApiv4Controller::class, 'resendotp']);

    Route::any('verifyOtpApi', [OpsApiv4Controller::class, 'verifyOtpApi']);
    Route::any('forgotpassword', [OpsApiv4Controller::class, 'forgotPassword']);
    Route::any('forgotPassword', [OpsApiv4Controller::class, 'forgotPassword']);
    Route::any('setPasswordApi', [OpsApiv4Controller::class, 'setPasswordApi']);
    Route::any('release_notification', [OpsApiv4Controller::class, 'release_notification']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('account-delete')->group(function () {
            Route::post('request-lists', [OpsApiv4Controller::class, 'accountDeleteRequestLists']);
            Route::post('request-view/{requestId}', [OpsApiv4Controller::class, 'accountDeleteRequestView']);
            Route::post('request-approve', [OpsApiv4Controller::class, 'accountDeleteRequestApprove']);
        });

        Route::prefix('charts')->group(function () {
            Route::post('users', [OpsApiv4Controller::class, 'chartUsers']);
            Route::post('key-collections', [OpsApiv4Controller::class, 'chartKeyCollection']);
        });

        Route::any('logoutHistoryLogs', [OpsApiv4Controller::class, 'logoutHistoryLogs']);
        Route::any('loginHistoryLogs', [OpsApiv4Controller::class, 'loginHistoryLogs']);
        Route::any('decryptstring', [OpsApiv4Controller::class, 'decryptstring']);
        Route::any('encryptstring', [OpsApiv4Controller::class, 'encryptstring']);
        Route::any('dashboardmenu', [OpsApiv4Controller::class, 'dashboardmenu']);
        Route::any('adminmenu', [OpsApiv4Controller::class, 'adminmenu']);
        Route::any('logininfo', [OpsApiv4Controller::class, 'logininfo']);
        Route::any('getpropertylist', [OpsApiv4Controller::class, 'getpropertylist']);
        Route::any('switchproperty', [OpsApiv4Controller::class, 'switchproperty']);
        Route::any('userroles', [OpsApiv4Controller::class, 'userroles']);
        Route::any('countries', [OpsApiv4Controller::class, 'countries']);
        Route::any('userRolesArray', [OpsApiv4Controller::class, 'userrolesarray']);
        Route::any('manager_notifications', [OpsApiv4Controller::class, 'manager_notifications']);
        Route::any('update_notification', [OpsApiv4Controller::class, 'update_notification']);
        Route::any('change_password', [OpsApiv4Controller::class, 'change_password']);

        Route::any('announcements', [OpsApiv4Controller::class, 'announcements']);
        Route::any('createannouncement', [OpsApiv4Controller::class, 'createannouncement']);
        Route::any('deleteannouncement', [OpsApiv4Controller::class, 'deleteannouncement']);
        Route::any('searchannouncement', [OpsApiv4Controller::class, 'searchannouncement']);

        Route::any('usersummarylist', [OpsApiv4Controller::class, 'usersummarylist']);
        Route::any('userinfo', [OpsApiv4Controller::class, 'userinfo']);
        Route::any('createuser', [OpsApiv4Controller::class, 'createuser']);
        Route::any('edituser', [OpsApiv4Controller::class, 'edituser']);
        Route::any('updateuser', [OpsApiv4Controller::class, 'updateuser']);
        Route::any('deleteuser', [OpsApiv4Controller::class, 'deleteuser']);
        Route::any('searchuser', [OpsApiv4Controller::class, 'searchuser']);
        Route::any('activateuser', [OpsApiv4Controller::class, 'activateuser']);
        Route::any('deactivateuser', [OpsApiv4Controller::class, 'deactivateuser']);
        Route::any('unitsummarytypes', [OpsApiv4Controller::class, 'unitsummarytypes']);
        Route::any('unitsummary', [OpsApiv4Controller::class, 'unitsummary']);
        Route::any('unitsummarysearch', [OpsApiv4Controller::class, 'unitsummarysearch']);
        Route::any('useraccess', [OpsApiv4Controller::class, 'useraccess']);
        Route::any('useraccesssearch', [OpsApiv4Controller::class, 'useraccesssearch']);
        Route::any('useraccessupdate', [OpsApiv4Controller::class, 'useraccessupdate']);
        Route::any('bulkuseraccess', [OpsApiv4Controller::class, 'bulkuseraccess']);
        Route::any('bulkuseraccesssearch', [OpsApiv4Controller::class, 'bulkuseraccesssearch']);
        Route::any('bulkuseraccessupdate', [OpsApiv4Controller::class, 'bulkuseraccessupdate']);
        Route::any('userunits', [OpsApiv4Controller::class, 'userunits']);
        Route::any('assignunit', [OpsApiv4Controller::class, 'assignunit']);
        Route::any('deleteunit', [OpsApiv4Controller::class, 'deleteunit']);
        Route::any('unitcards', [OpsApiv4Controller::class, 'unitcards']);
        Route::any('usercards', [OpsApiv4Controller::class, 'usercards']);
        Route::any('assigncard', [OpsApiv4Controller::class, 'assigncard']);
        Route::any('deleteusercard', [OpsApiv4Controller::class, 'deleteusercard']);
        Route::any('userdevicelists', [OpsApiv4Controller::class, 'userdevicelists']);
        Route::any('userdeviceupdate', [OpsApiv4Controller::class, 'userdeviceupdate']);
        Route::any('userunitdelete', [OpsApiv4Controller::class, 'userunitDelete']);
        Route::any('userunitactivate', [OpsApiv4Controller::class, 'userunitactivate']);
        Route::any('userunitdeactivate', [OpsApiv4Controller::class, 'userunitdeactivate']);
        Route::any('RemoteDoorOpen', [OpsApiv4Controller::class, 'remote_door_open']);
        Route::any('userlicenseplates', [OpsApiv4Controller::class, 'userlicenseplates']);
        Route::any('licenseplateinfo', [OpsApiv4Controller::class, 'licenseplateinfo']);
        Route::any('editlicenseplate', [OpsApiv4Controller::class, 'editlicenseplate']);
        Route::any('assignlicenseplate', [OpsApiv4Controller::class, 'assignlicenseplates']);
        Route::any('deletelicenseplate', [OpsApiv4Controller::class, 'deletlicenseplate']);

        Route::any('regSummary', [OpsApiv4Controller::class, 'regsummary']);
        Route::any('regDetails', [OpsApiv4Controller::class, 'regdetails']);
        Route::any('regApprove', [OpsApiv4Controller::class, 'regapprove']);
        Route::any('regReject', [OpsApiv4Controller::class, 'regreject']);
        Route::any('regDelete', [OpsApiv4Controller::class, 'regdelete']);
        Route::any('regSearch', [OpsApiv4Controller::class, 'regsearch']);

        Route::any('faceidsummary', [OpsApiv4Controller::class, 'faceidsummary']);
        Route::any('faceidnewsummary', [OpsApiv4Controller::class, 'faceidnewsummary']);
        Route::any('stafffaceids', [OpsApiv4Controller::class, 'stafffaceids']);
        Route::any('searchfaceid', [OpsApiv4Controller::class, 'searchfaceid']);
        Route::any('searchnewfaceid', [OpsApiv4Controller::class, 'searchnewfaceid']);
        Route::any('uploadstafffaceid', [OpsApiv4Controller::class, 'uploadstafffaceid']);
        Route::any('getroleslist', [OpsApiv4Controller::class, 'getroleslist']);
        Route::any('getstafflist', [OpsApiv4Controller::class, 'getstafflist']);
        Route::any('getuserlist', [OpsApiv4Controller::class, 'getuserlist']);
        Route::any('uploadoptions', [OpsApiv4Controller::class, 'uploadoptions']);
        Route::any('staffnewuploadlists', [OpsApiv4Controller::class, 'staffnewuploadlists']);
        Route::any('faceuploadapproval', [OpsApiv4Controller::class, 'faceuploadapproval']);
        Route::any('faceidupload', [OpsApiv4Controller::class, 'faceidupload']);
        Route::any('faceiddetail', [OpsApiv4Controller::class, 'faceiddetail']);
        Route::any('faceidedit', [OpsApiv4Controller::class, 'faceidedit']);
        Route::any('faceuploadcancel', [OpsApiv4Controller::class, 'faceuploadcancel']);
        Route::any('faceiddelete', [OpsApiv4Controller::class, 'faceiddelete']);
        Route::any('faceidAccess', [OpsApiv4Controller::class, 'faceidAccess']);
        Route::any('staffbluetoothdevices', [OpsApiv4Controller::class, 'staffbluetoothdevices']);
        Route::any('staffremotedevices', [OpsApiv4Controller::class, 'staffremotedevices']);
        Route::any('staffalldevices', [OpsApiv4Controller::class, 'staffalldevices']);
        Route::any('getaccesstoken', [OpsApiv4Controller::class, 'getaccesstoken']);
        Route::any('InsertBluetoothOpenRecord', [OpsApiv4Controller::class, 'StoreStaffOpenRecord']);
        Route::any('InsertCallUnitRecord', [OpsApiv4Controller::class, 'StoreCallUnitRecord']);


        Route::any('cardsummarylist', [OpsApiv4Controller::class, 'cardsummarylist']);
        Route::any('createcard', [OpsApiv4Controller::class, 'createcard']);
        Route::any('editcard', [OpsApiv4Controller::class, 'editcard']);
        Route::any('deletecard', [OpsApiv4Controller::class, 'deletecard']);
        Route::any('searchcard', [OpsApiv4Controller::class, 'searchcard']);

        Route::any('devicesummarylist', [OpsApiv4Controller::class, 'devicesummarylist']);
        Route::any('deviceinfo', [OpsApiv4Controller::class, 'deviceinfo']);
        Route::any('createdevice', [OpsApiv4Controller::class, 'createdevice']);
        Route::any('editdevice', [OpsApiv4Controller::class, 'editdevice']);
        Route::any('deletedevice', [OpsApiv4Controller::class, 'deletedevice']);
        Route::any('searchdevice', [OpsApiv4Controller::class, 'searchdevice']);
        Route::any('restartdevice', [OpsApiv4Controller::class, 'restartdevice']);
        Route::any('getlocation', [OpsApiv4Controller::class, 'getlocation']);
        Route::any('devicestatus', [OpsApiv4Controller::class, 'devicestatus']);

        Route::any('keycollectionlist', [OpsApiv4Controller::class, 'keycollectionlist']);
        Route::any('keycollectionnewlist', [OpsApiv4Controller::class, 'keycollectionnewlist']);
        Route::any('keycollectiontimeslot', [OpsApiv4Controller::class, 'keycollectiontimeslot']);
        Route::any('keycollectioninfo', [OpsApiv4Controller::class, 'keycollectioninfo']);
        Route::any('editkeycollection', [OpsApiv4Controller::class, 'editkeycollection']);
        Route::any('deletekeycollection', [OpsApiv4Controller::class, 'deletekeycollection']);
        Route::any('searchkeycollection', [OpsApiv4Controller::class, 'searchkeycollection']);
        Route::any('cancelkeycollection', [OpsApiv4Controller::class, 'cancelkeycollection']);
        Route::any('confirmkeycollection', [OpsApiv4Controller::class, 'confirmkeycollection']);

        Route::any('defectslist', [OpsApiv4Controller::class, 'defectslist']);
        Route::any('defectsnewlist', [OpsApiv4Controller::class, 'defectsnewlist']);
        Route::any('defectsinfo', [OpsApiv4Controller::class, 'defectsinfo']);
        Route::any('defectsupdate', [OpsApiv4Controller::class, 'defectsupdate']);
        Route::any('defectsinspectionupdate', [OpsApiv4Controller::class, 'defectsinspectionupdate']);
        Route::any('defectscancelinspection', [OpsApiv4Controller::class, 'defectscancelinspection']);

        //Final
        Route::any('defectfinalinspectionupdate', [OpsApiv4Controller::class, 'defectFinalInspectionUpdate']);
        Route::any('defectfinalcancelinspection', [OpsApiv4Controller::class, 'defectFinalInspectionCancel']);

        Route::any('deleteRectifiedImg', [OpsApiv4Controller::class, 'deleteRectifiedImg']);
        Route::any('defectssearch', [OpsApiv4Controller::class, 'defectssearch']);
        Route::any('defectshandoverupdate', [OpsApiv4Controller::class, 'defectshandoverupdate']);
        Route::any('deletedefect', [OpsApiv4Controller::class, 'deletedefect']);
        Route::any('defecttimeslot', [OpsApiv4Controller::class, 'defecttimeslot']);
        Route::any('defecttypes', [OpsApiv4Controller::class, 'defecttypes']);
        Route::post('defect-dashboard', [OpsApiv4Controller::class, 'defectDashboard']);

        Route::post('feedback/dashboard', [OpsApiv4Controller::class, 'feedbackDashboard']);
        Route::any('feedbacklist', [OpsApiv4Controller::class, 'feedbacklist']);
        Route::any('feedbacknewlist', [OpsApiv4Controller::class, 'feedbacknewlist']);
        Route::any('feedbackoptions', [OpsApiv4Controller::class, 'feedbackoptions']);
        Route::any('feedbackinfo', [OpsApiv4Controller::class, 'feedbackinfo']);
        Route::any('editfeedback', [OpsApiv4Controller::class, 'editfeedback']);
        Route::any('deletefeedback', [OpsApiv4Controller::class, 'deletefeedback']);
        Route::any('searchfeedback', [OpsApiv4Controller::class, 'searchfeedback']);
        Route::any('cancelfeedback', [OpsApiv4Controller::class, 'cancelfeedback']);
        Route::any('confirmfeedback', [OpsApiv4Controller::class, 'confirmfeedback']);

        Route::post('facility/dashboard', [OpsApiv4Controller::class, 'facilityDashboard']);
        Route::any('facilitylist', [OpsApiv4Controller::class, 'facilitylist']);
        Route::any('facilitynewlist', [OpsApiv4Controller::class, 'facilitynewlist']);
        Route::any('facilityoptions', [OpsApiv4Controller::class, 'facilityoptions']);
        Route::any('facilityinfo', [OpsApiv4Controller::class, 'facilityinfo']);
        Route::any('editfacility', [OpsApiv4Controller::class, 'editfacility']);
        Route::any('deletefacility', [OpsApiv4Controller::class, 'deletefacility']);
        Route::any('searchfacility', [OpsApiv4Controller::class, 'searchfacility']);
        Route::any('cancelfacility', [OpsApiv4Controller::class, 'cancelfacility']);
        Route::any('confirmfacility', [OpsApiv4Controller::class, 'confirmfacility']);
        Route::any('refundfacility', [OpsApiv4Controller::class, 'refundfacility']);
        Route::any('facilitytimeslot', [OpsApiv4Controller::class, 'facilitytimeslot']);
        Route::any('opn_facility_capture_amount', [OpsApiv4Controller::class, 'opn_facility_capture_amount']); //refund api
        Route::any('unitlist', [OpsApiv4Controller::class, 'unitlist']);
        Route::any('roleslist', [OpsApiv4Controller::class, 'roleslist']);

        Route::any('propertyinfo', [OpsApiv4Controller::class, 'propertyinfo']);
        Route::any('propertyedit', [OpsApiv4Controller::class, 'propertyedit']);
        Route::any('keycollectionsetting', [OpsApiv4Controller::class, 'keycollectionsetting']);
        Route::any('inspectionsetting', [OpsApiv4Controller::class, 'inspectionsetting']);

        Route::any('sharesettingslist', [OpsApiv4Controller::class, 'sharesettingslist']);
        Route::any('createsharesettings', [OpsApiv4Controller::class, 'createsharesettings']);

        Route::any('buildingsummarylist', [OpsApiv4Controller::class, 'buildingsummarylist']);
        Route::any('buildinginfo', [OpsApiv4Controller::class, 'buildinginfo']);
        Route::any('buildingcreate', [OpsApiv4Controller::class, 'buildingcreate']);
        Route::any('buildingdelete', [OpsApiv4Controller::class, 'buildingdelete']);
        Route::any('buildingedit', [OpsApiv4Controller::class, 'buildingedit']);

        Route::any('unitsummarylist', [OpsApiv4Controller::class, 'unitsummarylist']);
        Route::any('unitinfo', [OpsApiv4Controller::class, 'unitinfo']);
        Route::any('unitcreate', [OpsApiv4Controller::class, 'unitcreate']);
        Route::any('unitdelete', [OpsApiv4Controller::class, 'unitdelete']);
        Route::any('unitedit', [OpsApiv4Controller::class, 'unitedit']);
        Route::any('unitsearch', [OpsApiv4Controller::class, 'unitsearch']);

        Route::any('fboptionsummarylist', [OpsApiv4Controller::class, 'fboptionsummarylist']);
        Route::any('fboptioninfo', [OpsApiv4Controller::class, 'fboptioninfo']);
        Route::any('fboptioncreate', [OpsApiv4Controller::class, 'fboptioncreate']);
        Route::any('fboptiondelete', [OpsApiv4Controller::class, 'fboptiondelete']);
        Route::any('fboptionedit', [OpsApiv4Controller::class, 'fboptionedit']);
        Route::any('facilitytypelist', [OpsApiv4Controller::class, 'facilitytypelist']);
        Route::any('facilitytypeinfo', [OpsApiv4Controller::class, 'facilitytypeinfo']);
        Route::any('facilitytypecreate', [OpsApiv4Controller::class, 'facilitytypecreate']);
        Route::any('facilitytypedelete', [OpsApiv4Controller::class, 'facilitytypedelete']);
        Route::any('facilitytypeedit', [OpsApiv4Controller::class, 'facilitytypeedit']);

        Route::any('locationlist', [OpsApiv4Controller::class, 'locationlist']);
        Route::any('locationinfo', [OpsApiv4Controller::class, 'locationinfo']);
        Route::any('locationcreate', [OpsApiv4Controller::class, 'locationcreate']);
        Route::any('locationdelete', [OpsApiv4Controller::class, 'locationdelete']);
        Route::any('locationedit', [OpsApiv4Controller::class, 'locationedit']);

        Route::any('visipurposelist', [OpsApiv4Controller::class, 'visipurposelist']);
        Route::any('visipurposeinfo', [OpsApiv4Controller::class, 'visipurposeinfo']);
        Route::any('visipurposecreate', [OpsApiv4Controller::class, 'visipurposecreate']);
        Route::any('visipurposedelete', [OpsApiv4Controller::class, 'visipurposedelete']);
        Route::any('visipurposeedit', [OpsApiv4Controller::class, 'visipurposeedit']);

        Route::any('eformsetting_list', [OpsApiv4Controller::class, 'eformsetting_list']);
        Route::any('eformsetting_info', [OpsApiv4Controller::class, 'eformsetting_info']);
        Route::any('eformsetting_types', [OpsApiv4Controller::class, 'eformsetting_types']);
        Route::any('eformsetting_create', [OpsApiv4Controller::class, 'eformsetting_create']);
        Route::any('eformsetting_edit', [OpsApiv4Controller::class, 'eformsetting_edit']);
        Route::any('eformsetting_delete', [OpsApiv4Controller::class, 'eformsetting_delete']);
        Route::any('rolessummarylist', [OpsApiv4Controller::class, 'rolessummarylist']);
        Route::any('roleinfo', [OpsApiv4Controller::class, 'roleinfo']);
        Route::any('rolecreate', [OpsApiv4Controller::class, 'rolecreate']);
        Route::any('roleproperty', [OpsApiv4Controller::class, 'roleproperty']);
        Route::any('roledelete', [OpsApiv4Controller::class, 'roledelete']);
        Route::any('roleedit', [OpsApiv4Controller::class, 'roleedit']);

        Route::any('docsCatSummary', [OpsApiv4Controller::class, 'docsCatSummary']);
        Route::any('docsCatDelete', [OpsApiv4Controller::class, 'docsCatDelete']);
        Route::any('docsDeleteFile', [OpsApiv4Controller::class, 'docsDeleteFile']);
        Route::any('docsCatInfo', [OpsApiv4Controller::class, 'docsCatInfo']);
        Route::any('docsCatCreate', [OpsApiv4Controller::class, 'docsCatCreate']);
        Route::any('docsCatEdit', [OpsApiv4Controller::class, 'docsCatEdit']);

        Route::any('resFileSummary', [OpsApiv4Controller::class, 'resFileSummary']);
        Route::any('resFileSummaryNew', [OpsApiv4Controller::class, 'resFileSummaryNew']);
        Route::any('resFileDelete', [OpsApiv4Controller::class, 'resFileDelete']);
        Route::any('resFileInfo', [OpsApiv4Controller::class, 'resFileInfo']);
        Route::any('resFileCreate', [OpsApiv4Controller::class, 'resFileCreate']);
        Route::any('resFileEdit', [OpsApiv4Controller::class, 'resFileEdit']);
        Route::any('resFileSearch', [OpsApiv4Controller::class, 'resFileSearch']);

        Route::any('eformsettingsinfo', [OpsApiv4Controller::class, 'eformsettingsinfo']);

        Route::any('moveinoutsummary', [OpsApiv4Controller::class, 'moveinoutsummary']);
        Route::any('moveinoutdelete', [OpsApiv4Controller::class, 'moveinoutdelete']);
        Route::any('moveinoutinfo', [OpsApiv4Controller::class, 'moveinoutinfo']);
        Route::any('moveinoutedit', [OpsApiv4Controller::class, 'moveinoutedit']);
        Route::any('moveinoutpaymentsave', [OpsApiv4Controller::class, 'moveinoutpaymentsave']);
        Route::any('moveinoutinspectionsave', [OpsApiv4Controller::class, 'moveinoutinspectionsave']);
        Route::any('moveinoutsearch', [OpsApiv4Controller::class, 'moveinoutsearch']);

        Route::any('renovationsummary', [OpsApiv4Controller::class, 'renovationsummary']);
        Route::any('renovationdelete', [OpsApiv4Controller::class, 'renovationdelete']);
        Route::any('renovationinfo', [OpsApiv4Controller::class, 'renovationinfo']);
        Route::any('renovationedit', [OpsApiv4Controller::class, 'renovationedit']);
        Route::any('renovationsearch', [OpsApiv4Controller::class, 'renovationsearch']);
        Route::any('renovationpaymentsave', [OpsApiv4Controller::class, 'renovationpaymentsave']);
        Route::any('renovationinspectionsave', [OpsApiv4Controller::class, 'renovationinspectionsave']);

        Route::any('dooraccesssummary', [OpsApiv4Controller::class, 'dooraccesssummary']);
        Route::any('dooraccessdelete', [OpsApiv4Controller::class, 'dooraccessdelete']);
        Route::any('dooraccessinfo', [OpsApiv4Controller::class, 'dooraccessinfo']);
        Route::any('dooraccessedit', [OpsApiv4Controller::class, 'dooraccessedit']);
        Route::any('dooraccesssearch', [OpsApiv4Controller::class, 'dooraccesssearch']);
        Route::any('dooraccesspaymentsave', [OpsApiv4Controller::class, 'dooraccesspaymentsave']);
        Route::any('dooracknowledgementsave', [OpsApiv4Controller::class, 'dooracknowledgementsave']);

        Route::any('regvehiclesummary', [OpsApiv4Controller::class, 'regvehiclesummary']);
        Route::any('regvehicledelete', [OpsApiv4Controller::class, 'regvehicledelete']);
        Route::any('regvehicleinfo', [OpsApiv4Controller::class, 'regvehicleinfo']);
        Route::any('regvehicleedit', [OpsApiv4Controller::class, 'regvehicleedit']);
        Route::any('regvehiclesearch', [OpsApiv4Controller::class, 'regvehiclesearch']);
        Route::any('changeaddresssummary', [OpsApiv4Controller::class, 'changeaddresssummary']);
        Route::any('changeaddressdelete', [OpsApiv4Controller::class, 'changeaddressdelete']);
        Route::any('changeaddressinfo', [OpsApiv4Controller::class, 'changeaddressinfo']);
        Route::any('changeaddressedit', [OpsApiv4Controller::class, 'changeaddressedit']);
        Route::any('changeaddresssearch', [OpsApiv4Controller::class, 'changeaddresssearch']);

        Route::any('updateparticularsummary', [OpsApiv4Controller::class, 'updateparticularsummary']);
        Route::any('updateparticulardelete', [OpsApiv4Controller::class, 'updateparticulardelete']);
        Route::any('updateparticularinfo', [OpsApiv4Controller::class, 'updateparticularinfo']);
        Route::any('updateparticularedit', [OpsApiv4Controller::class, 'updateparticularedit']);
        Route::any('updateparticularsearch', [OpsApiv4Controller::class, 'updateparticularsearch']);

        Route::post('visitor/dashboard', [OpsApiv4Controller::class, 'visitorDashboard']);
        Route::any('visitorsummary', [OpsApiv4Controller::class, 'visitorsummary']);
        Route::any('visitornew', [OpsApiv4Controller::class, 'visitornew']);
        Route::any('visitor_types', [OpsApiv4Controller::class, 'visitor_types']);
        Route::any('availability_check', [OpsApiv4Controller::class, 'availability_check']);
        Route::any('visitorlimit_info', [OpsApiv4Controller::class, 'visitorlimit_info']);
        Route::any('visitorlimit_update', [OpsApiv4Controller::class, 'visitorlimit_update']);
        Route::any('visitorwalkin', [OpsApiv4Controller::class, 'visitorwalkin']);
        Route::any('visitordelete', [OpsApiv4Controller::class, 'visitordelete']);
        Route::any('visitorinfo', [OpsApiv4Controller::class, 'visitorinfo']);
        Route::any('visitoredit', [OpsApiv4Controller::class, 'visitoredit']);
        Route::any('visitorsearch', [OpsApiv4Controller::class, 'visitorsearch']);
        Route::any('visitorqrvalidation', [OpsApiv4Controller::class, 'visitorqrvalidation']);

        Route::any('dooropen', [OpsApiv4Controller::class, 'dooropen']);
        Route::any('searchdooropen', [OpsApiv4Controller::class, 'searchdooropen']);
        Route::any('bluetoothdooropen', [OpsApiv4Controller::class, 'bluetoothdooropen']);
        Route::any('searchbluetoothdooropen', [OpsApiv4Controller::class, 'searchbluetoothdooropen']);
        Route::any('dooropenfailed', [OpsApiv4Controller::class, 'dooropenfailed']);
        Route::any('searchdooropenfailed', [OpsApiv4Controller::class, 'searchdooropenfailed']);
        Route::any('callunit', [OpsApiv4Controller::class, 'callunit']);
        Route::any('searchcallunit', [OpsApiv4Controller::class, 'searchcallunit']);
        Route::any('qropenrecords', [OpsApiv4Controller::class, 'qropenrecords']);
        Route::any('searchqropenrecords', [OpsApiv4Controller::class, 'searchqropenrecords']);

        Route::any('availability_check', [OpsApiv4Controller::class, 'availability_check']);
        Route::any('visitorwalkin', [OpsApiv4Controller::class, 'visitorwalkin']);
        Route::any('visitordelete', [OpsApiv4Controller::class, 'visitordelete']);

        Route::any('invoicepayment/delete/{id}', [OpsApiv4Controller::class, 'paymentdelete']);
        Route::get('invoice/payment/{id}', [OpsApiv4Controller::class, 'payment']);
        Route::any('invoice/paymentsave/{id}', [OpsApiv4Controller::class, 'paymentsave']);
        Route::any('batches', [OpsApiv4Controller::class, 'batches']);
        Route::any('batchesearch', [OpsApiv4Controller::class, 'batchesearch']);
        Route::any('batch_invoices', [OpsApiv4Controller::class, 'batch_invoices']);
        Route::any('batchdelete', [OpsApiv4Controller::class, 'batchdelete']);
        Route::any('invoicedelete', [OpsApiv4Controller::class, 'invoicedelete']);
        Route::any('invoiceview', [OpsApiv4Controller::class, 'viewinvoice']);
        Route::any('invoiceupdate', [OpsApiv4Controller::class, 'invoiceupdate']);
        Route::any('paymentsave', [OpsApiv4Controller::class, 'paymentsave']);
        Route::any('paymentdelete', [OpsApiv4Controller::class, 'paymentdelete']);
        Route::any('paymentoverview', [OpsApiv4Controller::class, 'paymentoverview']);
        Route::any('invoice_report', [OpsApiv4Controller::class, 'invoice_report']);
        Route::any('report_search', [OpsApiv4Controller::class, 'report_search']);
        Route::any('send_notification', [OpsApiv4Controller::class, 'send_notification']);

        Route::any('paymentinfo', [OpsApiv4Controller::class, 'paymentinfo']);
        Route::any('paymentedit', [OpsApiv4Controller::class, 'paymentedit']);
        Route::any('holidayinfo', [OpsApiv4Controller::class, 'holidayinfo']);
        Route::any('holidayedit', [OpsApiv4Controller::class, 'holidayedit']);
        Route::any('mpadslist', [OpsApiv4Controller::class, 'mpadslist']);
        Route::any('mpadstypes', [OpsApiv4Controller::class, 'mpadstypes']);
        Route::any('mpadsconditions', [OpsApiv4Controller::class, 'mpadsconditions']);
        Route::any('mpadslikes', [OpsApiv4Controller::class, 'mpadslikes']);
        Route::any('mpadslikedelete', [OpsApiv4Controller::class, 'mpadsdeletelike']);
        Route::any('mpadsreports', [OpsApiv4Controller::class, 'mpadsreports']);
        Route::any('mpadsactivate', [OpsApiv4Controller::class, 'mpadsactivate']);
        Route::any('mpadsdeactivate', [OpsApiv4Controller::class, 'mpadsdeactivate']);
        Route::any('mpadssearch', [OpsApiv4Controller::class, 'mpadssearch']);
        Route::any('mpadswarninguser', [OpsApiv4Controller::class, 'mpadswarninguser']);
        Route::any('mpadsblockuser', [OpsApiv4Controller::class, 'mpadsblockuser']);
        Route::any('mpadshidereport', [OpsApiv4Controller::class, 'mpadshidereport']);
        Route::any('mpadsshowreport', [OpsApiv4Controller::class, 'mpadsshowreport']);
        Route::any('mpadsallreports', [OpsApiv4Controller::class, 'mpadsallreports']);
        Route::any('mpadsnewreport', [OpsApiv4Controller::class, 'mpadsnewreport']);
        Route::any('mpadsblocklistedusers', [OpsApiv4Controller::class, 'mpadsblocklistedusers']);
        Route::any('mpadsremovefromblocklist', [OpsApiv4Controller::class, 'mpadsremovefromblocklist']);
        Route::any('mpadsaddtoblocklist', [OpsApiv4Controller::class, 'mpadsaddtoblocklist']);

        Route::any('resichatlist', [OpsApiv4Controller::class, 'resichatlist']);
        Route::any('resichatallreports', [OpsApiv4Controller::class, 'resichatallreports']);
        Route::any('resichatnewreport', [OpsApiv4Controller::class, 'resichatnewreport']);
        Route::any('resichatreports', [OpsApiv4Controller::class, 'resichatreports']);
        Route::any('resichatactivate', [OpsApiv4Controller::class, 'resichatactivate']);
        Route::any('resichatdeactivate', [OpsApiv4Controller::class, 'resichatdeactivate']);
        Route::any('resichatsearch', [OpsApiv4Controller::class, 'resichatsearch']);
        Route::any('resichatcomments', [OpsApiv4Controller::class, 'resichatcomments']);
        Route::any('resichathidecomments', [OpsApiv4Controller::class, 'resichathidecomments']);
        Route::any('resichatshowcomments', [OpsApiv4Controller::class, 'resichatshowcomments']);
        Route::any('resichatblockuser', [OpsApiv4Controller::class, 'resichatblockuser']);
        Route::any('resichatwarninguser', [OpsApiv4Controller::class, 'resichatwarninguser']);
        Route::any('resichathidereport', [OpsApiv4Controller::class, 'resichathidereport']);
        Route::any('resichatshowreport', [OpsApiv4Controller::class, 'resichatshowreport']);
        Route::any('resichatblocklistedusers', [OpsApiv4Controller::class, 'resichatblocklistedusers']);
        Route::any('resichatremovefromblocklist', [OpsApiv4Controller::class, 'resichatremovefromblocklist']);
        Route::any('resichataddtoblocklist', [OpsApiv4Controller::class, 'resichataddtoblocklist']);
    });

    Route::resource('invoice', OpsApiv4Controller::class);
    Route::any('mpadslikes', [OpsApiv4Controller::class, 'mpadslikes']);
    Route::any('mpadsreports', [OpsApiv4Controller::class, 'mpadsreports']);
    Route::any('mpadsactivate', [OpsApiv4Controller::class, 'mpadsactivate']);
    Route::any('mpadsadeactivate', [OpsApiv4Controller::class, 'mpadsadeactivate']);
    Route::any('mpadssearch', [OpsApiv4Controller::class, 'mpadssearch']);
});

Route::prefix('/payment')->group(function () {
    Route::any('charges', [PaymentController::class, 'charges']);
    Route::any('testwebhook', [PaymentController::class, 'TestWebhook']);
    Route::any('livewebhook', [PaymentController::class, 'LiveWebhook']);
    Route::any('cronfacilitycharges', [PaymentController::class, 'charges']);
});

Route::prefix('/cron')->group(function () {
    Route::any('facility_pre_auth_charges', [CronController::class, 'facility_pre_auth_charges']);
    Route::any('facility_refund_charges', [CronController::class, 'facility_refund_charges']);
    Route::any('facility_deposit_charges', [CronController::class, 'facility_deposit_charges']);
    Route::any('announcement_send', [CronController::class, 'announcement_send']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
