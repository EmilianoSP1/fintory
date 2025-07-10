@extends('layouts.app')

@section('titulo','Ventas')

@section('contenido')

<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">
        Panel de Ventas
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        {{-- INGRESOS --}}
        <div class="bg-white rounded-xl shadow p-6 border">
            <h2 class="text-lg font-bold text-gray-700 mb-2 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                Ingresos
            </h2>
            <p class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($totalIngresos,2) }}</p>
            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                <li class="flex justify-between">Efectivo: <span class="font-semibold">${{ number_format($ingresosEfectivo,2) }}</span></li>
                <li class="flex justify-between">Tarjeta: <span class="font-semibold">${{ number_format($ingresosTarjeta,2) }}</span></li>
                <li class="flex justify-between">Vales: <span class="font-semibold">${{ number_format($ingresosVales,2) }}</span></li>
                <li class="flex justify-between">Pagos Clientes: <span class="font-semibold">${{ number_format($ingresosPagosClientes,2) }}</span></li>
                <li class="flex justify-between">Transferencia: <span class="font-semibold">${{ number_format($ingresosTransferencia,2) }}</span></li>
                <li class="flex justify-between">Otros: <span class="font-semibold">${{ number_format($ingresosOtros,2) }}</span></li>
            </ul>
            <form method="POST" action="{{ route('admin.descuento') }}" class="pt-2">
                @csrf
                <input type="hidden" name="tipo" value="ingreso">
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Monto a descontar</label>
                    <input type="number" step="0.01" min="0" name="monto" class="w-full border rounded p-2 text-sm" required />
                </div>
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Concepto</label>
                    <input type="text" name="concepto" maxlength="100" placeholder="Concepto o referencia..." class="w-full border rounded p-2 text-sm" required>
                </div>
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Motivo</label>
                    <select name="motivo" class="w-full border rounded p-2 text-sm">
                        <option value="Retiro de caja">Retiro de caja</option>
                        <option value="Pago proveedor">Pago proveedor</option>
                        <option value="Gasto operativo">Gasto operativo</option>
                        <option value="Renta">Renta</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Forma de descuento</label>
                    <select name="forma" class="w-full border rounded p-2 text-sm">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Vales">Vales</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-sm">
                    Descontar ingreso
                </button>
            </form>
        </div>

        {{-- EGRESOS --}}
        <div class="bg-white rounded-xl shadow p-6 border">
            <h2 class="text-lg font-bold text-gray-700 mb-2 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                Egresos
            </h2>
            <p class="text-2xl font-bold text-gray-900 mb-1">${{ number_format($totalEgresos,2) }}</p>
            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                <li class="flex justify-between">Efectivo: <span class="font-semibold">${{ number_format($egresosEfectivo,2) }}</span></li>
                <li class="flex justify-between">Transferencia: <span class="font-semibold">${{ number_format($egresosTransferencia,2) }}</span></li>
                <li class="flex justify-between">Crédito: <span class="font-semibold">${{ number_format($egresosCredito,2) }}</span></li>
                <li class="flex justify-between">Otros: <span class="font-semibold">${{ number_format($egresosOtros,2) }}</span></li>
            </ul>
            <form method="POST" action="{{ route('admin.descuento') }}" class="pt-2">
                @csrf
                <input type="hidden" name="tipo" value="egreso">
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Monto a descontar</label>
                    <input type="number" step="0.01" min="0" name="monto" class="w-full border rounded p-2 text-sm" required />
                </div>
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Concepto</label>
                    <input type="text" name="concepto" maxlength="100" placeholder="Concepto o referencia..." class="w-full border rounded p-2 text-sm" required>
                </div>
                <div class="mb-2">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Motivo</label>
                    <select name="motivo" class="w-full border rounded p-2 text-sm">
                        <option value="Retiro de caja">Retiro de caja</option>
                        <option value="Pago proveedor">Pago proveedor</option>
                        <option value="Gasto operativo">Gasto operativo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-semibold mb-1 text-gray-600">Forma de descuento</label>
                    <select name="forma" class="w-full border rounded p-2 text-sm">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Crédito">Crédito</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-sm">
                    Descontar egreso
                </button>
            </form>
        </div>
    </div>

    {{-- TABLA DE MOVIMIENTOS --}}
    <div class="bg-white rounded-xl shadow p-6 border mb-10">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Movimientos recientes en Ventas</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-left text-gray-700">
                <thead>
                    <tr class="border-b text-gray-500 uppercase text-[13px]">
                        <th class="py-2 px-2">Fecha</th>
                        <th class="py-2 px-2">Tipo</th>
                        <th class="py-2 px-2">Concepto</th>
                        <th class="py-2 px-2">Monto</th>
                        <th class="py-2 px-2">Forma</th>
                        <th class="py-2 px-2">Motivo</th>
                        <th class="py-2 px-2">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-2">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-2">
                                @if($mov->egreso > 0)
                                    <span class="text-red-500 font-semibold">Egreso</span>
                                @else
                                    <span class="text-green-500 font-semibold">Ingreso</span>
                                @endif
                            </td>
                            <td class="py-2 px-2">{{ $mov->concepto ?? '-' }}</td>
                            <td class="py-2 px-2">
                                ${{ number_format(
                                    $mov->egreso > 0 ? $mov->egreso : (
                                        $mov->efectivo + $mov->tarjeta + $mov->caldes +
                                        $mov->pagos_clientes + $mov->venta_transferencia + $mov->otros
                                    ), 2) }}
                            </td>
                            <td class="py-2 px-2">{{ $mov->forma ?? '-' }}</td>
                            <td class="py-2 px-2">{{ $mov->motivo ?? '-' }}</td>
<td class="py-2 px-2">
    <form action="{{ route('admin.movimiento.eliminar', $mov->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este movimiento?')">
        @csrf
        @method('DELETE')
        <input type="hidden" name="redirect_to" value="{{ route('admin.ventas.index') }}">
        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs">Eliminar</button>
    </form>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td class="py-2 px-2 text-center" colspan="7">No hay movimientos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
