@extends('layouts.app')

@section('title', 'Statut utilisateur')
@section('subtitle', $user->email)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <h2 class="card-title">Message personnel</h2>
      <div class="card-meta">{{ $user->name }} · ID {{ $user->id }}</div>
    </div>
    <a class="btn" href="{{ route('admin.users.show', $user) }}">← Retour</a>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
      @csrf
      @method('PUT')

      <label class="filters-label">Message</label>
      <textarea class="input" name="message" rows="4" style="width:100%;">{{ old('message', $status->message) }}</textarea>
      @error('message') <div class="alert alert-danger">{{ $message }}</div> @enderror

      <div style="display:flex; gap:10px; margin-top:10px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn" href="{{ route('admin.users.show', $user) }}">Annuler</a>
      </div>
    </form>
  </div>
</div>
@endsection
