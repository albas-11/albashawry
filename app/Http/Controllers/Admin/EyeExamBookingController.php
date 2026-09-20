<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EyeExamBooking;
use App\Models\Store;
use Illuminate\Http\Request;

class EyeExamBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = EyeExamBooking::with(['store', 'order', 'user'])->latest();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($storeId = $request->get('store_id')) {
            $query->where('store_id', $storeId);
        }

        if ($date = $request->get('date')) {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->paginate(20)->withQueryString();

        $stores = Store::orderBy('id')->get();

        $counts = [
            'all'       => EyeExamBooking::count(),
            'pending'   => EyeExamBooking::where('status', 'pending')->count(),
            'confirmed' => EyeExamBooking::where('status', 'confirmed')->count(),
            'done'      => EyeExamBooking::where('status', 'done')->count(),
            'cancelled' => EyeExamBooking::where('status', 'cancelled')->count(),
        ];

        return view('admin.eye_exam_bookings.index', compact('bookings', 'stores', 'counts'));
    }

    public function updateStatus(Request $request, EyeExamBooking $booking)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,done,cancelled'],
        ]);

        $booking->update(['status' => $data['status']]);

        return back()->with('success', __('admin.Booking status updated.'));
    }

    public function destroy(EyeExamBooking $booking)
    {
        $booking->delete();

        return back()->with('success', __('admin.Booking deleted.'));
    }
}
