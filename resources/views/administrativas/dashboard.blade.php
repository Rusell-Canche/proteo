@extends('layouts.master')

@section('page-title', 'Pagina de inicio')
@section('title', 'Inicio')

@section('content')
<div id="app">
    <dashboard-vue user-name="{{ $userName }}"></dashboard-vue>
</div>
@endsection