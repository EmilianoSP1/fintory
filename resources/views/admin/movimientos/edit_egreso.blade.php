@extends('layouts.app')

@section('titulo', 'Editar Egreso')

@section('contenido')
<div class="max-w-lg mx-auto mt-12 bg-white shadow-xl rounded-xl p-8 dark:bg-gray-900 dark:text-white">
    <h1 class="text-2xl font-bold mb-6 text-center">Editar Egreso</h1>

    {{-- Mensaje de errores --}}
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg dark:bg-red-800 dark:text-white">
            <ul class="list-disc pl-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.movimientos.egreso.actualizar', $movimiento->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-1 font-medium" for="egreso">Monto del Egreso</label>
            <input
                type="number"
                name="egreso"
                id="egreso"
                step="0.01"
                min="0"
                class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                value="{{ old('egreso', $movimiento->egreso) }}"
                required
            >
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium" for="egreso_tipo">Tipo de Egreso</label>
            <select name="egreso_tipo" id="egreso_tipo" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white" required>
                <option value="">Selecciona…</option>
                <option value="Efectivo"      {{ old('egreso_tipo', $movimiento->egreso_tipo)=='Efectivo' ? 'selected' : '' }}>Efectivo</option>
                <option value="Transferencia" {{ old('egreso_tipo', $movimiento->egreso_tipo)=='Transferencia' ? 'selected' : '' }}>Transferencia</option>
                <option value="Crédito"       {{ old('egreso_tipo', $movimiento->egreso_tipo)=='Crédito' ? 'selected' : '' }}>Crédito</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium" for="egreso_descripcion">Descripción</label>
            <input
                type="text"
                name="egreso_descripcion"
                id="egreso_descripcion"
                class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                value="{{ old('egreso_descripcion', $movimiento->egreso_descripcion) }}"
                required
            >
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium" for="egreso_nota">Nota / Factura</label>
            <input
                type="text"
                name="egreso_nota"
                id="egreso_nota"
                class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                value="{{ old('egreso_nota', $movimiento->egreso_nota) }}"
            >
        </div>

        {{-- Campos especiales solo para Transferencia o Crédito --}}
        <div id="campos-transferencia" style="display: none;">
            <div class="mb-4">
                <label class="block mb-1 font-medium" for="banco_personalizado">Banco / Destino</label>
                <input
                    type="text"
                    name="banco_personalizado"
                    id="banco_personalizado"
                    class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                    value="{{ old('banco_personalizado', $movimiento->banco_personalizado) }}"
                >
            </div>
        </div>
        <div id="campos-credito" style="display: none;">
            <div class="mb-4">
                <label class="block mb-1 font-medium" for="credito_origen">Origen del Crédito</label>
                <input
                    type="text"
                    name="credito_origen"
                    id="credito_origen"
                    class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                    value="{{ old('credito_origen', $movimiento->credito_origen) }}"
                >
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium" for="credito_otro_banco">Otro Banco</label>
                <input
                    type="text"
                    name="credito_otro_banco"
                    id="credito_otro_banco"
                    class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                    value="{{ old('credito_otro_banco', $movimiento->credito_otro_banco) }}"
                >
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium" for="proveedor_nombre">Proveedor</label>
                <input
                    type="text"
                    name="proveedor_nombre"
                    id="proveedor_nombre"
                    class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                    value="{{ old('proveedor_nombre', $movimiento->proveedor_nombre) }}"
                >
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium" for="egreso_vencimiento">Vencimiento</label>
                <input
                    type="date"
                    name="egreso_vencimiento"
                    id="egreso_vencimiento"
                    class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                    value="{{ old('egreso_vencimiento', optional($movimiento->egreso_vencimiento)->format('Y-m-d')) }}"
                >
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('admin.dashboard') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg dark:bg-gray-700 dark:text-white dark:hover:bg-gray-800">
                Cancelar
            </a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 font-semibold rounded-lg shadow">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
<script>
    // Muestra y oculta campos según tipo de egreso
    document.addEventListener('DOMContentLoaded', function() {
        const tipo = document.getElementById('egreso_tipo');
        const transferencia = document.getElementById('campos-transferencia');
        const credito = document.getElementById('campos-credito');

        function updateCampos() {
            transferencia.style.display = tipo.value === 'Transferencia' ? 'block' : 'none';
            credito.style.display = tipo.value === 'Crédito' ? 'block' : 'none';
        }
        tipo.addEventListener('change', updateCampos);
        updateCampos();
    });
</script>
@endsection
