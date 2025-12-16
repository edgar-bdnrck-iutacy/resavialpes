@extends('layouts.app')

@section('title', 'Planning')
@section('subtitle', 'Administration')

@section('content')
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
      <div>
        <h2 class="card-title">Planning du jour</h2>
        <div class="card-meta">Date : {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>
      </div>

      <div style="display:flex; gap:8px; align-items:center;">
        <a class="btn btn-outline" href="{{ route('admin.planning.day', ['date' => $prevDate]) }}">←</a>

        <form method="GET" action="{{ route('admin.planning.day') }}" style="display:flex; gap:8px; align-items:center;">
          <input type="date" name="date" value="{{ $date }}" class="filters-input">
          <button class="btn btn-primary" type="submit">Aller</button>
        </form>

        <a class="btn btn-outline" href="{{ route('admin.planning.day', ['date' => $nextDate]) }}">→</a>
      </div>
    </div>

    <div class="card-body" style="overflow:auto;">
      @php
        // Taille d’un slot 15 min en pixels (à ajuster si tu veux)
        $slotPx = 22;
        $timelineWidth = $slotCount * $slotPx;
      @endphp

      {{-- Entête heures --}}
      <div style="display:flex; gap:12px; align-items:flex-end; margin-bottom:10px;">
        <div style="min-width:260px; font-weight:600;">Avion</div>

        <div style="position:relative; width: {{ $timelineWidth }}px;">
          {{-- lignes verticales slots --}}
          <div style="display:flex;">
            @for($i=0; $i<$slotCount; $i++)
              <div style="width: {{ $slotPx }}px; height:30px; border-left:1px solid rgba(0,0,0,.08);"></div>
            @endfor
          </div>

          {{-- labels heures (toutes les heures) --}}
          <div style="position:absolute; top:0; left:0; right:0; height:30px;">
            @foreach($hours as $idx => $h)
              @php
                $left = ($idx * 4) * $slotPx; // 4 slots par heure
              @endphp
              <div style="position:absolute; left: {{ $left }}px; top:0; font-size:12px; opacity:.75;">
                {{ $h }}
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Lignes avions --}}
      <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($aircraft as $a)
          @php
            $bars = $barsByAircraft[$a->id] ?? [];
            $briefings = array_values(array_filter($bars, fn($b) => $b['type'] === 'BRIEFING'));
            $flights   = array_values(array_filter($bars, fn($b) => $b['type'] === 'FLIGHT'));
          @endphp

          <div style="display:flex; gap:12px;">
            {{-- Colonne avion --}}
            <div class="card" style="min-width:260px; padding:10px;">
              <div style="font-weight:700;">{{ $a->model }}</div>
              <div style="opacity:.8;">{{ $a->registration }}</div>
            </div>

            {{-- Timeline --}}
            <div class="card" style="padding:10px; position:relative; width: {{ $timelineWidth }}px;">
              {{-- Grille verticale --}}
              <div style="position:absolute; inset:10px; display:flex; pointer-events:none;">
                @for($i=0; $i<$slotCount; $i++)
                  <div style="width: {{ $slotPx }}px; border-left:1px solid rgba(0,0,0,.06);"></div>
                @endfor
              </div>

              {{-- Layer briefings (au-dessus) --}}
              <div style="position:relative; height:26px; margin-bottom:6px;">
                @foreach($briefings as $b)
                  @php
                    $left = $b['startMin'] / 15 * $slotPx;
                    $width = $b['durationMin'] / 15 * $slotPx;
                  @endphp
                  <div title="Briefing"
                       style="position:absolute; left: {{ $left }}px; width: {{ $width }}px; height:20px; border-radius:8px; border:1px solid rgba(0,0,0,.25); background: rgba(0,0,0,.03); display:flex; align-items:center; justify-content:center; font-size:12px;">
                    B
                  </div>
                @endforeach
              </div>

              {{-- Layer vols (ligne avion) --}}
              <div style="position:relative; height:34px;">
                @foreach($flights as $b)
                  @php
                    $left = $b['startMin'] / 15 * $slotPx;
                    $width = $b['durationMin'] / 15 * $slotPx;
                  @endphp
                  <div title="Vol"
                       style="position:absolute; left: {{ $left }}px; width: {{ $width }}px; height:28px; border-radius:10px; border:1px solid rgba(0,0,0,.35); background: rgba(0,0,0,.06); display:flex; align-items:center; padding:0 8px; font-size:12px;">
                    Vol
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>
@endsection
