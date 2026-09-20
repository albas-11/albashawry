<?php

namespace Tests\Feature;

use App\Models\EyeExamBooking;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EyeExamBookingTest extends TestCase
{
    use DatabaseTransactions;

    private Store $store;
    private Product $product;
    private \App\Models\User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::create([
            'name'      => ['en' => 'Test Branch', 'ar' => 'فرع الاختبار'],
            'address'   => ['en' => 'Test street', 'ar' => 'شارع الاختبار'],
            'city'      => 'Riyadh',
            'country'   => 'SA',
            'latitude'  => 24.7136,
            'longitude' => 46.6753,
            'phone'     => '0110000000',
        ]);

        $brand = \App\Models\Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand-' . uniqid()]);
        $category = \App\Models\Category::create(['name' => 'Test Cat', 'slug' => 'test-cat-' . uniqid()]);

        $this->product = Product::create([
            'name'        => 'Test Frame',
            'sku'         => 'SKU-' . uniqid(),
            'slug'        => 'test-frame-' . uniqid(),
            'description' => 'Test frame description',
            'price'       => 300,
            'stock'       => 5,
            'brand_id'    => $brand->id,
            'category_id' => $category->id,
            'type'        => 'eyeglasses',
        ]);

        \Spatie\Permission\Models\Permission::findOrCreate('view_orders', 'web');
        $adminRole = \Spatie\Permission\Models\Role::findOrCreate('Admin', 'web');
        $adminRole->givePermissionTo('view_orders');

        $this->admin = \App\Models\User::create([
            'name'     => 'Test Admin',
            'email'    => 'admin-' . uniqid() . '@test.local',
            'password' => bcrypt('password'),
        ]);
        $this->admin->assignRole($adminRole);
    }

    public function test_guest_can_create_a_booking(): void
    {
        $store   = $this->store;
        $product = $this->product;

        $res = $this->postJson('/eye-exam-bookings', [
            'customer_name'  => 'محمد اليوبي',
            'customer_phone' => '0534473335',
            'store_id'       => $store->id,
            'booking_date'   => now()->addDay()->format('Y-m-d'),
            'booking_time'   => '10:30',
            'flow'           => 'book_eye_test',
            'product_id'     => $product?->id,
        ]);

        $res->assertOk()->assertJson(['ok' => true]);

        $booking = EyeExamBooking::find($res->json('booking_id'));
        $this->assertNotNull($booking);
        $this->assertSame('محمد اليوبي', $booking->customer_name);
        $this->assertSame('0534473335', $booking->customer_phone);
        $this->assertSame('10:30', $booking->booking_time);
        $this->assertSame('pending', $booking->status);
        $this->assertSame($store->id, $booking->store_id);
        $this->assertNotNull($booking->store_name);
    }

    public function test_name_and_phone_are_required(): void
    {
        $store = $this->store;

        $this->postJson('/eye-exam-bookings', [
            'store_id'     => $store->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '10:30',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['customer_name', 'customer_phone']);

        $this->postJson('/eye-exam-bookings', [
            'customer_name'  => 'A',
            'customer_phone' => '123',
            'store_id'       => $store->id,
            'booking_date'   => now()->addDay()->format('Y-m-d'),
            'booking_time'   => '10:30',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['customer_name', 'customer_phone']);
    }

    public function test_admin_bookings_page_lists_the_booking(): void
    {
        $store = $this->store;

        $booking = EyeExamBooking::create([
            'store_id'       => $store->id,
            'store_name'     => 'Test store',
            'customer_name'  => 'Sara Test',
            'customer_phone' => '0500000000',
            'flow'           => 'book_eye_test',
            'booking_date'   => now()->addDay()->format('Y-m-d'),
            'booking_time'   => '11:00',
            'status'         => 'pending',
        ]);

        $admin = $this->admin;

        $this->actingAs($admin)
            ->get(route('admin.eye-exam-bookings.index'))
            ->assertOk()
            ->assertSee('Sara Test')
            ->assertSee('0500000000');
    }

    public function test_admin_can_update_status_and_delete(): void
    {
        $store = $this->store;

        $booking = EyeExamBooking::create([
            'store_id'       => $store->id,
            'customer_name'  => 'Status Test',
            'customer_phone' => '0500000001',
            'flow'           => 'book_eye_test',
            'booking_date'   => now()->addDay()->format('Y-m-d'),
            'booking_time'   => '12:00',
            'status'         => 'pending',
        ]);

        $admin = $this->admin;

        $this->actingAs($admin)
            ->post(route('admin.eye-exam-bookings.update-status', $booking), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame('confirmed', $booking->fresh()->status);

        $this->actingAs($admin)
            ->delete(route('admin.eye-exam-bookings.destroy', $booking))
            ->assertRedirect();

        $this->assertNull(EyeExamBooking::find($booking->id));
    }

    public function test_checkout_links_existing_booking_to_the_order(): void
    {
        $store = $this->store;

        $booking = EyeExamBooking::create([
            'store_id'       => $store->id,
            'customer_name'  => 'Linked Customer',
            'customer_phone' => '0500000002',
            'flow'           => 'book_eye_test',
            'booking_date'   => now()->addDay()->format('Y-m-d'),
            'booking_time'   => '09:00',
            'status'         => 'pending',
        ]);

        $order = \App\Models\Order::create([
            'order_number'   => 'TEST-' . uniqid(),
            'user_id'        => $this->admin->id,
            'shipping_name'    => 'Fallback Name',
            'shipping_email'   => 'test@example.com',
            'shipping_phone'   => '0599999999',
            'shipping_address' => 'Test address',
            'shipping_city'    => 'Riyadh',
            'shipping_country' => 'SA',
            'shipping_zip'     => '00000',
            'subtotal'       => 100,
            'total'          => 100,
            'payment_method' => 'cod',
            'status'         => 'pending',
            'payment_status' => 'pending',
        ]);

        $item = \App\Models\OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->product->id,
            'product_name' => 'Test frame',
            'product_sku'  => 'TEST-SKU',
            'price'        => 100,
            'quantity'     => 1,
            'total'        => 100,
        ]);

        $payload = [
            'flow'       => 'book_eye_test',
            'store_id'   => $store->id,
            'booking_id' => $booking->id,
            'schedule'   => ['day' => now()->addDay()->format('Y-m-d'), 'slot' => '09:00'],
        ];

        $controller = app(\App\Http\Controllers\CheckoutController::class);
        $method = new \ReflectionMethod($controller, 'syncEyeExamBooking');
        $method->setAccessible(true);
        $method->invoke($controller, $order, $item, $payload);

        $booking->refresh();
        $this->assertSame($order->id, $booking->order_id);
        $this->assertSame($item->id, $booking->order_item_id);
        $this->assertSame('confirmed', $booking->status);
        // les coordonnées saisies par le client ne sont pas écrasées
        $this->assertSame('Linked Customer', $booking->customer_name);
        $this->assertSame(1, EyeExamBooking::where('order_id', $order->id)->count());
    }

    public function test_checkout_creates_booking_when_none_was_recorded(): void
    {
        $store = $this->store;

        $order = \App\Models\Order::create([
            'order_number'   => 'TEST-' . uniqid(),
            'user_id'        => $this->admin->id,
            'shipping_name'    => 'Fallback Name',
            'shipping_email'   => 'test@example.com',
            'shipping_phone'   => '0599999999',
            'shipping_address' => 'Test address',
            'shipping_city'    => 'Riyadh',
            'shipping_country' => 'SA',
            'shipping_zip'     => '00000',
            'subtotal'       => 100,
            'total'          => 100,
            'payment_method' => 'cod',
            'status'         => 'pending',
            'payment_status' => 'pending',
        ]);

        $item = \App\Models\OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->product->id,
            'product_name' => 'Test frame',
            'product_sku'  => 'TEST-SKU',
            'price'        => 100,
            'quantity'     => 1,
            'total'        => 100,
        ]);

        $payload = [
            'flow'     => 'store_measurement',
            'store_id' => $store->id,
            'schedule' => ['day' => now()->addDays(2)->format('Y-m-d'), 'slot' => '14:00'],
        ];

        $controller = app(\App\Http\Controllers\CheckoutController::class);
        $method = new \ReflectionMethod($controller, 'syncEyeExamBooking');
        $method->setAccessible(true);
        $method->invoke($controller, $order, $item, $payload);

        $created = EyeExamBooking::where('order_id', $order->id)->first();
        $this->assertNotNull($created);
        $this->assertSame('Fallback Name', $created->customer_name);
        $this->assertSame('0599999999', $created->customer_phone);
        $this->assertSame('store_measurement', $created->flow);
        $this->assertSame('14:00', $created->booking_time);
    }
}
