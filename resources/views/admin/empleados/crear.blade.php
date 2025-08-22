@extends('layouts.app')

@section('titulo','Crear nuevo empleado')

@section('contenido')
  <h1 class="text-2xl font-bold mb-4">Crear nuevo empleado</h1>

  @if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.empleados.store') }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf

    {{-- Campo oculto para asegurar que el empleado se crea en la tienda activa --}}
    
    <div class="mb-4">
      <label class="block mb-1 font-medium">Nombre</label>
      <input type="text" name="name" value="{{ old('name') }}"
             class="w-full border rounded px-3 py-2 focus:outline-none" required>
    </div>

    <div class="mb-4">
      <label class="block mb-1 font-medium">Correo</label>
      <input type="email" name="email" value="{{ old('email') }}"
             class="w-full border rounded px-3 py-2 focus:outline-none" required>
    </div>

    <div class="mb-4">
      <label class="block mb-1 font-medium">Contraseña</label>
      <input type="password" name="password"
             class="w-full border rounded px-3 py-2 focus:outline-none" required>
    </div>

    {{-- Este es el campo que faltaba: --}}
    <div class="mb-6">
      <label class="block mb-1 font-medium">Confirmar contraseña</label>
      <input type="password" name="password_confirmation"
             class="w-full border rounded px-3 py-2 focus:outline-none" required>
    </div>

    <div class="flex items-center space-x-4">
      <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">
        Crear empleado
      </button>
      <a href="{{ route('admin.empleados') }}"
         class="text-gray-600 hover:underline">
        Cancelar
      </a>
    </div>
  </form>
@endsection
