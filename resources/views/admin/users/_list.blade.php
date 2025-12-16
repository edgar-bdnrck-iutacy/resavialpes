@php
  $nextDir = fn($field) => ($sortField === $field && $sortDir === 'asc') ? 'desc' : 'asc';
@endphp

<div class="card-meta" style="margin-bottom:10px;">
  {{ $users->count() }} utilisateur(s)
</div>

<table class="table">
  <thead>
    <tr>
      <th><a class="th-sortable" href="{{ route('admin.users.index', array_merge(request()->all(), ['sort'=>'first_name','dir'=>$nextDir('first_name')])) }}">Prénom</a></th>
      <th><a class="th-sortable" href="{{ route('admin.users.index', array_merge(request()->all(), ['sort'=>'last_name','dir'=>$nextDir('last_name')])) }}">Nom</a></th>
      <th>Email</th>
      <th><a class="th-sortable" href="{{ route('admin.users.index', array_merge(request()->all(), ['sort'=>'role','dir'=>$nextDir('role')])) }}">Rôle</a></th>
      <th>Tel pri.</th>
      <th>Tel seg.</th>
      <th>Tel mob.</th>
    </tr>
  </thead>

  <tbody>
  @foreach($users as $u)
    <tr class="table-row-clickable" onclick="window.location='{{ route('admin.users.show', $u) }}'">
      <td>{{ $u->first_name }}</td>
      <td>{{ $u->last_name }}</td>
      <td>{{ $u->email }}</td>
      <td>{{ $u->role }}</td>
      <td>{{ $u->tel_pri ?? '—' }}</td>
      <td>{{ $u->tel_seg ?? '—' }}</td>
      <td>{{ $u->tel_mob ?? '—' }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
