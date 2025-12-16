@extends('layouts.app')

@section('title', 'Statut global')
@section('subtitle', 'Administration')

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <h2 class="card-title">Message global</h2>
      <div class="card-meta">Affiché pour tout le monde</div>
    </div>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('admin.status.global.update') }}">
      @csrf
      @method('PUT')

      <label class="filters-label">Message</label>
      <textarea class="input" name="message" rows="4" style="width:100%;">{{ old('message', $status->message) }}</textarea>
      @error('message') <div class="alert alert-danger">{{ $message }}</div> @enderror

      <div style="display:flex; gap:10px; margin-top:10px;">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
@endsection
