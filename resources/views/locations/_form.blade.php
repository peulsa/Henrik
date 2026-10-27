@php($editing = isset($location))
@push('styles')<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">@endpush
<div class="form-grid">
    <div class="field"><label for="name">Nombre</label><input id="name" name="name" required maxlength="150" value="{{ old('name', $location->name ?? '') }}" placeholder="Ej. Bodega de Informática"></div>
    <div class="field"><label for="code">Código único</label><input id="code" name="code" required maxlength="50" value="{{ old('code', $location->code ?? '') }}" placeholder="Ej. MDO-TI-BOD"></div>
    <div class="field"><label for="type">Tipo</label><select id="type" name="type" required>@foreach(config('inventory.location_types') as $value => $label)<option value="{{ $value }}" @selected(old('type', $location->type ?? 'office') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="parent_id">Pertenece a</label><select id="parent_id" name="parent_id"><option value="">Nivel principal</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected((string)old('parent_id', $location->parent_id ?? '') === (string)$parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
    <div class="field full"><label for="address">Dirección o referencia</label><input id="address" name="address" maxlength="255" value="{{ old('address', $location->address ?? '') }}" placeholder="Dirección visible para el equipo"></div>
    <div class="field full"><label for="maps_url">Enlace de Google Maps u otro mapa</label><input id="maps_url" name="maps_url" type="url" maxlength="1000" value="{{ old('maps_url', $location->maps_url ?? '') }}" placeholder="https://maps.google.com/..."><small>Puedes pegar un enlace y/o marcar el punto exacto en el mapa.</small></div>
    <div class="field full"><label>Marca la ubicación en el mapa</label><div id="location-picker" class="real-map"></div><small>Haz clic sobre el mapa o arrastra el pin.</small></div>
    <input id="latitude" name="latitude" type="hidden" value="{{ old('latitude', $location->latitude ?? '') }}">
    <input id="longitude" name="longitude" type="hidden" value="{{ old('longitude', $location->longitude ?? '') }}">
    <div class="field full"><label for="notes">Notas</label><textarea id="notes" name="notes" maxlength="2000">{{ old('notes', $location->notes ?? '') }}</textarea></div>
    @if($editing)<div class="field full"><label class="check"><input type="hidden" name="active" value="0"><input type="checkbox" name="active" value="1" @checked(old('active', $location->active))> Ubicación activa</label></div>@endif
</div>
<div class="form-actions"><a class="button secondary" href="{{ $editing ? route('locations.show', $location) : route('locations.index') }}">Cancelar</a><button class="button primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear ubicación' }}</button></div>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const latitude = document.querySelector('#latitude');
    const longitude = document.querySelector('#longitude');
    const initial = latitude.value && longitude.value ? [Number(latitude.value), Number(longitude.value)] : [-34.2264, -70.9596];
    const map = L.map('location-picker').setView(initial, latitude.value ? 17 : 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 20, attribution: '&copy; OpenStreetMap'}).addTo(map);
    let marker = latitude.value ? L.marker(initial, {draggable: true}).addTo(map) : null;
    const savePoint = (point) => { latitude.value = point.lat.toFixed(7); longitude.value = point.lng.toFixed(7); };
    const placeMarker = (point) => { if (!marker) { marker = L.marker(point, {draggable: true}).addTo(map); marker.on('dragend', (event) => savePoint(event.target.getLatLng())); } else { marker.setLatLng(point); } savePoint(point); };
    if (marker) marker.on('dragend', (event) => savePoint(event.target.getLatLng()));
    map.on('click', (event) => placeMarker(event.latlng));
});
</script>
@endpush
