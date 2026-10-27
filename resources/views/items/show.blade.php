@extends('layouts.app')
@section('title', $item->name)
@section('content')
<div class="page-head">
    <div><span class="eyebrow">{{ $item->itemType?->name ?? $item->type }} · <span class="mono">{{ $item->sku }}</span></span><h1>{{ $item->name }}</h1><p>{{ $item->description ?: 'Sin descripción registrada.' }}</p></div>
    <div class="actions"><a class="button secondary" href="{{ route('items.edit', $item) }}">Editar artículo</a><a class="button secondary" href="{{ route('stock.receive.create', ['item_id' => $item->id]) }}">Recepcionar stock</a><a class="button primary" href="{{ route('movements.create', ['item_id' => $item->id]) }}">Registrar movimiento</a></div>
</div>
<div class="detail-grid"><div>
    <section class="card" style="margin-bottom:20px"><header class="card-header"><h2>Stock por ubicación</h2><span class="badge">Total {{ $item->total_stock }}</span></header>
        @if($item->stocks->isEmpty())<div class="empty">Sin ubicaciones asociadas.</div>@else
        <div class="table-wrap"><table><thead><tr><th>Ubicación</th><th>Disponible</th><th>Mínimo</th><th>Estado</th></tr></thead><tbody>
        @foreach($item->stocks as $stock)<tr><td><a class="table-link" href="{{ route('locations.show', $stock->location) }}">{{ $stock->location->name }}</a></td><td><strong>{{ $stock->quantity }}</strong></td><td>{{ $stock->threshold }}</td><td><span class="badge {{ $stock->is_low ? 'danger' : 'success' }}">{{ $stock->is_low ? 'Stock bajo' : 'Disponible' }}</span></td></tr>@endforeach
        </tbody></table></div>@endif
    </section>
    <section class="card" style="margin-bottom:20px"><header class="card-header"><h2>Dispositivos identificados</h2><a class="button small primary" href="{{ route('assets.create', ['item_id' => $item->id]) }}">+ Agregar dispositivo</a></header>
        @if($item->assets->isEmpty())<div class="empty">No hay dispositivos individuales registrados.</div>@else
        <div class="table-wrap"><table><thead><tr><th>Etiqueta</th><th>Serie</th><th>Ubicación</th><th>Estado</th></tr></thead><tbody>
        @foreach($item->assets as $asset)<tr><td><a class="table-link mono" href="{{ route('assets.show', $asset) }}">{{ $asset->asset_tag }}</a></td><td>{{ $asset->serial_number ?: '—' }}</td><td>{{ $asset->location?->name ?: 'Sin asignar' }}</td><td><span class="badge neutral">{{ config('inventory.asset_statuses.'.$asset->status) }}</span></td></tr>@endforeach
        </tbody></table></div>@endif
    </section>
    <section class="card"><header class="card-header"><h2>Historial de movimientos</h2></header>
        @if($item->movements->isEmpty())<div class="empty">Aún no hay movimientos para este artículo.</div>@else
        <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Movimiento</th><th>Cantidad</th><th>Responsable</th></tr></thead><tbody>
        @foreach($item->movements as $movement)<tr><td><a class="table-link" href="{{ route('movements.show', $movement) }}">{{ $movement->created_at->format('d-m-Y H:i') }}</a></td><td>{{ config('inventory.movement_types.'.$movement->type) }}<div class="muted small-text">@if($movement->type === 'receive')Destino: {{ $movement->toLocation?->name }}@else{{ $movement->fromLocation?->name ?? 'Sin origen interno' }} → {{ $movement->toLocation?->name ?? 'Entrega' }}@endif</div></td><td>{{ $movement->quantity }}</td><td class="small-text">{{ $movement->performed_by_email }}</td></tr>@endforeach
        </tbody></table></div>@endif
    </section>
</div><aside>
    <section class="card"><header class="card-header"><h2>Ficha</h2></header><div class="card-body detail-list"><div class="detail-item"><span>Marca</span><strong>{{ $item->brand ?: '—' }}</strong></div><div class="detail-item"><span>Modelo</span><strong>{{ $item->model ?: '—' }}</strong></div><div class="detail-item"><span>Categoría</span><strong>{{ $item->category }}</strong></div><div class="detail-item"><span>Ubicación asignada</span><strong>@if($item->location)<a href="{{ route('locations.inventory', $item->location) }}">{{ $item->location->name }}</a>@else Sin asignar @endif</strong></div><div class="detail-item" style="grid-column:1/-1"><span>Áreas compatibles</span><strong>{{ $item->serviceAreas->pluck('name')->join(', ') ?: 'Sin definir' }}</strong></div></div></section>
    <form method="post" action="{{ route('items.destroy', $item) }}" style="margin-top:15px" onsubmit="return confirm('¿Dar de baja este artículo? Su historial se conservará.')">@csrf @method('delete')<button class="button danger" type="submit">Dar de baja</button></form>
</aside></div>
@endsection
