<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class Store extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'address'];

    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'phone',
        'schedule',
    ];


    protected $casts = [
        'name' => 'array',
        'address' => 'array',
        'schedule' => 'array',
    ];

    // Get distance from a point (in kilometers)
    public function getDistanceFrom($latitude, $longitude)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    // Check if store is open now
public function isOpenNow()
{
    $schedule = $this->schedule;
    if (!$schedule || !isset($schedule['days'])) return false;

    $today = now()->format('Y-m-d');
    $nowTime = now()->format('H:i');

    $slots = $schedule['days'][$today]['slots'] ?? null;
    if (!$slots || !is_array($slots)) return false;

    // slot format can be: { "20:00": {cap, booked}, ... }
    // If the slot exists and has remaining capacity, consider it "open"
    if (array_key_exists($nowTime, $slots)) {
        $cap = (int)($slots[$nowTime]['cap'] ?? 0);
        $booked = (int)($slots[$nowTime]['booked'] ?? 0);
        return $cap > $booked;
    }

    return false;
}

public function getTodayHours()
{
    $schedule = $this->schedule;
    if (!$schedule || !isset($schedule['days'])) return __('Hours not available');

    $today = now()->format('Y-m-d');
    $slots = $schedule['days'][$today]['slots'] ?? null;

    if (!$slots || !is_array($slots) || count($slots) === 0) return __('Closed');

    $times = array_keys($slots);
    sort($times);

    return $times[0] . ' - ' . end($times);
}

}
