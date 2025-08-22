<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Movimiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    /**
     * Muestra el panel de empleado con los movimientos SOLO del día actual.
     */
public function dashboard()
{
    $hoy    = Carbon::today();
    $manana = Carbon::tomorrow();

    // Movimientos de HOY por tienda del usuario
    $movimientosHoy = Movimiento::where('tienda_id', Auth::user()->tienda_id)
        ->whereBetween('created_at', [$hoy, $manana])
        ->orderBy('created_at')
        ->get();

    // Separar ingresos y egresos
    $ingresos = $movimientosHoy->filter(fn($m) => (float)$m->egreso === 0.0);
    $egresos  = $movimientosHoy->filter(fn($m) => (float)$m->egreso > 0.0);

    // ==== Totales como Admin ====
    // Ingresos (brutos ya guardados; en transferencia ya restaste 0.36 en guardarCierre)
    $totalIngresos      = $ingresos->sum(fn($m) => $m->efectivo + $m->tarjeta + $m->caldes + $m->pagos_clientes + $m->venta_transferencia + $m->otros);
    $totalEfectivo      = $ingresos->sum('efectivo');
    $totalTarjeta       = $ingresos->sum('tarjeta');
    $totalVales         = $ingresos->sum('caldes');
    $totalPagos         = $ingresos->sum('pagos_clientes');
    $totalTransferencia = $ingresos->sum('venta_transferencia');
    $totalOtros         = $ingresos->sum('otros');

    // Egresos por tipo
    $totalEgresos             = $egresos->sum('egreso');
    $totalEgresoEfectivo      = $egresos->where('egreso_tipo', 'Efectivo')->sum('egreso');
    $totalEgresoTarjeta       = $egresos->where('egreso_tipo', 'Tarjeta')->sum('egreso');
    $totalEgresoTransferencia = $egresos->where('egreso_tipo', 'Transferencia')->sum('egreso');
    $totalEgresoCredito       = $egresos->where('egreso_tipo', 'Crédito')->sum('egreso');

    // ==== Emparejar filas ingreso|egreso como ya hacías ====
    $filas = [];
    $i     = 0;
    $n     = $movimientosHoy->count();
    while ($i < $n) {
        $curr = $movimientosHoy[$i];
        $fila = ['ingreso' => null, 'egreso' => null];

        if ((float)$curr->egreso === 0.0) {
            $fila['ingreso'] = $curr;
            if ($i + 1 < $n && (float)$movimientosHoy[$i+1]->egreso > 0.0) {
                $fila['egreso'] = $movimientosHoy[++$i];
            }
        } else {
            $fila['egreso'] = $curr;
        }

        $filas[] = $fila;
        $i++;
    }

    return view('empleado.dashboard', compact(
        'filas',
        // Totales de ingresos
        'totalIngresos',
        'totalEfectivo',
        'totalTarjeta',
        'totalVales',
        'totalPagos',
        'totalTransferencia',
        'totalOtros',
        // Totales de egresos
        'totalEgresos',
        'totalEgresoEfectivo',
        'totalEgresoTarjeta',
        'totalEgresoTransferencia',
        'totalEgresoCredito'
    ));
}


public function guardarCierre(Request $request)
{
    // 1) Validación
    $datos = $request->validate([
        'venta_efectivo'          => 'nullable|numeric|min:0',
        'venta_tarjeta'           => 'nullable|numeric|min:0',
        'venta_caldes'            => 'nullable|numeric|min:0',
        'pagos_clientes'          => 'nullable|numeric|min:0',
        'venta_transferencia'     => 'nullable|numeric|min:0',
        'concepto_tipo'           => 'nullable|string',
        'concepto'                => 'nullable|string|max:255',
        'otros_descripcion'       => 'nullable|string|max:255',
        'otros_monto'             => 'nullable|numeric|min:0',
        'egreso_monto'            => 'nullable|numeric|min:0',
        'egreso_tipo'             => 'nullable|string',
        'egreso_descripcion'      => 'nullable|string',
        'egreso_nota'             => 'nullable|string',
        'egreso_vencimiento'      => 'nullable|date',
        'transferencia_destino'   => 'nullable|string',
        'transferencia_otro_banco'=> 'nullable|string',
        'credito_origen'          => 'nullable|string',
        'credito_otro_banco'      => 'nullable|string',
        'banco_personalizado'     => 'nullable|string|max:255', 
        'proveedor_nombre'        => 'nullable|string|max:255',
    ]);

    // --- Lógica para guardar SOLO el banco correcto ---
// --- Lógica para guardar SOLO el banco correcto ---
// --- Lógica para guardar SOLO el banco correcto y proveedor ---
$bancoCredito = null;
$proveedorNombre = null;
if (
    isset($datos['egreso_tipo']) && $datos['egreso_tipo'] === 'Crédito'
    && isset($datos['credito_origen'])
) {
    if ($datos['credito_origen'] === 'Proveedor') {
        $proveedorNombre = $datos['proveedor_nombre'] ?? null;
    } elseif ($datos['credito_origen'] === 'Otros') {
        if (
            isset($datos['credito_otro_banco']) && $datos['credito_otro_banco'] === 'escribir'
            && !empty($datos['banco_personalizado'])
        ) {
            $bancoCredito = $datos['banco_personalizado'];
        } elseif (
            isset($datos['credito_otro_banco']) && !empty($datos['credito_otro_banco']) && $datos['credito_otro_banco'] !== 'escribir'
        ) {
            $bancoCredito = $datos['credito_otro_banco'];
        }
    }
}
    // 2) Creamos un batch para agrupar ingresos y egresos relacionados
    $batch = Str::uuid()->toString();

    DB::transaction(function() use ($datos, $batch, $bancoCredito, $proveedorNombre) {
        // INGRESO
        $vt = $datos['venta_transferencia'] ?? 0;
        if ($vt > 0) {
            $vt = max(0, $vt - 0.36);
        }

        Movimiento::create([
            'batch'               => $batch,
            'usuario_id'          => Auth::id(),
            'concepto'            => ($datos['otros_descripcion'] ?? null) ?: ($datos['concepto'] ?? ''),
            'efectivo'            => $datos['venta_efectivo']      ?? 0,
            'tarjeta'             => $datos['venta_tarjeta']       ?? 0,
            'caldes'              => $datos['venta_caldes']        ?? 0,
            'pagos_clientes'      => $datos['pagos_clientes']      ?? 0,
            'venta_transferencia' => $vt,
            'otros'               => $datos['otros_monto']         ?? 0,
            'otros_descripcion'   => $datos['otros_descripcion']   ?? null,
            'egreso'              => 0,
            'tienda_id'            => Auth::user()->tienda_id,
        ]);

        // EGRESO
        if (
            ($datos['egreso_monto'] ?? 0) > 0
            && ! empty($datos['egreso_tipo'])
        ) {
            // armar el texto de egreso
            $conceptoE = 'Cierre diario (Egreso) ' . $datos['egreso_tipo'];

            if ($datos['egreso_tipo'] === 'Transferencia') {
                $dest   = $datos['transferencia_destino'] ?? '';
                $suffix = $dest === 'Otros'
                    ? ' (' . ($datos['transferencia_otro_banco'] ?? '') . ')'
                    : '';
                $conceptoE .= " → {$dest}{$suffix}";
            }

            if ($datos['egreso_tipo'] === 'Crédito') {
                $ori    = $datos['credito_origen'] ?? '';
                $suffix = $ori === 'Otros'
                    ? ' (' . ($bancoCredito ?? '') . ')'
                    : '';
                $vto    = $datos['egreso_vencimiento'] ?? '';
                $conceptoE .= " → {$ori}{$suffix} (venc: {$vto})";
            }

            if (! empty($datos['egreso_descripcion'])) {
                $conceptoE .= ': ' . $datos['egreso_descripcion'];
            }

            Movimiento::create([
                'batch'                    => $batch,
                'usuario_id'               => Auth::id(),
                'tienda_id'                => Auth::user()->tienda_id, 
                'concepto'                 => $conceptoE,
                'efectivo'                 => 0,
                'tarjeta'                  => 0,
                'caldes'                   => 0,
                'pagos_clientes'           => 0,
                'venta_transferencia'      => 0,
                'otros'                    => 0,
                'otros_descripcion'        => null,
                'egreso'                   => $datos['egreso_monto'],
                'egreso_tipo'              => $datos['egreso_tipo'],
                'egreso_descripcion'       => $datos['egreso_descripcion']      ?? null,
                'egreso_nota'              => $datos['egreso_nota']             ?? null,
                'transferencia_destino'    => $datos['transferencia_destino']   ?? null,
                'transferencia_otro_banco' => $datos['transferencia_otro_banco']?? null,
                'credito_origen'           => $datos['credito_origen']          ?? null,
                'credito_otro_banco'       => $bancoCredito, // <--- SOLO AQUI
                'proveedor_nombre'         => $proveedorNombre,
                'egreso_vencimiento'       => $datos['egreso_vencimiento']      ?? null,
            ]);
        }
    });

    return back()->with('success', 'Cierre guardado correctamente.');
}

    public function eliminarMovimiento(Request $request, Movimiento $movimiento)
    {
        Movimiento::where('batch', $movimiento->batch)->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect(route('empleado.dashboard') . '#dashboard')
            ->with('success', 'Movimientos del batch eliminados.');
    }

    /**
     * Elimina un único movimiento. Responde JSON para AJAX.
     */
    public function destroy(Request $request, Movimiento $movimiento)
    {
        $movimiento->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect(route('empleado.dashboard') . '#dashboard')
            ->with('success', 'Movimiento eliminado correctamente.');
    }

    /**
     * Marca un movimiento de tipo Crédito como pagado.
     */
    public function marcarPagado(Movimiento $movimiento)
    {
        if ($movimiento->egreso_tipo === 'Crédito'
            && $movimiento->egreso_vencimiento
        ) {
            $movimiento->pagado = true;
            $movimiento->save();

            return back()->with('success', 'Movimiento marcado como pagado.');
        }

        return back()->with('error', 'No se pudo marcar este movimiento como pagado.');
    }

    /**
     * Muestra las estadísticas semanales (gráficas + tabla).
     */
    public function estadisticas(Request $request)
    {
        $selectedDate = Carbon::parse($request->query('date', now()->toDateString()));
        $inicioSem    = $selectedDate->copy()->startOfWeek();
        $finSem       = $selectedDate->copy()->endOfWeek();

        $detalleSemana = Movimiento::whereBetween('created_at', [
                $inicioSem->copy()->startOfDay(),
                $finSem->copy()->endOfDay()
            ])->orderBy('created_at')->get();

        $ingresosSemana = $detalleSemana
            ->where('egreso', 0)
            ->sum(fn($m) => 
                $m->efectivo 
              + $m->tarjeta 
              + $m->caldes 
              + $m->pagos_clientes 
              + $m->venta_transferencia 
              + $m->otros    // ahora incluye "otros"
            );

        $egresosSemana = $detalleSemana
            ->where('egreso', '>', 0)
            ->sum('egreso');

        $labels        = [];
        $datosIngresos = [];
        $datosEgresos  = [];

        for ($i = 0; $i < 7; $i++) {
            $dia           = $inicioSem->copy()->addDays($i);
            $labels[]      = $dia->locale('es')->isoFormat('dd D');

            $sumaIngresoDia = $detalleSemana
                ->whereBetween('created_at', [$dia->copy()->startOfDay(), $dia->copy()->endOfDay()])
                ->where('egreso', 0)
                ->sum(fn($m) => 
                    $m->efectivo 
                  + $m->tarjeta 
                  + $m->caldes 
                  + $m->pagos_clientes 
                  + $m->venta_transferencia 
                  + $m->otros
                );
            $datosIngresos[] = $sumaIngresoDia;

            $sumaEgresoDia = $detalleSemana
                ->whereBetween('created_at', [$dia->copy()->startOfDay(), $dia->copy()->endOfDay()])
                ->where('egreso', '>', 0)
                ->sum('egreso');
            $datosEgresos[] = $sumaEgresoDia;
        }

        return view('empleado.estadisticas', [
            'inicioSem'      => $inicioSem,
            'finSem'         => $finSem,
            'ingresosSemana' => $ingresosSemana,
            'egresosSemana'  => $egresosSemana,
            'detalleSemana'  => $detalleSemana,
            'jsonLabels'     => json_encode($labels),
            'jsonIngresos'   => json_encode($datosIngresos),
            'jsonEgresos'    => json_encode($datosEgresos),
        ]);
    }
}
