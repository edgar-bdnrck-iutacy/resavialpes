@extends('layouts.app')

@section('title', 'Aéronefs')
@section('subtitle', 'Gestion de la flotte Avialpes')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des aéronefs</h2>
            <div class="card-meta">
                {{ $aircraft->count() }} appareil(s)
            </div>
        </div>

        {{-- ===================== FILTRES ===================== --}}
        @php
            $currentFilters = $filters ?? [];
            $currentSort    = $sortField ?? 'registration';
            $currentDir     = $sortDir ?? 'asc';
        @endphp

        <form method="GET" class="filters" data-ajax-filter data-target="#aircraftList">
            <div class="filters-grid">

                {{-- Immatriculation --}}
                <div class="filters-field">
                    <label class="filters-label" for="registration">Immatriculation</label>
                    <input
                        id="registration"
                        type="text"
                        name="registration"
                        class="filters-input"
                        value="{{ $currentFilters['registration'] ?? '' }}"
                        placeholder="Ex : F-HVTG"
                    >
                </div>

                {{-- Modèle --}}
                <div class="filters-field">
                    <label class="filters-label" for="model">Modèle</label>
                    <select id="model" name="model" class="filters-select">
                        <option value="">Tous les modèles</option>
                        @foreach ($models as $model)
                            <option value="{{ $model }}"
                                {{ ($currentFilters['model'] ?? '') === $model ? 'selected' : '' }}>
                                {{ $model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filters-field">
                    <label class="filters-label" for="status">Statut</label>
                    <select id="status" name="status" class="filters-select">
                        @php $s = $currentFilters['status'] ?? ''; @endphp
                        <option value="">Tous les statuts</option>
                        <option value="Disponible" {{ $s === 'Disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="En maintenance" {{ $s === 'En maintenance' ? 'selected' : '' }}>En maintenance</option>
                        <option value="Défectueux" {{ $s === 'Défectueux' ? 'selected' : '' }}>Défectueux</option>
                        <option value="En vol" {{ $s === 'En vol' ? 'selected' : '' }}>En vol</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="filters-actions">
                    <a href="{{ route('admin.aircraft.index') }}" class="btn btn-secondary">
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        {{-- ===================== TABLEAU ===================== --}}
        <div id="aircraftList">
          @include('admin.aircraft._list', ['aircraft' => $aircraft, 'sortField' => $currentSort, 'sortDir' => $currentDir])
        </div>
    </div>
@endsection
