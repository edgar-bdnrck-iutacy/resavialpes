<?php

namespace App\Services;

use App\Models\Aircraft;

class AlertService
{
    /**
     * Retourne toutes les alertes actives du système
     */
    public function all(): array
    {
        $alerts = [];

        $aircraft = Aircraft::all();

        foreach ($aircraft as $a) {

            // 🔴 Potentiel critique
            if (!is_null($a->potentiel_restant) && $a->potentiel_restant < 10) {
                $alerts[] = [
                    'id'      => "aircraft-{$a->id}-potentiel-critique",
                    'level'   => 'danger',
                    'title'   => 'Alerte critique',
                    'message' => "{$a->registration} ({$a->model}) : potentiel critique ({$a->potentiel_restant} h)",
                ];
            }

            // 🟠 Potentiel faible
            elseif (!is_null($a->potentiel_restant) && $a->potentiel_restant < 50) {
                $alerts[] = [
                    'id'      => "aircraft-{$a->id}-potentiel-faible",
                    'level'   => 'warning',
                    'title'   => 'Alerte',
                    'message' => "{$a->registration} ({$a->model}) : potentiel faible ({$a->potentiel_restant} h)",
                ];
            }

            // 🔵 Statut bloquant
            if (in_array($a->status, ['En maintenance', 'Défectueux'], true)) {
                $alerts[] = [
                    'id'      => "aircraft-{$a->id}-status-{$a->status}",
                    'level'   => 'info',
                    'title'   => 'Information',
                    'message' => "{$a->registration} ({$a->model}) : {$a->status}",
                ];
            }
        }

        return $alerts;
    }

    /**
     * Niveau de gravité max (pour la couleur de la cloche)
     */
    public function maxLevel(array $alerts): string
    {
        if (collect($alerts)->contains(fn($a) => $a['level'] === 'danger')) {
            return 'danger';
        }

        if (collect($alerts)->contains(fn($a) => $a['level'] === 'warning')) {
            return 'warning';
        }

        return 'info';
    }
}
