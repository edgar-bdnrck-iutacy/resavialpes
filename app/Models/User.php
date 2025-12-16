<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\StatusUser;
use App\Models\Instructor;
use App\Models\Reservation;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Les rôles disponibles dans la plateforme.
     */
    public const ROLE_ELEVE       = 'Élève';
    public const ROLE_BREVETE     = 'Breveté';
    public const ROLE_INSTRUCTEUR = 'Instructeur';
    public const ROLE_ADMIN       = 'Admin';
    public const ROLE_MAINTENANCE = 'Maintenance';

    /**
     * Champs assignables en masse.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'login',
        'tel_pri',
        'tel_seg',
        'tel_mob',
    ];


    /**
     * Champs cachés dans les tableaux / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Retourne le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Statut personnalisé de l'utilisateur.
     */
    public function status()
    {
        return $this->hasOne(StatusUser::class);
    }

    /**
     * Profil instructeur (si role = instructeur).
     */
    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class);
    }

    /**
     * Réservations où l'utilisateur est pilote.
     */
    public function pilotReservations()
    {
        return $this->hasMany(Reservation::class, 'pilot_id');
    }

    /**
     * Réservations créées par cet utilisateur (admin, FI, élève).
     */
    public function createdReservations()
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function qualifications()
    {
        return $this->hasMany(\App\Models\UserQualification::class);
    }

    public function modelQualifications()
    {
        // Alias clair : qualifications = qualifications par modèle (table user_qualifications)
        return $this->hasMany(\App\Models\UserQualification::class, 'user_id');
    }

    /**
     * Statut personnel (message) affiché pour cet utilisateur.
     */
    public function statusUser()
    {
        return $this->hasOne(StatusUser::class, 'user_id');
    }

    public function aircraftQualifications()
    {
        return $this->hasMany(\App\Models\UserAircraftQualification::class, 'user_id');
    }
}
