<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $table = 'availability_slots';

    protected $fillable = [
        'instructor_id',
        'aircraft_id',
        'starts_at',
        'ends_at',
        'kind',
        'reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }
}
