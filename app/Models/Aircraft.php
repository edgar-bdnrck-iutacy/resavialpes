<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;
use Carbon\Carbon;


class Aircraft extends Model
{
    /**
     * Nom exact de la table en base
     */
    protected $table = 'aircraft';

    /**
     * Champs modifiables
     */
    protected $fillable = [
        'registration',
        'model',
        'status',
        'potentiel_restant',
    ];

    /**
     * ------------------------------------------------------------------
     * RELATIONS
     * ------------------------------------------------------------------
     */

    /**
     * Réservations associées à cet avion
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'aircraft_id');
    }

    /**
     * ------------------------------------------------------------------
     * LOGIQUE MÉTIER
     * ------------------------------------------------------------------
     */

    /**
     * Retourne la prochaine réservation future de l'avion
     * (null s'il n'y en a aucune)
     */
    public function prochaineReservation()
    {
        return $this->reservations()
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->first();
    }

    /**
     * Nombre d'heures pendant lesquelles l'avion est encore disponible
     *
     * - 0    : avion non disponible (statut ≠ "Disponible")
     * - null : avion disponible sans réservation prévue
     * - int  : nombre d'heures avant la prochaine réservation
     */
    public function heuresDisponibles(): ?int
    {
        // Si l'avion n'est pas disponible, aucune disponibilité
        if ($this->status !== 'Disponible') {
            return 0;
        }

        $prochaineReservation = $this->prochaineReservation();

        // Aucune réservation future → disponible indéfiniment
        if ($prochaineReservation === null) {
            return null;
        }

        return now()->diffInHours($prochaineReservation->starts_at);
    }

    public function userQualifications()
    {
        return $this->hasMany(\App\Models\UserAircraftQualification::class, 'aircraft_id');
    }

}
