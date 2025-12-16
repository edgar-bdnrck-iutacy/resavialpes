@extends('layouts.app')

@section('title', 'Modifier ' . $aircraft->registration)
@section('subtitle', 'Immatriculation / modèle / potentiel')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Modifier l’aéronef</h2>
                <div class="card-meta">{{ $aircraft->registration }}</div>
            </div>

            <div style="display:flex; gap:10px;">
                <a class="btn" href="{{ route('admin.aircraft.show', $aircraft) }}">← Retour</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.aircraft.update', $aircraft) }}" style="display:grid; gap:14px; max-width:520px;">
                @csrf
                @method('PUT')

                <div>
                    <label class="filters-label">Immatriculation</label>
                    <input class="input" name="registration" value="{{ old('registration', $aircraft->registration) }}">
                    @error('registration') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="filters-label">Modèle</label>
                    <input class="input" name="model" value="{{ old('model', $aircraft->model) }}">
                    @error('model') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="filters-label">Potentiel restant (heures)</label>
                    <input class="input" name="potentiel_restant" value="{{ old('potentiel_restant', $aircraft->potentiel_restant) }}">
                    @error('potentiel_restant') <div class="alert alert-danger">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex; gap:10px; margin-top:6px;">
                    <button class="btn btn-primary" type="submit">Enregistrer</button>
                    <a class="btn" href="{{ route('admin.aircraft.show', $aircraft) }}">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
