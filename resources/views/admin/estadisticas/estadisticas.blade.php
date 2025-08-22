<script src="{{ asset('js/estadisticas-admin.js') }}"></script>
@extends('layouts.app')

@section('titulo','Estadísticas')

@section('contenido')
  {{-- CONTENIDO PRINCIPAL SOLO, SIN HEADER NI MENÚ, EL LAYOUT LO HACE --}}
  <div class="pt-4 mx-4">
    <h1 class="text-3xl font-bold mb-4 text-center">
      Estadísticas 
      @if($periodo == 'mes') del Mes
      @elseif($periodo == 'año') del Año
      @else de la Semana
      @endif
    </h1>
    {{-- Selector de periodo --}}
    <form method="GET" action="{{ route('admin.estadisticas') }}" class="flex justify-center items-center gap-3 mb-4">
      <label class="text-gray-700 font-semibold">Ver:</label>
      <select name="periodo" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1">
        <option value="semana" {{ $periodo == 'semana' ? 'selected' : '' }}>Esta semana</option>
        <option value="mes"    {{ $periodo == 'mes'    ? 'selected' : '' }}>Este mes</option>
        <option value="año"    {{ $periodo == 'año'    ? 'selected' : '' }}>Este año</option>
      </select>
    </form>
  </div>

  @php
    // Formatear fechas para mostrar en el encabezado
    $formatoInicio = $inicioSem->format('d/m/Y');
    $formatoFin    = $finSem->format('d/m/Y');
  @endphp

  {{-- ================================================
     GRÁFICAS Y RESUMEN, EN LAYOUT HORIZONTAL
     ================================================ --}}
  <div class="mx-auto max-w-6xl mt-4 flex flex-col md:flex-row gap-6">
    {{-- IZQUIERDA: Dos gráficas pequeñas verticales --}}
    <div class="flex-1 flex flex-col gap-4">
      {{-- Gráfica 1: Barras de Ingresos por periodo --}}
      <div class="bg-white p-4 shadow rounded h-48">
        <h2 class="text-lg font-medium mb-2 text-gray-700 text-center">
          @if($periodo == 'mes')
            Ingresos por Día
          @elseif($periodo == 'año')
            Ingresos por Mes
          @else
            Ingresos Diarios
          @endif
        </h2>
        <canvas id="chartIngresos" class="w-full h-full"></canvas>
      </div>
      {{-- Gráfica 2: Línea de Egresos por periodo --}}
      <div class="bg-white p-4 shadow rounded h-48">
        <h2 class="text-lg font-medium mb-2 text-gray-700 text-center">
          @if($periodo == 'mes')
            Egresos por Día
          @elseif($periodo == 'año')
            Egresos por Mes
          @else
            Egresos Diarios
          @endif
        </h2>
        <canvas id="chartEgresos" class="w-full h-full"></canvas>
      </div>
    </div>

    {{-- DERECHA: Tarjetas de resumen --}}
    <div class="w-full md:w-1/3 grid grid-cols-1 gap-4">
      {{-- Tarjeta Ingresos Totales --}}
      <div class="bg-green-100 border-l-4 border-green-600 p-6 rounded shadow">
        <div class="text-sm font-medium text-gray-700">
          Ingresos 
          @if($periodo == 'mes')
            este mes
          @elseif($periodo == 'año')
            este año
          @else
            esta semana
          @endif
          <br>
          <span class="text-xs text-gray-500">({{ $formatoInicio }} - {{ $formatoFin }})</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-green-800">
          ${{ number_format($ingresosSemana, 2) }}
        </div>
      </div>

      {{-- Tarjeta Egresos Totales --}}
      <div class="bg-red-100 border-l-4 border-red-600 p-6 rounded shadow">
        <div class="text-sm font-medium text-gray-700">
          Egresos 
          @if($periodo == 'mes')
            este mes
          @elseif($periodo == 'año')
            este año
          @else
            esta semana
          @endif
          <br>
          <span class="text-xs text-gray-500">({{ $formatoInicio }} - {{ $formatoFin }})</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-red-800">
          ${{ number_format($egresosSemana, 2) }}
        </div>
      </div>
    </div>
  </div>

  {{-- =========== DESGLOSE DE INGRESOS POR MÉTODO ========== --}}
  <div class="mx-auto max-w-6xl mt-4 md:mt-6 grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="bg-gray-50 p-4 rounded shadow text-center">
      <div class="text-sm font-medium text-gray-700">Efectivo</div>
      <div class="mt-1 text-xl font-bold text-gray-900">
        ${{ number_format($ingresosEfectivo, 2) }}
      </div>
    </div>
    <div class="bg-gray-50 p-4 rounded shadow text-center">
      <div class="text-sm font-medium text-gray-700">Tarjeta</div>
      <div class="mt-1 text-xl font-bold text-gray-900">
        ${{ number_format($ingresosTarjeta, 2) }}
      </div>
    </div>
    <div class="bg-gray-50 p-4 rounded shadow text-center">
      <div class="text-sm font-medium text-gray-700">Vales</div>
      <div class="mt-1 text-xl font-bold text-gray-900">
        ${{ number_format($ingresosVales, 2) }}
      </div>
    </div>
    <div class="bg-gray-50 p-4 rounded shadow text-center">
      <div class="text-sm font-medium text-gray-700">Pagos Clientes</div>
      <div class="mt-1 text-xl font-bold text-gray-900">
        ${{ number_format($ingresosPagosClientes, 2) }}
      </div>
    </div>
    <div class="bg-gray-50 p-4 rounded shadow text-center">
      <div class="text-sm font-medium text-gray-700">Transferencia</div>
      <div class="mt-1 text-xl font-bold text-gray-900">
        ${{ number_format($ingresosTransferencia, 2) }}
      </div>
    </div>
  </div>

  {{-- ================================================
     TABLA DETALLADA DE MOVIMIENTOS DEL PERIODO
     ================================================ --}}
  <div class="mx-auto max-w-6xl mt-8 bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th colspan="6" class="px-6 py-4 text-left text-xl font-semibold text-gray-700">
            Detalle de Movimientos ({{ $formatoInicio }} - {{ $formatoFin }})
          </th>
        </tr>
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Fecha</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Concepto</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Tipo</th>
          <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Ingreso</th>
          <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Egreso</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Método</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($detalleSemana as $mov)
          <tr>
            {{-- Fecha --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
              {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y') }}
            </td>

            {{-- Concepto --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
              {{ $mov->concepto }}
            </td>

            {{-- Tipo (Ingreso vs Egreso) --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
              @if($mov->egreso == 0)
                Ingreso
              @else
                Egreso
              @endif
            </td>

            {{-- Monto de Ingreso (si aplica) --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
              @if($mov->egreso == 0)
                ${{ number_format(($mov->efectivo ?? 0) + ($mov->tarjeta ?? 0) + ($mov->caldes ?? 0) + ($mov->pagos_clientes ?? 0) + ($mov->venta_transferencia ?? 0) + ($mov->otros ?? 0), 2) }}
              @else
                —
              @endif
            </td>

            {{-- Monto de Egreso (si aplica) --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
              @if($mov->egreso > 0)
                ${{ number_format($mov->egreso, 2) }}
              @else
                —
              @endif
            </td>

            {{-- Método de pago/instrumentos --}}
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
              @if($mov->egreso == 0)
                @php
                  $metodos = [];
                  if($mov->efectivo > 0) $metodos[] = 'Efectivo';
                  if($mov->tarjeta > 0)  $metodos[] = 'Tarjeta';
                  if($mov->caldes > 0)   $metodos[] = 'Vales';
                  if($mov->pagos_clientes > 0) $metodos[] = 'Pagos Clientes';
                  if($mov->venta_transferencia > 0) $metodos[] = 'Transferencia';
                  if($mov->otros > 0) $metodos[] = 'Otros';
                @endphp
                {{ implode(', ', $metodos) }}
              @else
                {{ $mov->egreso_tipo ?? '—' }}
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
              No hay movimientos registrados para este periodo.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Incluimos Chart.js desde CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Datos provenientes de PHP (JSON)
      const labels       = {!! $jsonLabels !!};
      const ingresosData = {!! $jsonIngresos !!};
      const egresosData  = {!! $jsonEgresos !!};

      // --- Gráfica 1: Barras de Ingresos ---
      const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
      new Chart(ctxIngresos, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Ingresos',
            data: ingresosData,
            backgroundColor: 'rgba(34, 197, 94, 0.7)',   // verde semitransparente
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              grid: { display: false }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => '$' + value.toLocaleString()
              }
            }
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: context => {
                  let label = 'Ingresos: ';
                  if (context.parsed.y !== null) {
                    label += '$' + context.parsed.y.toLocaleString();
                  }
                  return label;
                }
              }
            }
          }
        }
      });

      // --- Gráfica 2: Línea de Egresos ---
      const ctxEgresos = document.getElementById('chartEgresos').getContext('2d');
      new Chart(ctxEgresos, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Egresos',
            data: egresosData,
            fill: false,
            borderColor: 'rgba(239, 68, 68, 1)',    // rojo
            backgroundColor: 'rgba(239, 68, 68, 0.7)',
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(239, 68, 68, 0.9)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              grid: { display: false }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => '$' + value.toLocaleString()
              }
            }
          },
          plugins: {
            legend: {
              position: 'top',
              labels: {
                boxWidth: 12,
                padding: 10
              }
            },
            tooltip: {
              callbacks: {
                label: context => {
                  let label = 'Egresos: ';
                  if (context.parsed.y !== null) {
                    label += '$' + context.parsed.y.toLocaleString();
                  }
                  return label;
                }
              }
            }
          }
        }
      });
    });
  </script>
@endsection
