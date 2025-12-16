@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="auth-card">
  <div class="auth-card-header">
    <h2 class="auth-title">Connexion</h2>
    <div class="auth-subtitle">Email • Login • Téléphone</div>
  </div>

  <div class="auth-card-body">
    @if ($errors->any())
      <div class="alert alert-danger" style="margin-bottom:12px;">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
      @csrf

      <div class="auth-field">
        <label class="auth-label">Identifiant</label>
        <div class="auth-input-wrap">
          <span class="auth-icon">👤</span>
          <input
            class="auth-input"
            name="identifier"
            value="{{ old('identifier') }}"
            placeholder="email@… / login / 06…"
            required
            autofocus
          >
        </div>
      </div>

      <div class="auth-field">
        <label class="auth-label">Mot de passe</label>
        <div class="auth-input-wrap">
          <span class="auth-icon">🔒</span>
          <input id="passwordInput" class="auth-input" type="password" name="password" placeholder="••••••••" required style="padding-right:92px;">
          <span class="auth-action">
            <button id="togglePasswordBtn" type="button" class="btn" style="padding:6px 10px; border-radius:12px;">
              Afficher
            </button>
          </span>
        </div>
      </div>

      <div class="auth-row">
        <label style="display:flex; gap:8px; align-items:center;">
          <input type="checkbox" name="remember" value="1">
          <span>Rester connecté</span>
        </label>

        <span>Besoin d’un reset ? Contacte un Admin</span>
      </div>

      <button class="auth-btn" type="submit">Se connecter</button>
    </form>
  </div>
</div>

<script>
  (function () {
    const input = document.getElementById('passwordInput');
    const btn = document.getElementById('togglePasswordBtn');
    if (!input || !btn) return;

    btn.addEventListener('click', () => {
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.textContent = isHidden ? 'Masquer' : 'Afficher';
    });
  })();
</script>
@endsection
