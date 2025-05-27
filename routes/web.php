<?php

use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Frontend\CreditPurchaseController;

if (!function_exists('registerResourceRoutes')) {
    /**
     * Helper function to register resource routes with common optional methods.
     * Routes defined within this helper will automatically respect group prefixes and 'as' naming.
     *
     * @param string $name The base name for routes and path segment (e.g., 'permissions', 'users').
     *                     For PetReviews frontend mounted at '/', this is an empty string.
     * @param string $controller The controller class name.
     * @param array $options Options:
     *                       - 'massDestroy' (bool): Add massDestroy route. Default true.
     *                       - 'storeMedia' (bool): Add storeMedia route. Default false.
     *                       - 'storeCKEditorImages' (bool): Add storeCKEditorImages route. Default false.
     *                       - 'except' (array): Routes to exclude from the resource. Default [].
     *                       - 'additionalPostRoutes' (array): ['route_path_segment' => 'methodName'] (e.g. ['{id}/restore' => 'restore'])
     *                       - 'additionalGetRoutes' (array): ['route_path_segment' => 'methodName']
     *                       - 'additionalPutRoutes' (array): ['route_path_segment' => 'methodName']
     *                       - 'customResourceName' (string|null): Path for Route::resource(). Defaults to $name. (e.g. '/' for PetReviews)
     *                       - 'names' (array|null): Specific names for resource routes, useful with customResourceName='/'.
     */
    function registerResourceRoutes(string $name, string $controller, array $options = [])
    {
        $resourcePath = $options['customResourceName'] ?? $name;
        
        // Determine path prefix for additional routes. If $name is empty (PetReviews case), path is just segment.
        $pathPrefix = !empty($name) ? $name . '/' : '';

        // massDestroy path:
        // If $name is 'users', path is 'users/destroy'. Route name 'users.massDestroy'.
        // If $name is '' (PetReviews frontend), path is 'destroy'. Route name '.massDestroy'.
        // Group 'as' prefixes (e.g. 'admin.' or 'frontend.pet-reviews.') are added automatically by Laravel.
        $massDestroyPath = ($name === '' && ($options['customResourceName'] ?? '') === '/') ? 'destroy' : $name . '/destroy';

        if ($options['massDestroy'] ?? true) {
            Route::delete($massDestroyPath, [$controller, 'massDestroy'])->name($name . '.massDestroy');
        }

        if ($options['storeMedia'] ?? false) {
            Route::post($pathPrefix . 'media', [$controller, 'storeMedia'])->name($name . '.storeMedia');
        }

        if ($options['storeCKEditorImages'] ?? false) {
            Route::post($pathPrefix . 'ckmedia', [$controller, 'storeCKEditorImages'])->name($name . '.storeCKEditorImages');
        }

        if (!empty($options['additionalPostRoutes'])) {
            foreach ($options['additionalPostRoutes'] as $segment => $method) {
                Route::post($pathPrefix . $segment, [$controller, $method])->name($name . '.' . \Illuminate\Support\Str::snake($method));
            }
        }
        if (!empty($options['additionalPutRoutes'])) {
            foreach ($options['additionalPutRoutes'] as $segment => $method) {
                Route::put($pathPrefix . $segment, [$controller, $method])->name($name . '.' . \Illuminate\Support\Str::snake($method));
            }
        }
        if (!empty($options['additionalGetRoutes'])) {
            foreach ($options['additionalGetRoutes'] as $segment => $method) {
                Route::get($pathPrefix . $segment, [$controller, $method])->name($name . '.' . \Illuminate\Support\Str::snake($method));
            }
        }
        
        $resourceRoute = Route::resource($resourcePath, $controller, ['except' => $options['except'] ?? []]);
        
        if (!empty($options['names'])) {
            $resourceRoute->names($options['names']);
        }
    }
}

Route::view('/', 'welcome');
Route::get('userVerification/{token}', 'UserVerificationController@approve')->name('userVerification');
Auth::routes();

// Admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', '2fa', 'admin']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    
    registerResourceRoutes('permissions', 'PermissionsController');
    registerResourceRoutes('roles', 'RolesController');
    registerResourceRoutes('users', 'UsersController', [
         'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('pets', 'PetsController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('bookings', 'BookingController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('pet-reviews', 'PetReviewsController');
    registerResourceRoutes('chats', 'ChatController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('user-alerts', 'UserAlertsController', [
        'except' => ['edit', 'update'],
        'additionalGetRoutes' => ['read' => 'read'],
    ]);
    registerResourceRoutes('supports', 'SupportController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('email-logs', 'EmailLogController');
    registerResourceRoutes('spam-ips', 'SpamIpController');

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

    registerResourceRoutes('permissions', 'PermissionsController');
    registerResourceRoutes('roles', 'RolesController');
    registerResourceRoutes('users', 'UsersController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('pets', 'PetsController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);

    // Requests routes
    Route::get('requests', 'RequestController@index')->name('requests.index');
    Route::put('requests/{booking}', 'RequestController@update')->name('requests.update');

    // Booking routes (Frontend)
    registerResourceRoutes('bookings', 'BookingController', [ // Controller is App\Http\Controllers\Frontend\BookingController due to group namespace
        'storeMedia' => true, 
        'storeCKEditorImages' => true,
        'additionalPutRoutes' => ['{booking}/complete' => 'complete'],
    ]);

    // Pet Reviews (Frontend)
    Route::prefix('pet-reviews')->name('pet-reviews.')->group(function () {
        Route::get('create/{booking}', 'PetReviewsController@create')->name('create'); // frontend.pet-reviews.create
        Route::post('store', 'PetReviewsController@store')->name('store');          // frontend.pet-reviews.store
        
        // For PetReviews: $name is empty string for correct route naming within the group.
        // massDestroy path will be 'destroy', name '.massDestroy' -> 'frontend.pet-reviews.massDestroy'
        // resource path '/', names like 'index' -> 'frontend.pet-reviews.index'
        registerResourceRoutes('', 'PetReviewsController', [ 
            'customResourceName' => '/',      // Route::resource('/', ...) results in /pet-reviews/
            'massDestroy' => true,            // DELETE /pet-reviews/destroy
            'except' => ['create', 'store'],  // Handled above
            'names' => [ 'index' => 'index', 'show' => 'show', 'edit' => 'edit', 'update' => 'update', 'destroy' => 'destroy'],
        ]);
    });

    registerResourceRoutes('chats', 'ChatController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('user-alerts', 'UserAlertsController', [ // massDestroy is true by default
        'except' => ['edit', 'update'], // No 'read' route for frontend
    ]);
    registerResourceRoutes('supports', 'SupportController', [
        'storeMedia' => true, 'storeCKEditorImages' => true,
    ]);
    registerResourceRoutes('email-logs', 'EmailLogController');
    registerResourceRoutes('spam-ips', 'SpamIpController');

    Route::get('frontend/profile', 'ProfileController@index')->name('profile.index');
    Route::post('frontend/profile', 'ProfileController@update')->name('profile.update');
    Route::post('frontend/profile/destroy', 'ProfileController@destroy')->name('profile.destroy');
    Route::post('frontend/profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/toggle-two-factor', 'ProfileController@toggleTwoFactor')->name('profile.toggle-two-factor');

    Route::get('credit-logs', [App\Http\Controllers\Frontend\CreditLogController::class, 'index'])->name('credit-logs.index');

    // Credit purchase routes
    Route::get('credits/purchase', [CreditPurchaseController::class, 'showPurchaseForm'])->name('credits.purchase');
    Route::post('credits/checkout', [CreditPurchaseController::class, 'createCheckoutSession'])->name('credits.checkout');
    Route::get('credits/success', [CreditPurchaseController::class, 'handleSuccess'])->name('credits.success');

    // Subscription routes
    Route::get('subscription', 'SubscriptionController@showSubscriptionPage')->name('subscription.index');
    Route::post('subscription/checkout', 'SubscriptionController@createCheckoutSession')->name('subscription.checkout');
    Route::get('subscription/success', 'SubscriptionController@handleSuccess')->name('subscription.success');
    Route::post('subscription/cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
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
