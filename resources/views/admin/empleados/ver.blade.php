@extends('layouts.app')

@section('titulo','Detalle de Empleado')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Empleado: {{ $empleado->name }}</h1>

  <ul class="bg-white shadow rounded p-4 space-y-2">
    <li><strong>ID:</strong> {{ $empleado->id }}</li>
    <li><strong>Nombre:</strong> {{ $empleado->name }}</li>
    <li><strong>Email:</strong> {{ $empleado->email }}</li>
    <li><strong>Creado:</strong> {{ $empleado->created_at->format('Y-m-d H:i') }}</li>
  </ul>

  <div class="mt-4 space-x-2">
    <a href="{{ route('admin.empleados') }}"
       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
      Volver
    </a>
    <a href="{{ route('admin.empleados.editar', $empleado) }}"
       class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
      Editar
    </a>
    <form action="{{ route('admin.empleados.eliminar', $empleado) }}"
          method="POST" class="inline">
      @csrf @method('DELETE')
      <button type="submit"
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              onclick="return confirm('¿Eliminar este empleado?')">
        Eliminar
      </button>
    </form>
  </div>
@endsection
