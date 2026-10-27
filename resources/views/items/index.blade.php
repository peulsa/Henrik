@extends('layouts.app')
@section('title', 'Inventario')
@section('content')
<div class="page-head"><div><span class="eyebrow">Catálogo</span><h1>Inventario</h1><p>Administra cada equipo, dispositivo, repuesto, periférico o insumo antes de registrar existencias.</p></div><div class="actions"><a class="button secondary" href="{{ route('item-types.index') }}">Administrar tipos</a><a class="button secondary" href="{{ route('stock.receive.create') }}">Recepcionar stock</a><a class="button primary" href="{{ route('items.create') }}">+ Agregar al catálogo</a></div></div>
<form class="filters" method="get">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, SKU, marca o modelo">
    <select name="type"><option value="">Todos los tipos</option>@foreach($types as $type)<option value="{{ $type->slug }}" @selected(request('type') === $type->slug)>{{ $type->name }}</option>@endforeach</select>
    <button class="button secondary" type="submit">Filtrar</button>
</form>
<section class="card">
    @if($items->isEmpty())<div class="empty"><strong>No hay artículos registrados</strong>Crea el primer insumo, repuesto o equipo del inventario.</div>@else
    <div class="table-wrap"><table><thead><tr><th>Artículo</th><th>Tipo</th><th>Ubicación</th><th>Áreas</th><th>Stock total</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
    @foreach($items as $item)<tr>
        <td><a class="table-link" href="{{ route('items.show', $item) }}">{{ $item->name }}</a><div class="muted small-text"><span class="mono">{{ $item->sku }}</span>@if($item->brand || $item->model) · {{ trim($item->brand.' '.$item->model) }}@endif</div></td>
        <td><span class="badge neutral">{{ $item->itemType?->name ?? $item->type }}</span></td>
        <td class="small-text">@if($item->location)<a class="table-link" href="{{ route('locations.inventory', $item->location) }}">{{ $item->location->name }}</a>@else Sin asignar @endif</td>
        <td class="small-text">{{ $item->serviceAreas->pluck('name')->join(', ') ?: 'Sin definir' }}</td>
        <td><strong>{{ $item->total_stock }}</strong></td>
        <td><span class="badge {{ $item->active ? 'success' : 'neutral' }}">{{ $item->active ? 'Activo' : 'Inactivo' }}</span></td>
        <td><div class="actions"><a class="button small secondary" href="{{ route('items.edit', $item) }}">Editar</a><form method="post" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este artículo del inventario?')">@csrf @method('DELETE')<button class="button small danger" type="submit">Eliminar</button></form></div></td>
    </tr>@endforeach
    </tbody></table></div><div class="card-body pagination">{{ $items->links() }}</div>@endif
</section>
@endsection
