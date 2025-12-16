<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function show(Request $request, string $token)
    {
        $email = (string) $request->query('email', '');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $row = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$row) {
            return back()->withErrors(['email' => 'Lien invalide ou expiré.']);
        }

        if (!Hash::check($validated['token'], $row->token)) {
            return back()->withErrors(['token' => 'Lien invalide ou expiré.']);
        }

        // Expiration optionnelle : 2h
        if ($row->created_at && now()->diffInMinutes($row->created_at) > 120) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()->withErrors(['token' => 'Lien expiré.']);
        }

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', 'Mot de passe mis à jour. Vous pouvez vous connecter.');
    }
}
