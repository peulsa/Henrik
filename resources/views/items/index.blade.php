@extends('layouts.app')
@section('title', 'Inventario')
@section('content')
<div class="page-head"><div><span class="eyebrow">Catálogo</span><h1>Inventario</h1><p>Equipos, repuestos e insumos disponibles para las distintas áreas.</p></div><div class="actions"><a class="button primary" href="{{ route('items.create') }}">+ Nuevo artículo</a></div></div>
<form class="filters" method="get">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, SKU, marca o modelo">
    <select name="type"><option value="">Todos los tipos</option>@foreach(config('inventory.item_types') as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select>
    <button class="button secondary" type="submit">Filtrar</button>
</form>
<section class="card">
    @if($items->isEmpty())<div class="empty"><strong>No hay artículos registrados</strong>Crea el primer insumo, repuesto o equipo del inventario.</div>@else
    <div class="table-wrap"><table><thead><tr><th>Artículo</th><th>Tipo</th><th>Áreas</th><th>Stock total</th><th>Estado</th></tr></thead><tbody>
    @foreach($items as $item)<tr>
        <td><a class="table-link" href="{{ route('items.show', $item) }}">{{ $item->name }}</a><div class="muted small-text"><span class="mono">{{ $item->sku }}</span>@if($item->brand || $item->model) · {{ trim($item->brand.' '.$item->model) }}@endif</div></td>
        <td><span class="badge neutral">{{ config('inventory.item_types.'.$item->type) }}</span></td>
        <td class="small-text">{{ $item->serviceAreas->pluck('name')->join(', ') ?: 'Sin definir' }}</td>
        <td><strong>{{ $item->total_stock }}</strong> <span class="muted">{{ $item->unit }}</span></td>
        <td><span class="badge {{ $item->active ? 'success' : 'neutral' }}">{{ $item->active ? 'Activo' : 'Inactivo' }}</span></td>
    </tr>@endforeach
    </tbody></table></div><div class="card-body pagination">{{ $items->links() }}</div>@endif
</section>
@endsection
