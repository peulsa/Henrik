@extends('layouts.app')
@section('title', 'Registrar artículo')
@section('content')
<div class="page-head"><div><span class="eyebrow">Inventario</span><h1>Registrar artículo</h1><p>Primero comprueba que no exista y luego crea su ficha individual en el catálogo.</p></div></div>
<form class="card card-body" method="post" action="{{ route('items.store') }}">@csrf @include('items._form')</form>
@endsection
