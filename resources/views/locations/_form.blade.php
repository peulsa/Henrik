@php($editing = isset($location))
<div class="form-grid">
    <div class="field"><label for="name">Nombre</label><input id="name" name="name" required maxlength="150" value="{{ old('name', $location->name ?? '') }}" placeholder="Ej. Bodega de Informática"></div>
    <div class="field"><label for="code">Código único</label><input id="code" name="code" required maxlength="50" value="{{ old('code', $location->code ?? '') }}" placeholder="Ej. MDO-TI-BOD"></div>
    <div class="field"><label for="type">Tipo</label><select id="type" name="type" required>@foreach(config('inventory.location_types') as $value => $label)<option value="{{ $value }}" @selected(old('type', $location->type ?? 'office') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="parent_id">Pertenece a</label><select id="parent_id" name="parent_id"><option value="">Nivel principal</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected((string)old('parent_id', $location->parent_id ?? '') === (string)$parent->id)>{{ $parent->name }}</option>@endforeach</select></div>
    <div class="field full"><label for="address">Dirección o referencia</label><input id="address" name="address" maxlength="255" value="{{ old('address', $location->address ?? '') }}"></div>
    <div class="field"><label for="map_x">Posición horizontal del mapa (%)</label><input id="map_x" name="map_x" type="number" min="0" max="100" value="{{ old('map_x', $location->map_x ?? '') }}"></div>
    <div class="field"><label for="map_y">Posición vertical del mapa (%)</label><input id="map_y" name="map_y" type="number" min="0" max="100" value="{{ old('map_y', $location->map_y ?? '') }}"></div>
    <div class="field full"><label for="notes">Notas</label><textarea id="notes" name="notes" maxlength="2000">{{ old('notes', $location->notes ?? '') }}</textarea></div>
    @if($editing)<div class="field full"><label class="check"><input type="hidden" name="active" value="0"><input type="checkbox" name="active" value="1" @checked(old('active', $location->active))> Ubicación activa</label></div>@endif
</div>
<div class="form-actions"><a class="button secondary" href="{{ $editing ? route('locations.show', $location) : route('locations.index') }}">Cancelar</a><button class="button primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear ubicación' }}</button></div>
