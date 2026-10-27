@extends('layouts.app')
@section('title', 'Ubicaciones')
@section('content')
<div class="page-head"><div><span class="eyebrow">Organización territorial</span><h1>Ubicaciones</h1><p>Crea todas las sedes, edificios, pisos, oficinas, bodegas o zonas que necesites.</p></div><div class="actions"><a class="button primary" href="{{ route('locations.create') }}">+ Nueva ubicación</a></div></div>
<div class="grid-2">
<section class="card"><header class="card-header"><h2>Listado</h2><span class="badge neutral">{{ $locations->count() }} registradas</span></header>
@if($locations->isEmpty())<div class="empty">No hay ubicaciones.</div>@else<div class="table-wrap"><table><thead><tr><th>Ubicación</th><th>Tipo</th><th>Stock</th></tr></thead><tbody>@foreach($locations as $location)<tr><td><a class="table-link" href="{{ route('locations.show', $location) }}">{{ $location->name }}</a><div class="muted small-text">{{ $location->parent?->name ?: $location->address }}</div></td><td><span class="badge neutral">{{ config('inventory.location_types.'.$location->type) }}</span></td><td>{{ $location->stocks->sum('quantity') }} unidades</td></tr>@endforeach</tbody></table></div>@endif</section>
<section><div class="map" aria-label="Mapa esquemático de ubicaciones">@foreach($locations->whereNotNull('map_x')->whereNotNull('map_y') as $location)<a class="map-pin" href="{{ route('locations.show', $location) }}" style="left:{{ $location->map_x }}%;top:{{ $location->map_y }}%">{{ $location->name }}</a>@endforeach</div><p class="muted small-text">Mapa referencial. Las coordenadas se definen como porcentajes para adaptarse a cualquier plano futuro.</p></section>
</div>
@endsection
