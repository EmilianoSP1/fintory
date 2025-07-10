@extends('layouts.app')

@section('contenido')
  <h1>Empleados</h1>
  @if(session('success'))
    <div class="text-green-600">{{ session('success') }}</div>
  @endif
@endsection
