@php
    $currentSort = $sortField ?? 'registration';
    $currentDir  = $sortDir ?? 'asc';
@endphp

<div class="card-meta" style="margin-bottom: 10px;">
    {{ $aircraft->count() }} appareil(s)
</div>

@if ($aircraft->isEmpty())
    <p style="margin-top: 1rem;">
        Aucun aéronef ne correspond aux critères sélectionnés.
    </p>
@else
    <div class="table-container" style="margin-top: 0.75rem;">
        <table>
            <thead>
                <tr>
                    {{-- Immatriculation --}}
                    @php $dir = ($currentSort === 'registration' && $currentDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <th>
                        <a href="{{ route('admin.aircraft.index', array_merge(request()->all(), ['sort' => 'registration', 'dir' => $dir])) }}"
                           class="th-sortable">
                            Immatriculation
                            @if($currentSort === 'registration')
                                <i data-lucide="{{ $currentDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Modèle --}}
                    @php $dir = ($currentSort === 'model' && $currentDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <th>
                        <a href="{{ route('admin.aircraft.index', array_merge(request()->all(), ['sort' => 'model', 'dir' => $dir])) }}"
                           class="th-sortable">
                            Modèle
                            @if($currentSort === 'model')
                                <i data-lucide="{{ $currentDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Statut --}}
                    @php $dir = ($currentSort === 'status' && $currentDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <th>
                        <a href="{{ route('admin.aircraft.index', array_merge(request()->all(), ['sort' => 'status', 'dir' => $dir])) }}"
                           class="th-sortable">
                            Statut
                            @if($currentSort === 'status')
                                <i data-lucide="{{ $currentDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Potentiel --}}
                    @php $dir = ($currentSort === 'potentiel_restant' && $currentDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <th>
                        <a href="{{ route('admin.aircraft.index', array_merge(request()->all(), ['sort' => 'potentiel_restant', 'dir' => $dir])) }}"
                           class="th-sortable">
                            Potentiel
                            @if($currentSort === 'potentiel_restant')
                                <i data-lucide="{{ $currentDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon"></i>
                            @endif
                        </a>
                    </th>

                    {{-- Disponibilité --}}
                    @php $dir = ($currentSort === 'disponibilite' && $currentDir === 'asc') ? 'desc' : 'asc'; @endphp
                    <th>
                        <a href="{{ route('admin.aircraft.index', array_merge(request()->all(), ['sort' => 'disponibilite', 'dir' => $dir])) }}"
                           class="th-sortable">
                            Disponibilité
                            @if($currentSort === 'disponibilite')
                                <i data-lucide="{{ $currentDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="icon"></i>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach ($aircraft as $a)
                    <tr class="table-row-clickable"
                        onclick="window.location='{{ route('admin.aircraft.show', $a) }}'">
                        <td>{{ $a->registration }}</td>
                        <td>{{ $a->model }}</td>

                        {{-- Statut (badge) --}}
                        <td>
                            @switch($a->status)
                                @case('Disponible')
                                    <span class="badge badge-green">Disponible</span>
                                    @break
                                @case('En maintenance')
                                    <span class="badge badge-blue">En maintenance</span>
                                    @break
                                @case('Défectueux')
                                    <span class="badge badge-red">Défectueux</span>
                                    @break
                                @case('En vol')
                                    <span class="badge badge-blue">En vol</span>
                                    @break
                                @default
                                    <span class="badge badge-blue">{{ $a->status }}</span>
                            @endswitch
                        </td>

                        {{-- Potentiel (badge couleur) --}}
                        <td>
                            @php
                                $p = is_null($a->potentiel_restant) ? null : (int) $a->potentiel_restant;

                                if (is_null($p)) {
                                    $badgeClass = 'badge-blue';
                                    $label = 'Non renseigné';
                                } elseif ($p < 10) {
                                    $badgeClass = 'badge-red';
                                    $label = $p . ' h';
                                } elseif ($p < 50) {
                                    $badgeClass = 'badge-orange';
                                    $label = $p . ' h';
                                } else {
                                    $badgeClass = 'badge-green';
                                    $label = $p . ' h';
                                }
                            @endphp

                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                        </td>

                        {{-- Disponibilité (badge dynamique) --}}
                        <td>
                            @if($a->status !== 'Disponible')
                                —
                            @elseif($a->heuresDisponibles() === null)
                                <span class="badge badge-green">Aucune réservation prévue</span>
                            @else
                                <span class="badge badge-blue">Encore {{ $a->heuresDisponibles() }} h</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
