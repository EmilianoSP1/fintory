@extends('layouts.app')

@section('titulo', 'Datos de Movimientos')

@section('contenido')
<div class="bg-white dark:bg-gray-900 shadow rounded-xl p-6" style="height: 90vh; display: flex; flex-direction: column;">
    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100 text-center">Todos los movimientos registrados</h2>

    {{-- FILTROS AVANZADOS --}}
    <form method="GET" class="flex flex-wrap gap-2 items-center mb-4">
        <div>
            <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Empleado</label>
            <select name="empleado" class="px-2 py-1 rounded border dark:bg-gray-800 dark:text-white">
                <option value="">Todos</option>
                @foreach($empleados as $emp)
                    <option value="{{ $emp->id }}" {{ request('empleado') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Tipo</label>
            <select name="tipo" class="px-2 py-1 rounded border dark:bg-gray-800 dark:text-white">
                <option value="">Todos</option>
                <option value="ingreso" {{ request('tipo') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                <option value="egreso" {{ request('tipo') == 'egreso' ? 'selected' : '' }}>Egreso</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="border px-2 py-1 rounded dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Buscar</label>
            <input type="text" name="buscar" placeholder="Concepto, nota..." value="{{ request('buscar') }}" class="border px-2 py-1 rounded dark:bg-gray-800 dark:text-white">
        </div>
        <div class="flex gap-2 mt-5 md:mt-0">
            <button type="submit" class="px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition font-semibold shadow">Buscar</button>
            <a href="{{ route('admin.datos') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">Limpiar</a>
<a href="{{ route('admin.datos.excel', request()->all()) }}" class="px-4 py-2 rounded bg-green-100 text-green-700 hover:bg-green-200 font-semibold transition">Excel</a>
<a href="{{ route('admin.datos.pdf', request()->all()) }}" class="px-4 py-2 rounded bg-red-100 text-red-700 hover:bg-red-200 font-semibold transition">PDF</a>

        </div>
    </form>

    {{-- RESUMEN RÁPIDO --}}
    <div class="flex flex-wrap gap-6 mb-4">
        <div class="p-2 bg-green-50 dark:bg-green-900 rounded text-green-700 dark:text-green-200">
            Total ingresos: <span class="font-bold">{{ number_format($totalIngresos, 2) }}</span>
        </div>
        <div class="p-2 bg-red-50 dark:bg-red-900 rounded text-red-700 dark:text-red-200">
            Total egresos: <span class="font-bold">{{ number_format($totalEgresos, 2) }}</span>
        </div>
    </div>

    {{-- TABLA DATOS, SCROLL SOLO VERTICAL --}}
    <div class="overflow-y-auto" style="flex:1; min-height:0;">
        <table class="min-w-full text-xs md:text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800">
                    <th class="py-2 px-3 font-semibold text-left">Empleado</th>
                    <th class="py-2 px-3 font-semibold text-left">Día</th>
                    <th class="py-2 px-3 font-semibold text-left">Fecha</th>
                    <th class="py-2 px-3 font-semibold text-left">Otros</th>
                    <th class="py-2 px-3 font-semibold text-left">Efectivo</th>
                    <th class="py-2 px-3 font-semibold text-left">Tarjeta</th>
                    <th class="py-2 px-3 font-semibold text-left">Vales</th>
                    <th class="py-2 px-3 font-semibold text-left">Pagos</th>
                    <th class="py-2 px-3 font-semibold text-left">Transferencia</th>
                    <th class="py-2 px-3 font-semibold text-left">Concepto</th>
                    <th class="py-2 px-3 font-semibold text-left">Monto</th>
                    <th class="py-2 px-3 font-semibold text-left">Nota/Factura</th>
                    <th class="py-2 px-3 font-semibold text-left">Origen/Destino</th>
                    <th class="py-2 px-3 font-semibold text-left">Vencimiento</th>
                    <th class="py-2 px-3 font-semibold text-left">Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $mov)
                <tr class="even:bg-gray-50 dark:even:bg-gray-800">
                    <td class="py-2 px-3">{{ $mov->usuario->name ?? '-' }}</td>
                    <td class="py-2 px-3">{{ \Illuminate\Support\Carbon::parse($mov->created_at)->locale('es')->isoFormat('dddd') }}</td>
                    <td class="py-2 px-3">{{ \Illuminate\Support\Carbon::parse($mov->created_at)->format('Y-m-d') }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->otros, 2) }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->efectivo, 2) }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->tarjeta, 2) }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->caldes, 2) }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->pagos_clientes, 2) }}</td>
                    <td class="py-2 px-3">{{ number_format($mov->venta_transferencia, 2) }}</td>
                    <td class="py-2 px-3">{{ $mov->concepto }}</td>
                    <td class="py-2 px-3 font-semibold" style="color:
                        {{ $mov->egreso > 0 ? '#e53e3e' : ($mov->efectivo + $mov->tarjeta + $mov->caldes + $mov->pagos_clientes + $mov->venta_transferencia + $mov->otros > 0 ? '#16a34a' : '#444') }}">
                        {{ $mov->egreso > 0 ? '-' : '+' }}{{ number_format($mov->egreso > 0 ? $mov->egreso : ($mov->efectivo + $mov->tarjeta + $mov->caldes + $mov->pagos_clientes + $mov->venta_transferencia + $mov->otros), 2) }}
                    </td>
                    <td class="py-2 px-3">{{ $mov->egreso_nota ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $mov->credito_origen ?? $mov->banco_personalizado ?? $mov->proveedor_nombre ?? '-' }}</td>
                    <td class="py-2 px-3">
                        {{ $mov->egreso_vencimiento ? \Illuminate\Support\Carbon::parse($mov->egreso_vencimiento)->format('Y-m-d') : '-' }}
                    </td>
                    <td class="py-2 px-3">
                        {{ \Illuminate\Support\Carbon::parse($mov->created_at)->setTimezone('America/Mexico_City')->format('H:i:s') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="py-4 text-center text-gray-400">No hay datos para mostrar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN ABAJO --}}
    <div class="mt-2">
        {{ $movimientos->appends(request()->all())->links() }}
    </div>
</div>
@endsection
