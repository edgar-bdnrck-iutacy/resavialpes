<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AircraftController extends Controller
{
    /**
     * Affiche la liste des aéronefs avec filtres et tri.
     */
    public function index(Request $request)
    {
        // Liste des modèles distincts pour le filtre
        $models = Aircraft::query()
            ->select('model')
            ->whereNotNull('model')
            ->distinct()
            ->orderBy('model')
            ->pluck('model');
    
        $query = Aircraft::query();
    
        // --- Filtres -------------------------------------------------
    
        if ($registration = $request->input('registration')) {
            $query->where('registration', 'ILIKE', '%' . $registration . '%');
        }
    
        if ($model = $request->input('model')) {
            $query->where('model', $model);
        }
    
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
    
        // --- Tri -----------------------------------------------------
        $sortable = ['registration', 'model', 'status', 'potentiel_restant'];
        $sortField = $request->input('sort', 'registration');
        $sortDir   = $request->input('dir', 'asc');

        if (!in_array($sortField, $sortable, true)) $sortField = 'registration';
        if (!in_array($sortDir, ['asc', 'desc'], true)) $sortDir = 'asc';

        if ($sortField === 'potentiel_restant') {
            $query->orderByRaw("potentiel_restant {$sortDir} NULLS LAST");
        } else {
            $query->orderBy($sortField, $sortDir);
        }
    
        $items = $query->get();

        if ($request->ajax()) {
            return view('admin.aircraft._list', [
                'aircraft' => $items,
                'sortField' => $sortField,
                'sortDir' => $sortDir,
            ]);
        }

        return view('admin.aircraft.index', [
            'aircraft'  => $query->get(),
            'models'    => $models,
            'filters'   => $request->only(['registration', 'model', 'status']),
            'sortField' => $sortField,
            'sortDir'   => $sortDir,
        ]);
    }
    /**
     * Détails d’un aéronef
     */
    public function show(Aircraft $aircraft)
    {
        $statusOptions = [
            'Disponible'   => 'Disponible',
            'Maintenance'  => 'Maintenance',
            'Indisponible' => 'Indisponible',
        ];

        return view('admin.aircraft.show', compact('aircraft', 'statusOptions'));
    }

    /**
     * Formulaire d’édition “sensible”
     */
    public function edit(Aircraft $aircraft)
    {
        return view('admin.aircraft.edit', compact('aircraft'));
    }

    /**
     * Enregistrer modifications “sensibles”
     */
    public function update(Request $request, Aircraft $aircraft)
    {
        $validated = $request->validate([
            'registration' => [
                'required', 'string', 'max:30',
                \Illuminate\Validation\Rule::unique('aircraft', 'registration')->ignore($aircraft->id),
            ],
            'model' => ['required', 'string', 'max:100'],
            'potentiel_restant' => ['nullable', 'numeric', 'min:0'],
        ]);

        $aircraft->update($validated);

        return redirect()
            ->route('admin.aircraft.show', $aircraft)
            ->with('success', 'Aéronef mis à jour.');
    }

    /**
     * Mise à jour rapide du statut (depuis la page show)
     */
    public function updateStatus(Request $request, Aircraft $aircraft)
    {
        $validated = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in(['Disponible', 'Maintenance', 'Indisponible'])],
        ]);

        $aircraft->status = $validated['status'];
        $aircraft->save();

        return back()->with('success', 'Statut mis à jour.');
    }

}
