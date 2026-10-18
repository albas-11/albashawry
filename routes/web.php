<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\{
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
    AccessoryFrontController,
    OtoShippingController,
    PixelController,
    EyeExamBookingController,
    PageController as PageFrontController
};

use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboard,
    ProductController as AdminProduct,
    BrandController as AdminBrand,
    CategoryController as AdminCategory,
    OrderController as AdminOrder,
    AdminUserController as AdminUser,
    BannerController as AdminBanner,
    StoreController as AdminStore,
    ProfileController as AdminProfile,
    ContactLensController,
    ContactMessageController,
    TryonFileController,
    ReviewController as AdminReview,
    CouponController as AdminCoupon,
    NotificationController as AdminNotification,
    SettingsController,
    EyeExamBookingController as AdminEyeExamBooking,
    PageController as AdminPage
};

// Client aliases 
use App\Http\Controllers\{
    ReviewController as ClientReview,
    OrderController as ClientOrder
};

/*
    PUBLIC ROUTES
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/filter-by-category', [HomeController::class, 'filterByCategory'])->name('home.filter');

// Google Auth
Route::get('login/google', [SocialiteController::class, 'googleLogin'])->name('login.google');
Route::get('login/google/callback', [SocialiteController::class, 'googleCallback'])->name('login.google.callback');

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Products (Quick routes)
Route::get('/products/sunglasses', [ProductController::class, 'sunglasses'])->name('products.sunglasses');
Route::get('/products/eyeglasses', [ProductController::class, 'eyeglasses'])->name('products.eyeglasses');
Route::get('/products/clip-ons', [ProductController::class, 'clipons'])->name('products.clipon');

// Contact lenses
Route::get('/products/contact-lenses', [ContactLensFrontController::class, 'index'])->name('products.contact-lenses');
Route::get('/products/contact-lenses/{contactLens}', [ContactLensFrontController::class, 'show'])->name('products.show_lenses');

// Accessories
Route::get('/products/accessories', [AccessoryFrontController::class, 'index'])->name('products.accessories');
Route::get('/products/accessories/{accessory}', [AccessoryFrontController::class, 'show'])->name('products.accessories_show');

// Products (Main group)
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

// Free eye-exam booking (enregistré dès la confirmation dans la modale produit)
Route::post('/eye-exam-bookings', [EyeExamBookingController::class, 'store'])->name('eye-exam-bookings.store');

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

// OTO Shipping (Public)
Route::post('/shipping/calculate', [OtoShippingController::class, 'calculateShipping'])
    ->name('shipping.calculate');

Route::get('/track/oto/{otoId}', [OtoShippingController::class, 'trackShipment'])
    ->name('shipping.track.oto');

Route::post('/webhooks/oto', [OtoShippingController::class, 'webhook'])
    ->name('webhooks.oto');

Route::get('/checkout/tap-callback', [CheckoutController::class, 'tapCallback'])
    ->name('checkout.tap_callback');
    
// TELR Payment (Public callbacks)
Route::prefix('payment/telr')->name('payment.telr.')->group(function () {
    Route::get('/callback', [PaymentController::class, 'paymentCallback'])->name('callback');
    Route::get('/cancel',   [PaymentController::class, 'paymentCancel'])->name('cancel');
    Route::get('/decline',  [PaymentController::class, 'paymentDecline'])->name('decline');
});

Route::get('/checkout/telr/callback', [CheckoutController::class, 'telrCallback'])
    ->name('checkout.telr.callback');

Route::get('/checkout/telr/cancel', [CheckoutController::class, 'telrCancel'])
    ->name('checkout.telr.cancel');

Route::get('/checkout/telr/decline', [CheckoutController::class, 'telrDecline'])
    ->name('checkout.telr.decline');
 
/*
    AUTHENTICATED ROUTES (CLIENT)
*/
Route::middleware(['auth'])->group(function () {

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
    });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
        Route::post('/clear', [WishlistController::class, 'clear'])->name('clear');
    });

    // Client Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [ClientOrder::class, 'index'])->name('index');
        Route::get('/{order}', [ClientOrder::class, 'show'])->name('show');
        Route::post('/', [ClientOrder::class, 'store'])->name('store');
        Route::post('/{order}/payment-callback', [ClientOrder::class, 'paymentCallback'])->name('payment.callback');
    });

    Route::get('/my-orders', [ClientOrder::class, 'index'])->name('my-orders');

    // Client Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ClientReview::class, 'store'])->name('store');
        Route::get('/create/{order}', [ClientReview::class, 'create'])->name('create');
    });

    // Account
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

/*
    ADMIN ROUTES
*/

// Pixels (Admin only)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('pixels', PixelController::class);
});

// Admin Panel (Admin|User)
Route::middleware(['auth', 'role:Admin|User'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [AdminProfile::class, 'index'])->name('profile');
        Route::put('/', [AdminProfile::class, 'update'])->name('profile.update');
        Route::put('/password', [AdminProfile::class, 'updatePassword'])->name('profile.password');
    });

    // Settings (Admin only)
    Route::middleware(['role:Admin'])->prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::put('/', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/test-oto', [SettingsController::class, 'testOto'])->name('settings.test-oto');
    });

    // Pages du site (About Us / Policy / Terms of Use)
    Route::middleware(['role:Admin'])->prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [AdminPage::class, 'index'])->name('index');
        Route::get('/{page}/edit', [AdminPage::class, 'edit'])->name('edit');
        Route::put('/{page}', [AdminPage::class, 'update'])->name('update');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AdminNotification::class, 'index'])->name('index');
        Route::get('/{id}/open', [AdminNotification::class, 'open'])->name('open');
        Route::post('/{id}/read', [AdminNotification::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [AdminNotification::class, 'markAllAsRead'])->name('readAll');
        Route::delete('/{id}', [AdminNotification::class, 'destroy'])->name('destroy');
    });

    // Products
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('products', AdminProduct::class);
        Route::post('products/{product}/set-primary-image', [AdminProduct::class, 'setPrimaryImage'])
            ->name('products.set-primary');
    });

    // Contact Lenses (Admin)
    Route::prefix('contact-lenses')->name('contact-lenses.')->middleware(['permission:manage_products'])->group(function () {
        Route::get('/', [ContactLensController::class, 'index'])->name('index');
        Route::get('/create', [ContactLensController::class, 'create'])->name('create');
        Route::post('/', [ContactLensController::class, 'store'])->name('store');
        Route::get('/{contact_lens}/edit', [ContactLensController::class, 'edit'])->name('edit');
        Route::put('/{contact_lens}', [ContactLensController::class, 'update'])->name('update');
        Route::delete('/{contact_lens}', [ContactLensController::class, 'destroy'])->name('destroy');
    });
    Route::delete('/admin/contact-lenses/{contact_lens}/images/{image}', [ContactLensController::class, 'destroyImage'])
        ->name('contact-lenses.images.destroy');

    // Accessories
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('accessories', \App\Http\Controllers\Admin\AccessoryController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // Brands
    Route::middleware(['permission:manage_brands'])->group(function () {
        Route::resource('brands', AdminBrand::class);
    });

    // Categories
    Route::middleware(['permission:manage_categories'])->group(function () {
        Route::resource('categories', AdminCategory::class);
    });

    // Orders
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
        Route::get('/clients', [AdminUser::class, 'clients'])->name('clients.index');
    });

    // Banners
    Route::middleware(['permission:manage_banners'])->group(function () {
        Route::resource('banners', AdminBanner::class);
    });

    // Tryon Files
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

    // Reviews
    Route::middleware(['permission:manage_reviews'])->prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReview::class, 'index'])->name('index');
        Route::get('/{review}', [AdminReview::class, 'show'])->name('show');
        Route::post('/{review}/approve', [AdminReview::class, 'approve'])->name('approve');
        Route::delete('/{review}', [AdminReview::class, 'destroy'])->name('destroy');
    });

    // Contact Messages
    Route::middleware(['permission:manage_contacts'])->prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('index');
        Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
    });

    // Stores
    Route::middleware(['permission:manage_stores'])->group(function () {
        Route::resource('stores', AdminStore::class)->names('stores');
    });

    // Eye exam bookings
    Route::middleware(['permission:view_orders'])->prefix('eye-exam-bookings')->name('eye-exam-bookings.')->group(function () {
        Route::get('/', [AdminEyeExamBooking::class, 'index'])->name('index');
        Route::post('/{booking}/status', [AdminEyeExamBooking::class, 'updateStatus'])->name('update-status');
        Route::delete('/{booking}', [AdminEyeExamBooking::class, 'destroy'])->name('destroy');
    });

    Route::middleware(['role:Admin'])->prefix('shipping/oto')->name('oto.')->group(function () {
        Route::post('/orders/{order}/shipment', [OtoShippingController::class, 'createShipment'])
            ->name('createShipment');

        Route::get('/orders/{order}/label', [OtoShippingController::class, 'fetchLabel'])
            ->name('fetchLabel');

        Route::post('/orders/{order}/oto-id', [OtoShippingController::class, 'saveOtoId'])
            ->name('saveOtoId');

        Route::post('/orders/{order}/reset', [OtoShippingController::class, 'resetShipment'])
            ->name('resetShipment');

        Route::post('/shipment/cancel', [OtoShippingController::class, 'cancelShipment'])
            ->name('cancelShipment');
    });
});

/*
    AUTH PAGES + STATIC
*/

Route::get('/auth', function () {
    return view('auth.auth');
})->middleware('guest')->name('auth');

// Laravel Breeze
require __DIR__ . '/auth.php';

// Pages éditables depuis l'admin (les noms de routes restent inchangés)
Route::get('/about-us', [PageFrontController::class, 'show'])->defaults('slug', 'about')->name('about');
Route::get('/policy', [PageFrontController::class, 'show'])->defaults('slug', 'policy')->name('policy');
Route::get('/terms-of-use', [PageFrontController::class, 'show'])->defaults('slug', 'terms')->name('terms');

// Debug test
Route::middleware(['auth', 'role:admin,user'])->get('/admin-test', function () {
    return 'ACCESS OK';
});

Route::get('/api/stores', [StoreController::class, 'getStoresJson'])->name('stores.json');
// Sitemap XML routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])
    ->name('sitemap.index');

Route::get('/pages.xml', [\App\Http\Controllers\SitemapController::class, 'pages'])
    ->name('sitemap.pages');

Route::get('/categories.xml', [\App\Http\Controllers\SitemapController::class, 'categories'])
    ->name('sitemap.categories');

Route::get('/brands.xml', [\App\Http\Controllers\SitemapController::class, 'brands'])
    ->name('sitemap.brands');

Route::get('/products-{page}.xml', [\App\Http\Controllers\SitemapController::class, 'products'])
    ->whereNumber('page')
    ->name('sitemap.products');