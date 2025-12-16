<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth'])
    ->group(function () {

    // ✅ Mets TOUTES tes routes /admin/... ici
    // Exemple :
    // Route::get('/admin/users', ...)->name('admin.users.index');
    // ...

    // Admin - Liste des aéronefs
    Route::get('/admin/aircraft', [AircraftController::class, 'index'])
        ->name('admin.aircraft.index');
    
    // Admin - Détails d’un aéronef
    Route::get('/admin/aircraft/{aircraft}', [AircraftController::class, 'show'])
        ->name('admin.aircraft.show');
    
    // Admin - Edition “sensible” (immat / modèle / potentiel)
    Route::get('/admin/aircraft/{aircraft}/edit', [AircraftController::class, 'edit'])
        ->name('admin.aircraft.edit');
    
    Route::put('/admin/aircraft/{aircraft}', [AircraftController::class, 'update'])
        ->name('admin.aircraft.update');
    
    // Admin - Mise à jour rapide du statut (depuis la page détails)
    Route::patch('/admin/aircraft/{aircraft}/status', [AircraftController::class, 'updateStatus'])
        ->name('admin.aircraft.status.update');
    
    
    // Admin - Liste des utilisateurs
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users.index');
    
    // Admin - Détails d’un utilisateur
    Route::get('/admin/users/{user}', [UserController::class, 'show'])
        ->name('admin.users.show');
    
    // Admin - Edition “sensible” (nom / email)
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.users.edit');
    
    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');
    
    Route::put('/admin/users/{user}/role', [UserController::class, 'updateRole'])
        ->name('admin.users.role.update');
    
    Route::put('/admin/users/{user}/qualifications', [UserController::class, 'updateQualifications'])
        ->name('admin.users.qualifications.update');
    
    // Admin - reset password link
    Route::post('/admin/users/{user}/password-reset', [UserController::class, 'adminPasswordReset'])
        ->name('admin.users.password.reset');
    
    // Admin - Statut global
    Route::get('/admin/status/global', [StatusController::class, 'editGlobal'])
        ->name('admin.status.global.edit');
    
    Route::put('/admin/status/global', [StatusController::class, 'updateGlobal'])
        ->name('admin.status.global.update');
    
    // Admin - Statut par utilisateur
    Route::get('/admin/users/{user}/status', [StatusController::class, 'editUser'])
        ->name('admin.users.status.edit');
    
    Route::put('/admin/users/{user}/status', [StatusController::class, 'updateUser'])
        ->name('admin.users.status.update');
    
    Route::post('/admin/users/{user}/qualifications/{aircraft}', [\App\Http\Controllers\UserController::class, 'setAircraftQualification'])
      ->name('admin.users.qualifications.set');

    Route::post('/admin/users/{user}/qualifications-model', [UserController::class, 'saveModelQualifications'])
        ->name('admin.users.qualifications_model.save');

    Route::post('/admin/users/{user}/qualifications-model', [\App\Http\Controllers\UserController::class, 'setModelQualification'])
        ->name('admin.users.qualifications_model.set');

    Route::post('/admin/users/{user}/qualifications-model', [\App\Http\Controllers\UserController::class, 'saveModelQualifications'])
        ->name('admin.users.qualifications_model.save');

    // Planning (jour) - Admin only
    Route::get('/planning', [\App\Http\Controllers\AdminPlanningController::class, 'day'])
        ->name('planning.day');


});


// Public - reset password form (no email sending required)
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'show'])
    ->name('password.reset.form');

Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'update'])
    ->name('password.reset.update');









    
/* // routes/web.php (à supprimer après usage)

use Illuminate\Support\Facades\File;

Route::get('/test-blade', function () {
    $path = resource_path('views');
    $files = File::allFiles($path);

    echo "<h1>Rapport d'analyse Blade</h1>";
    echo "<style>body{font-family:sans-serif} .error{color:red;font-weight:bold} .ok{color:green} table{border-collapse:collapse; width:100%} td,th{border:1px solid #ddd; padding:8px}</style>";
    echo "<table><thead><tr><th>Fichier</th><th>État</th><th>Détails</th></tr></thead><tbody>";

    // Les paires à vérifier
    $pairs = [
        ['@if', '@endif'],
        ['@foreach', '@endforeach'],
        ['@forelse', '@endforelse'],
        ['@auth', '@endauth'],
        ['@guest', '@endguest'],
        ['@section', '@endsection'], // Attention: @stop ferme aussi section, mais comptons endsection
        ['@push', '@endpush'],
        ['@can', '@endcan'],
    ];

    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') continue;
        
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $file->getPathname());
        
        $errors = [];
        
        foreach ($pairs as $pair) {
            $open = substr_count($content, $pair[0]);
            // Pour section, on exclut @section('...') sans fermeture (rare mais possible en yield simple)
            // Pour simplification, on compte juste les occurrences strictes
            
            // Cas spécial pour section qui peut se fermer par stop ou show
            if ($pair[0] === '@section') {
                $close = substr_count($content, '@endsection') + substr_count($content, '@stop') + substr_count($content, '@show');
            } else {
                $close = substr_count($content, $pair[1]);
            }

            if ($open !== $close) {
                $errors[] = "<b>{$pair[0]}</b> ($open) vs <b>{$pair[1]}</b> ($close)";
            }
        }

        if (count($errors) > 0) {
            echo "<tr><td>{$relativePath}</td><td class='error'>DÉSÉQUILIBRE</td><td>" . implode('<br>', $errors) . "</td></tr>";
        } else {
            // Décommentez pour voir les fichiers OK
            // echo "<tr><td>{$relativePath}</td><td class='ok'>OK</td><td>Correct</td></tr>";
        }
    }
    echo "</tbody></table>";
});

Route::get('/debug-layout', function () {
    $path = resource_path('views/layouts/app.blade.php');
    if (!file_exists($path)) {
        return "LE FICHIER N'EXISTE PAS !";
    }
    return highlight_string(file_get_contents($path), true);
}); */