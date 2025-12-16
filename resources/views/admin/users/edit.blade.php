@extends('layouts.app')

@section('title', 'Modifier utilisateur')
@section('subtitle', $user->email)

@section('content')
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h2 class="card-title">Modifier l’utilisateur</h2>
        <div class="card-meta">ID {{ $user->id }}</div>
      </div>
      <a class="btn" href="{{ route('admin.users.show', $user) }}">← Retour</a>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display:grid; gap:14px; max-width:520px;">
        @csrf
        @method('PUT')

        <div class="filters-grid">
          <div>
            <label class="filters-label">Prénom</label>
            <input class="input" name="first_name" value="{{ old('first_name', $user->first_name) }}">
            @error('first_name') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Nom</label>
            <input class="input" name="last_name" value="{{ old('last_name', $user->last_name) }}">
            @error('last_name') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Email</label>
            <input class="input" name="email" value="{{ old('email', $user->email) }}">
            @error('email') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Rôle</label>
            <input class="input" name="role" value="{{ old('role', $user->role) }}" placeholder="Élève / Breveté / Instructeur / Admin / Maintenance">
            @error('role') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Tel pri.</label>
            <input class="input" name="tel_pri" value="{{ old('tel_pri', $user->tel_pri) }}">
            @error('tel_pri') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Tel seg.</label>
            <input class="input" name="tel_seg" value="{{ old('tel_seg', $user->tel_seg) }}">
            @error('tel_seg') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="filters-label">Tel mob.</label>
            <input class="input" name="tel_mob" value="{{ old('tel_mob', $user->tel_mob) }}">
            @error('tel_mob') <div class="alert alert-danger">{{ $message }}</div> @enderror
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
