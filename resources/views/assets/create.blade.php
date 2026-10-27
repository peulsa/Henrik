@extends('layouts.app')
@section('title', 'Agregar dispositivo')
@section('content')
<div class="page-head"><div><span class="eyebrow">Inventario por ubicación</span><h1>Agregar dispositivo</h1><p>Registra una unidad física usando un artículo que ya exista en el catálogo.</p></div></div>
<form class="card card-body" method="post" action="{{ route('assets.store') }}">@csrf<input type="hidden" name="status" value="in_stock">
<div class="form-grid">
    <div class="field"><label for="item_id">Artículo existente</label><select id="item_id" name="item_id" required><option value="">Selecciona un artículo</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected((string)old('item_id', $selectedItem) === (string)$item->id)>{{ $item->name }} · {{ $item->sku }}</option>@endforeach</select></div>
    <div class="field"><label for="location_id">Ubicación</label><select id="location_id" name="location_id" required><option value="">Selecciona una ubicación</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('location_id', $selectedLocation) === (string)$location->id)>{{ $location->name }}</option>@endforeach</select></div>
    <div class="field"><label for="asset_tag">Etiqueta patrimonial</label><input id="asset_tag" name="asset_tag" required value="{{ old('asset_tag') }}" placeholder="Ej. MDO-TI-0001"></div>
    <div class="field"><label for="serial_number">Número de serie</label><input id="serial_number" name="serial_number" value="{{ old('serial_number') }}"></div>
    <div class="field"><label for="assigned_to_email">Correo asignado</label><input id="assigned_to_email" type="email" name="assigned_to_email" value="{{ old('assigned_to_email') }}"></div>
    <div class="field"><label for="acquired_at">Fecha de adquisición</label><input id="acquired_at" type="date" name="acquired_at" value="{{ old('acquired_at') }}"></div>
    <div class="field full"><label for="notes">Notas</label><textarea id="notes" name="notes">{{ old('notes') }}</textarea></div>
</div><div class="form-actions"><a class="button secondary" href="{{ url()->previous() }}">Cancelar</a><button class="button primary" type="submit">Agregar dispositivo</button></div></form>
@endsection
