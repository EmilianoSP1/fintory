{{-- resources/views/empleado/dashboard.blade.php --}}
@extends('layouts.app')

@section('titulo','Panel de Empleado')

{{-- Estilos de scroll sutil (idénticos a admin para UX consistente) --}}
<style>
  #scrollbar-superior {
      background: transparent;
  }
  #scrollbar-superior::-webkit-scrollbar {
      height: 6px;
      background: #f3f4f6;
      border-radius: 8px;
  }
  #scrollbar-superior::-webkit-scrollbar-thumb {
      background: rgba(120,120,130,0.38);
      border-radius: 8px;
      border: 1px solid #e5e7eb;
  }
  #scrollbar-superior {
      scrollbar-color: rgba(120,120,130,0.38) #f3f4f6;
      scrollbar-width: thin;
  }

  #tabla-movimientos::-webkit-scrollbar,
  #tabla-cortes::-webkit-scrollbar {
      height: 6px;
      background: #f3f4f6;
      border-radius: 8px;
  }
  #tabla-movimientos::-webkit-scrollbar-thumb,
  #tabla-cortes::-webkit-scrollbar-thumb {
      background: rgba(120,120,130,0.38);
      border-radius: 8px;
      border: 1px solid #e5e7eb;
  }
  #tabla-movimientos,
  #tabla-cortes {
      scrollbar-color: rgba(120,120,130,0.38) #f3f4f6;
      scrollbar-width: thin;
  }
</style>

@section('contenido')
  {{-- HEADER propio de Empleado (se mantiene; NO es el header de admin) --}}
  <div 
    x-data="{ openMenu: false }" 
    class="fixed top-0 left-0 w-full bg-white shadow z-50 transition-colors dark:bg-gray-900"
  >
    <div class="flex items-center h-14 px-4 md:px-6">
      <button
        @click="openMenu = !openMenu"
        class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:hover:bg-gray-800"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-200"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <span class="ml-4 text-xl font-semibold text-gray-800 dark:text-gray-100">Fintory</span>
      <div class="flex-1 flex justify-center space-x-4">
        <a href="{{ route('empleado.inventario') }}"
           class="relative text-black text-sm font-medium px-2 hover:text-gray-600 transition dark:text-gray-100 dark:hover:text-indigo-400">
          Inventario
          <span class="absolute top-1/2 right-0 h-6 border-r border-black transform -translate-y-1/2 dark:border-gray-400"></span>
        </a>
        <a href="{{ route('empleado.proveedores') }}"
           class="text-black text-sm font-medium px-2 hover:text-gray-600 transition dark:text-gray-100 dark:hover:text-indigo-400">
          Proveedores
        </a>
      </div>

      {{-- Botón modo oscuro --}}
      <div 
        x-data="{
          dark: localStorage.getItem('modoOscuro') === 'true',
          toggle() {
              this.dark = !this.dark;
              document.documentElement.classList.toggle('dark', this.dark);
              localStorage.setItem('modoOscuro', this.dark);
          },
          init() {
              document.documentElement.classList.toggle('dark', this.dark);
          }
        }" 
        x-init="init"
        class="ml-2"
      >
        <button @click="toggle"
          class="p-2 rounded-full border border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 transition"
          title="Cambiar modo oscuro/claro"
        >
          <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="currentColor"/>
              <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.364-7.364l-1.414 1.414M7.05 17.95l-1.414 1.414m12.728 0l-1.414-1.414M7.05 6.05L5.636 4.636"/>
          </svg>
          <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="currentColor"
                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/>
          </svg>
        </button>
      </div>
      <div class="w-6"></div>
    </div>

    {{-- Menú lateral --}}
    <div 
      x-show="openMenu" 
      x-transition:enter="transition ease-out duration-200" 
      x-transition:enter-start="-translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="-translate-x-full"
      class="fixed inset-0 z-50 flex"
      style="display: none;"
    >
      <div @click="openMenu = false" class="fixed inset-0 bg-black/40"></div>
      <aside class="relative bg-white w-64 h-full shadow-lg dark:bg-gray-900 transition-colors">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <span class="text-xl font-bold text-gray-800 dark:text-white">Menú</span>
        </div>
        <nav class="flex flex-col py-2">
          <a href="{{ route('empleado.dashboard') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor"><path d="M3 12l2-2 4 4 8-8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Panel
          </a>
          <a href="{{ route('empleado.inventario') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
            Inventario
          </a>
          <a href="{{ route('empleado.proveedores') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor"><rect width="16" height="16" x="4" y="4" stroke-width="2"/></svg>
            Proveedores
          </a>
          <form method="POST" action="{{ route('logout') }}" class="px-6 py-3">
            @csrf
            <button type="submit" class="flex items-center gap-3 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 w-full px-2 py-2 rounded transition font-medium">
              <svg class="w-5 h-5" fill="none" stroke="currentColor">
                <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Cerrar sesión
            </button>
          </form>
        </nav>
      </aside>
    </div>
  </div>

  {{-- CUERPO --}}
  <div class="pt-14 bg-gray-100 min-h-screen transition-colors dark:bg-gray-950">
    <div id="dashboard" class="mx-2.5">
      <h1 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-white">Panel de Empleado</h1>

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

      {{-- ================= FORMULARIO DE CIERRE (se mantiene igual al tuyo) ================= --}}
      <form
        action="{{ route('empleado.cierre') }}"
        method="POST"
        x-data="{
          bruto: @js(old('egreso_monto',0)),
          tipo:  @js(old('egreso_tipo','')),
          otrosSeleccionado: @js(old('concepto_tipo', '')),
          transfer_bruto: @js(old('venta_transferencia',0)),
          comision: 0.36
        }"
        class="space-y-8"
        autocomplete="off"
      >
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {{-- INGRESOS --}}
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
              <div>
                <label class="block mb-1 dark:text-gray-200">Venta Tarjeta</label>
                <input type="number" name="venta_tarjeta" step="0.01"
                       value="{{ old('venta_tarjeta') }}"
                       class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-white" placeholder="0.00">
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
                <div x-show="transfer_bruto > 0" class="text-sm text-gray-600 mt-1 dark:text-gray-300">
                  Comisión fija por transferencia: <strong>$0.36</strong><br>
                  Total neto ingreso: <strong x-text="(transfer_bruto > 0 ? (transfer_bruto-0.36).toFixed(2) : '0.00')"></strong>
                </div>
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
                  <option value="Crédito">Crédito</option>
                  {{-- Si también egresan por Tarjeta, puedes añadirla aquí igual que en admin --}}
                  <option value="Tarjeta">Tarjeta</option>
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
                    :value="proveedorCredito === 'escribir' ? proveedorPersonalizado : (proveedorCredito ? proveedorCredito : '')">
                </div>
                <div>
                  <label class="block mb-1 dark:text-gray-200">Vencimiento</label>
                  <input type="date" name="egreso_vencimiento" class="w-full border rounded px-3 py-2 dark:bg-gray-900 dark:text-gray-100">
                </div>
              </div>
            </div>

            <div class="mt-6 text-right">
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg">
                Enviar
              </button>
            </div>
          </div>
        </div>
      </form>

      {{-- ==================== TOTALES Y CONCEPTOS INGRESOS/EGRESOS (igual que admin, sin filtro fecha) ==================== --}}
      <div class="max-w-7xl mx-auto px-4">
        <div class="w-full flex flex-col gap-5 items-start mb-10 mt-10">
          {{-- Línea INGRESOS --}}
          <div class="flex flex-row gap-5 items-center">
            {{-- Total INGRESOS --}}
            <div class="bg-white border border-gray-200 rounded-lg px-8 py-4 shadow-sm text-center" style="min-width:210px;">
              <span class="block text-[11px] text-gray-700 font-semibold uppercase tracking-wider mb-1">Total Ingresos</span>
              <span class="text-2xl font-extrabold text-indigo-700">${{ number_format($totalIngresos ?? 0, 2) }}</span>
            </div>

            {{-- Conceptos de INGRESOS (netos como en admin: restando egresos por método) --}}
            <div class="flex flex-row gap-2">
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">OTROS</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalOtros ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">EFECTIVO</span>
                <span class="block text-base font-bold text-gray-800">
                  ${{ number_format(($totalEfectivo ?? 0) - ($totalEgresoEfectivo ?? 0), 2) }}
                </span>
                <span class="block text-[10px] text-gray-400 mt-1">
                  <span class="font-semibold">({{ number_format($totalEfectivo ?? 0,2) }} - {{ number_format($totalEgresoEfectivo ?? 0,2) }})</span>
                </span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TARJETA</span>
                <span class="block text-base font-bold text-gray-800">
                  ${{ number_format(($totalTarjeta ?? 0) - ($totalEgresoTarjeta ?? 0), 2) }}
                </span>
                <span class="block text-[10px] text-gray-400 mt-1">
                  <span class="font-semibold">({{ number_format($totalTarjeta ?? 0,2) }} - {{ number_format($totalEgresoTarjeta ?? 0,2) }})</span>
                </span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">VALES</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalVales ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">PAGOS</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalPagos ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TRANSFERENCIA</span>
                <span class="block text-base font-bold text-gray-800">
                  ${{ number_format(($totalTransferencia ?? 0) - ($totalEgresoTransferencia ?? 0), 2) }}
                </span>
                <span class="block text-[10px] text-gray-400 mt-1">
                  <span class="font-semibold">({{ number_format($totalTransferencia ?? 0,2) }} - {{ number_format($totalEgresoTransferencia ?? 0,2) }})</span>
                </span>
              </div>
            </div>
          </div>

          {{-- Línea EGRESOS --}}
          <div class="flex flex-row gap-5 items-center">
            {{-- Total EGRESOS --}}
            <div class="bg-white border border-gray-200 rounded-lg px-8 py-4 shadow-sm text-center" style="min-width:210px;">
              <span class="block text-[11px] text-gray-700 font-semibold uppercase tracking-wider mb-1">Total Egresos</span>
              <span class="text-2xl font-extrabold text-rose-700">${{ number_format($totalEgresos ?? 0, 2) }}</span>
            </div>

            {{-- Conceptos EGRESOS --}}
            <div class="flex flex-row gap-2">
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">EFECTIVO</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoEfectivo ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TARJETA</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoTarjeta ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">TRANSFERENCIA</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoTransferencia ?? 0, 2) }}</span>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-center min-w-[95px]">
                <span class="block text-[10px] text-gray-600 uppercase font-semibold mb-1">CRÉDITO</span>
                <span class="block text-base font-bold text-gray-800">${{ number_format($totalEgresoCredito ?? 0, 2) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ==================== TABLA GRANDE DE MOVIMIENTOS (igual a admin pero SIN acciones) ==================== --}}

      {{-- Scroll superior sincronizado --}}
      <div id="scrollbar-superior" style="width:100%;overflow-x:auto;overflow-y:hidden;height:12px;margin-bottom:1px;">
        <div id="scrollbar-superior-content" style="width:1800px;height:1px;"></div>
      </div>

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
              {{-- (en empleado NO hay columna de acciones de ingreso) --}}
              <th class="px-0 border-l border-gray-200"></th>
              <th class="px-3 py-2 text-center">Concepto</th>
              <th class="px-3 py-2 text-center">Monto</th>
              <th class="px-3 py-2 text-center">Nota/Factura</th>
              <th class="px-3 py-2 text-center">Origen/Destino</th>
              <th class="px-3 py-2 text-center">Vencimiento</th>
              <th class="px-3 py-2 text-center">Tarjeta (Egreso)</th>
              <th class="px-3 py-2 text-center">Tipo Egreso</th>
              {{-- (en empleado NO hay columna de acciones de egreso) --}}
            </tr>
          </thead>

          <tbody class="bg-white">
            @forelse($filas as $fila)
              @php
                $m = $fila['ingreso'] ?? null;
                $e = $fila['egreso'] ?? null;

                // Para la celda "Empleado" tomamos quien creó el movimiento
                $usuario = null;
                if($m) $usuario = $m->usuario ?? null;
                elseif($e) $usuario = $e->usuario ?? null;
              @endphp

              <tr class="hover:bg-gray-50">
                {{-- Empleado --}}
                <td class="px-3 py-2 text-center align-middle">
                  {{ $usuario ? $usuario->name : '—' }}
                </td>

                {{-- ==== INGRESO ==== --}}
                @if($m)
                  <td class="px-3 py-2 text-center align-middle">
                    {{ $m->created_at->locale('es')->isoFormat('dddd') }}
                  </td>
                  <td class="px-3 py-2 text-center align-middle">
                    {{ $m->created_at->format('d/m/Y') }}
                  </td>

                  {{-- Otros con descripción expandible --}}
                  @php
                    $fullDesc   = trim($m->concepto ?? '');
                    $words      = $fullDesc === '' ? [] : explode(' ', $fullDesc);
                    $preview    = implode(' ', array_slice($words, 0, 13));
                    $remaining  = array_slice($words, 13);
                  @endphp
                  <td class="px-3 py-2 align-top" style="max-width: 160px; word-break: break-all; overflow-wrap: break-word;">
                    <div x-data="{ open: false }" class="whitespace-normal text-xs text-gray-800">
                      <div class="font-semibold text-center">
                        ${{ number_format($m->otros, 2) }}
                      </div>
                      <div class="mt-1">
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
                @else
                  {{-- Sin ingreso: 8 celdas en blanco --}}
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                @endif

                {{-- Separador --}}
                <td class="px-0 border-l border-gray-200"></td>

                {{-- ==== EGRESO ==== --}}
                @php
                  // Colores pastel según el estado del crédito (mismo criterio que admin)
                  $trClass = '';
                  if($e && $e->egreso_tipo === 'Crédito') {
                      if($e->pagado) {
                          $trClass = 'bg-green-100';
                      } else {
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

                @if($e)
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
                  {{-- TARJETA (Egreso) --}}
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    @if($e->egreso_tipo === 'Tarjeta')
                      ${{ number_format($e->egreso, 2) }}
                    @else
                      $0.00
                    @endif
                  </td>
                  {{-- Tipo de egreso --}}
                  <td class="px-3 py-2 text-center align-middle {{ $trClass }}">
                    {{ $e->egreso_tipo ?? '—' }}
                  </td>
                  {{-- (sin acciones en empleado) --}}
                @else
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                  <td class="px-3 py-2 text-center align-middle">$0.00</td>
                  <td class="px-3 py-2 text-center align-middle">—</td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="18" class="px-3 py-4 text-center text-gray-500">
                  No hay movimientos registrados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Sincronización del scroll superior con la tabla --}}
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const scrollSup = document.getElementById('scrollbar-superior');
          const scrollSupContent = document.getElementById('scrollbar-superior-content');
          const scrollDiv = document.getElementById('tabla-movimientos');

          function syncWidth() {
            const table = scrollDiv.querySelector('table');
            if (table) {
              scrollSupContent.style.width = table.scrollWidth + 'px';
            }
          }
          syncWidth();
          window.addEventListener('resize', syncWidth);

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

      {{-- ==================== TABLA DE CORTES REALIZADOS (3a tabla) ==================== --}}
      @php
        // Armamos cortes por FECHA y EMPLEADO a partir de $filas (sólo con ingresos)
        $cortes = [];
        if(isset($filas)) {
          foreach ($filas as $f) {
            $m = $f['ingreso'] ?? null;
            if(!$m) continue;

            $fechaKey = $m->created_at->format('Y-m-d');
            $empleado = $m->usuario->name ?? '—';

            $cortes[$fechaKey] = $cortes[$fechaKey] ?? [];
            $cortes[$fechaKey][$empleado] = $cortes[$fechaKey][$empleado] ?? [
              'otros' => 0, 'efectivo' => 0, 'tarjeta' => 0,
              'vales' => 0, 'pagos' => 0, 'transferencia' => 0
            ];

            $cortes[$fechaKey][$empleado]['otros']          += (float)($m->otros ?? 0);
            $cortes[$fechaKey][$empleado]['efectivo']       += (float)($m->efectivo ?? 0);
            $cortes[$fechaKey][$empleado]['tarjeta']        += (float)($m->tarjeta ?? 0);
            $cortes[$fechaKey][$empleado]['vales']          += (float)($m->caldes ?? 0);
            $cortes[$fechaKey][$empleado]['pagos']          += (float)($m->pagos_clientes ?? 0);
            $cortes[$fechaKey][$empleado]['transferencia']  += (float)($m->venta_transferencia ?? 0);
          }
          krsort($cortes); // fechas recientes primero
        }
      @endphp

      <div class="max-w-7xl mx-auto bg-white shadow rounded-lg mt-10 overflow-x-auto" id="tabla-cortes">
        <div class="px-4 pt-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Cortes realizados por empleados</h2>
          <p class="text-xs text-gray-500 mb-2">Agrupado por fecha y empleado (suma de ingresos del día).</p>
        </div>
        <table class="w-full text-xs border border-gray-200 divide-y divide-gray-200">
          <thead class="bg-gray-50 text-gray-700 uppercase">
            <tr>
              <th class="px-3 py-2 text-center">Fecha</th>
              <th class="px-3 py-2 text-center">Empleado</th>
              <th class="px-3 py-2 text-center">Otros</th>
              <th class="px-3 py-2 text-center">Efectivo</th>
              <th class="px-3 py-2 text-center">Tarjeta</th>
              <th class="px-3 py-2 text-center">Vales</th>
              <th class="px-3 py-2 text-center">Pagos</th>
              <th class="px-3 py-2 text-center">Transferencia</th>
              <th class="px-3 py-2 text-center">Total ingreso del día</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            @forelse($cortes ?? [] as $fecha => $porEmpleado)
              @foreach($porEmpleado as $empleado => $tot)
                @php
                  $totalDia = ($tot['otros'] + $tot['efectivo'] + $tot['tarjeta'] + $tot['vales'] + $tot['pagos'] + $tot['transferencia']);
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-center align-middle">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                  <td class="px-3 py-2 text-center align-middle">{{ $empleado }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['otros'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['efectivo'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['tarjeta'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['vales'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['pagos'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle">${{ number_format($tot['transferencia'], 2) }}</td>
                  <td class="px-3 py-2 text-center align-middle font-semibold text-indigo-700">
                    ${{ number_format($totalDia, 2) }}
                  </td>
                </tr>
              @endforeach
            @empty
              <tr>
                <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                  No hay cortes registrados aún.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div> {{-- /#dashboard --}}
  </div> {{-- /.bg --}}
@endsection
