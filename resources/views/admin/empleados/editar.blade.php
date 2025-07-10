@extends('layouts.app')

@section('titulo','Editar Empleado')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Editar {{ $empleado->name }}</h1>

  @if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.empleados.actualizar', $empleado) }}" method="POST">
    @csrf @method('PUT')

    <label class="block mb-2">Nombre</label>
    <input type="text" name="name" value="{{ old('name', $empleado->name) }}"
           class="w-full px-3 py-2 mb-4 border rounded focus:outline-none"
           required>

    <label class="block mb-2">Correo</label>
    <input type="email" name="email" value="{{ old('email', $empleado->email) }}"
           class="w-full px-3 py-2 mb-4 border rounded focus:outline-none"
           required>

    <label class="block mb-2">Nueva contraseña (dejar vacío para no cambiar)</label>
    <input type="password" name="password"
           class="w-full px-3 py-2 mb-4 border rounded focus:outline-none">

    <label class="block mb-2">Confirmar contraseña</label>
    <input type="password" name="password_confirmation"
           class="w-full px-3 py-2 mb-6 border rounded focus:outline-none">

    <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      Guardar cambios
    </button>

    <a href="{{ route('admin.empleados') }}"
       class="ml-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
      Cancelar
    </a>
  </form>
@endsection
