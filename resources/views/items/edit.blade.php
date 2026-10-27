@extends('layouts.app')
@section('title', 'Editar '.$item->name)
@section('content')
<div class="page-head"><div><span class="eyebrow">Inventario</span><h1>Editar artículo</h1><p>{{ $item->name }} · {{ $item->sku }}</p></div></div>
<form class="card card-body" method="post" action="{{ route('items.update', $item) }}">@csrf @method('put') @include('items._form')</form>
@endsection
