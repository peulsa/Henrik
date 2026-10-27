@extends('layouts.app')
@section('title', 'Editar '.$location->name)
@section('content')
<div class="page-head"><div><span class="eyebrow">Ubicaciones</span><h1>Editar ubicación</h1><p>{{ $location->name }} · {{ $location->code }}</p></div></div>
<form class="card card-body" method="post" action="{{ route('locations.update', $location) }}">@csrf @method('put') @include('locations._form')</form>
@endsection
