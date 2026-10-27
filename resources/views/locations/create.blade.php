@extends('layouts.app')
@section('title', 'Nueva ubicación')
@section('content')
<div class="page-head"><div><span class="eyebrow">Ubicaciones</span><h1>Nueva ubicación</h1><p>La jerarquía puede crecer desde una sede hasta oficinas o zonas específicas.</p></div></div>
<form class="card card-body" method="post" action="{{ route('locations.store') }}">@csrf @include('locations._form')</form>
@endsection
