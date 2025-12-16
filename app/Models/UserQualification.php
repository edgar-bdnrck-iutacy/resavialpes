<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQualification extends Model
{
    protected $table = 'user_qualifications';

    protected $fillable = [
        'user_id',
        'model',
        'level',
    ];

    protected $casts = [
        'level' => 'string',
    ];

}
