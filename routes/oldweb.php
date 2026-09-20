<?php

use Illuminate\Support\Facades\Http;

use App\Http\Controllers\{
    AccessoryFrontController,
    HomeController,
    ProductController,
    CartController,
    CheckoutController,
    WishlistController,
    ReviewController,
    TryonController,
    AccountController,
    LanguageController,
    BrandController,
    ContactController,
    ContactLensFrontController,
    OrderController,
    PaymentController,
    ProfileController,
    SocialiteController,
    StoreController,
    OtoShippingController
};
use App\Http\Controllers\Admin\{
    AccessoryController,
    DashboardController as AdminDashboard,
    ProductController as AdminProduct,
    BrandController as AdminBrand,
    CategoryController as AdminCategory,
    OrderController as AdminOrder,
    AdminUserController as AdminUser,
    BannerController as AdminBanner,
    StoreController as AdminStore,
    ProfileController as AdminProfile,
    CategoryController,
    ContactLensController,
    ContactMessageController,
    TryonFileController as AdminTryonFile,
    ReviewController as AdminReview,
    CouponController as AdminCoupon,
    DashboardController,
    TryonFileController,
    UserController,
    NotificationController as AdminNotification,
    SettingsController
};
use App\Http\Controllers\{
    ReviewController as ClientReview,
    OrderController as ClientOrder,
    PixelController
};
use Illuminate\Support\Facades\Route;


// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/filter-by-category', [HomeController::class, 'filterByCategory'])->name('home.filter');

// Google Auth
Route::get('login/google', [SocialiteController::class, 'googleLogin'])->name('login.google');
Route::get('login/google/callback', [SocialiteController::class, 'googleCallback'])->name('login.google.callback');

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Products
Route::get('/products/sunglasses', [ProductController::class, 'sunglasses'])->name('products.sunglasses');
Route::get('/products/eyeglasses', [ProductController::class, 'eyeglasses'])->name('products.eyeglasses');
Route::get('/products/clip-ons', [ProductController::class, 'clipons'])->name('products.clipon');
Route::get('/products/contact-lenses', [ContactLensFrontController::class, 'index'])->name('products.contact-lenses');
Route::get('/products/contact-lenses/{contactLens}', [ContactLensFrontController::class, 'show'])->name('products.show_lenses');
Route::get('/products/accessories', [AccessoryFrontController::class, 'index'])->name('products.accessories');
Route::get('/products/accessories/{accessory}', [AccessoryFrontController::class, 'show'])->name('products.accessories_show');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category');
    Route::get('/brand/{slug}', [ProductController::class, 'brand'])->name('brand');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
});

// Brands
Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
Route::get('/brand/{slug}', [BrandController::class, 'show'])->name('brand');

// Stores
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/stores/{id}', [StoreController::class, 'show'])->name('stores.show');
Route::get('/api/stores', [StoreController::class, 'getStoresJson'])->name('stores.json');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// Virtual Try-On
Route::prefix('tryon')->name('tryon.')->group(function () {
    Route::get('/{productId}', [TryonController::class, 'show'])->name('show');
    Route::get('/data/{productId}', [TryonController::class, 'getData'])->name('data');
});

// Contact
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

// Order Tracking (Public)
Route::get('/order/{order}/track', [OrderController::class, 'track'])->name('orders.track');

//  OTO SHIPPING - PUBLIC TRACKING
Route::get('/track/oto/{otoId}', [OtoShippingController::class, 'trackShipment'])
    ->name('shipping.track.oto');

//  TELR PAYMENT - CALLBACKS (NO AUTH)
Route::prefix('payment/telr')->name('payment.telr.')->group(function () {
    Route::get('/callback', [PaymentController::class, 'paymentCallback'])->name('callback');
    Route::get('/cancel', [PaymentController::class, 'paymentCancel'])->name('cancel');
    Route::get('/decline', [PaymentController::class, 'paymentDecline'])->name('decline');
});

Route::post('/webhooks/oto', [OtoShippingController::class, 'webhook'])
    ->name('webhooks.oto');

// AUTHENTICATED ROUTES (CLIENT)

Route::middleware(['auth'])->group(function () {

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');

        //Telr Return Routes
        Route::get('/telr/callback', [CheckoutController::class, 'telrCallback'])->name('telr.callback');
        Route::get('/telr/cancel',   [CheckoutController::class, 'telrCancel'])->name('telr.cancel');
        Route::get('/telr/decline',  [CheckoutController::class, 'telrDecline'])->name('telr.decline');
    });

    // Payment Routes (Authenticated)
    // Route::prefix('payment')->name('payment.')->group(function () {
    //     Route::get('/telr/callback', [PaymentController::class, 'telrCallback'])
    //         ->name('payment.telr.callback');

    //     Route::get('/telr/cancel', [PaymentController::class, 'telrCancel'])
    //         ->name('payment.telr.cancel');

    //     Route::get('/telr/decline', [PaymentController::class, 'telrDecline'])
    //         ->name('payment.telr.decline');

    //     Route::post('/telr/initiate/{order}', [PaymentController::class, 'initiatePayment'])
    //         ->name('payment.telr.initiate');
    //     Route::get('/{order}', [PaymentController::class, 'show'])->name('show');
    //     Route::post('/{order}/process', [PaymentController::class, 'process'])->name('process');
    //     Route::get('/{payment}/success', [PaymentController::class, 'success'])->name('success');
    //     Route::get('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');

    //     // Telr - Initiate Payment (Authenticated)
    //     Route::post('/telr/initiate/{order}', [PaymentController::class, 'initiatePayment'])
    //         ->name('telr.initiate');
    // });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
        Route::post('/clear', [WishlistController::class, 'clear'])->name('clear');
    });

    // CLIENT ORDERS
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [ClientOrder::class, 'index'])->name('index');
        Route::get('/{order}', [ClientOrder::class, 'show'])->name('show');
        Route::post('/', [ClientOrder::class, 'store'])->name('store');
        Route::post('/{order}/payment-callback', [ClientOrder::class, 'paymentCallback'])->name('payment.callback');
    });

    // My Orders (Alternative route)
    Route::get('/my-orders', [ClientOrder::class, 'index'])->name('my-orders');

    // CLIENT REVIEWS
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ClientReview::class, 'store'])->name('store');
        Route::get('/create/{order}', [ClientReview::class, 'create'])->name('create');
    });

    // User Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{id}', [AccountController::class, 'orderShow'])->name('orders.show');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    });

    // Profile Routes
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [AccountController::class, 'profile'])->name('profile.edit');
    Route::put('/profile/update', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [AccountController::class, 'destroy'])->name('profile.destroy');


});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('pixels', PixelController::class);
});

// ADMIN ROUTES

Route::middleware(['auth', 'role:Admin|User'])->prefix('admin')->name('admin.')->group(function () {

    // DASHBOARD - Accessible à tous les admins/users
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // PROFIL - Accessible à tous
    Route::prefix('profile')->group(function () {
        Route::get('/', [AdminProfile::class, 'index'])->name('profile');
        Route::put('/', [AdminProfile::class, 'update'])->name('profile.update');
        // Update password
        Route::put('/password', [AdminProfile::class, 'updatePassword'])
            ->name('profile.password');
    });

    // PARAMÈTRES - Admin uniquement
    Route::middleware(['role:Admin'])->prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::put('/', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotification::class, 'index'])->name('index');
        Route::get('/{id}/open', [AdminNotification::class, 'open'])
            ->name('open');
        Route::post('/{id}/read', [AdminNotification::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [AdminNotification::class, 'markAllAsRead'])->name('readAll');
        Route::delete('/{id}', [AdminNotification::class, 'destroy'])->name('destroy');
    });
    // PRODUITS - Permission requise
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('products', AdminProduct::class);
        Route::post('products/{product}/set-primary-image', [AdminProduct::class, 'setPrimaryImage'])
            ->name('products.set-primary');
    });
    Route::prefix('contact-lenses')->name('contact-lenses.')->middleware(['permission:manage_products'])->group(function () {
        Route::get('/', [ContactLensController::class, 'index'])->name('index');
        Route::get('/create', [ContactLensController::class, 'create'])->name('create');
        Route::post('/', [ContactLensController::class, 'store'])->name('store');
        Route::get('/{contact_lens}/edit', [ContactLensController::class, 'edit'])->name('edit');
        Route::put('/{contact_lens}', [ContactLensController::class, 'update'])->name('update');
        Route::delete('/{contact_lens}', [ContactLensController::class, 'destroy'])->name('destroy');
    });


    // ACCESSOIRES - Permission requise
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('accessories', AccessoryController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });


    // MARQUES - Permission requise
    Route::middleware(['permission:manage_brands'])->group(function () {
        Route::resource('brands', AdminBrand::class);
    });

    // CATÉGORIES - Permission requise
    Route::middleware(['permission:manage_categories'])->group(function () {
        Route::resource('categories', AdminCategory::class);
    });

    // COMMANDES - Permission requise
    Route::middleware(['permission:view_orders'])->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrder::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrder::class, 'show'])->name('show');
        Route::put('/{order}', [AdminOrder::class, 'update'])->name('update');
        Route::post('/{order}/update-status', [AdminOrder::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/approve', [AdminOrder::class, 'approve'])->name('approve');
        Route::post('/{order}/ship', [AdminOrder::class, 'markAsShipped'])->name('ship');
        Route::post('/{order}/deliver', [AdminOrder::class, 'markAsDelivered'])->name('deliver');
        Route::post('/{order}/cancel', [AdminOrder::class, 'cancel'])->name('cancel');
        Route::get('/{order}/print', [AdminOrder::class, 'printInvoice'])->name('print');
        Route::delete('/{order}', [AdminOrder::class, 'destroy'])->name('destroy');
    });

    // Users
    Route::middleware(['permission:manage_users'])->group(function () {
        Route::resource('users', AdminUser::class);
        Route::delete('users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
    });

    // Clients
    Route::middleware(['permission:manage_clients'])->group(function () {
        Route::get('/clients', [AdminUser::class, 'clients'])
            ->name('clients.index');
    });

    // BANNIÈRES - Permission requise
    Route::middleware(['permission:manage_banners'])->group(function () {
        Route::resource('banners', AdminBanner::class);
    });

    // ESSAYAGE VIRTUEL - Permission requise
    Route::middleware(['permission:manage_tryon'])->prefix('tryon')->name('tryon.')->group(function () {
        Route::get('/', [TryonFileController::class, 'index'])->name('index');
        Route::get('/create', [TryonFileController::class, 'create'])->name('create');
        Route::post('/', [TryonFileController::class, 'store'])->name('store');
        Route::get('/{tryonFile}/edit', [TryonFileController::class, 'edit'])->name('edit');
        Route::put('/{tryonFile}', [TryonFileController::class, 'update'])->name('update');
        Route::delete('/{tryonFile}', [TryonFileController::class, 'destroy'])->name('destroy');
        Route::get('/{tryonFile}/calibrate', [TryonFileController::class, 'calibrate'])->name('calibrate');
        Route::put('/{tryonFile}/calibrate', [TryonFileController::class, 'updateCalibration'])->name('updateCalibration');
    });

    // AVIS - Permission requise
    Route::middleware(['permission:manage_reviews'])->prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReview::class, 'index'])->name('index');
        Route::get('/{review}', [AdminReview::class, 'show'])->name('show');
        Route::post('/{review}/approve', [AdminReview::class, 'approve'])->name('approve');
        Route::delete('/{review}', [AdminReview::class, 'destroy'])->name('destroy');
    });

    // MESSAGES DE CONTACT - Permission requise
    Route::middleware(['permission:manage_contacts'])->prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('index');
        Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
    });

    // MAGASINS - Permission requise
    Route::middleware(['permission:manage_stores'])->group(function () {
        Route::resource('stores', AdminStore::class)->names('stores');
    });

    // EXPÉDITION OTO - Permission requise
    Route::middleware(['role:Admin'])->prefix('shipping/oto')->name('oto.')->group(function () {
        Route::post('/orders/{order}/shipment', [OtoShippingController::class, 'createShipment'])
            ->name('createShipment');
        Route::post('/shipment/cancel', [OtoShippingController::class, 'cancelShipment'])
            ->name('cancelShipment');
    });
});

// AUTH ROUTES

Route::get('/auth', function () {
    return view('auth.auth');
})->middleware('guest')->name('auth');

// Authentication Routes (Laravel Breeze)
require __DIR__ . '/auth.php';

Route::get('/policy', function () {
    return view('policy');
})->name('policy');
Route::view('/about-us', 'about')->name('about');




Route::middleware(['auth', 'role:admin,user'])->get('/admin-test', function () {
    return 'ACCESS OK';
});
