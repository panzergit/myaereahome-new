<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\StaticController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OpnController;
use App\Http\Controllers\VisitorBookingController;
use App\Http\Controllers\JoininspectionAppointmentController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserMoreInfoController;
use App\Http\Controllers\UserLicensePlateController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChatterBoxController;
use App\Http\Controllers\MpAdsController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FinanceShareSettingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\EformSettingController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\HolidaySettingController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FeedbackOptionController;
use App\Http\Controllers\DefectLocationController;
use App\Http\Controllers\PropertyController;
// use App\Http\Controllers\HelpBannerController;
use App\Http\Controllers\HomeBannerController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\EconciergeController;
use App\Http\Controllers\FacilityTypeController;
use App\Http\Controllers\VisitorTypeController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserFacialIdController;
use App\Http\Controllers\FacilityBookingController;
use App\Http\Controllers\DigitalAccessController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ModuleSettingController;
use App\Http\Controllers\UnittakeoverAppointmentController;
use App\Http\Controllers\CondodocFileController;
use App\Http\Controllers\DocsCategoryController;
// use App\Http\Controllers\CondoDocController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\UserGuideController;
use App\Http\Controllers\ResidentFileSubmissionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\EformMovingInOutController;
use App\Http\Controllers\EformRenovationController;
use App\Http\Controllers\EformDoorAccessController;
use App\Http\Controllers\EformRegVehicleController;
use App\Http\Controllers\EformChangeAddressController;
use App\Http\Controllers\EformParticularController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\PayrollController;

Route::get('/', [StaticController::class, 'landing']);
Route::any('privacypolicy', [StaticController::class, 'privacypolicy']);
Route::any('termsconditions', [StaticController::class, 'termsconditions']);
Route::any('faq/profile', [StaticController::class, 'faqprofile']);
Route::any('faq/unit-takeover', [StaticController::class, 'faqunittakeover']);
Route::any('faq/defects', [StaticController::class, 'faqdefects']);
Route::any('faq/joint-inspection', [StaticController::class, 'faqjointinspection']);
Route::any('faq/feedback', [StaticController::class, 'faqfeedback']);
Route::any('faq/facilities', [StaticController::class, 'faqfacilities']);
Route::any('contact-us', [StaticController::class, 'contactus']);
Route::any('enquiry', [StaticController::class, 'enquiry']);
Route::any('test-my-sms', [FrontController::class, 'testMySmsm']);
Route::get('loginotp', [FrontController::class, 'loginotp']);
Route::any('forgotpassword', [FrontController::class, 'forgotpassword']);
Route::any('forgotloginotp', [FrontController::class, 'forgotloginotp']);
Route::any('forgotpassword_checkotp', [FrontController::class, 'forgotpassword_checkotp']);
Route::any('resetpassword', [FrontController::class, 'resetpassword']);

Route::any('verifyotp', [FrontController::class, 'verifyotp']);
Route::any('setpassword', [FrontController::class, 'setpassword']);
Route::post('/updatepassword', [FrontController::class, 'updatepassword']);
Route::post('/resendotp', [FrontController::class, 'resendotp']);
Route::post('checkotp', [FrontController::class, 'checkotp'])->name('checkotp');
Route::any('devicestatus', [DeviceController::class, 'devicestatus']);
Route::get('dashboard_reports', [FrontController::class, 'dashboardReports']);
Route::get('email_prev', [FrontController::class, 'emailPrev']);
Route::get('email_prev_two', [FrontController::class, 'emailPrevTwo']);
Route::get('email_prev_three', [FrontController::class, 'emailPrevThree']);

Route::get('signup', [AuthController::class, 'signup']);
Route::post('/opslogin', [AuthController::class, 'login']);

Route::any('visitor-save', ['as' => 'visitor-save', 'uses' => [VisitorBookingController::class, 'store']]);

Route::any('opn_payment_status_update', [OpnController::class, 'opn_payment_status_update']);

/* cron URL */
Route::get('cron_reminderemail', [JoininspectionAppointmentController::class, 'cron_reminderemail']);

Route::get('opslogin', function () {
	Auth::logout();
	return view('auth.login');
})->name('opslogin');

Route::get('home', [HomeController::class, 'index'])->name('home');

Route::prefix('opslogin')->middleware(['auth', 'otp-validation'])->group(function () {
	
	Route::group(['middleware' => ['check-permission:5']], function () {

		Route::prefix('user')->group(function () {
			Route::post('save-signature', [FrontController::class, 'saveSignature']);
			Route::get('profile', [FrontController::class, 'userProfile']);
			Route::get('settings', [FrontController::class, 'settings']);
			Route::post('settingpassword', [FrontController::class, 'settingpassword']);
			Route::post('settingprofilepic', [FrontController::class, 'settingprofilepic']);
		});

		Route::prefix('facility')->group(function () {
			Route::any('new', [FacilityBookingController::class, 'new']);
			Route::any('updatecancelstatus', [FacilityBookingController::class, 'updatecancelstatus']);
			Route::any('updateconfirmstatus', [FacilityBookingController::class, 'updateconfirmstatus']);
			Route::any('refunddeposit', [FacilityBookingController::class, 'refunddeposit']);
			Route::any('cancellationrefund', [FacilityBookingController::class, 'cancellationrefund']);
			Route::any('search', [FacilityBookingController::class, 'search']);
		});
		Route::resource('facility', FacilityBookingController::class);

		Route::any('exportfacility', [ExportController::class, 'exportfacility']);
	});

	Route::group(['middleware' => ['check-permission:7']], function () {

		Route::prefix('user')->group(function () {

			Route::any('queryupdate', [UserMoreInfoController::class, 'queryupdate']);
			Route::any('unitqueryupdate', [UserMoreInfoController::class, 'unitqueryupdate']);
			Route::any('bluetoothdevice_query', [UserMoreInfoController::class, 'bluetoothdevice_query']);
			Route::any('remotedevice_query', [UserMoreInfoController::class, 'remotedevice_query']);
			Route::any('serviceaccess_updatequery', [UserMoreInfoController::class, 'serviceaccess_updatequery']);

			Route::get('userproperties/{id}', [UserMoreInfoController::class, 'userproperties']);
			Route::any('assignproperty', [UserMoreInfoController::class, 'assignproperty']);
			Route::get('deleteproperty/{id}', [UserMoreInfoController::class, 'deleteproperty']);
			Route::any('uploadcsv', [UserMoreInfoController::class, 'uploadcsv']);
			Route::any('importcsv', [UserMoreInfoController::class, 'importcsv']);
			Route::any('userencrypt', [UserMoreInfoController::class, 'userencrypt']);
			Route::any('encrypt', [UserMoreInfoController::class, 'encrypt']);
			Route::any('switchproperty', [UserMoreInfoController::class, 'switchproperty']);
			Route::any('logs', [UserMoreInfoController::class, 'logs']);
			Route::any('import', [UserMoreInfoController::class, 'importcsv']);
			Route::any('adminsearch', [UserMoreInfoController::class, 'adminsearch']);
			Route::any('search', [UserMoreInfoController::class, 'search']);
			Route::any('logsearch', [UserMoreInfoController::class, 'logsearch']);
			Route::get('delete/{id}', [UserMoreInfoController::class, 'destroy']);
			Route::any('accessupdate', [UserMoreInfoController::class, 'accessupdate']);
			Route::any('access', [UserMoreInfoController::class, 'access']);
			Route::get('activate/{id}', [UserMoreInfoController::class, 'activate']);
			Route::get('deactivate/{id}', [UserMoreInfoController::class, 'deactivate']);
			Route::get('userunits/{id}', [UserMoreInfoController::class, 'userunits']);
			Route::any('assignunit', [UserMoreInfoController::class, 'assignunit']);
			Route::get('deleteunit/{id}', [UserMoreInfoController::class, 'deleteunit']);
			Route::any('assignunitupdate/{id}', [UserMoreInfoController::class, 'assignunitupdate']);
			Route::get('usercards/{id}', [UserMoreInfoController::class, 'usercards']);
			Route::any('assigncard', [UserMoreInfoController::class, 'assigncard']);
			Route::get('deletecard/{id}', [UserMoreInfoController::class, 'deletecard']);
			Route::any('userdevices/{id}', [UserMoreInfoController::class, 'userdevices']);
			Route::any('assigndevice', [UserMoreInfoController::class, 'assigndevice']);
			Route::any('useraccess/{id}', [UserMoreInfoController::class, 'useraccess']);
			Route::any('assignaccess', [UserMoreInfoController::class, 'assignaccess']);
			Route::any('accesssearch', [UserMoreInfoController::class, 'accesssearch']);
			Route::any('info/{id}', [UserMoreInfoController::class, 'info']);

			Route::get('account-delete-requests', [UserMoreInfoController::class, 'listACDeleteRequests']);
			Route::any('export', [ExportController::class, 'userExport']);
		});

		Route::prefix('userunit')->group(function () {
			Route::get('delete/{id}', [UserMoreInfoController::class, 'deleteuserunit']);
			Route::get('activate/{id}', [UserMoreInfoController::class, 'activateuserunit']);
			Route::get('deactivate/{id}', [UserMoreInfoController::class, 'deactivateuserunit']);
		});

		Route::prefix('licenseplate')->group(function () {
			Route::get('lists/{id}', [UserLicensePlateController::class, 'list']);
			Route::get('add/{id}', [UserLicensePlateController::class, 'add']);
			Route::post('save', [UserLicensePlateController::class, 'save']);
			Route::get('delete/{id}', [UserLicensePlateController::class, 'destroy']);
		});
		Route::resource('licenseplate', UserLicensePlateController::class);


		Route::prefix('registrations')->group(function () {
			Route::any('search', [UserRegistrationController::class, 'search']);
			Route::get('view/{id}', [UserRegistrationController::class, 'view']);
			Route::get('delete/{id}', [UserRegistrationController::class, 'delete']);
			Route::get('approve/{id}', [UserRegistrationController::class, 'approve']);
			Route::get('reject/{id}', [UserRegistrationController::class, 'reject']);
			Route::any('cancelregistration', [UserRegistrationController::class, 'cancelregistration']);
		});
		Route::resource('registrations', UserRegistrationController::class);

		Route::get('user/rights/{id}', [UserMoreInfoController::class, 'rights']);
		Route::resource('user', UserMoreInfoController::class);

		Route::resource('user/rights', UserPermissionController::class);

		Route::resource('exportusers', ExportController::class);
	});


	Route::get('loghistory/delete/{id}', [ActivityLogController::class, 'destroy']);
	Route::any('loghistory/search', [ActivityLogController::class, 'search']);
	Route::resource('loghistory', ActivityLogController::class);

	Route::prefix('resichat')->group(function () {
		Route::get('replies/delete/{id}', [ChatterBoxController::class, 'repliesdestroy']);
		Route::get('report/delete/{id}', [ChatterBoxController::class, 'reportsdestroy']);
		Route::get('replies/{id}', [ChatterBoxController::class, 'replies']);
		Route::get('reports/{id}', [ChatterBoxController::class, 'reports']);
		Route::get('delete/{id}', [ChatterBoxController::class, 'destroy']);
		Route::any('search', [ChatterBoxController::class, 'search']);
		Route::any('activate/{id}', [ChatterBoxController::class, 'activate']);
		Route::any('deactivate/{id}', [ChatterBoxController::class, 'deactivate']);
		Route::any('allreports', [ChatterBoxController::class, 'allreports']);
		Route::any('viewreports/{id}', [ChatterBoxController::class, 'viewreports']);
		Route::any('hidereport/{id}', [ChatterBoxController::class, 'hidereport']);
		Route::any('showreport/{id}', [ChatterBoxController::class, 'showreport']);
		Route::any('warninguser/{id}', [ChatterBoxController::class, 'warninguser']);
		Route::any('blockuser/{id}', [ChatterBoxController::class, 'blockuser']);
		Route::any('blockedusers', [ChatterBoxController::class, 'blockedusers']);
		Route::any('unblockuser/{id}', [ChatterBoxController::class, 'unblockuser']);
		Route::any('blockagainuser/{id}', [ChatterBoxController::class, 'blockagainuser']);
	});
	Route::resource('resichat', ChatterBoxController::class);

	Route::prefix('marketplace')->group(function () {
		Route::get('likes/delete/{id}', [MpAdsController::class, 'likesdestroy']);
		Route::get('report/delete/{id}', [MpAdsController::class, 'reportsdestroy']);
		Route::get('likes/{id}', [MpAdsController::class, 'likes']);
		Route::get('reports/{id}', [MpAdsController::class, 'reports']);
		Route::get('delete/{id}', [MpAdsController::class, 'destroy']);
		Route::any('search', [MpAdsController::class, 'search']);
		Route::any('activate/{id}', [MpAdsController::class, 'activate']);
		Route::any('deactivate/{id}', [MpAdsController::class, 'deactivate']);
		Route::any('allreports', [MpAdsController::class, 'allreports']);
		Route::any('viewreports/{id}', [MpAdsController::class, 'viewreports']);
		Route::any('hidereport/{id}', [MpAdsController::class, 'hidereport']);
		Route::any('showreport/{id}', [MpAdsController::class, 'showreport']);
		Route::any('warninguser/{id}', [MpAdsController::class, 'warninguser']);
		Route::any('blockuser/{id}', [MpAdsController::class, 'blockuser']);
		Route::any('blockedusers', [MpAdsController::class, 'blockedusers']);
		Route::any('unblockuser/{id}', [MpAdsController::class, 'unblockuser']);
		Route::any('blockagainuser/{id}', [MpAdsController::class, 'blockagainuser']);
	});
	Route::resource('marketplace', MpAdsController::class);

	Route::get('configuration/landing', [SettingController::class, 'landing'])->middleware('check-permission:23');

	Route::get('configuration/role/delete/{id}', [RoleController::class, 'destroy'])->middleware('check-permission:23');
	Route::any('configuration/role/search', [RoleController::class, 'search'])->middleware('check-permission:23');
	Route::resource('configuration/role', RoleController::class)->middleware('check-permission:23');

	Route::get('configuration/sharesettings/delete/{id}', [FinanceShareSettingController::class, 'destroy'])->middleware('check-permission:73');
	Route::any('configuration/sharesettings/search', [FinanceShareSettingController::class, 'search'])->middleware('check-permission:73');
	Route::resource('configuration/sharesettings', FinanceShareSettingController::class)->middleware('check-permission:73');

	Route::group(['middleware' => ['check-permission:24']], function () {

		Route::get('unit_summary/{id}', [UnitController::class, 'unit_summary']);
		Route::get('unit_summary/{id}/{tab}', [UnitController::class, 'unit_summary']);
		Route::get('unitlist', [UnitController::class, 'unitlist']);
		Route::get('unitlist/delete/{id}', [UnitController::class, 'destroy']);
		Route::any('unitlist/search', [UnitController::class, 'search']);
		Route::any('unitsummary/search', [UnitController::class, 'summarysearch']);

		Route::prefix('configuration')->group(function () {
			Route::any('unit/encrypt', [UnitController::class, 'encrypt']);
			Route::any('unit/uploadcsv', [UnitController::class, 'uploadcsv']);
			Route::any('unit/importcsv', [UnitController::class, 'importcsv']);
			Route::any('unit/bulkupload', [UnitController::class, 'bulkupload']);
			Route::any('unit/import', [UnitController::class, 'importcsv']);
			Route::get('unit_summary/{id}/{tab}', [UnitController::class, 'redirect_summary']);
			Route::get('unit_summary/{id}/{tab}', [UnitController::class, 'unit_summary']);
			Route::get('unit/delete/{id}', [UnitController::class, 'destroy']);
			Route::any('unit/search', [UnitController::class, 'search']);
			Route::any('building/uploadcsv', [BuildingController::class, 'uploadcsv'])->middleware('check-permission:49');
			Route::any('building/importcsv', [BuildingController::class, 'importcsv'])->middleware('check-permission:49');
			Route::any('building/bulkupload', [BuildingController::class, 'bulkupload'])->middleware('check-permission:49');
		});
		Route::resource('configuration/unit', UnitController::class);
	});

	Route::get('configuration/building/delete/{id}', [BuildingController::class, 'destroy'])->middleware('check-permission:49');
	Route::any('configuration/building/search', [BuildingController::class, 'search'])->middleware('check-permission:49');
	Route::resource('configuration/building', BuildingController::class)->middleware('check-permission:49');

	Route::get('configuration/eform_setting/preview/{id}', [EformSettingController::class, 'preview'])->middleware('check-permission:39');
	Route::get('configuration/eform_setting/delete/{id}', [EformSettingController::class, 'destroy'])->middleware('check-permission:39');
	Route::any('configuration/eform_setting/search', [EformSettingController::class, 'search'])->middleware('check-permission:39');
	Route::resource('configuration/eform_setting', EformSettingController::class)->middleware('check-permission:39');

	Route::resource('configuration/payment_setting', PaymentSettingController::class)->middleware('check-permission:46');
	Route::resource('configuration/holiday_setting', HolidaySettingController::class)->middleware('check-permission:53');

	Route::any('card/search', [CardController::class, 'search'])->middleware('check-permission:38');
	Route::get('card/delete/{id}', [CardController::class, 'destroy'])->middleware('check-permission:38');
	Route::resource('card', CardController::class)->middleware('check-permission:38');

	Route::any('device/new', [DeviceController::class, 'new'])->middleware('check-permission:48');
	Route::get('device/restart/{id}', [DeviceController::class, 'restart'])->middleware('check-permission:48');
	Route::any('device/search', [DeviceController::class, 'search'])->middleware('check-permission:48');
	Route::any('device/batchassign/{id}', [DeviceController::class, 'batchassign'])->middleware('check-permission:48');
	Route::any('device/batchassignemp/{id}', [DeviceController::class, 'batchassignemp'])->middleware('check-permission:48');

	Route::get('device/delete/{id}', [DeviceController::class, 'destroy'])->middleware('check-permission:48');
	Route::resource('device', DeviceController::class)->middleware('check-permission:48');

	Route::get('configuration/feedback/delete/{id}', [FeedbackOptionController::class, 'destroy'])->middleware('check-permission:26');
	Route::any('configuration/feedback/search', [FeedbackOptionController::class, 'search'])->middleware('check-permission:26');
	Route::resource('configuration/feedback', FeedbackOptionController::class)->middleware('check-permission:26');

	Route::get('configuration/defect/delete/{id}', [DefectLocationController::class, 'destroy'])->middleware('check-permission:27');
	Route::any('configuration/defect/search', [DefectLocationController::class, 'search'])->middleware('check-permission:27');
	Route::resource('configuration/defect', DefectLocationController::class)->middleware('check-permission:27');
	
	Route::get('configuration/collectionappoinment', [PropertyController::class, 'collectionappoinment'])->middleware('check-permission:9');
	Route::any('configuration/collectionappoinmentupdate/{id}', [PropertyController::class, 'collectionappoinmentupdate'])->middleware('check-permission:9');

	Route::get('configuration/inspectionappoinment', [PropertyController::class, 'inspectionappoinment'])->middleware('check-permission:59');
	Route::any('configuration/inspectionappoinmentupdate/{id}', [PropertyController::class, 'inspectionappoinmentupdate'])->middleware('check-permission:59');

	Route::group(['middleware' => ['check-permission:28']], function () {

		Route::get('configuration/property/access/{id}', [PropertyController::class, 'access']);
		Route::any('configuration/property/accessupdate/{id}', [PropertyController::class, 'accessupdate']);

		Route::get('configuration/property/destroy/{id}', [PropertyController::class, 'destroy']);
		Route::any('configuration/dashboard', [PropertyController::class, 'dashboard']);
		Route::any('configuration/dashboardupdate/{id}', [PropertyController::class, 'dashboardupdate']);
		Route::get('configuration/property/delete/{id}', [PropertyController::class, 'delete']);
		Route::any('configuration/property/search', [PropertyController::class, 'search']);
		Route::get('configuration/property/activate/{id}', [PropertyController::class, 'activate']);
		Route::get('configuration/property/deactivate/{id}', [PropertyController::class, 'deactivate']);
		Route::resource('configuration/property', PropertyController::class);

		// Route::resource('configuration/helpbanner', HelpBannerController::class);
		// Route::get('configuration/helpbanner/delete/{id}', [HelpBannerController::class, 'destroy']);
		// Route::any('configuration/helpbanner/search', [HelpBannerController::class, 'search']);
		// Route::get('configuration/helpbanner/activate/{id}', [HelpBannerController::class, 'activate']);
		// Route::get('configuration/helpbanner/deactivate/{id}', [HelpBannerController::class, 'deactivate']);

		Route::get('configuration/banner/delete/{id}', [HomeBannerController::class, 'destroy']);
		Route::any('configuration/banner/search', [HomeBannerController::class, 'search']);
		Route::get('configuration/banner/activate/{id}', [HomeBannerController::class, 'activate']);
		Route::get('configuration/banner/deactivate/{id}', [HomeBannerController::class, 'deactivate']);
		Route::resource('configuration/banner', HomeBannerController::class);
		
		Route::get('configuration/ads/delete/{id}', [AdController::class, 'destroy']);
		Route::any('configuration/ads/search', [AdController::class, 'search']);
		Route::get('configuration/ads/activate/{id}', [AdController::class, 'activate']);
		Route::get('configuration/ads/deactivate/{id}', [AdController::class, 'deactivate']);
		Route::resource('configuration/ads', AdController::class);

		Route::get('configuration/econcierge/delete/{id}', [EconciergeController::class, 'destroy']);
		Route::any('configuration/econcierge/search', [EconciergeController::class, 'search']);
		Route::get('configuration/econcierge/activate/{id}', [EconciergeController::class, 'activate']);
		Route::get('configuration/econcierge/deactivate/{id}', [EconciergeController::class, 'deactivate']);
		Route::resource('configuration/econcierge', EconciergeController::class);

	});

	Route::get('configuration/facility/delete/{id}', [FacilityTypeController::class, 'destroy'])->middleware('check-permission:29');
	Route::any('configuration/facility/search', [FacilityTypeController::class, 'search'])->middleware('check-permission:29');
	Route::resource('configuration/facility', FacilityTypeController::class)->middleware('check-permission:29');

	Route::any('configuration/purpose/settings', [VisitorTypeController::class, 'updatesettings'])->middleware('check-permission:37');
	Route::get('configuration/purpose/delete/{id}', [VisitorTypeController::class, 'destroy'])->middleware('check-permission:37');
	Route::any('configuration/purpose/search', [VisitorTypeController::class, 'search'])->middleware('check-permission:37');
	Route::resource('configuration/purpose', VisitorTypeController::class)->middleware('check-permission:37');

	Route::any('feedback/submit', [FeedbackController::class, 'submit'])->middleware('check-permission:6');
	Route::any('feedback_save', [FeedbackController::class, 'save'])->middleware('check-permission:6');
	Route::any('feedback/lists', [FeedbackController::class, 'submitlists'])->middleware('check-permission:6');
	Route::any('feedbacks/new', [FeedbackController::class, 'new'])->middleware('check-permission:6');
	Route::any('feedbacks/search', [FeedbackController::class, 'search'])->middleware('check-permission:6');
	Route::any('feedbacks/summary', [FeedbackController::class, 'summary'])->middleware('check-permission:6');

	Route::get('feedbacks/delete/{id}', [FeedbackController::class, 'destroy'])->middleware('check-permission:6');
	Route::resource('feedbacks', FeedbackController::class)->middleware('check-permission:6');

	Route::middleware('check-permission:3')->group(function () {
		Route::any('defect/submit', [DefectController::class, 'submit'])->middleware('check-permission:3');
		Route::any('defect_save', [DefectController::class, 'save'])->middleware('check-permission:3');
		Route::any('defect/lists', [DefectController::class, 'submitlists'])->middleware('check-permission:3');
		Route::any('defects/list/{id}', [DefectController::class, 'viewSubmission'])->middleware('check-permission:3');
		Route::any('defects/submission-update/{id}', [DefectController::class, 'submissionUpdate'])->middleware('check-permission:3');
		Route::any('defects/new', [DefectController::class, 'new'])->middleware('check-permission:3');
		Route::any('defects/search', [DefectController::class, 'search'])->middleware('check-permission:3');
		Route::get('defects/delete/{id}', [DefectController::class, 'destroy'])->middleware('check-permission:3');
		Route::any('defects/handover/{id}', [DefectController::class, 'handover'])->middleware('check-permission:3');
		Route::any('defects/final-inspection/{id}', [DefectController::class, 'finalInspection']);
		Route::any('defects/final-inspection-update/{id}', [DefectController::class, 'finalInspectionUpdate']);
		Route::any('defects/final-inspection-cancel', [DefectController::class, 'cancelFinalInspection']);
		Route::any('defects/handoverupdate', [DefectController::class, 'handoverupdate'])->middleware('check-permission:3');
		Route::resource('defects', DefectController::class)->middleware('check-permission:3');
	});

	Route::get('supplier/delete/{id}', [SupplierController::class, 'destroy'])->middleware('check-permission:79');
	Route::resource('supplier', SupplierController::class)->middleware('check-permission:79');

	Route::any('faceid/submit', [UserFacialIdController::class, 'submit'])->middleware('check-permission:50');
	Route::post('faceid/updatecancelstatus', [UserFacialIdController::class, 'updatecancelstatus'])->middleware('check-permission:50');
	Route::any('faceid/updateconfirmstatus', [UserFacialIdController::class, 'updateconfirmstatus'])->middleware('check-permission:50');
	Route::any('faceid/lists', [UserFacialIdController::class, 'submitlists'])->middleware('check-permission:50');
	Route::any('faceid/new', [UserFacialIdController::class, 'new'])->middleware('check-permission:50');
	Route::any('faceid/search', [UserFacialIdController::class, 'search'])->middleware('check-permission:50');
	Route::any('faceid/summarysearch', [UserFacialIdController::class, 'summarysearch'])->middleware('check-permission:50');
	Route::get('faceid/delete/{id}', [UserFacialIdController::class, 'destroy'])->middleware('check-permission:50');
	Route::any('faceid/accessfaceid', [UserFacialIdController::class, 'accessfaceid'])->middleware('check-permission:50');
	Route::resource('faceid', UserFacialIdController::class)->middleware('check-permission:50');


	Route::any('digitalaccess/dooropen', [DigitalAccessController::class, 'dooropen'])->middleware('check-permission:56');
	Route::any('digitalaccess/remotedooropen', [DigitalAccessController::class, 'remotedooropen'])->middleware('check-permission:69');
	Route::any('digitalaccess/bluetoothdooropen', [DigitalAccessController::class, 'bluetoothdooropen'])->middleware('check-permission:69');
	Route::any('digitalaccess/dooropenfailed', [DigitalAccessController::class, 'dooropenfailed'])->middleware('check-permission:66');
	Route::any('digitalaccess/callunit', [DigitalAccessController::class, 'callunit'])->middleware('check-permission:67');
	Route::any('digitalaccess/facerecognition', [DigitalAccessController::class, 'facerecognition'])->middleware('check-permission:50');
	Route::any('digitalaccess/qropenrecords', [DigitalAccessController::class, 'qropenrecords'])->middleware('check-permission:68');

	Route::resource('chatterbox', ChatterBoxController::class)->middleware('check-permission:76');

	Route::any('digitalaccess/searchdooropen', [DigitalAccessController::class, 'searchdooropen'])->middleware('check-permission:56');
	Route::any('digitalaccess/searchbluetoothdooropen', [DigitalAccessController::class, 'searchbluetoothdooropen'])->middleware('check-permission:69');

	Route::any('digitalaccess/searchdooropenfailed', [DigitalAccessController::class, 'searchdooropenfailed'])->middleware('check-permission:66');
	Route::any('digitalaccess/searchcallunit', [DigitalAccessController::class, 'searchcallunit'])->middleware('check-permission:67');
	Route::any('digitalaccess/searchfacerecognition', [DigitalAccessController::class, 'searchfacerecognition'])->middleware('check-permission:50');
	Route::any('digitalaccess/searchqropenrecords', [DigitalAccessController::class, 'searchqropenrecords'])->middleware('check-permission:68');

	Route::resource('digitalaccess', DigitalAccessController::class)->middleware('check-permission:51');

	Route::middleware('check-permission:1')->group(function () {
		Route::get('announcement/search', [AnnouncementController::class, 'search']);
		Route::get('announcement/delete/{id}', [AnnouncementController::class, 'destroy']);
		Route::get('announcement/view/{id}', [AnnouncementController::class, 'viewdetails']);
		Route::resource('announcement', AnnouncementController::class);
	});

	Route::get('configuration/menu/delete/{id}', [ModuleSettingController::class, 'destroy'])->middleware('check-permission:22');
	Route::resource('configuration/menu', ModuleSettingController::class)->middleware('check-permission:22');

	Route::any('configuration/updatepassword', [UserMoreInfoController::class, 'password'])->middleware('check-permission:9');
	Route::any('configuration/savepassword', [UserMoreInfoController::class, 'savepassword'])->middleware('check-permission:9');
	Route::resource('configuration/setting', SettingController::class)->middleware('check-permission:9');
	Route::post('delete_company_logo', [SettingController::class, 'deleteCompanyLogo'])->middleware('check-permission:9');

	Route::any('takeover_appt/search', [UnittakeoverAppointmentController::class, 'search'])->middleware('check-permission:2');
	Route::any('book_appt/updatecancelstatus', [UnittakeoverAppointmentController::class, 'updatecancelstatus'])->middleware('check-permission:2');
	Route::any('book_appt/updateconfirmstatus', [UnittakeoverAppointmentController::class, 'updateconfirmstatus'])->middleware('check-permission:2');
	Route::any('book_appt/message', [UnittakeoverAppointmentController::class, 'message'])->middleware('check-permission:2');
	Route::any('appt_thankyou', [UnittakeoverAppointmentController::class, 'thankyou'])->middleware('check-permission:2');
	Route::any('book_appt', [UnittakeoverAppointmentController::class, 'create'])->middleware('check-permission:2');
	Route::any('takeover_appt/lists', [UnittakeoverAppointmentController::class, 'lists'])->middleware('check-permission:2');
	Route::resource('takeover_appt', UnittakeoverAppointmentController::class)->middleware('check-permission:2');

	Route::any('inspection_appt/search', [JoininspectionAppointmentController::class, 'search'])->middleware('check-permission:4');
	Route::post('book_inspection/updatecancelstatus', [JoininspectionAppointmentController::class, 'updatecancelstatus'])->middleware('check-permission:4');
	Route::any('book_inspection/updateconfirmstatus', [JoininspectionAppointmentController::class, 'updateconfirmstatus'])->middleware('check-permission:4');
	Route::any('book_inspection/message', [JoininspectionAppointmentController::class, 'message'])->middleware('check-permission:4');
	Route::any('inspection_thankyou', [JoininspectionAppointmentController::class, 'thankyou'])->middleware('check-permission:4');
	Route::any('book_inspection', [JoininspectionAppointmentController::class, 'create'])->middleware('check-permission:4');
	Route::any('inspection_appt/lists', [JoininspectionAppointmentController::class, 'lists'])->middleware('check-permission:4');
	Route::resource('inspection_appt', JoininspectionAppointmentController::class)->middleware('check-permission:4');

	Route::get('takeover_appt/delete/{id}', [AnnouncementController::class, 'destroy'])->middleware('check-permission:1');

	Route::any('exportinspection', [ExportController::class, 'inspection'])->middleware('check-permission:4');
	Route::any('exporttakeover', [ExportController::class, 'takeover'])->middleware('check-permission:2');
	Route::any('exportcard', [ExportController::class, 'exportcard'])->middleware('check-permission:42');
	Route::any('exportdevice', [ExportController::class, 'exportdevice'])->middleware('check-permission:48');
	Route::any('exportdefects', [ExportController::class, 'exportdefects'])->middleware('check-permission:3');
	Route::any('exportfileupload', [ExportController::class, 'exportfileupload'])->middleware('check-permission:33');
	Route::any('exportvisitors', [ExportController::class, 'exportvisitors'])->middleware('check-permission:34');
	Route::any('exportmoveinout', [ExportController::class, 'exportmoveinout'])->middleware('check-permission:40');
	Route::any('exportrenovation', [ExportController::class, 'exportrenovation'])->middleware('check-permission:41');
	Route::any('exportdooraccess', [ExportController::class, 'exportdooraccess'])->middleware('check-permission:42');
	Route::any('exportregvehicleiu', [ExportController::class, 'exportregvehicleiu'])->middleware('check-permission:43');
	Route::any('exportmailingaddress', [ExportController::class, 'exportmailingaddress'])->middleware('check-permission:44');
	Route::any('exportparticular', [ExportController::class, 'exportparticular'])->middleware('check-permission:45');
	Route::any('exportfeedback', [ExportController::class, 'exportfeedback'])->middleware('check-permission:26');
	Route::any('exportmultiunituser', [ExportController::class, 'exportmultiunituser']);

	Route::any('docs-files/view/{id}', [CondodocFileController::class, 'viewfiles'])->middleware('check-permission:32');
	Route::any('docs-files/addfile/{id}', [CondodocFileController::class, 'addfiles'])->middleware('check-permission:32');
	Route::get('docs-files/delete/{id}', [CondodocFileController::class, 'destroy'])->middleware('check-permission:32');
	Route::resource('docs-files', CondodocFileController::class)->middleware('check-permission:32');

	Route::get('docs-category/delete/{id}', [DocsCategoryController::class, 'destroy'])->middleware('check-permission:32');
	Route::resource('docs-category', DocsCategoryController::class)->middleware('check-permission:32');

	// Route::resource('condo-docs', CondoDocController::class)->middleware('check-permission:32');
	// Route::get('condo-docs/delete/{id}', [CondoDocController::class, 'destroy'])->middleware('check-permission:32');
	// Route::any('condo-docs/search', [CondoDocController::class, 'search'])->middleware('check-permission:32');
	// Route::get('condo-docs/view/{id}', [CondoDocController::class, 'viewdetails'])->middleware('check-permission:32');
	// Route::get('condo-docs/delete/{id}', [CondoDocController::class, 'destroy'])->middleware('check-permission:32');

	Route::get('magazine/delete/{id}', [MagazineController::class, 'destroy']);
	Route::resource('magazine', MagazineController::class);

	Route::get('userguide/delete/{id}', [UserGuideController::class, 'destroy']);
	Route::resource('userguide', UserGuideController::class);

	Route::any('residents-uploads/search', [ResidentFileSubmissionController::class, 'search'])->middleware('check-permission:33');

	Route::any('residents-uploads/new', [ResidentFileSubmissionController::class, 'new'])->middleware('check-permission:33');
	Route::get('residents-uploads/delete/{id}', [ResidentFileSubmissionController::class, 'destroy'])->middleware('check-permission:33');
	Route::resource('residents-uploads', ResidentFileSubmissionController::class)->middleware('check-permission:33');

	Route::group(['middleware' => ['check-permission:34']], function () {

		Route::get('visitor-summary/{id}/manualscan', [VisitorBookingController::class, 'manualscan']);
		Route::get('visitor-summary/{id}/facialscan/{data}', [VisitorBookingController::class, 'facialscan']);
		Route::get('visitor-summary/{id}/edit{data}', [VisitorBookingController::class, 'edit']);
		Route::any('visitor-summary/search', [VisitorBookingController::class, 'search']);
		Route::any('visitor-summary/new', [VisitorBookingController::class, 'new']);
		Route::get('visitor-summary/delete/{id}', [VisitorBookingController::class, 'destroy']);
		Route::get('visitor-summary/view/{id}', [VisitorBookingController::class, 'viewdetails']);
		Route::resource('visitor-summary', VisitorBookingController::class);
	});

	Route::group(['middleware' => ['check-permission:61']], function () {
		Route::any('invoice/bulkdelete', [FinanceController::class, 'bulkdelete']);
		Route::any('invoice/uploadcsv', [FinanceController::class, 'uploadcsv']);
		Route::any('invoice/importcsv', [FinanceController::class, 'importcsv']);

		Route::any('invoicepayment/bounceback', [FinanceController::class, 'bounceback']);
		Route::any('invoicepayment/delete/{id}', [FinanceController::class, 'paymentdelete']);

		Route::get('invoice/payment/{id}', [FinanceController::class, 'payment']);
		Route::any('invoice/paymentsave/{id}', [FinanceController::class, 'paymentsave']);

		Route::any('invoiceview/{id}', [FinanceController::class, 'viewinvoice']);
		Route::any('paymentoverview', [FinanceController::class, 'paymentoverview']);

		Route::prefix('invoice')->group(function () {
			Route::any('batchsearch', [FinanceController::class, 'batchsearch']);
			Route::any('search', [FinanceController::class, 'search']);
			Route::any('invoice_report', [FinanceController::class, 'invoice_report']);
			Route::any('paidlists', [FinanceController::class, 'paidlists']);
			Route::any('paidlist_search', [FinanceController::class, 'paidlist_search']);
			Route::any('report_search', [FinanceController::class, 'report_search']);
			Route::any('sendnotification/{id}', [FinanceController::class, 'send_notification']);
			Route::any('invoice_first_reminder', [FinanceController::class, 'invoice_first_reminder']);
			Route::any('invoice_second_reminder', [FinanceController::class, 'invoice_second_reminder']);
		});
	});
	Route::any('batchdelete/{id}', [FinanceController::class, 'batchdelete'])->middleware('check-permission:71');
	Route::get('invoice/lists/{id}', [FinanceController::class, 'lists'])->middleware('check-permission:71');
	Route::any('invoicedelete/{id}', [FinanceController::class, 'destroy'])->middleware('check-permission:72');
	Route::any('invoice-payment', [FinanceController::class, 'invoice-payment'])->middleware('check-permission:62');
	Route::any('invoicepreview/{id}', [FinanceController::class, 'invoicepreview'])->middleware('check-permission:62');
	Route::any('invoicesend/{id}', [FinanceController::class, 'invoicesend'])->middleware('check-permission:62');
	Route::any('testinvoicesend/{id}', [FinanceController::class, 'testinvoicesend'])->middleware('check-permission:62');
	Route::any('invoice/back/{id}', [FinanceController::class, 'back'])->middleware('check-permission:62');
	Route::resource('invoice', FinanceController::class)->middleware('check-permission:61');

	Route::get('eform/moveinout/payment/{id}', [EformMovingInOutController::class, 'payment'])->middleware('check-permission:40');
	Route::any('eform/moveinout/paymentsave/{id}', [EformMovingInOutController::class, 'paymentsave'])->middleware('check-permission:40');
	Route::get('eform/moveinout/inspection/{id}', [EformMovingInOutController::class, 'inspection'])->middleware('check-permission:40');
	Route::any('eform/moveinout/inspectionsave/{id}', [EformMovingInOutController::class, 'inspectionsave'])->middleware('check-permission:40');
	Route::any('eform/moveinout/search', [EformMovingInOutController::class, 'search'])->middleware('check-permission:40');
	Route::get('eform/moveinout/delete/{id}', [EformMovingInOutController::class, 'destroy'])->middleware('check-permission:40');
	Route::resource('eform/moveinout', EformMovingInOutController::class)->middleware('check-permission:40');

	Route::get('eform/renovation/payment/{id}', [EformRenovationController::class, 'payment'])->middleware('check-permission:41');
	Route::any('eform/renovation/paymentsave/{id}', [EformRenovationController::class, 'paymentsave'])->middleware('check-permission:41');
	Route::get('eform/renovation/inspection/{id}', [EformRenovationController::class, 'inspection'])->middleware('check-permission:41');
	Route::any('eform/renovation/inspectionsave/{id}', [EformRenovationController::class, 'inspectionsave'])->middleware('check-permission:41');
	Route::any('eform/renovation/search', [EformRenovationController::class, 'search'])->middleware('check-permission:41');
	Route::get('eform/renovation/delete/{id}', [EformRenovationController::class, 'destroy'])->middleware('check-permission:41');
	Route::resource('eform/renovation', EformRenovationController::class)->middleware('check-permission:41');

	Route::get('eform/dooraccess/payment/{id}', [EformDoorAccessController::class, 'payment'])->middleware('check-permission:42');
	Route::any('eform/dooraccess/paymentsave/{id}', [EformDoorAccessController::class, 'paymentsave'])->middleware('check-permission:42');
	Route::get('eform/dooraccess/acknowledgement/{id}', [EformDoorAccessController::class, 'acknowledgement'])->middleware('check-permission:42');
	Route::any('eform/dooraccess/acknowledgementsave/{id}', [EformDoorAccessController::class, 'acknowledgementsave'])->middleware('check-permission:42');
	Route::any('eform/dooraccess/search', [EformDoorAccessController::class, 'search'])->middleware('check-permission:42');
	Route::get('eform/dooraccess/delete/{id}', [EformDoorAccessController::class, 'destroy'])->middleware('check-permission:42');
	Route::resource('eform/dooraccess', EformDoorAccessController::class)->middleware('check-permission:42');

	Route::any('eform/regvehicle/search', [EformRegVehicleController::class, 'search'])->middleware('check-permission:43');
	Route::get('eform/regvehicle/delete/{id}', [EformRegVehicleController::class, 'destroy'])->middleware('check-permission:43');
	Route::resource('eform/regvehicle', EformRegVehicleController::class)->middleware('check-permission:43');

	Route::any('eform/changeaddress/search', [EformChangeAddressController::class, 'search'])->middleware('check-permission:44');
	Route::get('eform/changeaddress/delete/{id}', [EformChangeAddressController::class, 'destroy'])->middleware('check-permission:44');
	Route::resource('eform/changeaddress', EformChangeAddressController::class)->middleware('check-permission:44');

	Route::any('eform/particular/search', [EformParticularController::class, 'search'])->middleware('check-permission:45');
	Route::get('eform/particular/delete/{id}', [EformParticularController::class, 'destroy'])->middleware('check-permission:45');
	Route::resource('eform/particular', EformParticularController::class)->middleware('check-permission:45');
});

Route::get('devicestatus', [DeviceController::class, 'devicestatus'])->name('devicestatus');
Route::get('accessfaceid', [UserFacialIdController::class, 'accessfaceid'])->name('accessfaceid');

Route::get('home', [HomeController::class, 'index'])->middleware('auth');
Route::get('opslogin/home', [HomeController::class, 'index'])->middleware('auth');
Route::get('autocomplete-search_fileno', [PayrollController::class, 'autocompletefileno'])->name('autocompletefileno');
Route::get('autocomplete-search', [UserMoreInfoController::class, 'autocomplete'])->name('autocomplete');
Route::get('autocomplete-search_id', [UserMoreInfoController::class, 'autocompleteid'])->name('autocompleteid');
Route::get('getuserlist', [UserMoreInfoController::class, 'getuser'])->name('getuserlist');
Route::get('gettakeovertimeslots', [UnittakeoverAppointmentController::class, 'gettimeslots'])->name('gettakeovertimeslots');
Route::get('getinspectiontimeslots', [JoininspectionAppointmentController::class, 'gettimeslots'])->name('getinspectiontimeslots');
Route::get('getfacilitytimeslots', [FacilityBookingController::class, 'gettimeslots'])->name('getfacilitytimeslots');
Route::get('refundfacility', [FacilityBookingController::class, 'refundfacility'])->name('refundfacility');

Route::get('getbuildings', [BuildingController::class, 'getbuildings'])->name('getbuildings');
Route::get('getunitlists', [UnitController::class, 'getunitlist'])->name('getunitlist');
Route::get('getroles', [UserMoreInfoController::class, 'getroles'])->name('getroles');
Route::get('getunits', [UserMoreInfoController::class, 'getunits'])->name('getunits');
Route::get('getblockunits', [UserMoreInfoController::class, 'getblockunits'])->name('getblockunits');
Route::get('getbuildingunitlists', [UserMoreInfoController::class, 'getbuildingunitlists'])->name('getbuildingunitlists');
Route::get('getunituserlists', [UserMoreInfoController::class, 'getunituserlists'])->name('getunituserlists');
Route::get('getunitusernewlists', [UserMoreInfoController::class, 'getunitusernewlists'])->name('getunitusernewlists');
Route::get('getinvoicetypeamount', [FinanceController::class, 'invoicetypeamount'])->name('getinvoicetypeamount');
Route::get('getmanagerlists', [UserMoreInfoController::class, 'getmanagerlists'])->name('getmanagerlists');
Route::get('deleteCondoFile', [DocsCategoryController::class, 'deleteCondoFile'])->name('deleteCondoFile');
Route::get('deleteUserguideFile', [UserGuideController::class, 'deleteUserguideFile'])->name('deleteUserguideFile');
Route::get('deleteMagazineFile', [DocsCategoryController::class, 'deleteMagazineFile'])->name('deleteMagazineFile');
Route::get('getlocationtypes', [DefectController::class, 'getlocationtypes'])->name('getlocationtypes');


Route::get('getcards', [CardController::class, 'getcards'])->name('getcards');
Route::get('availability_check', [VisitorBookingController::class, 'availability_check'])->name('availability_check');

Route::get('getmanagerlists', [UserMoreInfoController::class, 'getmanagerlists'])->name('getmanagerlists');

Route::get('firebase', [FirebaseController::class, 'index']);
Route::get('logout', [UserMoreInfoController::class, 'logout']);

require __DIR__ . '/auth.php';
