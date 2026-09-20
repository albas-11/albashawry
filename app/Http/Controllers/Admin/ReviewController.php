<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index()
    {
        $reviews = Review::with(['user', 'order', 'reviewable'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $review->load(['user', 'order', 'reviewable']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return redirect()->back()->with('success', __('Review successfully approved'));
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', __('Review deleted successfully'));
    }
}
