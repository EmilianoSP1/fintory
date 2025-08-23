{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('titulo','Panel de Administración')



<style>
#scrollbar-superior {
    background: transparent;
}
#scrollbar-superior::-webkit-scrollbar {
    height: 6px;
    background: #f3f4f6; /* Gris muy claro */
    border-radius: 8px;
}
#scrollbar-superior::-webkit-scrollbar-thumb {
    background: rgba(120,120,130,0.38); /* Gris oscuro con opacidad */
    border-radius: 8px;
    border: 1px solid #e5e7eb; /* Borde más claro para suavizar */
}
#scrollbar-superior {
    scrollbar-color: rgba(120,120,130,0.38) #f3f4f6;
    scrollbar-width: thin;
}

#tabla-movimientos::-webkit-scrollbar {
    height: 6px;
    background: #f3f4f6;
    border-radius: 8px;
}
#tabla-movimientos::-webkit-scrollbar-thumb {
    background: rgba(120,120,130,0.38);
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}
#tabla-movimientos {
    scrollbar-color: rgba(120,120,130,0.38) #f3f4f6;
    scrollbar-width: thin;
}
</style>






@section('contenido')
  {{-- CUERPO DASHBOARD + FORMULARIO + TABLA --}}
  <div class="bg-gray-100 min-h-screen transition-colors dark:bg-gray-950">
    <div id="dashboard" class="mx-2.5 pt-14">
      <h1 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-white">Panel de Administración</h1>
      @if(session('success'))
        <div 
          x-data="{ show: true }"
          x-init="setTimeout(() => show = false, 2000)"
          x-show="show"
          x-transition:enter="transition ease-out duration-300"
          x-transition:leave="transition ease-in duration-300"
          class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow dark:bg-green-800 dark:text-green-100"
        >
          {{ session('success') }}
        </div>
        <script>
          window.onload = () => { location.hash = '#tabla-movimientos'; };
        </script>
      @endif


      {{-- FORMULARIO DE CIERRE --}}
      <form
        action="{{ route('admin.cierre') }}"
        method="POST"
x-data="{
  bruto: @js(old('egreso_monto',0)),
  tipo:  @js(old('egreso_tipo','')),
  otrosSeleccionado: @js(old('concepto_tipo', '')),
  transfer_bruto: @js(old('venta_transferencia',0)),
  tarjeta_bruto: @js(old('venta_tarjeta',0)),  // <-- agrega esto
  comision: 0.36
}"
        class="space-y-8"
        autocomplete="off"
      >
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white shadow rounded-lg p-6 dark:bg-gray-800">
            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
              Cierre del <span class="font-semibold">{{ now()->format('d/m/Y') }}</span>
            </p>
            <div class="grid gap-4 mt-4">
              <div>
                <label class="block mb-1 dark:text-gray-200">Venta Efectivo</label>
                <input type="number" name="venta_efectivo" step="0.01"
                       value="{{ old('venta_efectivo') }}"
                       class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" placeholder="0.00">
              </div>
<label class="block mb-1 dark:text-gray-200">Venta Tarjeta</label>
<input 
  type="number" 
  name="venta_tarjeta" 
  step="0.01"
  x-model.number="tarjeta_bruto"
  value="{{ old('venta_tarjeta') }}"
  class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" 
  placeholder="0.00"
/>
<div x-show="tarjeta_bruto > 0" class="text-sm text-gray-600 mt-1 dark:text-gray-300">
    Comisión por tarjeta (0.36%): 
    <strong x-text="(tarjeta_bruto*0.0036).toFixed(2)"></strong><br>
    Total neto ingreso: 
    <strong x-text="(tarjeta_bruto > 0 ? (tarjeta_bruto-(tarjeta_bruto*0.0036)).toFixed(2) : '0.00')"></strong>
</div>

              <div>
                <label class="block mb-1 dark:text-gray-200">Vales</label>
                <input type="number" name="venta_caldes" step="0.01"
                       value="{{ old('venta_caldes') }}"
                       class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" placeholder="0.00">
              </div>
              <div>
                <label class="block mb-1 dark:text-gray-200">Pagos Clientes</label>
                <input type="number" name="pagos_clientes" step="0.01"
                       value="{{ old('pagos_clientes') }}"
                       class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" placeholder="0.00">
              </div>
              <div>
                <label class="block mb-1 dark:text-gray-200">Venta Transferencia</label>
                <input 
                  type="number" 
                  name="venta_transferencia" 
                  step="0.01"
                  x-model.number="transfer_bruto"
                  value="{{ old('venta_transferencia') }}"
                  class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" 
                  placeholder="0.00"
                >
              </div>
              <div>
                <label class="block mb-1 dark:text-gray-200">Otros</label>
                <select name="concepto_tipo" x-model="otrosSeleccionado" class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white">
                  <option value="">Selecciona...</option>
                  <option value="otros">Otros</option>
                </select>
              </div>
              <div x-show="otrosSeleccionado === 'otros'" x-cloak>
                <label class="block mb-1 dark:text-gray-200">Descripción</label>
                <input type="text" name="otros_descripcion" value="{{ old('otros_descripcion') }}"
                       class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100" placeholder="Describe el ingreso">
              </div>
              <div x-show="otrosSeleccionado === 'otros'" x-cloak>
                <label class="block mb-1 dark:text-gray-200">Monto</label>
                <input type="number" name="otros_monto" value="{{ old('otros_monto') }}"
                       step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100" placeholder="0.00" min="0" inputmode="decimal">
              </div>
            </div>
          </div>




{{-- EGRESOS --}}
<div class="bg-white shadow rounded-lg p-6 dark:bg-gray-800">
  <h2 class="font-semibold text-lg mb-4 dark:text-white">Egresos</h2>
  <div class="grid gap-4">
    <div>
      <label class="block mb-1 dark:text-gray-200">Tipo de egreso</label>
      <select name="egreso_tipo" x-model="tipo"
              class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white">
        <option value="">Selecciona…</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option> {{-- ← Nueva opción --}}
        <option value="Crédito">Crédito</option>
      </select>
    </div>
    <div>
      <label class="block mb-1 dark:text-gray-200">Monto bruto</label>
      <input type="number" name="egreso_monto" x-model.number="bruto" step="0.01"
             class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100" placeholder="0.00">
    </div>
    <div x-show="tipo" x-cloak class="col-span-full grid gap-4">
      <div>
        <label class="block mb-1 dark:text-gray-200">Descripción</label>
        <input type="text" name="egreso_descripcion" value="{{ old('egreso_descripcion') }}"
               class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100" placeholder="Detalle del egreso">
      </div>
      <div>
        <label class="block mb-1 dark:text-gray-200">Nota / Factura</label>
        <input type="text" name="egreso_nota" value="{{ old('egreso_nota') }}"
               class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100" placeholder="Núm. de nota o factura">
      </div>
    </div>

    {{-- SOLO SI ES CRÉDITO --}}
    <div 
      x-show="tipo==='Crédito'" 
      x-cloak 
      class="col-span-full grid md:grid-cols-3 gap-4"
      x-data="{
        origenCredito: '',
        bancoCredito: '',
        bancoPersonalizado: '',
        proveedorCredito: '',
        proveedorPersonalizado: ''
      }"
    >
      <div>
        <label class="block mb-1 dark:text-gray-200">Origen</label>
        <select name="credito_origen" x-model="origenCredito"
                class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100">
          <option value="">Selecciona…</option>
          <option value="Meli">Meli</option>
          <option value="Klar">Klar</option>
          <option value="Proveedor">Proveedor</option>
          <option value="Otros">Otros</option>
        </select>
      </div>
      <div x-show="origenCredito==='Otros'" x-cloak>
        <label class="block mb-1 dark:text-gray-200">Banco (si “Otros”)</label>
        <select name="credito_otro_banco" x-model="bancoCredito"
                class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100">
          <option value="">Elige banco…</option>
          <option value="Banorte">Banorte</option>
          <option value="Santander">Santander</option>
          <option value="Citibanamex">Citibanamex</option>
          <option value="HSBC">HSBC</option>
          <option value="Scotiabank">Scotiabank</option>
          <option value="escribir">Escribir banco</option>
        </select>
        <input
          x-show="bancoCredito==='escribir'"
          x-model="bancoPersonalizado"
          type="text"
          name="banco_personalizado"
          placeholder="Escribe el nombre del banco"
          class="mt-2 w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100"
        >
      </div>
      <div x-show="origenCredito==='Proveedor'" x-cloak>
        <label class="block mb-1 dark:text-gray-200">Proveedor</label>
        <select x-model="proveedorCredito" class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100">
          <option value="">Elige proveedor…</option>
          <option value="Truper">Truper</option>
          <option value="Plastilimpio">Plastilimpio</option>
          <option value="escribir">Escribe proveedor</option>
        </select>
        <input
          x-show="proveedorCredito==='escribir'"
          x-model="proveedorPersonalizado"
          type="text"
          class="mt-2 w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100"
          placeholder="Escribe el nombre del proveedor"
        >
        <input type="hidden" name="proveedor_nombre"
               :value="proveedorCredito === 'escribir' ? proveedorPersonalizado : (proveedorCredito || '')">
      </div>
      <div>
        <label class="block mb-1 dark:text-gray-200">Vencimiento</label>
        <input type="date" name="egreso_vencimiento"
               class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100">
      </div>
    </div>
  </div>
  <div class="mt-6 text-right">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg">
      Enviar
    </button>
  </div>
</div>

      </form>






{{-- ==================== TOTALES Y CONCEPTOS INGRESOS/EGRESOS + FILTRO DE FECHA ==================== --}}
<div class="max-w-7xl mx-auto px-4">
  <div class="w-full flex flex-col gap-5 items-start mb-10 mt-10">
    {{-- Línea INGRESOS --}}
    <div class="flex flex-row gap-5 items-center">
      {{-- Total INGRESOS --}}
      <div class="bg-white border border-gray-200 rounded-lg px-8 py-4 shadow-sm text-center" style="min-width:210px;">
        <span class="block text-[11px] text-gray-700 font-semibold uppercase tracking-wider mb-1">Total Ingresos</span>
<span class="text-2xl font-extrabold text-indigo-700">
  ${{ number_format($totalIngresosNeto, 2) }}
</span>
      </div>
      {{-- Conceptos INGRESOS (netos) --}}
      <div class="flex flex-row gap-2">
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">OTROS</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalOtros, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">EFECTIVO</span>
          <span class="block text-base font-bold text-gray-800">
            ${{ number_format($totalEfectivo - $totalEgresoEfectivo, 2) }}
          </span>
          <span class="block text-[10px] text-gray-400 mt-1">
            <span class="font-semibold">({{ number_format($totalEfectivo,2) }} - {{ number_format($totalEgresoEfectivo,2) }})</span>
          </span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TARJETA</span>
          <span class="block text-base font-bold text-gray-800">
            ${{ number_format($totalTarjeta - ($totalEgresoTarjeta ?? 0), 2) }}
          </span>
          <span class="block text-[10px] text-gray-400 mt-1">
            <span class="font-semibold">({{ number_format($totalTarjeta,2) }} - {{ number_format($totalEgresoTarjeta ?? 0,2) }})</span>
          </span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">VALES</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalVales, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">PAGOS</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalPagos, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TRANSFERENCIA</span>
          <span class="block text-base font-bold text-gray-800">
            ${{ number_format($totalTransferencia - ($totalEgresoTransferencia ?? 0), 2) }}
          </span>
          <span class="block text-[10px] text-gray-400 mt-1">
            <span class="font-semibold">({{ number_format($totalTransferencia,2) }} - {{ number_format($totalEgresoTransferencia ?? 0,2) }})</span>
          </span>
        </div>
      </div>
    </div>

    {{-- Línea EGRESOS --}}
    <div class="flex flex-row gap-5 items-center">
      {{-- Total EGRESOS --}}
      <div class="bg-white border border-gray-200 rounded-lg px-8 py-4 shadow-sm text-center" style="min-width:210px;">
        <span class="block text-[11px] text-gray-700 font-semibold uppercase tracking-wider mb-1">Total Egresos</span>
        <span class="text-2xl font-extrabold text-rose-700">${{ number_format($totalEgresos, 2) }}</span>
      </div>
      {{-- Conceptos EGRESOS --}}
      <div class="flex flex-row gap-2">
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">EFECTIVO</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoEfectivo, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TARJETA</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoTarjeta ?? 0, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TRANSFERENCIA</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoTransferencia, 2) }}</span>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
          <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">CRÉDITO</span>
          <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoCredito, 2) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Selector de fecha para filtrar movimientos --}}
  <div class="w-full flex justify-start mb-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" 
          class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded shadow px-5 py-3">
      <label for="filtro_fecha" class="font-semibold text-sm text-gray-700 dark:text-gray-200">
        Ir a fecha:
      </label>
      <input 
        type="date" 
        name="fecha" 
        id="filtro_fecha"
        value="{{ request('fecha') }}"
        max="{{ now()->format('Y-m-d') }}"
        class="border rounded px-2 py-1 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700"
      >
      <button type="submit" 
              class="ml-2 px-4 py-1 bg-indigo-700 hover:bg-indigo-800 text-white rounded text-sm">
        Buscar
      </button>
      @if(request('fecha'))
        <a href="{{ route('admin.dashboard') }}" 
           class="text-xs text-gray-500 dark:text-gray-400 underline ml-3 hover:text-indigo-600 dark:hover:text-indigo-400">
          Ver semana actual
        </a>
      @endif
    </form>
  </div>








{{-- Scroll superior minimalista: SOLO ÉSTE, antes de la tabla --}}
<div id="scrollbar-superior" style="width:100%;overflow-x:auto;overflow-y:hidden;height:12px;margin-bottom:1px;">
  <div id="scrollbar-superior-content" style="width:1800px;height:1px;"></div>
</div>

{{-- Tabla de Movimientos Agrupados --}}
<div id="tabla-movimientos"
    class="max-w-7xl mx-auto bg-white shadow rounded-lg overflow-x-auto"
    style="scrollbar-color: #6366f1 #f3f4f6; scrollbar-width: thin;">
    <table class="w-full text-xs border border-gray-200 divide-y divide-gray-200">
        <thead class="bg-gray-50 text-gray-700 uppercase">
            <tr class="tracking-wide">
                <th class="px-3 py-2 text-center">Empleado</th>
                <th class="px-3 py-2 text-center">Día</th>
                <th class="px-3 py-2 text-center">Fecha</th>
                <th class="px-3 py-2 text-center">Otros</th>
                <th class="px-3 py-2 text-center">Efectivo</th>
                <th class="px-3 py-2 text-center">Tarjeta</th>
                <th class="px-3 py-2 text-center">Vales</th>
                <th class="px-3 py-2 text-center">Pagos</th>
                <th class="px-3 py-2 text-center">Transferencia</th>
                <th class="px-3 py-2 text-center">Accion</th>
                <th class="px-0 border-l border-gray-200"></th>
                <th class="px-3 py-2 text-center">Concepto</th>
                <th class="px-3 py-2 text-center">Monto</th>
                <th class="px-3 py-2 text-center">Nota/Factura</th>
                <th class="px-3 py-2 text-center">Origen/Destino</th>
                <th class="px-3 py-2 text-center">Vencimiento</th>
                <th class="px-3 py-2 text-center">Tarjeta (Egreso)</th>
                <th class="px-3 py-2 text-center">Tipo Egreso</th>
                <th class="px-3 py-2 text-center">Acción</th>
            </tr>
        </thead>

        <tbody class="bg-white">
            @forelse($filas as $fila)
            <tr class="hover:bg-gray-50">
                {{-- Empleado --}}
                <td class="px-3 py-2 text-center align-middle">
                  @php
                    $usuario = null;
                    if($fila['ingreso']) $usuario = $fila['ingreso']->usuario ?? null;
                    elseif($fila['egreso']) $usuario = $fila['egreso']->usuario ?? null;
                  @endphp
                  {{ $usuario ? $usuario->name : '—' }}
                </td>

                {{-- ==== INGRESO ==== --}}
                @php
                    $m = $fila['ingreso'];
                    $ingresoValido = false;
                    if ($m) {
                        $ingresoValido =
                            ($m->otros ?? 0) > 0 ||
                            ($m->efectivo ?? 0) > 0 ||
                            ($m->tarjeta ?? 0) > 0 ||
                            ($m->caldes ?? 0) > 0 ||
                            ($m->pagos_clientes ?? 0) > 0 ||
                            ($m->venta_transferencia ?? 0) > 0;
                    }
                @endphp

                @if($m)
                    <td class="px-3 py-2 text-center align-middle">
                        {{ $m->created_at->locale('es')->isoFormat('dddd') }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        {{ $m->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-3 py-2 align-top" style="max-width: 160px; word-break: break-all; overflow-wrap: break-word;">
                        <div x-data="{ open: false }" class="whitespace-normal text-xs text-gray-800">
                            <div class="font-semibold text-center">
                                ${{ number_format($m->otros, 2) }}
                            </div>
                            <div class="mt-1">
                                @php
                                    $fullDesc   = trim($m->concepto ?? '');
                                    $words      = $fullDesc === '' ? [] : explode(' ', $fullDesc);
                                    $preview    = implode(' ', array_slice($words, 0, 13));
                                    $remaining  = array_slice($words, 13);
                                @endphp
                                <span x-show="!open">
                                    {{ $preview }}@if(count($remaining))…@endif
                                </span>
                                <span x-show="open">
                                    @foreach(array_chunk($remaining, 2) as $chunk)
                                        <div>{{ implode(' ', $chunk) }}</div>
                                    @endforeach
                                </span>
                                @if(count($remaining))
                                    <button
                                        @click="open = !open"
                                        class="mt-1 block text-indigo-600 hover:underline text-xs"
                                    >
                                        <span x-text="open ? 'menos' : 'más'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        ${{ number_format($m->efectivo, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        ${{ number_format($m->tarjeta, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        ${{ number_format($m->caldes, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        ${{ number_format($m->pagos_clientes, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        ${{ number_format($m->venta_transferencia, 2) }}
                    </td>
                    <td class="px-3 py-2 text-center align-middle">
                        <div class="flex items-center justify-center gap-1">
                            @if($ingresoValido)
                                {{-- Botón Editar --}}
                                <a href="{{ route('admin.movimiento.edit', $m->id) }}"
                                class="w-6 h-6 flex items-center justify-center rounded bg-indigo-700 hover:bg-indigo-800 transition"
                                title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M9 11l6 6M4 13v7h7l9-9a2.121 2.121 0 00-3-3l-9 9z"/>
                                    </svg>
                                </a>
                                {{-- Botón Eliminar --}}
                                <form action="{{ route('admin.movimiento.destroy', $m->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar este ingreso?')"
                                    class="m-0 p-0 inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ route('admin.dashboard') }}">
                                    <button type="submit"
                                            class="w-6 h-6 flex items-center justify-center rounded bg-red-600 hover:bg-red-700 transition"
                                            title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                {{-- Botones deshabilitados --}}
                                <button disabled
                                        class="w-6 h-6 flex items-center justify-center rounded bg-indigo-300 opacity-50 cursor-not-allowed"
                                        title="Sin ingreso para editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M9 11l6 6M4 13v7h7l9-9a2.121 2.121 0 00-3-3l-9 9z"/>
                                    </svg>
                                </button>
                                <button disabled
                                        class="w-6 h-6 flex items-center justify-center rounded bg-red-300 opacity-50 cursor-not-allowed"
                                        title="Sin ingreso para eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                @else
                    {{-- No hay objeto de ingreso --}}
                    <td class="px-3 py-2 text-center align-middle">—</td>
                    <td class="px-3 py-2 text-center align-middle">—</td>
                    <td class="px-3 py-2 text-center align-middle">—</td>
                    <td class="px-3 py-2 text-center align-middle">$0.00</td>
                    <td class="px-3 py-2 text-center align-middle">$0.00</td>
                    <td class="px-3 py-2 text-center align-middle">$0.00</td>
                    <td class="px-3 py-2 text-center align-middle">$0.00</td>
                    <td class="px-3 py-2 text-center align-middle">$0.00</td>
                    <td class="px-3 py-2 text-center align-middle">
                        <div class="flex items-center justify-center gap-1">
                            <button disabled
                                class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-indigo-300 opacity-50 cursor-not-allowed"
                                title="Sin ingreso para editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 13v7h7l9-9a2.121 2.121 0 00-3-3l-9 9z"/>
                                </svg>
                            </button>
                            <button disabled
                                class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-red-300 opacity-50 cursor-not-allowed"
                                title="Sin ingreso para eliminar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                @endif
                {{-- Separador --}}
                <td class="px-0 border-l border-gray-200"></td>

                {{-- ==== EGRESO ==== --}}
                @php
                    $trClass = '';
                    $mostrarBotonPagar = false;
                    $e = $fila['egreso'] ?? null;
                    if($e && $e->egreso_tipo === 'Crédito') {
                        if($e->pagado) {
                            $trClass = 'bg-green-100';
                        } else {
                            $mostrarBotonPagar = true;
                            if($e->egreso_vencimiento) {
                                $vencimiento = \Carbon\Carbon::parse($e->egreso_vencimiento);
                                $hoy = \Carbon\Carbon::now();
                                $diff = $hoy->diffInDays($vencimiento, false);
                                if($diff > 7) {
                                    $trClass = 'bg-blue-50';
                                } elseif($diff > 2) {
                                    $trClass = 'bg-yellow-100';
                                } elseif($diff >= 0) {
                                    $trClass = 'bg-red-200';
                                } else {
                                    $trClass = 'bg-red-400 text-white';
                                }
                            } else {
                                $trClass = 'bg-blue-50';
                            }
                        }
                    }
                @endphp

                @if($fila['egreso'])
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    {{ $e->egreso_descripcion ?? '—' }}
                  </td>
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    ${{ number_format($e->egreso, 2) }}
                  </td>
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    {{ $e->egreso_nota ?? '—' }}
                  </td>
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    @if($e->egreso_tipo === 'Transferencia')
                      {{ $e->transferencia_destino }}
                    @elseif($e->egreso_tipo === 'Crédito')
                      @if($e->credito_origen === 'Proveedor')
                        {{ $e->proveedor_nombre ?? 'Proveedor' }}
                      @elseif($e->credito_origen === 'Otros')
                        {{ $e->credito_otro_banco }}
                      @else
                        {{ $e->credito_origen }}
                      @endif
                    @else
                      —
                    @endif
                  </td>
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    {{ optional($e->egreso_vencimiento)->format('d/m/Y') ?? '—' }}
                  </td>
                  {{-- TARJETA (nuevo) --}}
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    @if($e->egreso_tipo === 'Tarjeta')
                      ${{ number_format($e->egreso, 2) }}
                    @else
                      $0.00
                    @endif
                  </td>
                  {{-- TIPO DE EGRESO (nuevo) --}}
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    {{ $e->egreso_tipo ?? '—' }}
                  </td>
                  {{-- Botones de acción para EGRESO --}}
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    <div class="flex items-center justify-center gap-1">
                      {{-- Botón Editar --}}
                      <a href="{{ route('admin.movimiento.edit', $e->id) }}"
                         class="w-6 h-6 flex items-center justify-center rounded bg-indigo-700 hover:bg-indigo-800 transition"
                         title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536M9 11l6 6M4 13v7h7l9-9a2.121 2.121 0 00-3-3l-9 9z"/>
                        </svg>
                      </a>
                      {{-- Botón Eliminar --}}
                      <form action="{{ route('admin.movimiento.destroy', $e->id) }}"
                            method="POST" class="m-0 p-0 inline"
                            onsubmit="return confirm('¿Eliminar este egreso?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-6 h-6 flex items-center justify-center rounded bg-red-600 hover:bg-red-700 transition"
                                title="Eliminar">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z"/>
                          </svg>
                        </button>
                      </form>
                      {{-- Botón PAGAR SOLO PARA CRÉDITO NO PAGADO --}}
                      @if($e->egreso_tipo === 'Crédito' && !$e->pagado)
                        <form method="POST" action="{{ route('admin.credito.pagar', $e->id) }}" class="m-0 p-0 inline">
                          @csrf
                          <button type="submit"
                            class="px-3 py-1 text-xs rounded-md bg-green-500 hover:bg-green-600 text-white font-semibold shadow"
                            title="Marcar como pagado">
                            Pagar
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                @else
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">
                    <div class="flex items-center justify-center gap-1">
                      {{-- Botón editar deshabilitado --}}
                      <button disabled
                        class="w-6 h-6 flex items-center justify-center rounded bg-indigo-300 opacity-50 cursor-not-allowed"
                        title="Sin egreso para editar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536M9 11l6 6M4 13v7h7l9-9a2.121 2.121 0 00-3-3l-9 9z"/>
                        </svg>
                      </button>
                      {{-- Botón eliminar deshabilitado --}}
                      <button disabled
                        class="w-6 h-6 flex items-center justify-center rounded bg-red-300 opacity-50 cursor-not-allowed"
                        title="Sin egreso para eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                @endif

                @php
                    // Colores pastel según el estado del crédito
                    $trClass = '';
                    $mostrarBotonPagar = false;

                    if(isset($e) && $e->egreso_tipo === 'Crédito') {
                        if($e->pagado) {
                            $trClass = 'bg-green-100';
                        } else {
                            $mostrarBotonPagar = true;
                            if($e->egreso_vencimiento) {
                                $vencimiento = \Carbon\Carbon::parse($e->egreso_vencimiento);
                                $hoy = \Carbon\Carbon::now();
                                $diff = $hoy->diffInDays($vencimiento, false);
                                if($diff > 7) {
                                    $trClass = 'bg-blue-50';
                                } elseif($diff > 2) {
                                    $trClass = 'bg-yellow-100';
                                } elseif($diff >= 0) {
                                    $trClass = 'bg-red-200';
                                } else {
                                    $trClass = 'bg-red-400 text-white';
                                }
                            } else {
                                $trClass = 'bg-blue-50';
                            }
                        }
                    }
                @endphp

              </tr>
            @empty
              <tr>
                <td colspan="19" class="px-3 py-4 text-center text-gray-500">
                  No hay movimientos registrados.
                </td>
              </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollSup = document.getElementById('scrollbar-superior');
    const scrollSupContent = document.getElementById('scrollbar-superior-content');
    const scrollDiv = document.getElementById('tabla-movimientos');

    // Si la tabla cambia de tamaño, igualar el ancho del scroll superior
    function syncWidth() {
        const table = scrollDiv.querySelector('table');
        scrollSupContent.style.width = table.scrollWidth + 'px';
    }
    syncWidth();
    window.addEventListener('resize', syncWidth);

    // Sincronizar scroll horizontal
    let fromTable = false, fromSup = false;

    scrollSup.addEventListener('scroll', function() {
        if (!fromTable) {
            fromSup = true;
            scrollDiv.scrollLeft = scrollSup.scrollLeft;
        }
        fromTable = false;
    });
    scrollDiv.addEventListener('scroll', function() {
        if (!fromSup) {
            fromTable = true;
            scrollSup.scrollLeft = scrollDiv.scrollLeft;
        }
        fromSup = false;
    });
});
</script>
@endsection
