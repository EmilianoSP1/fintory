@extends('layouts.app')

@section('titulo','Editar usuario')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Editar {{ $usuario->name }}</h1>

  @if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.usuarios.actualizar', $usuario) }}" method="POST">
    @csrf @method('PUT')
    <label class="block mb-2">Nombre</label>
    <input type="text" name="name" value="{{ old('name',$usuario->name) }}"
           class="w-full px-3 py-2 mb-4 border rounded focus:outline-none">

    <label class="block mb-2">Email</label>
    <input type="email" name="email" value="{{ old('email',$usuario->email) }}"
           class="w-full px-3 py-2 mb-4 border rounded focus:outline-none">

    <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      Guardar cambios
    </button>
    <a href="{{ route('admin.usuarios') }}"
       class="ml-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
      Cancelar
    </a>
  </form>
@endsection
