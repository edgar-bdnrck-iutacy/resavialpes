<?php

namespace App\Http\Controllers;

use App\Models\StatusGlobal;
use App\Models\StatusUser;
use App\Models\User;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    // Page d’édition du message global
    public function editGlobal()
    {
        $status = StatusGlobal::query()->first();
        if (!$status) {
            $status = StatusGlobal::create(['message' => '']);
        }

        return view('admin.status.global', compact('status'));
    }

    // Sauvegarde du message global
    public function updateGlobal(Request $request)
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $status = StatusGlobal::query()->first();
        if (!$status) {
            $status = StatusGlobal::create(['message' => '']);
        }

        $status->update(['message' => $validated['message'] ?? '']);

        return redirect()
            ->route('admin.status.global.edit')
            ->with('success', 'Statut global mis à jour.');
    }

    // Page d’édition du message perso d’un user
    public function editUser(User $user)
    {
        $status = StatusUser::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['message' => '']
        );

        return view('admin.status.user', compact('user', 'status'));
    }

    // Sauvegarde du message perso d’un user
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $status = StatusUser::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['message' => '']
        );

        $status->update(['message' => $validated['message'] ?? '']);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Statut utilisateur mis à jour.');
    }
}
