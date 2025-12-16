@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('subtitle', 'Administration')

@section('content')
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h2 class="card-title">Liste des utilisateurs</h2>
        <div class="card-meta">—</div>
      </div>
    </div>

    <div class="card-body">
      <form
        id="usersFilters"
        method="GET"
        action="{{ route('admin.users.index') }}"
        class="filters"
        style="margin-bottom:12px;"
        data-ajax-filter
        data-target="#usersList"
      >
        <div class="filters-grid">
          <div class="filters-field">
            <label class="filters-label" for="q">Recherche</label>
            <input
              id="q"
              class="filters-input"
              name="q"
              placeholder="Nom, prénom, email, tel pri/seg/mob..."
              value="{{ $q }}"
            >
          </div>

          <div class="filters-field">
            <label class="filters-label" for="role">Rôle</label>
            <select id="role" name="role" class="filters-input">
              <option value="">Tous</option>
              @foreach($roles as $r)
                <option value="{{ $r }}" @selected($roleFilter === $r)>{{ $r }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>

      <div id="usersList">
        @include('admin.users._list', [
          'users'     => $users,
          'sortField' => $sortField,
          'sortDir'   => $sortDir
        ])
      </div>

    </div>
  </div>
@endsection
