<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusGlobal extends Model
{
    use HasFactory;

    protected $table = 'status_global';

    protected $fillable = [
        'message',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
