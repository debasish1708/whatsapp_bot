<?php

use App\Http\Controllers\Admin\PendingAccountController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\BusinessRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Business\Common\ChatHistoryController;
use App\Http\Controllers\Business\Common\JobApplicationController;
use App\Http\Controllers\Business\Common\PlaceSearchController;
use App\Http\Controllers\Business\Common\VerifyEmailController;
use App\Http\Controllers\Business\Restaurant\CustomerController;
use App\Http\Controllers\Business\Restaurant\MemberController;
use App\Http\Controllers\Business\Restaurant\OfferController;
use App\Http\Controllers\Business\Restaurant\OrderController;
use App\Http\Controllers\Business\Restaurant\TableBookingController;
use App\Http\Controllers\Business\Restaurant\TableController;
use App\Http\Controllers\Business\Restaurant\TableHourController;
use App\Http\Controllers\Business\Restaurant\TableReservationController;
use App\Http\Controllers\Business\School\AnnouncementController;
use App\Http\Controllers\Business\School\ClubController;
use App\Http\Controllers\Business\School\EventCalenderController;
use App\Http\Controllers\Business\School\JobApplicantController;
use App\Http\Controllers\Business\School\JobOfferController;
use App\Http\Controllers\Business\School\ProfileController;
use App\Http\Controllers\Business\Restaurant\ProfileController as RestaurantProfileController;
use App\Http\Controllers\Business\School\PsychologicalSupportController;
use App\Http\Controllers\Business\School\SchoolAdmissionController;
use App\Http\Controllers\Business\School\SosAlAlertController;
use App\Http\Controllers\Business\Restaurant\MenuItemController;
use App\Http\Controllers\Business\Restaurant\AnnouncementController as RestaurantAnnouncementController;
use App\Http\Controllers\Business\Restaurant\JobOfferController as RestaurantJobOfferController;
use App\Http\Controllers\Business\School\StudentController;
use App\Http\Controllers\Common\ChangePasswordController;
use App\Http\Controllers\Common\DeleteAccountController;
use App\Http\Middleware\CheckIfAdminApprovedMiddleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\VerifyIfRestaurantProfileCompleted;
use App\Http\Controllers\Stripe\SubscriptionController;
use App\Models\Restaurant;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/',[BusinessRegisterController::class, 'login'])->name('business.login');
Route::post('/register',[BusinessRegisterController::class, 'register'])->name('business.register.store');
Route::get('/register',[BusinessRegisterController::class, 'showRegistrationForm'])->name('business.register.show');

Route::middleware(['auth','restrict.school.profile.setup'])->prefix('school')->group(function () {
  Route::get('/dashboard', [HomePage::class, 'index'])->name('school.dashboard');
});

// Main Page Route
// Route::get('/', [HomePage::class, 'index'])->name('pages-home');
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// authentication
// Route::get('/login', [LoginBasic::class, 'index'])->name('auth-login-basic');
// Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

// school

// routes/web.php
Route::post('/admin/otp',[LoginController::class,'verifyAdminOtp'])->name('admin.otp.verify');

Route::middleware(['auth'])->group(function () {
  Route::get('/subscribe', [SubscriptionController::class, 'showForm'])->name('subscribe.form');
  Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');
  Route::post('/subscribe', [SubscriptionController::class, 'createSubscription'])->name('subscribe.create');
  Route::get('/subscription/success', [SubscriptionController::class, 'afterSuccess'])->name('subscribe.success');
  Route::get('/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('billing.portal');
  Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
  Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

  Route::get('chat-history', [ChatHistoryController::class, 'index'])->name('chat-history.index')->middleware('ensure.subscribed');
  Route::get('chat-history/{user}', [ChatHistoryController::class, 'show'])->name('chat-history.show')->middleware('ensure.subscribed');

  Route::get('accounts/delete-requests', [DeleteAccountController::class, 'index'])->name('accounts.delete-request');
  Route::get('accounts/{account}/delete-requests', [DeleteAccountController::class, 'show'])->name('accounts.delete-request.show');
  Route::put('accounts/{account}/delete-requests/accept', [DeleteAccountController::class, 'approve'])->name('accounts.delete-request.accept');
  Route::put('accounts/{account}/delete-requests/reject', [DeleteAccountController::class, 'reject'])->name('accounts.delete-request.reject');
  Route::post('accounts/request-delete', [DeleteAccountController::class, 'requestDeletion'])->name('accounts.request-delete');
});

Route::group([], function(){
  Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
  Route::post('/reset-link', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.reset-link');
  Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
  Route::put('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth'])->prefix('school')->group(function () {
  Route::resource('profile',ProfileController::class)->except('show','destroy')->names('school.profile');
  Route::middleware(['restrict.admin','restrict.school.profile.setup','ensure.subscribed'])->group(function (){
    Route::resource('announcement',AnnouncementController::class)->except('create')->names('school.announcement');
    Route::resource('club-activity',ClubController::class)->except('create')->names('school.club-activities');
    Route::resource('psychological-support',PsychologicalSupportController::class)->names('school.psychological-support');
    Route::resource('sos-alerts',SosAlAlertController::class)->except('create')->names('school.sos-alerts');
    Route::resource('event-calender',EventCalenderController::class)->except('create','show','edit')->names('school.event-calender');
    Route::get('form-application',[SchoolAdmissionController::class,'index'])->name('school.admission.index');
    Route::get('school-admissions/{school_admission}',[SchoolAdmissionController::class,'showAdmissionData'])->name('school.admissions.show');
    Route::put('school-admissions/{school_admission}/accept',[SchoolAdmissionController::class,'accept'])
            ->name('school.admissions.accept');
    Route::put('school-admissions/{school_admission}/reject',[SchoolAdmissionController::class,'reject'])
            ->name('school.admissions.reject');
    Route::put('school-admissions/{school_admission}/refund',[SchoolAdmissionController::class,'refund'])
            ->name('school.admissions.refund');
    Route::resource('job-offer',JobOfferController::class)->except('create')->names('school.job-offer');
    Route::resource('students',StudentController::class)->names('school.students');
    Route::post('/students/import',[StudentController::class, 'import'])->name('school.students.import');

    Route::resource('job-applicant',JobApplicantController::class)->except('create')->names('school.job-applicant');
    Route::get('job-offer/{jobOffer}/applicants',[JobApplicantController::class, 'applicants'])->name('school.job-offer.applicants');
    Route::resource('members',App\Http\Controllers\Business\School\MemberController::class)->names('school.members');
  });
});

Route::middleware([VerifyIfRestaurantProfileCompleted::class,'auth'])->prefix('restaurants')->group(function(){
  Route::get('dashboard',[HomePage::class, 'restaurantDashboard'])->name('restaurant.dashboard');
});
//Restaurant dashboard routes
Route::middleware(['auth'])->prefix('restaurants')->group(function () {
  // Route::get('dashboard',[HomePage::class, 'restaurantDashboard'])->name('restaurant.dashboard');
  Route::get('profile',[RestaurantProfileController::class, 'show'])->name('restaurant.profile.index');
  Route::get('profile/edit',[RestaurantProfileController::class, 'edit'])->name('restaurant.profile.edit');
  Route::put('profile/update',[RestaurantProfileController::class, 'update'])->name('restaurant.profile.update');
  Route::middleware([VerifyIfRestaurantProfileCompleted::class, CheckIfAdminApprovedMiddleware::class,'ensure.subscribed'])->group(function () {
    // Route::get('dashboard',[HomePage::class, 'restaurantDashboard'])->name('restaurant.dashboard');
    Route::resource('menu-items', MenuItemController::class);
    Route::resource('announcements', RestaurantAnnouncementController::class);
    Route::resource('job-offers', RestaurantJobOfferController::class);
    Route::resource('customers', CustomerController::class)->names('restaurant.customers');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('restaurant.customers.import');
    Route::resource('offers', OfferController::class)->names('restaurant.offers');
    Route::put('orders/{order}/mark-delivered', [OrderController::class, 'markAsDelivered'])->name('restaurant.orders.mark-delivered');
    Route::put('orders/{order}/mark-canceled', [OrderController::class, 'markAsCanceled'])->name('restaurant.orders.mark-canceled');
    Route::resource('orders', OrderController::class)->names('restaurant.orders');

    Route::resource('job-applicant', App\Http\Controllers\Business\Restaurant\JobApplicantController::class)
                    ->except('create')
                    ->names('restaurant.job-applicant');

    Route::get('job-offer/{jobOffer}/applicants',[App\Http\Controllers\Business\Restaurant\JobApplicantController::class, 'applicants'])
                ->name('job-offers.applicants');

    Route::resource('tables',TableController::class)->except(['show','create']);

    Route::get('tables-hours', [TableHourController::class, 'show'])->name('restaurant.tables.hours');
    Route::post('tables-hours', [TableHourController::class, 'store'])->name('restaurant.tables.hours.store');

    Route::resource('/tables/reservation',TableReservationController::class)->only(['index','destroy']);
    Route::put('/table/reservation/{reservation}/accept',[TableReservationController::class,'accept'])->name('table.reservation.accept');
    Route::put('/table/reservation/{reservation}/reject',[TableReservationController::class,'reject'])->name('table.reservation.reject');

    Route::resource('members',MemberController::class);
  });

});

// place search
// Route::get('/place-search',PlaceSearchController::class)->name('place.search');
Route::get('/v1/place-autocomplete',PlaceSearchController::class)->name('place.search');
// web.php
Route::get('/restaurant/{restaurant}/available-items', [OfferController::class, 'getAvailableItems'])->name('restaurant.available-items');


Route::group([],function(){
  Route::get('/billing',function(){
    return view('content.business.school.payment.subscription-payment');
  })->name('subscribe.form')->middleware(['restrict.school.profile.setup','restrict.admin']);

  Route::get('/payment',function(){
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.business.school.payment.subscription-payment',['pageConfigs' => $pageConfigs]);
  })->name('school-subscription');

  Route::get('/restaurant-order/payment-success',[OrderController::class,'paymentSuccess'])->name('restaurant.admission.payment.success');
  Route::get('/restaurant-order/payment-failed',[OrderController::class,'paymentSuccess'])->name('restaurant.admission.payment.failed');

  Route::get('/school-admission/payment-success',[SchoolAdmissionController::class,'paymentSuccess'])->name('school.admission.payment.success');
  Route::get('/school-admission/payment-failed',[SchoolAdmissionController::class,'paymentSuccess'])->name('school.admission.payment.failed');
  Route::get('/admission-form/{schoolId}',[SchoolAdmissionController::class,'show'])->name('school-admission-form');
  Route::post('/school/admission-form',[SchoolAdmissionController::class,'store'])->name('school.admission.store');

  // Route::get('/job-application/{jobOfferId}',[JobOfferController::class,'showForm'])->name('job-offer.form');
  // Route::post('/job-application',[JobOfferController::class,'storeApplication'])->name('job-offer.storeApplication');

  Route::get('/job-application/{jobOfferId}',[JobApplicationController::class,'index'])->name('job-application.form');
  Route::post('/job-application',[JobApplicationController::class,'store'])->name('job-application.storeApplication');

  Route::get('/restaurant/{restaurant}/table_booking/{user}',[TableBookingController::class,'showBookingForm'])->name('restaurant.table.form');
  Route::post('/restaurant/table/book',[TableBookingController::class,'store'])->name('restaurant.table.book');

  Route::get('/school/{school}/sos-aleart/send',[App\Http\Controllers\Business\form\SchoolSosAleartController::class,'create'])
                                                                                                          ->name('school.sosl-aleart.create');
  Route::post('/school/sos-aleart',[App\Http\Controllers\Business\form\SchoolSosAleartController::class,'store'])->name('school.sos-aleart.store');
});


//admin Routes
Route::middleware(IsAdmin::class)->prefix('admin')->group(function () {
  Route::get('dashboard',[HomePage::class, 'adminDashboard'])->name('admin.dashboard');
  Route::resource('restaurants', RestaurantController::class);
  Route::resource('schools', SchoolController::class);
  Route::put('restaurants/{restaurant}/approve', [RestaurantController::class, 'approve'])->name('restaurants.approve');
  Route::put('schools/{school}/approve', [SchoolController::class, 'approve'])->name('schools.approve');
  Route::put('restaurants/{restaurant}/reject', [RestaurantController::class, 'reject'])->name('restaurants.reject');
  Route::put('schools/{school}/reject', [SchoolController::class, 'reject'])->name('schools.reject');

  Route::resource('users', UserController::class)->names('admin.users');
  Route::get('/pendig-accounts',[PendingAccountController::class, 'index'])->name('admin.pending-accounts');
});

Route::get('/verify-email',VerifyEmailController::class);

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});


// Route::get('/test-email',function(){
//   $user = User::findOrFail('9f353d27-a5f2-4ca3-abc3-9eaf8aecddf8');
//   $user->notify(new WelcomeNotification());
//   return 'Email sent';
// });

Route::fallback(function () {
  return '<h1>404 Not Found</h1>';
});
