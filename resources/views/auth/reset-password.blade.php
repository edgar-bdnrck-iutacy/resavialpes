@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')
@section('subtitle', 'Définir un nouveau mot de passe')

@section('content')
@if ($errors->any())
  <div class="alert alert-danger" style="margin-bottom:12px;">
    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:12px;">
    {{ session('success') }}
  </div>
@endif

<div class="card" style="max-width:520px;">
  <div class="card-header">
    <h2 class="card-title">Nouveau mot de passe</h2>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('password.reset.update') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <label class="filters-label">Email</label>
      <input class="input" name="email" value="{{ old('email', $email) }}" required>

      <label class="filters-label" style="margin-top:10px;">Nouveau mot de passe</label>
      <input class="input" type="password" name="password" required>

      <label class="filters-label" style="margin-top:10px;">Confirmer</label>
      <input class="input" type="password" name="password_confirmation" required>

      <div style="margin-top:12px;">
        <button class="btn btn-primary" type="submit">Mettre à jour</button>
      </div>
    </form>
  </div>
</div>
@endsection
