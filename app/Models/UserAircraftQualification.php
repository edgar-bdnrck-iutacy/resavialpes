<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAircraftQualification extends Model
{
    protected $table = 'user_aircraft_qualifications';

    protected $fillable = [
        'user_id',
        'aircraft_id',
        'level',
        'notes',
        'decided_by_user_id',
        'decided_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }
}
