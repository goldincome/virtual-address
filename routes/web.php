<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OffDayController;
use App\Http\Controllers\Front\InvoiceController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\UserMailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailUsageController;
use App\Http\Controllers\Front\ContactUsController;
use App\Http\Controllers\Admin\OpeningDayController;
use App\Http\Controllers\Admin\MailSettingController;
use App\Http\Controllers\Admin\MeetingRoomController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\ConferenceRoomController;
use App\Http\Controllers\Admin\FeatureSettingController;
use App\Http\Controllers\Front\PaymentWebhookController;
use App\Http\Controllers\Front\VirtualAddressController;
use App\Http\Controllers\Admin\PlanRoomDiscountController;
use App\Http\Controllers\Front\MeetingRoomOrderController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Front\ConferenceRoomOrderController;
use App\Http\Controllers\Front\VirtualAddressOrderController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Front\MeetingRoomController as FrontMeetingRoomController;
use App\Http\Controllers\Front\ConferenceRoomController as FrontConferenceRoomController;

// Handles Stripe payments webhooks
Route::post('/stripe/webhook', [PaymentWebhookController::class, 'handleWebhook'])->name('cashier.webhook');

Route::middleware('web')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::resource('contact-us', ContactUsController::class)->only('index','store');
    Route::get('about-us', [PageController::class, 'aboutUs'])->name('about-us.index');
    Route::get('terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service.index');
    Route::get('privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy.index');

    Route::get('virtual-address/{slug}', [VirtualAddressController::class, 'show'])->name('virtual-address.show');
    Route::resource('virtual-address', VirtualAddressController::class)->except('show','destroy','update');
    Route::resource('meeting-rooms', FrontMeetingRoomController::class)->except('destroy','update');
    Route::get('/schedule/slots', [FrontConferenceRoomController::class, 'getTimeSlots'])->name('time.slots');
    Route::resource('conference-rooms', FrontConferenceRoomController::class)->except('destroy','update');
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/renew', [UserController::class, 'renew'])->name('subscription.renew');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('process.payment');
    Route::get('/payment/cancelled', [PaymentController::class, 'paymentCancelled'])->name('payment.cancelled');
    Route::get('/payment/pp/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('/payment/stp/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/checkout/success/{order}', [CartController::class, 'checkoutSuccess'])->name('checkout.success');
    Route::get('virtual-address-orders/{ref_no}', [VirtualAddressOrderController::class, 'showOrderDetail'])->name('virtual-address-orders.order-detail');
    Route::resource('virtual-address-orders', VirtualAddressOrderController::class);
    Route::resource('meeting-room-orders', MeetingRoomOrderController::class);
    Route::resource('conference-room-orders', ConferenceRoomOrderController::class);
    Route::resource('mails', UserMailController::class);
    Route::resource('invoices', InvoiceController::class)->only('index', 'show', 'print');
});

//Admin and Super Admin route
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('off-days', OffDayController::class);
    Route::resource('opening-days', OpeningDayController::class)->only(['index','store']);
    Route::get('/mails/search', [MailController::class, 'search'])->name('mails.search');
    Route::resource('mail-usages', MailUsageController::class)->except('destroy');
    Route::post('mail-usages/{usage}/mark-billed', [MailUsageController::class, 'markBilled'])->name('mail-usages.mark-billed');
    Route::resource('plan-room-discounts', PlanRoomDiscountController::class)->except(['show']);
    Route::resource('mails', MailController::class)->except(['edit', 'update']);
    Route::resource('mail-settings', MailSettingController::class);
    Route::resource('meeting-rooms', MeetingRoomController::class);
    Route::resource('conference-rooms', ConferenceRoomController::class);
    Route::resource('plans', PlanController::class);
    Route::resource('feature-settings', FeatureSettingController::class);
    Route::post('orders/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::resource('orders', OrderController::class);

    //Route::get('plan-feature/{plan}/features/create', [PlanFeatureController::class, 'create'])->name('features.create');
    //Route::post('plan-feature/{plan}/features', [PlanFeatureController::class, 'store'])->name('features.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','super_admin'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});
require __DIR__.'/auth.php';

require __DIR__.'/admin-auth.php';