<!-- resources/views/admin/empleados/index.blade.php -->
@extends('layouts.app')

@section('titulo','Empleados')

@section('contenido')
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Empleados</h1>
    <a href="{{ route('admin.empleados.crear') }}"
       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      Crear empleado
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  <table class="min-w-full bg-white shadow rounded">
    <thead>
      <tr class="bg-gray-200">
        <th class="px-4 py-2">ID</th>
        <th class="px-4 py-2">Nombre</th>
        <th class="px-4 py-2">Correo</th>
        <th class="px-4 py-2">Creado</th>
        <th class="px-4 py-2">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($empleados as $e)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $e->id }}</td>
        <td class="px-4 py-2">{{ $e->name }}</td>
        <td class="px-4 py-2">{{ $e->email }}</td>
        <td class="px-4 py-2">{{ $e->created_at->format('Y-m-d') }}</td>
        <td class="px-4 py-2 space-x-2">
          <a href="{{ route('admin.empleados.ver', $e) }}"
             class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
            Ver
          </a>
          <a href="{{ route('admin.empleados.editar', $e) }}"
             class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Editar
          </a>
          <form action="{{ route('admin.empleados.eliminar', $e) }}"
                method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                    onclick="return confirm('¿Eliminar este empleado?')">
              Eliminar
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endsection
