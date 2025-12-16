@extends('layouts.app')

@section('title', 'Aéronef ' . $aircraft->registration)
@section('subtitle', 'Détails et statut')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">{{ $aircraft->registration }}</h2>
                <div class="card-meta">{{ $aircraft->model }}</div>
            </div>

            <div style="display:flex; gap:10px;">
                <a class="btn" href="{{ route('admin.aircraft.index') }}">← Retour</a>
                <a class="btn btn-primary" href="{{ route('admin.aircraft.edit', $aircraft) }}">Modifier l’aéronef</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card-body">
            <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <p><strong>Immatriculation :</strong> {{ $aircraft->registration }}</p>
                    <p><strong>Modèle :</strong> {{ $aircraft->model }}</p>
                    <p><strong>Potentiel restant :</strong>
                        {{ is_null($aircraft->potentiel_restant) ? 'Non renseigné' : $aircraft->potentiel_restant . ' h' }}
                    </p>
                </div>

                <div>
                    <p>
                        <strong>Actuellement occupé :</strong>
                        <em>Auto-géré plus tard par le planning</em>
                    </p>

                    <hr>

                    <h3 style="margin-top:0;">Statut (modifiable)</h3>

                    <form method="POST" action="{{ route('admin.aircraft.status.update', $aircraft) }}" style="display:flex; gap:10px; align-items:center;">
                        @csrf
                        @method('PATCH')

                        <select name="status" class="input">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($aircraft->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button class="btn btn-primary" type="submit">Enregistrer</button>
                    </form>

                    @error('status')
                        <div class="alert alert-danger" style="margin-top:10px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
@endsection
