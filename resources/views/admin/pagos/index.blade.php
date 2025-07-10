@extends('layouts.app')

@section('titulo', 'Pagos a Empleados')

@section('contenido')
<div class="max-w-5xl mx-auto mt-6">
    {{-- Alerta de éxito --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded bg-green-100 text-green-700 shadow flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row gap-8">
        {{-- FORMULARIO NUEVO PAGO --}}
        <div class="w-full md:w-1/3 bg-white p-6 rounded-2xl shadow border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-bold text-xl text-gray-700 dark:text-white mb-4">Nuevo Pago</h2>
            <form action="{{ route('admin.pagos.guardar') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Empleado</label>
                    <select name="empleado_id" class="form-select w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white" required>
                        <option value="">Seleccionar</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Concepto</label>
                    <input type="text" name="concepto" class="form-input w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Monto ($)</label>
                    <input type="number" step="0.01" name="monto" class="form-input w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Fecha</label>
                    <input type="date" name="fecha" class="form-input w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Método</label>
                    <select name="metodo" class="form-select w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white">
                        <option>Efectivo</option>
                        <option>Transferencia</option>
                        <option>Cheque</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 dark:text-gray-300">Descripción</label>
                    <textarea name="descripcion" rows="2" class="form-textarea w-full rounded border-gray-300 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <button class="mt-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-xl w-full transition">Guardar Pago</button>
            </form>
        </div>

        {{-- TABLA PAGOS --}}
        <div class="w-full md:w-2/3">
            <div class="flex flex-wrap justify-between mb-4 gap-2 items-center">
                {{-- FILTROS --}}
                <form method="GET" class="flex flex-wrap gap-1 items-center">
                    <input type="text" name="buscar" placeholder="Buscar empleado o concepto" value="{{ request('buscar') }}" class="form-input rounded border-gray-300 dark:bg-gray-800 dark:text-white text-sm" />
                    <input type="date" name="desde" value="{{ request('desde') }}" class="form-input rounded border-gray-300 dark:bg-gray-800 dark:text-white text-xs" />
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-input rounded border-gray-300 dark:bg-gray-800 dark:text-white text-xs" />
                    <button class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 px-3 py-1 rounded text-xs font-semibold transition">Filtrar</button>
                    @if(request()->hasAny(['buscar','desde','hasta']))
                        <a href="{{ route('admin.pagos') }}" class="text-xs text-gray-500 underline ml-2">Limpiar</a>
                    @endif
                </form>
                {{-- EXPORTAR / IMPRIMIR --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.pagos.excel', request()->query()) }}" class="bg-green-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-green-600 transition">Descargar Excel</a>
                    <a href="{{ route('admin.pagos.pdf', request()->query()) }}" class="bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-600 transition">Descargar PDF</a>
                    <button onclick="window.print()" class="bg-gray-700 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-gray-900 transition">Imprimir</button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-2xl shadow border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Empleado</th>
                            <th class="px-3 py-2 text-left font-semibold">Concepto</th>
                            <th class="px-3 py-2 text-right font-semibold">Monto</th>
                            <th class="px-3 py-2 text-left font-semibold">Fecha</th>
                            <th class="px-3 py-2 text-left font-semibold">Método</th>
                            <th class="px-3 py-2 text-left font-semibold">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-900">
                                <td class="px-3 py-1">{{ $pago->empleado->name ?? '-' }}</td>
                                <td class="px-3 py-1">{{ $pago->concepto }}</td>
                                <td class="px-3 py-1 text-right">${{ number_format($pago->monto, 2) }}</td>
                                <td class="px-3 py-1">{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                                <td class="px-3 py-1">{{ $pago->metodo }}</td>
                                <td class="px-3 py-1">{{ $pago->descripcion }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-3 text-center text-gray-400 dark:text-gray-500">Sin pagos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-2">
                    {{ $pagos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
