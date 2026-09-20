<?php

namespace App\Mail\Admin;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewReviewAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function build()
    {
        return $this->subject("⭐ Nouvel avis client - {$this->review->rating} étoiles")
                    ->markdown('emails.admin.new-review');
    }
}
