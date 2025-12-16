<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilot_id',
        'instructor_id',
        'aircraft_id',
        'starts_at',
        'ends_at',
        'status',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function pilot()
    {
        return $this->belongsTo(User::class, 'pilot_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
