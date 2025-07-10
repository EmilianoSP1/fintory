@extends('layouts.app')

@section('titulo','Detalle de usuario')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Detalle de {{ $usuario->name }}</h1>
  <ul class="bg-white shadow rounded p-4 space-y-2">
    <li><strong>ID:</strong> {{ $usuario->id }}</li>
    <li><strong>Nombre:</strong> {{ $usuario->name }}</li>
    <li><strong>Email:</strong> {{ $usuario->email }}</li>
    <li><strong>Registrado:</strong> {{ $usuario->created_at->format('Y-m-d H:i') }}</li>
  </ul>
  <a href="{{ route('admin.usuarios') }}"
     class="mt-4 inline-block px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
    Volver
  </a>
@endsection
