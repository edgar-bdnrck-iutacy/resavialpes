<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            if (\Illuminate\Support\Facades\Route::has('admin.users.index')) {
                return redirect()->route('admin.users.index');
            }
            
            if (\Illuminate\Support\Facades\Route::has('admin.planning.day')) {
                return redirect()->route('admin.planning.day');
            }
            
            // Fallback ultra sûr : URL directe
            return redirect('/admin/users');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
            'remember'   => ['nullable'],
        ]);

        $identifier = trim($validated['identifier']);
        $remember = $request->boolean('remember');

        $user = User::query()
            ->where('email', $identifier)
            ->orWhere('login', $identifier)
            ->orWhere('tel_pri', $identifier)
            ->orWhere('tel_seg', $identifier)
            ->orWhere('tel_mob', $identifier)
            ->first();

        if (!$user) {
            return back()->withErrors(['identifier' => "Identifiant inconnu (email/login/téléphone)."])->withInput();
        }

        if (!Auth::attempt(['email' => $user->email, 'password' => $validated['password']], $remember)) {
            return back()->withErrors(['password' => "Mot de passe incorrect."])->withInput();
        }

        $request->session()->regenerate();

        if (\Illuminate\Support\Facades\Route::has('admin.users.index')) {
            return redirect()->route('admin.users.index');
        }

        return redirect('/');

    }
}
