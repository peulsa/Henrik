@extends('layouts.app')
@section('title', 'Ubicaciones')
@push('styles')<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">@endpush
@section('content')
<div class="page-head"><div><span class="eyebrow">Organización territorial</span><h1>Ubicaciones</h1><p>Sedes, edificios, oficinas, bodegas y zonas del inventario municipal.</p></div><div class="actions"><a class="button primary" href="{{ route('locations.create') }}">+ Nueva ubicación</a></div></div>
<section class="card" style="margin-bottom:20px"><div id="locations-map" class="real-map large" aria-label="Mapa real de ubicaciones"></div></section>
<section class="card"><header class="card-header"><h2>Listado</h2><span class="badge neutral">{{ $locations->count() }} registradas</span></header>
@if($locations->isEmpty())<div class="empty">No hay ubicaciones.</div>@else<div class="table-wrap"><table><thead><tr><th>Ubicación</th><th>Tipo</th><th>Stock</th><th>Acciones</th></tr></thead><tbody>@foreach($locations as $location)<tr><td><a class="table-link" href="{{ route('locations.show', $location) }}">{{ $location->name }}</a><div class="muted small-text">{{ $location->parent?->name ?: $location->address }}</div></td><td><span class="badge neutral">{{ config('inventory.location_types.'.$location->type) }}</span></td><td>{{ $location->stocks->sum('quantity') }}</td><td><div class="actions"><a class="button small secondary" href="{{ route('locations.edit', $location) }}">Editar ubicación</a><a class="button small primary" href="{{ route('locations.inventory', $location) }}">Inventario</a></div></td></tr>@endforeach</tbody></table></div>@endif</section>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const points = @json($mapLocations);
    const map = L.map('locations-map').setView([-34.2264, -70.9596], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 20, attribution: '&copy; OpenStreetMap'}).addTo(map);
    points.forEach((point) => {
        const popup = document.createElement('div');
        const name = document.createElement('strong');
        const link = document.createElement('a');
        name.textContent = point.name;
        link.textContent = 'Ver ubicación';
        link.href = point.url;
        popup.append(name, document.createElement('br'), link);
        L.marker([point.lat, point.lng]).addTo(map).bindPopup(popup);
    });
    if (points.length > 1) map.fitBounds(points.map((point) => [point.lat, point.lng]), {padding: [35, 35]});
    if (points.length === 1) map.setView([points[0].lat, points[0].lng], 17);
});
</script>
@endpush
