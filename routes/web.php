<?php

use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome');
Route::get('userVerification/{token}', 'UserVerificationController@approve')->name('userVerification');
Auth::routes();

// Admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', '2fa', 'admin']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/media', 'UsersController@storeMedia')->name('users.storeMedia');
    Route::post('users/ckmedia', 'UsersController@storeCKEditorImages')->name('users.storeCKEditorImages');
    Route::resource('users', 'UsersController');

    // Pets
    Route::delete('pets/destroy', 'PetsController@massDestroy')->name('pets.massDestroy');
    Route::post('pets/media', 'PetsController@storeMedia')->name('pets.storeMedia');
    Route::post('pets/ckmedia', 'PetsController@storeCKEditorImages')->name('pets.storeCKEditorImages');
    Route::resource('pets', 'PetsController');

    // Booking
    Route::delete('bookings/destroy', 'BookingController@massDestroy')->name('bookings.massDestroy');
    Route::post('bookings/media', 'BookingController@storeMedia')->name('bookings.storeMedia');
    Route::post('bookings/ckmedia', 'BookingController@storeCKEditorImages')->name('bookings.storeCKEditorImages');
    Route::resource('bookings', 'BookingController');

    // Pet Reviews
    Route::delete('pet-reviews/destroy', 'PetReviewsController@massDestroy')->name('pet-reviews.massDestroy');
    Route::resource('pet-reviews', 'PetReviewsController');

    // Chat
    Route::delete('chats/destroy', 'ChatController@massDestroy')->name('chats.massDestroy');
    Route::post('chats/media', 'ChatController@storeMedia')->name('chats.storeMedia');
    Route::post('chats/ckmedia', 'ChatController@storeCKEditorImages')->name('chats.storeCKEditorImages');
    Route::resource('chats', 'ChatController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Support
    Route::delete('supports/destroy', 'SupportController@massDestroy')->name('supports.massDestroy');
    Route::post('supports/media', 'SupportController@storeMedia')->name('supports.storeMedia');
    Route::post('supports/ckmedia', 'SupportController@storeCKEditorImages')->name('supports.storeCKEditorImages');
    Route::resource('supports', 'SupportController');

    // Email Log
    Route::delete('email-logs/destroy', 'EmailLogController@massDestroy')->name('email-logs.massDestroy');
    Route::resource('email-logs', 'EmailLogController');

    // Spam Ip
    Route::delete('spam-ips/destroy', 'SpamIpController@massDestroy')->name('spam-ips.massDestroy');
    Route::resource('spam-ips', 'SpamIpController');

    Route::get('messenger', 'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');
});

// Profile routes
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
        Route::post('profile/two-factor', 'ChangePasswordController@toggleTwoFactor')->name('password.toggleTwoFactor');
    }
});

// Frontend routes
Route::group(['as' => 'frontend.', 'namespace' => 'Frontend', 'middleware' => ['auth', '2fa']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/media', 'UsersController@storeMedia')->name('users.storeMedia');
    Route::post('users/ckmedia', 'UsersController@storeCKEditorImages')->name('users.storeCKEditorImages');
    Route::resource('users', 'UsersController');

    // Pets
    Route::delete('pets/destroy', 'PetsController@massDestroy')->name('pets.massDestroy');
    Route::post('pets/media', 'PetsController@storeMedia')->name('pets.storeMedia');
    Route::post('pets/ckmedia', 'PetsController@storeCKEditorImages')->name('pets.storeCKEditorImages');
    Route::resource('pets', 'PetsController');

    // Requests routes
    Route::get('requests', 'RequestController@index')->name('requests.index');
    Route::put('requests/{booking}', 'RequestController@update')->name('requests.update');

    // Booking routes
    Route::delete('bookings/destroy', 'BookingController@massDestroy')->name('bookings.massDestroy');
    Route::post('bookings/media', 'BookingController@storeMedia')->name('bookings.storeMedia');
    Route::post('bookings/ckmedia', 'BookingController@storeCKEditorImages')->name('bookings.storeCKEditorImages');
    Route::resource('bookings', 'BookingController');
    Route::put('bookings/{booking}/complete', 'BookingController@complete')->name('bookings.complete');

    // Pet Reviews
    Route::prefix('pet-reviews')->name('pet-reviews.')->group(function () {
        Route::get('create/{booking}', 'PetReviewsController@create')->name('create');
        Route::post('store', 'PetReviewsController@store')->name('store');
        Route::delete('destroy', 'PetReviewsController@massDestroy')->name('massDestroy');
        Route::resource('/', 'PetReviewsController', ['names' => [
            'index' => 'index',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]])->except(['create', 'store']);
    });

    // Chat
    Route::delete('chats/destroy', 'ChatController@massDestroy')->name('chats.massDestroy');
    Route::post('chats/media', 'ChatController@storeMedia')->name('chats.storeMedia');
    Route::post('chats/ckmedia', 'ChatController@storeCKEditorImages')->name('chats.storeCKEditorImages');
    Route::resource('chats', 'ChatController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Support
    Route::delete('supports/destroy', 'SupportController@massDestroy')->name('supports.massDestroy');
    Route::post('supports/media', 'SupportController@storeMedia')->name('supports.storeMedia');
    Route::post('supports/ckmedia', 'SupportController@storeCKEditorImages')->name('supports.storeCKEditorImages');
    Route::resource('supports', 'SupportController');

    // Email Log
    Route::delete('email-logs/destroy', 'EmailLogController@massDestroy')->name('email-logs.massDestroy');
    Route::resource('email-logs', 'EmailLogController');

    // Spam Ip
    Route::delete('spam-ips/destroy', 'SpamIpController@massDestroy')->name('spam-ips.massDestroy');
    Route::resource('spam-ips', 'SpamIpController');

    Route::get('frontend/profile', 'ProfileController@index')->name('profile.index');
    Route::post('frontend/profile', 'ProfileController@update')->name('profile.update');
    Route::post('frontend/profile/destroy', 'ProfileController@destroy')->name('profile.destroy');
    Route::post('frontend/profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/toggle-two-factor', 'ProfileController@toggleTwoFactor')->name('profile.toggle-two-factor');

    Route::get('credit-logs', [App\Http\Controllers\Frontend\CreditLogController::class, 'index'])->name('credit-logs.index');
});

// Two Factor Authentication routes
Route::group(['namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    if (file_exists(app_path('Http/Controllers/Auth/TwoFactorController.php'))) {
        Route::get('two-factor', 'TwoFactorController@show')->name('twoFactor.show');
        Route::post('two-factor', 'TwoFactorController@check')->name('twoFactor.check');
        Route::get('two-factor/resend', 'TwoFactorController@resend')->name('twoFactor.resend');
    }
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/frontend/bookings/{booking}/messages', [App\Http\Controllers\Frontend\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/frontend/bookings/{booking}/messages', [App\Http\Controllers\Frontend\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/frontend/bookings/{booking}/messages/read', [App\Http\Controllers\Frontend\ChatController::class, 'markAsRead'])->name('chat.read');
});
