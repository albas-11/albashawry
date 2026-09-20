<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Admin\NewReviewNotification;
use App\Mail\Admin\NewReviewAdminMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Afficher le formulaire de création
     */
    public function create(Order $order)
    {
        // Vérifier que la commande appartient au client
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'You can only leave a review after receiving your order.');
        }

        $existingReview = Review::where('user_id', auth()->id())
            ->where('reviewable_type', Order::class)
            ->where('reviewable_id', $order->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('info', 'You have already left a review for this order.');
        }

        return view('client.reviews.create', compact('order'));
    }

    /**
     * Enregistrer un avis
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:3|max:1000',
            'reviewable_id' => 'required|integer',
            'reviewable_type' => 'required|in:product,contact_lens,accessory'
        ]);

        try {
            DB::beginTransaction();
            // Vérifier si un avis existe déjà
            $existingReview = Review::where('user_id', auth()->id())
                ->where('reviewable_type', $validated['reviewable_type'])
                ->where('reviewable_id', $validated['reviewable_id'])
                ->first();

            if ($existingReview) {
                return redirect()->back()->with('error', 'You have already left a review for this order.');
            }

            // Créer la review
            $review = Review::create([
                'user_id' => auth()->id(),
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'reviewable_id' => $validated['reviewable_id'],
                'reviewable_type' => $validated['reviewable_type'],
                'is_verified' => true,
                'is_approved' => true,
            ]);

            Log::info('Review successfully created', ['review_id' => $review->id]);

            // Notifier les admins
            $this->notifyAdmins($review);

            DB::commit();

            return redirect()->back()->with('success', 'Thank you for your feedback!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création review', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'An error has occurred. Please try again.')
                ->withInput();
        }
    }

    /**
     * Notifier les admins
     */
    private function notifyAdmins(Review $review)
    {
        try {
            $adminRole = \App\Models\Role::where('name', 'admin')->first();

            if (!$adminRole) {
                Log::warning('Admin role not found');
                return;
            }

            $admins = User::where('role_id', $adminRole->id)
                ->whereNotNull('email')
                ->get();

            Log::info('Admins found for notification', ['count' => $admins->count()]);

            if ($admins->isEmpty()) {
                Log::warning('No admin found with email');
                return;
            }

            // Notification database
            Notification::send($admins, new NewReviewNotification($review));

            // Email aux admins
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new NewReviewAdminMail($review));
                    Log::info('Email sent to admin', ['email' => $admin->email]);
                } catch (\Exception $e) {
                    Log::error('Error sending admin email', [
                        'email' => $admin->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error notification admins', [
                'error' => $e->getMessage(),
                'review_id' => $review->id
            ]);
        }
    }
}
