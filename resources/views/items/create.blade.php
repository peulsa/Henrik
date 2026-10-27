@extends('layouts.app')
@section('title', 'Nuevo artículo')
@section('content')
<div class="page-head"><div><span class="eyebrow">Inventario</span><h1>Nuevo artículo</h1><p>Registra un insumo, repuesto o tipo de equipo.</p></div></div>
<form class="card card-body" method="post" action="{{ route('items.store') }}">@csrf @include('items._form')</form>
@endsection
