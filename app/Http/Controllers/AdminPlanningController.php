<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminPlanningController extends Controller
{
    public function day(Request $request)
    {
        // Admin-only (pour l’instant)
        abort_unless(auth()->check() && (auth()->user()->role ?? null) === 'Admin', 403);

        $dateStr = $request->query('date', now()->toDateString());
        $day = Carbon::parse($dateStr)->startOfDay();

        // Plage 07:00 -> 21:00
        $start = $day->copy()->setTime(7, 0, 0);
        $end   = $day->copy()->setTime(21, 0, 0);

        $rangeMinutes = $start->diffInMinutes($end); // 14h = 840 min

        $aircraft = Aircraft::query()
            ->orderBy('model')
            ->orderBy('registration')
            ->get();

        // Réservations du jour (vols + briefings) qui touchent l’intervalle [start, end]
        $reservations = Reservation::query()
            ->where(function ($q) use ($start, $end) {
                $q->where('starts_at', '<', $end)
                  ->where('ends_at', '>', $start);
            })
            ->whereIn('type', ['FLIGHT', 'BRIEFING'])
            ->get()
            ->groupBy('aircraft_id');

        // On transforme en "bars" positionnées en minutes (plus simple à afficher)
        $barsByAircraft = [];
        foreach ($aircraft as $a) {
            $bars = [];
            foreach (($reservations[$a->id] ?? collect()) as $r) {
                $s = Carbon::parse($r->starts_at);
                $e = Carbon::parse($r->ends_at);

                // clamp dans la plage
                if ($s->lt($start)) $s = $start->copy();
                if ($e->gt($end))   $e = $end->copy();

                $startMin = $start->diffInMinutes($s);
                $endMin   = $start->diffInMinutes($e);

                // sécurité
                if ($endMin <= $startMin) continue;

                $bars[] = [
                    'id' => $r->idreservation ?? $r->id ?? null,
                    'type' => $r->type,
                    'status' => $r->status,
                    'startMin' => $startMin,
                    'endMin' => $endMin,
                    'durationMin' => $endMin - $startMin,
                    'label' => $r->type === 'BRIEFING' ? 'Briefing' : 'Vol',
                ];
            }
            $barsByAircraft[$a->id] = $bars;
        }

        // Slots 15 min (affichage de l’échelle)
        $slotCount = intdiv($rangeMinutes, 15); // 56
        $hours = [];
        for ($h = 7; $h <= 21; $h++) {
            $hours[] = sprintf('%02d:00', $h);
        }

        return view('admin.planning.day', [
            'date' => $day->toDateString(),
            'prevDate' => $day->copy()->subDay()->toDateString(),
            'nextDate' => $day->copy()->addDay()->toDateString(),
            'aircraft' => $aircraft,
            'barsByAircraft' => $barsByAircraft,
            'slotCount' => $slotCount,
            'rangeMinutes' => $rangeMinutes,
            'hours' => $hours,
        ]);
    }
}
