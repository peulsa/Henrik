@extends('layouts.app')
@section('title', 'Registrar movimiento')
@section('content')
<div class="page-head"><div><span class="eyebrow">Stock</span><h1>Registrar movimiento</h1><p>El stock se actualizará y el movimiento quedará guardado de forma permanente.</p></div></div>
<form class="card card-body" method="post" action="{{ route('movements.store') }}">@csrf
<div class="form-grid">
    <div class="field"><label for="item_id">Artículo</label><select id="item_id" name="item_id" required><option value="">Selecciona un artículo</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected((string)old('item_id', $selectedItem) === (string)$item->id)>{{ $item->name }} · {{ $item->sku }}</option>@endforeach</select></div>
    <div class="field"><label for="type">Tipo de movimiento</label><select id="type" name="type" required data-movement-type>@foreach(config('inventory.movement_types') as $value => $label)<option value="{{ $value }}" @selected(old('type', 'receive') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="quantity">Cantidad</label><input id="quantity" name="quantity" type="number" min="1" required value="{{ old('quantity', 1) }}"></div>
    <div class="field"><label for="asset_id">Equipo identificado (opcional)</label><select id="asset_id" name="asset_id"><option value="">Movimiento por cantidad</option>@foreach($items as $item)@foreach($item->assets as $asset)<option value="{{ $asset->id }}" @selected((string)old('asset_id') === (string)$asset->id)>{{ $asset->asset_tag }} · {{ $item->name }}</option>@endforeach @endforeach</select><small>Si seleccionas un equipo, la cantidad debe ser 1.</small></div>
    <div class="field" data-from-field><label for="from_location_id">Ubicación de origen</label><select id="from_location_id" name="from_location_id"><option value="">Sin origen interno</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('from_location_id') === (string)$location->id)>{{ $location->name }}</option>@endforeach</select></div>
    <div class="field" data-to-field><label for="to_location_id">Ubicación de destino</label><select id="to_location_id" name="to_location_id"><option value="">Sin destino interno</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('to_location_id') === (string)$location->id)>{{ $location->name }}</option>@endforeach</select></div>
    <div class="field"><label for="reference">Referencia</label><input id="reference" name="reference" maxlength="100" value="{{ old('reference') }}" placeholder="OC, ticket o documento"></div>
    <div class="field full"><label for="notes">Notas</label><textarea id="notes" name="notes" maxlength="2000" placeholder="Motivo, destinatario o detalle del movimiento">{{ old('notes') }}</textarea></div>
</div><div class="form-actions"><a class="button secondary" href="{{ route('movements.index') }}">Cancelar</a><button class="button primary" type="submit">Registrar movimiento</button></div></form>
@endsection
