<?php

namespace App\Notifications\Admin;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Customer Review',
            'message' => "{$this->review->user->name} left a review ({$this->review->rating}⭐)",
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'user_name' => $this->review->user->name,
            'type' => 'new_review',
            'url' => route('admin.reviews.show', $this->review->id),
            'icon' => 'star',
            'color' => 'warning'
        ];
    }
}
