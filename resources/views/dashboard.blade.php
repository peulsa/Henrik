@extends('layouts.app')

@section('title', 'Resumen')

@section('content')
<div class="page-head">
    <div><span class="eyebrow">Inventario tecnológico</span><h1>Resumen operativo</h1><p>Stock, equipos y movimientos de la Oficina de Informática.</p></div>
    <div class="actions"><a class="button secondary" href="{{ route('items.create') }}">+ Nuevo insumo</a><a class="button primary" href="{{ route('movements.create') }}">+ Registrar movimiento</a></div>
</div>

<section class="stats" aria-label="Indicadores">
    <article class="stat"><span class="stat-label">Artículos activos</span><strong class="stat-value">{{ $itemCount }}</strong><span class="stat-note">Equipos, repuestos e insumos</span></article>
    <article class="stat"><span class="stat-label">Ubicaciones</span><strong class="stat-value">{{ $locationCount }}</strong><span class="stat-note">Sedes, oficinas y bodegas</span></article>
    <article class="stat"><span class="stat-label">Equipos identificados</span><strong class="stat-value">{{ $assetCount }}</strong><span class="stat-note">Con etiqueta o número de serie</span></article>
    <article class="stat"><span class="stat-label">Alertas de stock</span><strong class="stat-value">{{ $lowStockCount }}</strong><span class="stat-note">En el mínimo o por debajo</span></article>
</section>

<div class="grid-2">
    <section class="card">
        <header class="card-header"><h2>Stock que requiere atención</h2><a class="button small secondary" href="{{ route('items.index') }}">Ver inventario</a></header>
        @if($lowStocks->isEmpty())
            <div class="empty"><strong>Sin alertas pendientes</strong>Los niveles de stock registrados están sobre su mínimo.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Artículo</th><th>Ubicación</th><th>Stock</th></tr></thead><tbody>
                @foreach($lowStocks as $stock)<tr><td><a class="table-link" href="{{ route('items.show', $stock->item) }}">{{ $stock->item->name }}</a><div class="muted small-text mono">{{ $stock->item->sku }}</div></td><td>{{ $stock->location->name }}</td><td><span class="badge danger">{{ $stock->quantity }} / mín. {{ $stock->threshold }}</span></td></tr>@endforeach
            </tbody></table></div>
        @endif
    </section>

    <section class="card">
        <header class="card-header"><h2>Actividad reciente</h2><a class="button small secondary" href="{{ route('movements.index') }}">Ver historial</a></header>
        @if($recentMovements->isEmpty())
            <div class="empty"><strong>Aún no hay movimientos</strong>Registra una recepción para comenzar a controlar el stock.</div>
        @else
            <div class="card-body"><ol class="timeline">
                @foreach($recentMovements as $movement)<li><a class="table-link" href="{{ route('movements.show', $movement) }}">{{ config('inventory.movement_types.'.$movement->type) }} · {{ $movement->item->name }}</a><div class="muted small-text">{{ $movement->quantity }} · {{ $movement->fromLocation?->name ?? 'Exterior' }} → {{ $movement->toLocation?->name ?? 'Entrega' }}</div><time>{{ $movement->created_at->diffForHumans() }} · {{ $movement->performed_by_email }}</time></li>@endforeach
            </ol></div>
        @endif
    </section>
</div>
@endsection
