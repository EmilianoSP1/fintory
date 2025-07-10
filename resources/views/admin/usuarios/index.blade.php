@extends('layouts.app')

@section('titulo','Usuarios registrados')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Usuarios registrados</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  <table class="min-w-full bg-white shadow rounded">
    <thead>
      <tr class="bg-gray-200">
        <th class="px-4 py-2">#</th>
        <th class="px-4 py-2">Nombre</th>
        <th class="px-4 py-2">Email</th>
        <th class="px-4 py-2">Creado</th>
        <th class="px-4 py-2">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($usuarios as $u)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $u->id }}</td>
        <td class="px-4 py-2">{{ $u->name }}</td>
        <td class="px-4 py-2">{{ $u->email }}</td>
        <td class="px-4 py-2">{{ $u->created_at->format('Y-m-d') }}</td>
        <td class="px-4 py-2 space-x-2">
          <a href="{{ route('admin.usuarios.ver', $u) }}"
             class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Ver</a>
          <a href="{{ route('admin.usuarios.editar', $u) }}"
             class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</a>
          <form action="{{ route('admin.usuarios.eliminar', $u) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                    onclick="return confirm('¿Eliminar usuario?')">
              Eliminar
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endsection
