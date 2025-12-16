<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserQualification;
use App\Models\Aircraft;
use App\Models\UserAircraftQualification;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $roleFilter = trim((string) $request->query('role', ''));

        $sortField = $request->query('sort', 'last_name');
        $sortDir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Tri autorisé uniquement sur ces colonnes
        $sortable = ['first_name', 'last_name', 'role'];

        $roles = [
            'Admin',
            'Breveté',
            'Élève',
            'Instructeur',
            'Maintenance',
        ];

        if (!in_array($sortField, $sortable, true)) {
            $sortField = 'last_name';
        }

        $query = User::query();
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('first_name', 'ILIKE', "%{$q}%")
                   ->orWhere('last_name', 'ILIKE', "%{$q}%")
                   ->orWhere('email', 'ILIKE', "%{$q}%")
                   ->orWhere('tel_pri', 'ILIKE', "%{$q}%")
                   ->orWhere('tel_seg', 'ILIKE', "%{$q}%")
                   ->orWhere('tel_mob', 'ILIKE', "%{$q}%");
            });
        }

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        if ($sortField === 'last_name') {
            $query->orderBy('last_name', $sortDir)->orderBy('first_name', $sortDir);
        } elseif ($sortField === 'first_name') {
            $query->orderBy('first_name', $sortDir)->orderBy('last_name', $sortDir);
        } else { // role
            $query->orderBy('role', $sortDir)
                  ->orderBy('last_name', 'asc')
                  ->orderBy('first_name', 'asc');
        }

        $users = $query->get();

        if (request()->ajax()) {
            return view('admin.users._list', [
                'users' => $users,
                'sortField' => $sortField,
                'sortDir' => $sortDir,
            ]);
        }

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'roleFilter' => $roleFilter,
            'roles' => $roles,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
        ]);
    }

    public function show(\App\Models\User $user)
    {
        // Rôles fixes (ne dépendent pas de la DB)
        $roles = ['Admin', 'Breveté', 'Élève', 'Instructeur', 'Maintenance'];

        // Liste des modèles existants (depuis aircraft)
        $availableModels = \App\Models\Aircraft::query()
            ->select('model')
            ->whereNotNull('model')
            ->where('model', '!=', '')
            ->distinct()
            ->orderBy('model')
            ->pluck('model');

        // Qualifications par modèle pour cet utilisateur
        $user->load('modelQualifications');
        $userQualifsByModel = $user->modelQualifications->keyBy('model');

        $modelLevels = [];
        foreach ($availableModels as $m) {
            $modelLevels[$m] = optional($userQualifsByModel->get($m))->level ?? 'INSTRUCTOR_ONLY';
        }

        $lastQualificationUpdateRaw = UserQualification::where('user_id', $user->id)->max('updated_at');
            
        // max() peut renvoyer une string → on convertit proprement
        $lastQualificationUpdate = $lastQualificationUpdateRaw
            ? Carbon::parse($lastQualificationUpdateRaw)
            : null;
            
        // $user->updated_at est déjà un Carbon
        $lastUpdateAt = $user->updated_at;
            
        if ($lastQualificationUpdate && $lastQualificationUpdate->greaterThan($lastUpdateAt)) {
            $lastUpdateAt = $lastQualificationUpdate;
        }

        return view('admin.users.show', [
            'user' => $user,
            'roles' => $roles,
            'availableModels' => $availableModels,
            'userQualifsByModel' => $userQualifsByModel,
            'modelLevels' => $modelLevels,
            'lastUpdateAt' => $lastUpdateAt,
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role'    => ['required', 'string', 'max:50'],
            'tel_pri' => ['nullable', 'string', 'max:32'],
            'tel_seg' => ['nullable', 'string', 'max:32'],
            'tel_mob' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update($validated);

        // Sync table instructors selon le rôle
        if (($validated['role'] ?? null) === 'Instructeur') {
            Instructor::updateOrCreate(
                ['user_id' => $user->id],
                ['is_active' => true]
            );
        } else {
            Instructor::where('user_id', $user->id)->update(['is_active' => false]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour.');
    }
    
    public function updateRole(Request $request, \App\Models\User $user)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['Admin', 'Instructeur'], true)) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('error', "Vous n'avez pas la permission de modifier les qualifications.");
        }

        $roles = ['Admin', 'Instructeur', 'Breveté', 'Élève', 'Maintenance'];
    
        $validated = $request->validate([
            'role' => ['required', 'string', \Illuminate\Validation\Rule::in($roles)],
        ]);
    
        $user->update(['role' => $validated['role']]);
    
        // Sync table instructors selon le rôle
        if ($validated['role'] === 'Instructeur') {
            Instructor::updateOrCreate(
                ['user_id' => $user->id],
                ['is_active' => true]
            );
        } else {
            Instructor::where('user_id', $user->id)->update(['is_active' => false]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Rôle mis à jour.');
    }

    public function updateQualifications(Request $request, \App\Models\User $user)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['Admin', 'Instructeur'], true)) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('error', "Vous n'avez pas la permission de modifier les qualifications.");
        }

        $availableModels = \App\Models\Aircraft::query()
            ->whereNotNull('model')
            ->where('model', '!=', '')
            ->distinct()
            ->pluck('model')
            ->values()
            ->all();

        $validated = $request->validate([
            'models' => ['array'],
            'models.*' => ['string', \Illuminate\Validation\Rule::in($availableModels)],
        ]);

        $models = $validated['models'] ?? [];

        // Remplace tout (simple et fiable)
        $user->qualifications()->delete();

        foreach ($models as $m) {
            $user->qualifications()->create(['model' => $m]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Qualifications mises à jour.');
    }

    public function adminPasswordReset(User $user)
    {
        // Token brut (à mettre dans l'URL) + token hashé (à stocker)
        $rawToken = Str::random(64);
        $hashedToken = Hash::make($rawToken);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashedToken, 'created_at' => now()]
        );

        $link = route('password.reset.form', ['token' => $rawToken]) . '?email=' . urlencode($user->email);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Lien de réinitialisation généré.')
            ->with('reset_link', $link);
    }

    public function setAircraftQualification(Request $request, User $user, Aircraft $aircraft)
    {
        // uniquement pour les élèves pour l'instant
        if (($user->role ?? null) !== 'Élève') {
            return redirect()->back()->with('error', 'Les qualifications ne s’appliquent qu’aux élèves pour le moment.');
        }

        $validated = $request->validate([
            'level' => 'required|in:INSTRUCTOR_ONLY,SOLO_BRIEFING,QUALIFIED',
        ]);

        UserAircraftQualification::updateOrCreate(
            ['user_id' => $user->id, 'aircraft_id' => $aircraft->id],
            [
                'level' => $validated['level'],
                'decided_by_user_id' => auth()->id(),
                'decided_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Qualification mise à jour.');
    }

    public function setModelQualification(Request $request, User $user)
    {
        // Uniquement pour les élèves
        if (($user->role ?? null) !== 'Élève') {
            return redirect()->back()->with('error', 'Les qualifications ne s’appliquent qu’aux élèves pour le moment.');
        }

        $validated = $request->validate([
            'model' => 'required|string',
            'level' => 'required|in:INSTRUCTOR_ONLY,SOLO_BRIEFING,QUALIFIED',
        ]);

        UserQualification::updateOrCreate(
            ['user_id' => $user->id, 'model' => $validated['model']],
            ['level' => $validated['level']]
        );

        return redirect()->back()->with('success', 'Qualification mise à jour.');
    }

    public function saveModelQualifications(Request $request, User $user)
    {
        if (($user->role ?? null) !== 'Élève') {
            return redirect()->back()->with('error', 'Les qualifications ne s’appliquent qu’aux élèves pour le moment.');
        }

        $validated = $request->validate([
            'levels' => 'required|array',
            'levels.*' => 'required|in:INSTRUCTOR_ONLY,SOLO_BRIEFING,QUALIFIED',
        ]);

        foreach ($validated['levels'] as $model => $level) {
            if (!is_string($model) || trim($model) === '') continue;

            \App\Models\UserQualification::updateOrCreate(
                ['user_id' => $user->id, 'model' => $model],
                ['level' => $level]
            );
        }

        return redirect()->back()->with('success', 'Qualifications enregistrées.');
    }
}
