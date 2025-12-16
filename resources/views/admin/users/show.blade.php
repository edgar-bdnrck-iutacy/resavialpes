@extends('layouts.app')

@section('title', 'Utilisateur')
@section('subtitle', $user->first_name . ' ' . $user->last_name)

@section('content')

<div style="margin-bottom: 12px;">
  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
    ← Retour à la liste des utilisateurs
  </a>
  @if(session('success'))
  <div class="alert alert-success" style="margin-bottom:12px;">
      {{ session('success') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:12px;">
        @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
        @endforeach
    </div>
    @endif
</div>
    
    <div class="card">
        {{-- Informations utilisateur --}}
    <div class="card-header card-header-actions">
      <div class="card-header-left">
        <h3 class="card-title">
          {{ $user->first_name }} {{ $user->last_name }}
        </h3>

        <div class="card-meta">
          Dernière mise à jour :
          {{ $lastUpdateAt->format('d/m/Y à H:i') }}
        </div>
      </div>
    
      <div class="card-header-right">
        <a class="btn btn-secondary" href="{{ route('admin.users.edit', $user) }}">
          Modifier
        </a>
    
        <form method="POST"
              action="{{ route('admin.users.password.reset', $user) }}"
              onsubmit="return confirm('Êtes-vous sûr de vouloir générer un lien de réinitialisation du mot de passe pour cet utilisateur ?');"
              style="display:inline;">
          @csrf
            <button class="btn btn-danger" type="submit">
              <i data-lucide="key" class="icon"></i>
              &nbsp Réinitialiser mot de passe
            </button>
        </form>
      </div>
    </div>


    <div class="card-body">
      {{-- Infos principales --}}
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px;">
        <div class="card" style="padding:12px;">
          <div class="card-meta" style="margin-bottom:6px;">Email</div>
          <div style="font-weight:600;">{{ $user->email }}</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="card-meta" style="margin-bottom:6px;">Login</div>
          <div style="font-weight:600;">{{ $user->login ?? '—' }}</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="card-meta" style="margin-bottom:6px;">Téléphone principal</div>
          <div style="font-weight:600;">{{ $user->tel_pri ?? '—' }}</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="card-meta" style="margin-bottom:6px;">Téléphone secondaire</div>
          <div style="font-weight:600;">{{ $user->tel_seg ?? '—' }}</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="card-meta" style="margin-bottom:6px;">Téléphone mobile</div>
          <div style="font-weight:600;">{{ $user->tel_mob ?? '—' }}</div>
        </div>
    </div>


    @if(session('reset_link'))
      <div class="alert alert-success" style="margin-bottom:10px;">
        Lien généré : <a href="{{ session('reset_link') }}">{{ session('reset_link') }}</a>
      </div>
    @endif

      {{-- Changement de rôle (confirmation obligatoire) --}}
      <hr style="margin:14px 0; opacity:.2;">

      <form method="POST" action="{{ route('admin.users.role.update', $user) }}"
            onsubmit="return confirmRoleChangeCards(this);">
        @csrf
        @method('PUT')

        <div class="card-header card-header-actions" style="padding:0; margin-bottom:10px;">
          <div class="card-header-left">
            <h3 class="card-title">Rôle</h3>
            <div class="card-meta">Un seul rôle possible</div>
          </div>

          <div class="card-header-right">
            <button class="btn btn-primary" type="submit">
              Enregistrer le rôle
            </button>
          </div>
        </div>

      
        <input type="hidden" name="old_role" value="{{ $user->role }}">
      
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:8px; margin-top:10px;">
          @foreach($roles as $r)
            <label class="card pick-card pick-card--radio">
              <input class="pick-input" type="radio" name="role" value="{{ $r }}"
                     @checked($user->role === $r) required>
              <span class="pick-label">{{ $r }}</span>
            </label>
          @endforeach
        </div>
      
      </form>
      
      <script>
        function confirmRoleChangeCards(form) {
          const oldRole = form.querySelector('input[name="old_role"]').value;
          const checked = form.querySelector('input[name="role"]:checked');
          const newRole = checked ? checked.value : oldRole;
      
          if (oldRole === newRole) return true;
          return confirm(`Êtes-vous sûr de vouloir changer le rôle de "${oldRole}" vers "${newRole}" ?`);
        }
      </script>

      <hr style="margin:16px 0; opacity:.2;">

      
      {{-- Qualifications (élèves uniquement) --}}
      @if(($user->role ?? null) === 'Élève')
        @php
          $levels = [
            'INSTRUCTOR_ONLY' => 'Instructeur obligatoire',
            'SOLO_BRIEFING'   => 'Solo briefing obligatoire',
            'QUALIFIED'       => 'Qualifié',
          ];
        
          // Cercle indicateur en haut à droite du modèle
          $levelDot = [
            'INSTRUCTOR_ONLY' => '○', // vide
            'SOLO_BRIEFING'   => '◐', // demi
            'QUALIFIED'       => '●', // plein
          ];
        
          // Surbrillance carte modèle (PAS sur les cartes de choix)
          // - aucune pour INSTRUCTOR_ONLY
          // - subtile pour SOLO_BRIEFING
          // - visible pour QUALIFIED
          $modelCardStyle = [
            'INSTRUCTOR_ONLY' => '',
            'SOLO_BRIEFING'   => 'box-shadow: 0 0 0 2px rgba(13, 109, 253, 0.28);',
            'QUALIFIED'       => 'box-shadow: 0 0 0 3px rgba(13, 109, 253, 0.5);',
          ];
        @endphp
        
        <form method="POST" action="{{ route('admin.users.qualifications_model.save', $user) }}">
          @csrf
        
          <div class="card-header card-header-actions" style="padding:0; margin-bottom:10px;">
            <div class="card-header-left">
              <h3 class="card-title">Qualifications (par modèle)</h3>
              <div class="card-meta">Par défaut : Instructeur obligatoire</div>
            </div>
        
            <div class="card-header-right">
              <button class="btn btn-primary" type="submit">
                Enregistrer les qualifications
              </button>
            </div>
          </div>
        
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:8px;">
            @foreach($availableModels as $model)
              @php
                $current = $modelLevels[$model] ?? 'INSTRUCTOR_ONLY';
              @endphp

              <div class="card" style="padding:12px; {{ $modelCardStyle[$current] ?? '' }}">
                {{-- Haut : modèle en haut à gauche --}}
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                  <div style="font-weight:600;">
                    {{ $model }}
                  </div>

                  <div style="font-weight:800; font-size:16px; line-height:1;">
                    {{ $levelDot[$current] ?? '○' }}
                  </div>
                </div>

                {{-- Bas : sélection "comme Rôle" --}}
                <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:8px;">
                  @foreach($levels as $k => $label)
                    <label class="card pick-card pick-card--radio" style="margin:0; padding:8px 10px;">
                      <input
                        class="pick-input"
                        type="radio"
                        name="levels[{{ $model }}]"
                        value="{{ $k }}"
                        @checked($current === $k)
                        required
                      >
                      <span class="pick-label" style="text-align:center; font-size:12px; line-height:1.1;">
                        {!! nl2br(e($label)) !!}
                      </span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </form>
      @endif
    </div>
  </div>
@endsection
