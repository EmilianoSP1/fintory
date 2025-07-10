{{-- resources/views/admin/movimientos/edit_ingreso.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Editar Ingreso')

@section('contenido')
<div class="max-w-lg mx-auto mt-10 bg-white shadow-lg rounded-xl p-8 dark:bg-gray-900">
    <h2 class="text-2xl font-bold mb-6 text-center text-indigo-700 dark:text-indigo-400">Editar Ingreso</h2>
    <form method="POST" action="{{ route('admin.movimientos.ingreso.actualizar', $movimiento->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Concepto</label>
            <input type="text" name="concepto" value="{{ old('concepto', $movimiento->concepto) }}" required class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Efectivo</label>
            <input type="number" step="0.01" name="efectivo" value="{{ old('efectivo', $movimiento->efectivo) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Tarjeta</label>
            <input type="number" step="0.01" name="tarjeta" value="{{ old('tarjeta', $movimiento->tarjeta) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Vales</label>
            <input type="number" step="0.01" name="caldes" value="{{ old('caldes', $movimiento->caldes) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Pagos Clientes</label>
            <input type="number" step="0.01" name="pagos_clientes" value="{{ old('pagos_clientes', $movimiento->pagos_clientes) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Transferencia</label>
            <input type="number" step="0.01" name="venta_transferencia" value="{{ old('venta_transferencia', $movimiento->venta_transferencia) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Otros</label>
            <input type="number" step="0.01" name="otros" value="{{ old('otros', $movimiento->otros) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1 dark:text-gray-100">Descripción (Otros)</label>
            <input type="text" name="otros_descripcion" value="{{ old('otros_descripcion', $movimiento->otros_descripcion) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white">
        </div>
        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 transition">Cancelar</a>
            <button type="submit" class="px-4 py-2 rounded bg-indigo-700 hover:bg-indigo-800 text-white font-semibold transition">Guardar</button>
        </div>
    </form>
</div>
@endsection
