<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'home'])->name('web.home');
Route::get('/instruments', [WebController::class, 'instruments'])->name('web.instruments');
Route::get('/category/{name}', [WebController::class, 'category'])->name('web.category');
Route::get('/instrument/{id}', [WebController::class, 'instrument'])->name('web.instrument');
Route::get('/instrument/{id}/guidelines', [WebController::class, 'instrumentGuidelines'])->name('web.instrument.guidelines');

Route::get('/login', [WebController::class, 'loginForm'])->name('web.login');
Route::post('/login', [WebController::class, 'login'])->name('web.login.submit');
Route::get('/signup', [WebController::class, 'signupForm'])->name('web.signup');
Route::post('/signup', [WebController::class, 'signup'])->name('web.signup.submit');
Route::get('/verify-otp', [WebController::class, 'verifyOtpForm'])->name('web.verify-otp.form');
Route::post('/verify-otp', [WebController::class, 'verifyOtp'])->name('web.verify-otp.submit');
Route::get('/forgot-password', [WebController::class, 'forgotPasswordForm'])->name('web.forgot-password.form');
Route::post('/forgot-password', [WebController::class, 'forgotPassword'])->name('web.forgot-password.submit');
Route::get('/verify-reset-otp', [WebController::class, 'verifyResetOtpForm'])->name('web.verify-reset-otp.form');
Route::post('/verify-reset-otp', [WebController::class, 'verifyResetOtp'])->name('web.verify-reset-otp.submit');
Route::get('/reset-password', [WebController::class, 'resetPasswordForm'])->name('web.reset-password.form');
Route::post('/reset-password', [WebController::class, 'resetPassword'])->name('web.reset-password.submit');

Route::get('/bag', [WebController::class, 'bag'])->name('web.bag');
Route::post('/bag/add/{id}', [WebController::class, 'addToBag'])->name('web.bag.add');
Route::post('/bag/remove/{id}', [WebController::class, 'removeFromBag'])->name('web.bag.remove');

Route::middleware('user.session')->group(function () {
    Route::post('/logout', [WebController::class, 'logout'])->name('web.logout');
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('web.dashboard');
    Route::get('/booking-form', [WebController::class, 'bookingForm'])->name('web.booking.form');
    Route::post('/booking-form', [WebController::class, 'submitBooking'])->name('web.booking.submit');
    Route::get('/booking-confirmation/{id}', [WebController::class, 'bookingConfirmation'])->name('web.booking.confirmation');

    Route::get('/profile', [WebController::class, 'profile'])->name('web.profile');
    Route::get('/profile/export-bookings', [WebController::class, 'exportMyBookings'])->name('web.profile.export-bookings');
    Route::get('/my-bookings', [WebController::class, 'myBookings'])->name('web.my-bookings');
    Route::get('/favorites', [WebController::class, 'favorites'])->name('web.favorites');
    Route::post('/favorites/add/{id}', [WebController::class, 'addToFavorites'])->name('web.favorites.add');
    Route::post('/favorites/remove/{id}', [WebController::class, 'removeFromFavorites'])->name('web.favorites.remove');
    Route::get('/queue-status', [WebController::class, 'queueStatus'])->name('web.queue-status');
    Route::post('/queue/join/{instrumentId}', [WebController::class, 'joinQueue'])->name('web.queue.join');
});

Route::get('/admin/login', [WebController::class, 'adminLoginForm'])->name('web.admin.login');
Route::post('/admin/login', [WebController::class, 'adminLogin'])->name('web.admin.login.submit');
Route::get('/admin/signup', [WebController::class, 'adminSignupForm'])->name('web.admin.signup');
Route::post('/admin/signup', [WebController::class, 'adminSignup'])->name('web.admin.signup.submit');

Route::middleware('admin.session')->prefix('admin')->group(function () {
    Route::post('/logout', [WebController::class, 'adminLogout'])->name('web.admin.logout');
    Route::get('/', [WebController::class, 'adminDashboard'])->name('web.admin.dashboard');

    Route::get('/instruments', [WebController::class, 'adminInstruments'])->name('web.admin.instruments');
    Route::post('/instruments', [WebController::class, 'adminInstrumentStore'])->name('web.admin.instruments.store');
    Route::post('/instruments/bulk-upload', [WebController::class, 'adminInstrumentBulkUpload'])->name('web.admin.instruments.bulk-upload');
    Route::post('/instruments/{id}/update', [WebController::class, 'adminInstrumentUpdate'])->name('web.admin.instruments.update');
    Route::post('/instruments/{id}/delete', [WebController::class, 'adminInstrumentDelete'])->name('web.admin.instruments.delete');

    Route::get('/bookings', [WebController::class, 'adminBookings'])->name('web.admin.bookings');
    Route::get('/bookings/export', [WebController::class, 'adminExportBookings'])->name('web.admin.bookings.export');
    Route::post('/bookings/{id}/approve', [WebController::class, 'approveBooking'])->name('web.admin.bookings.approve');
    Route::post('/bookings/{id}/reject', [WebController::class, 'rejectBooking'])->name('web.admin.bookings.reject');

    Route::get('/queue', [WebController::class, 'adminQueue'])->name('web.admin.queue');
    Route::post('/queue/approve', [WebController::class, 'approveQueue'])->name('web.admin.queue.approve');
    Route::post('/queue/reject', [WebController::class, 'rejectQueue'])->name('web.admin.queue.reject');

    Route::get('/analytics', [WebController::class, 'adminAnalytics'])->name('web.admin.analytics');
    Route::get('/users', [WebController::class, 'adminUsers'])->name('web.admin.users');
    Route::get('/users/export', [WebController::class, 'adminExportUsers'])->name('web.admin.users.export');
});

Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::get('/bootstrap', [AjaxController::class, 'bootstrap'])->name('bootstrap');
    Route::get('/search-suggestions', [AjaxController::class, 'searchSuggestions'])->name('search-suggestions');

    Route::post('/auth/login', [AjaxController::class, 'authLogin'])->name('auth.login');
    Route::post('/auth/signup', [AjaxController::class, 'authSignup'])->name('auth.signup');
    Route::post('/auth/logout', [AjaxController::class, 'authLogout'])->name('auth.logout');
    Route::get('/auth/me', [AjaxController::class, 'authMe'])->name('auth.me');

    Route::get('/instruments', [AjaxController::class, 'instruments'])->name('instruments');
    Route::get('/instruments/{id}', [AjaxController::class, 'instrument'])->name('instrument');
    Route::get('/instruments/{id}/related', [AjaxController::class, 'relatedInstruments'])->name('instrument.related');
    Route::get('/instruments/{id}/availability', [AjaxController::class, 'instrumentAvailability'])->name('instrument.availability');

    Route::get('/bag', [AjaxController::class, 'bagList'])->name('bag.list');
    Route::post('/bag/add/{id}', [AjaxController::class, 'bagAdd'])->name('bag.add');
    Route::post('/bag/remove/{id}', [AjaxController::class, 'bagRemove'])->name('bag.remove');

    Route::get('/favorites', [AjaxController::class, 'favoritesList'])->name('favorites.list');
    Route::post('/favorites/toggle/{id}', [AjaxController::class, 'favoritesToggle'])->name('favorites.toggle');

    Route::post('/booking/validate', [AjaxController::class, 'bookingValidate'])->name('booking.validate');
    Route::post('/booking/submit', [AjaxController::class, 'bookingSubmit'])->name('booking.submit');
    Route::get('/bookings', [AjaxController::class, 'userBookings'])->name('bookings.list');

    Route::post('/queue/join/{id}', [AjaxController::class, 'joinQueue'])->name('queue.join');
    Route::get('/queue', [AjaxController::class, 'queueStatus'])->name('queue.status');

    Route::get('/notifications', [AjaxController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-read', [AjaxController::class, 'notificationsMarkRead'])->name('notifications.mark-read');
});

Route::middleware('admin.session')->prefix('ajax/admin')->name('ajax.admin.')->group(function () {
    Route::get('/instruments', [AjaxController::class, 'adminInstruments'])->name('instruments');
    Route::get('/bookings', [AjaxController::class, 'adminBookings'])->name('bookings');
    Route::get('/users', [AjaxController::class, 'adminUsers'])->name('users');
    Route::get('/analytics', [AjaxController::class, 'adminAnalytics'])->name('analytics');
});
