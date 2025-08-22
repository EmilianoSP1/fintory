<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Movimiento;
use App\Models\Pago;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PagosExport;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Log;



class AdminController extends Controller
{
    /**
     * Panel principal de administración:
     * Muestra los movimientos de todos los empleados de los últimos 7 días.
     */// 
private function esMovimientoVacio($mov) {
    if (!$mov) return true;
    return
        ($mov->efectivo ?? 0) == 0 &&
        ($mov->tarjeta ?? 0) == 0 &&
        ($mov->caldes ?? 0) == 0 &&
        ($mov->pagos_clientes ?? 0) == 0 &&
        ($mov->venta_transferencia ?? 0) == 0 &&
        ($mov->otros ?? 0) == 0 &&
        ($mov->egreso ?? 0) == 0;
}

public function dashboard(Request $request)
{
    $tienda_id = session('tienda_id', 1);

    // Determina el periodo de corte (semana, mes, año, todos, etc.)
    $corte = $request->input('periodo', 'semana'); // por default 'semana'

    if ($request->filled('fecha')) {
        // Filtrar solo por fecha exacta (sin importar hora)
        $movimientosSemana = \App\Models\Movimiento::where('tienda_id', $tienda_id)
            ->whereDate('created_at', $request->fecha)
            ->orderBy('created_at')
            ->with('usuario')
            ->get()
            ->values();
    } else {
        // Últimos 7 días (desde hace 6 días hasta hoy)
        $hace7 = \Carbon\Carbon::now()->subDays(6)->startOfDay();
        $hoy   = \Carbon\Carbon::now()->endOfDay();

        $movimientosSemana = \App\Models\Movimiento::where('tienda_id', $tienda_id)
            ->whereBetween('created_at', [$hace7, $hoy])
            ->orderBy('created_at')
            ->with('usuario')
            ->get()
            ->values();
    }

    $filas = [];
    $i     = 0;
    $n     = $movimientosSemana->count();

    while ($i < $n) {
        $curr = $movimientosSemana[$i];
        $fila = ['ingreso' => null, 'egreso' => null];

        if ($curr->egreso == 0) {
            $fila['ingreso'] = $curr;
            if (
                $i + 1 < $n &&
                $movimientosSemana[$i + 1]->egreso > 0 &&
                $movimientosSemana[$i + 1]->batch == $curr->batch
            ) {
                $fila['egreso'] = $movimientosSemana[++$i];
            }
        } else {
            $fila['egreso'] = $curr;
        }

        if (!$this->esMovimientoVacio($fila['ingreso']) || !$this->esMovimientoVacio($fila['egreso'])) {
            $filas[] = $fila;
        }

        $i++;
    }

    $ingresos = $movimientosSemana->where('egreso', 0);
    $totalOtros         = $ingresos->sum('otros');
    $totalEfectivo      = $ingresos->sum('efectivo');
    $totalTarjeta       = $ingresos->sum('tarjeta');
    $totalVales         = $ingresos->sum('caldes');
    $totalPagos         = $ingresos->sum('pagos_clientes');
    $totalTransferencia = $ingresos->sum('venta_transferencia');
    $totalIngresos      = $totalOtros + $totalEfectivo + $totalTarjeta + $totalVales + $totalPagos + $totalTransferencia;

    $egresos = $movimientosSemana->where('egreso', '>', 0);
    $totalEgresos              = $egresos->sum('egreso');
    $totalEgresoEfectivo       = $egresos->where('egreso_tipo', 'Efectivo')->sum('egreso');
    $totalEgresoTransferencia  = $egresos->where('egreso_tipo', 'Transferencia')->sum('egreso');
    $totalEgresoCredito        = $egresos->where('egreso_tipo', 'Crédito')->sum('egreso');
    $totalEgresoTarjeta       = $egresos->where('egreso_tipo', 'Tarjeta')->sum('egreso');

    // Devuelve la vista con la variable $corte
return view('admin.dashboard', compact(
    'filas',
    'totalIngresos',
    'totalOtros',
    'totalEfectivo',
    'totalTarjeta',
    'totalVales',
    'totalPagos',
    'totalTransferencia',
    'totalEgresos',
    'totalEgresoEfectivo',
    'totalEgresoTransferencia',
    'totalEgresoCredito',
    'totalEgresoTarjeta',   // <<< y agrégalo aquí
    'corte'
));
} 




public function listaEmpleados()
{
    $tienda_id = session('tienda_id', 1); // Usa la tienda activa
    $empleados = Usuario::where('rol', 'empleado')
                        ->where('tienda_id', $tienda_id)
                        ->get();
    return view('admin.empleados.index', compact('empleados'));
}





   public function guardarCierre(Request $request)
{
    $datos = $request->validate([
        'venta_efectivo'      => 'nullable|numeric|min:0',
        'venta_tarjeta'       => 'nullable|numeric|min:0',
        'venta_caldes'        => 'nullable|numeric|min:0',
        'pagos_clientes'      => 'nullable|numeric|min:0',
        'venta_transferencia' => 'nullable|numeric|min:0',
        'concepto_tipo'       => 'nullable|string',
        'otros_descripcion'   => 'nullable|string',
        'otros_monto'         => 'nullable|numeric|min:0',
        'egreso_tipo'         => 'nullable|string',
        'egreso_monto'        => 'nullable|numeric|min:0',
        'egreso_descripcion'  => 'nullable|string',
        'egreso_nota'         => 'nullable|string',
        'credito_origen'      => 'nullable|string',
        'credito_otro_banco'  => 'nullable|string',
        'banco_personalizado' => 'nullable|string',
        'proveedor_nombre'    => 'nullable|string',
        'egreso_vencimiento'  => 'nullable|date',
    ]);

    // Creamos un UUID para batch
    $batch = Str::uuid()->toString();

    DB::transaction(function() use ($datos, $batch) {
        $tienda_id = session('tienda_id', 1); // <-- AQUÍ tomamos la tienda activa

        // INGRESO “Otros”
        if (
            ($datos['concepto_tipo'] ?? '') === 'otros'
            && ($datos['otros_monto'] ?? 0) > 0
        ) {
            Movimiento::create([
                'batch'               => $batch,
                'usuario_id'          => Auth::id(),
                'concepto'            => $datos['otros_descripcion'] ?? 'Otros',
                'efectivo'            => 0,
                'tarjeta'             => 0,
                'caldes'              => 0,
                'pagos_clientes'      => 0,
                'venta_transferencia' => 0,
                'otros'               => $datos['otros_monto'],
                'otros_descripcion'   => $datos['otros_descripcion'],
                'egreso'              => 0,
                'tienda_id'           => $tienda_id, // <-- AGREGADO
            ]);
        } else {
            // Ingreso normal (con comisión de transferencia)
// Ingreso normal (con comisión de tarjeta)
$tarjeta = $datos['venta_tarjeta'] ?? 0;
if ($tarjeta > 0) {
    $comision = round($tarjeta * 0.0036, 2); // Calcula comisión al 0.36%
    $tarjeta = max(0, $tarjeta - $comision); // Resta la comisión al bruto
}

Movimiento::create([
    'batch'               => $batch,
    'usuario_id'          => Auth::id(),
    'concepto'            => $datos['concepto_tipo'] ?? '',
    'efectivo'            => $datos['venta_efectivo'] ?? 0,
    'tarjeta'             => $tarjeta, // <-- Ya con comisión descontada
    'caldes'              => $datos['venta_caldes']   ?? 0,
    'pagos_clientes'      => $datos['pagos_clientes'] ?? 0,
    'venta_transferencia' => $datos['venta_transferencia'] ?? 0, // <-- Sin comisión
    'otros'               => 0,
    'otros_descripcion'   => null,
    'egreso'              => 0,
    'tienda_id'           => $tienda_id,
]);

        }

        // EGRESO
        if (
            ($datos['egreso_monto'] ?? 0) > 0
            && ! empty($datos['egreso_tipo'])
        ) {
            // Armamos el texto de concepto de egreso
            $conceptoE = 'Cierre diario (Egreso) ' . $datos['egreso_tipo'];

            if ($datos['egreso_tipo'] === 'Transferencia') {
                $dest   = $datos['banco_personalizado'] 
                            ?? $datos['proveedor_nombre'] 
                            ?? '';
                $conceptoE .= " → {$dest}";
            }

            if ($datos['egreso_tipo'] === 'Crédito') {
                $ori    = $datos['credito_origen'] ?? '';
                $suffix = $ori === 'Otros'
                    ? ' (' . ($datos['credito_otro_banco'] ?? '') . ')'
                    : '';
                $vto    = $datos['egreso_vencimiento'] ?? '';
                $conceptoE .= " → {$ori}{$suffix} (venc: {$vto})";
            }

            if (! empty($datos['egreso_descripcion'])) {
                $conceptoE .= ': ' . $datos['egreso_descripcion'];
            }

            Movimiento::create([
                'batch'               => $batch,
                'usuario_id'          => Auth::id(),
                'concepto'            => $conceptoE,
                'efectivo'            => 0,
                'tarjeta'             => 0,
                'caldes'              => 0,
                'pagos_clientes'      => 0,
                'venta_transferencia' => 0,
                'otros'               => 0,
                'otros_descripcion'   => null,
                'egreso'              => $datos['egreso_monto'],
                'egreso_tipo'         => $datos['egreso_tipo'],
                'egreso_descripcion'  => $datos['egreso_descripcion'] ?? null,
                'egreso_nota'         => $datos['egreso_nota']        ?? null,
                'credito_origen'      => $datos['credito_origen']     ?? null,
                'credito_otro_banco'  => $datos['credito_otro_banco'] ?? null,
                'banco_personalizado' => $datos['banco_personalizado']?? null,
                'proveedor_nombre'    => $datos['proveedor_nombre']   ?? null,
                'egreso_vencimiento'  => $datos['egreso_vencimiento'] ?? null,
                'tienda_id'           => $tienda_id, // <-- AGREGADO
            ]);
        }
    });

    return redirect()->route('admin.dashboard')
                     ->with('success', 'Cierre registrado correctamente');
}


    public function formCrearEmpleado()
    {
        return view('admin.empleados.crear');
    }

public function crearEmpleado(Request $request)
{
    $datos = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|string|min:8|confirmed',
        // Quita tienda_id de aquí
    ]);

    Usuario::create([
        'name'      => $datos['name'],
        'email'     => $datos['email'],
        'password'  => \Hash::make($datos['password']),
        'rol'       => 'empleado',
        'tienda_id' => session('tienda_id', 1), // <-- aquí siempre la actual
    ]);

    return redirect()->route('admin.empleados')
                    ->with('success', 'Empleado creado con éxito.');
}



    public function verEmpleado(Usuario $empleado)
    {
        return view('admin.empleados.ver', compact('empleado'));
    }

    public function formEditarEmpleado(Usuario $empleado)
    {
        return view('admin.empleados.editar', compact('empleado'));
    }

    public function actualizarEmpleado(Request $request, Usuario $empleado)
    {
        $datos = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$empleado->id}",
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $empleado->name  = $datos['name'];
        $empleado->email = $datos['email'];
        if (! empty($datos['password'])) {
            $empleado->password = Hash::make($datos['password']);
        }
        $empleado->save();

        return redirect()->route('admin.empleados')
                         ->with('success', 'Empleado actualizado con éxito.');
    }

    public function eliminarEmpleado(Usuario $empleado)
    {
        $empleado->delete();
        return redirect()->route('admin.empleados')
                         ->with('success', 'Empleado eliminado con éxito.');
    }

    public function listaUsuarios()
    {
        $usuarios = Usuario::where('rol', 'usuario')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function verUsuario(Usuario $usuario)
    {
        return view('admin.usuarios.ver', compact('usuario'));
    }

    public function formEditarUsuario(Usuario $usuario)
    {
        return view('admin.usuarios.editar', compact('usuario'));
    }

    public function actualizarUsuario(Request $request, Usuario $usuario)
    {
        $datos = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$usuario->id}",
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $usuario->name  = $datos['name'];
        $usuario->email = $datos['email'];
        if (! empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }
        $usuario->save();

        return redirect()->route('admin.usuarios')
                         ->with('success', 'Usuario actualizado con éxito.');
    }

    public function eliminarUsuario(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios')
                         ->with('success', 'Usuario eliminado con éxito.');
    }


    public function compras()
    {
        return view('admin.compras.index');
    }
    // En App\Http\Controllers\AdminController.php

public function editMovimiento($id)
{
    $movimiento = Movimiento::findOrFail($id);

    if ($movimiento->egreso > 0) {
        // Es un egreso
        return view('admin.movimientos.edit_egreso', compact('movimiento'));
    } else {
        // Es un ingreso
        return view('admin.movimientos.edit_ingreso', compact('movimiento'));
    }
}

public function destroyMovimiento($id, Request $request)
{
    $movimiento = Movimiento::findOrFail($id);
    $movimiento->delete();

    // Si hay 'redirect_to' en el formulario, regresa ahí
    if ($request->filled('redirect_to')) {
        return redirect($request->input('redirect_to'))->with('success', 'Movimiento eliminado correctamente.');
    }

    // Si no, que regrese al dashboard por default
    return redirect()->route('admin.dashboard')->with('success', 'Movimiento eliminado correctamente.');
}




// EDITAR INGRESO (formulario)
public function formEditarIngreso($id)
{
    $movimiento = Movimiento::findOrFail($id);
    // Asegúrate de tener la vista admin/ingreso_editar.blade.php
    return view('admin.ingreso_editar', compact('movimiento'));
}




// ACTUALIZAR INGRESO (proceso)
public function actualizarIngreso(Request $request, $id)
{
    $movimiento = Movimiento::findOrFail($id);

    $request->validate([
        'concepto'            => 'nullable|string|max:255',
        'efectivo'            => 'nullable|numeric|min:0',
        'tarjeta'             => 'nullable|numeric|min:0',
        'caldes'              => 'nullable|numeric|min:0',
        'pagos_clientes'      => 'nullable|numeric|min:0',
        'venta_transferencia' => 'nullable|numeric|min:0',
        'otros'               => 'nullable|numeric|min:0'
    ]);

    $movimiento->concepto            = $request->concepto;
    $movimiento->efectivo            = $request->efectivo;
    $movimiento->tarjeta             = $request->tarjeta;
    $movimiento->caldes              = $request->caldes;
    $movimiento->pagos_clientes      = $request->pagos_clientes;
    $movimiento->venta_transferencia = $request->venta_transferencia;
    $movimiento->otros               = $request->otros;
    $movimiento->save();

    return redirect()->route('admin.dashboard')->with('success', 'Ingreso actualizado correctamente');
}

// ELIMINAR INGRESO
public function eliminarIngreso($id)
{
    $movimiento = Movimiento::findOrFail($id);
    $movimiento->delete();

    return redirect()->route('admin.dashboard')->with('success', 'Ingreso eliminado correctamente');
}



public function actualizarEgreso(Request $request, $id)
{
    $movimiento = Movimiento::findOrFail($id);

    $request->validate([
        'concepto'           => 'nullable|string|max:255',
        'efectivo'           => 'nullable|numeric|min:0',
        'tarjeta'            => 'nullable|numeric|min:0',
        'caldes'             => 'nullable|numeric|min:0',
        'pagos_clientes'     => 'nullable|numeric|min:0',
        'venta_transferencia'=> 'nullable|numeric|min:0',
        'otros'              => 'nullable|numeric|min:0',
        'egreso'             => 'required|numeric|min:0',
        'egreso_tipo'        => 'nullable|string|max:255',
        'egreso_descripcion' => 'nullable|string|max:255',
        'egreso_nota'        => 'nullable|string|max:255',
        'credito_origen'     => 'nullable|string|max:255',
        'credito_otro_banco' => 'nullable|string|max:255',
        'banco_personalizado'=> 'nullable|string|max:255',
        'proveedor_nombre'   => 'nullable|string|max:255',
        'egreso_vencimiento' => 'nullable|date',
    ]);

    $movimiento->fill($request->all());
    $movimiento->save();

    return redirect()->route('admin.dashboard')->with('success', 'Egreso actualizado correctamente');
}

public function estadisticas(Request $request)
{
    $periodo = $request->input('periodo', 'semana');
    $hoy = \Carbon\Carbon::now();

    // === Selección de rango de fechas según el periodo ===
if ($periodo === 'mes') {
    $inicioSem = $hoy->copy()->startOfMonth();
    $finSem    = $hoy->copy()->endOfMonth();
} elseif ($periodo === 'año') {
    $inicioSem = $hoy->copy()->startOfYear();
    $finSem    = $hoy->copy()->endOfYear();
} else { // Semana por defecto: ÚLTIMOS 7 DÍAS (igual que dashboard)
    $inicioSem = $hoy->copy()->subDays(6)->startOfDay();
    $finSem    = $hoy->copy()->endOfDay();
}


    // === Traer todos los movimientos del periodo seleccionado, filtrando por tienda ===
    $movimientosSemana = \App\Models\Movimiento::where('tienda_id', session('tienda_id', 1))
        ->whereBetween('created_at', [$inicioSem, $finSem])
        ->get();

    // === Suma de INGRESOS del periodo completo ===
    $ingresosSemana = $movimientosSemana
        ->where('egreso', 0)
        ->sum(function($m) {
            return
                ($m->efectivo ?? 0)
              + ($m->tarjeta ?? 0)
              + ($m->caldes ?? 0)
              + ($m->pagos_clientes ?? 0)
              + ($m->venta_transferencia ?? 0)
              + ($m->otros ?? 0);
        });

    // === Desglose de ingresos por método ===
    $ingresosEfectivo      = $movimientosSemana->where('egreso', 0)->sum('efectivo');
    $ingresosTarjeta       = $movimientosSemana->where('egreso', 0)->sum('tarjeta');
    $ingresosVales         = $movimientosSemana->where('egreso', 0)->sum('caldes');
    $ingresosPagosClientes = $movimientosSemana->where('egreso', 0)->sum('pagos_clientes');
    $ingresosTransferencia = $movimientosSemana->where('egreso', 0)->sum('venta_transferencia');

    // === Suma de EGRESOS del periodo ===
    $egresosSemana = $movimientosSemana->where('egreso', '>', 0)->sum('egreso');

    // === Detalle para la tabla, SOLO SI NO ES MOVIMIENTO VACÍO ===
    $detalleSemana = $movimientosSemana
        ->filter(function($mov) {
            return
                ($mov->efectivo ?? 0) != 0 ||
                ($mov->tarjeta ?? 0) != 0 ||
                ($mov->caldes ?? 0) != 0 ||
                ($mov->pagos_clientes ?? 0) != 0 ||
                ($mov->venta_transferencia ?? 0) != 0 ||
                ($mov->otros ?? 0) != 0 ||
                ($mov->egreso  ?? 0) != 0;
        })
        ->sortBy('created_at');

    // === PREPARAR DATOS PARA LAS GRÁFICAS (ajustar rango según periodo) ===
    $labels        = [];
    $ingresosPorDia = [];
    $egresosPorDia  = [];

    if ($periodo === 'semana') {
        for ($dia = 0; $dia < 7; $dia++) {
            $fecha = $inicioSem->copy()->addDays($dia);
            $labels[] = $fecha->format('D d/m');

            $ingresosPorDia[] = $movimientosSemana
                ->where('egreso', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfDay(),
                    $fecha->copy()->endOfDay(),
                ])->sum(function($m) {
                    return
                        ($m->efectivo ?? 0)
                      + ($m->tarjeta ?? 0)
                      + ($m->caldes ?? 0)
                      + ($m->pagos_clientes ?? 0)
                      + ($m->venta_transferencia ?? 0)
                      + ($m->otros ?? 0);
                });

            $egresosPorDia[] = $movimientosSemana
                ->where('egreso', '>', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfDay(),
                    $fecha->copy()->endOfDay(),
                ])->sum('egreso');
        }
    } elseif ($periodo === 'mes') {
        $diasEnMes = $inicioSem->daysInMonth;
        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $fecha = $inicioSem->copy()->day($dia);
            $labels[] = $fecha->format('d/m');

            $ingresosPorDia[] = $movimientosSemana
                ->where('egreso', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfDay(),
                    $fecha->copy()->endOfDay(),
                ])->sum(function($m) {
                    return
                        ($m->efectivo ?? 0)
                      + ($m->tarjeta ?? 0)
                      + ($m->caldes ?? 0)
                      + ($m->pagos_clientes ?? 0)
                      + ($m->venta_transferencia ?? 0)
                      + ($m->otros ?? 0);
                });

            $egresosPorDia[] = $movimientosSemana
                ->where('egreso', '>', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfDay(),
                    $fecha->copy()->endOfDay(),
                ])->sum('egreso');
        }
    } else { // año
        for ($mes = 1; $mes <= 12; $mes++) {
            $fecha = $inicioSem->copy()->month($mes)->startOfMonth();
            $labels[] = $fecha->format('M Y');

            $ingresosPorDia[] = $movimientosSemana
                ->where('egreso', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfMonth(),
                    $fecha->copy()->endOfMonth(),
                ])->sum(function($m) {
                    return
                        ($m->efectivo ?? 0)
                      + ($m->tarjeta ?? 0)
                      + ($m->caldes ?? 0)
                      + ($m->pagos_clientes ?? 0)
                      + ($m->venta_transferencia ?? 0)
                      + ($m->otros ?? 0);
                });

            $egresosPorDia[] = $movimientosSemana
                ->where('egreso', '>', 0)
                ->whereBetween('created_at', [
                    $fecha->copy()->startOfMonth(),
                    $fecha->copy()->endOfMonth(),
                ])->sum('egreso');
        }
    }

    // JSON para JS en la vista
    $jsonLabels   = json_encode($labels);
    $jsonIngresos = json_encode($ingresosPorDia);
    $jsonEgresos  = json_encode($egresosPorDia);

    return view('admin.estadisticas.estadisticas', [
        'inicioSem'            => $inicioSem,
        'finSem'               => $finSem,
        'ingresosSemana'       => $ingresosSemana,
        'egresosSemana'        => $egresosSemana,
        'detalleSemana'        => $detalleSemana,
        'jsonLabels'           => $jsonLabels,
        'jsonIngresos'         => $jsonIngresos,
        'jsonEgresos'          => $jsonEgresos,
        'periodo'              => $periodo,
        // Pasa el desglose a la vista:
        'ingresosEfectivo'      => $ingresosEfectivo,
        'ingresosTarjeta'       => $ingresosTarjeta,
        'ingresosVales'         => $ingresosVales,
        'ingresosPagosClientes' => $ingresosPagosClientes,
        'ingresosTransferencia' => $ingresosTransferencia,
    ]);
}

public function pagos(Request $request)
{
    // Filtro por tienda
    $query = Pago::with('empleado')
        ->where('tienda_id', session('tienda_id', 1));

    // Filtros de búsqueda
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $query->whereHas('empleado', function($q) use ($buscar) {
            $q->where('name', 'like', "%$buscar%");
        })->orWhere('concepto', 'like', "%$buscar%");
    }

    if ($request->filled('desde')) {
        $query->where('fecha', '>=', $request->desde);
    }
    if ($request->filled('hasta')) {
        $query->where('fecha', '<=', $request->hasta);
    }

    $pagos = $query->orderBy('fecha', 'desc')->paginate(10);

    $empleados = Usuario::where('rol', 'empleado')->get();

    return view('admin.pagos.index', compact('pagos', 'empleados'));
}


public function guardarPago(Request $request)
{
    $request->validate([
        'empleado_id' => 'required|exists:users,id',
        'concepto'    => 'required|string|max:255',
        'monto'       => 'required|numeric|min:0',
        'fecha'       => 'required|date',
        'metodo'      => 'required|string|max:50',
        'descripcion' => 'nullable|string|max:1000',
    ]);

    $data = $request->all();
    $data['tienda_id'] = session('tienda_id', 1); // Aquí asignas la tienda activa

    Pago::create($data);

    return redirect()->route('admin.pagos')->with('success', 'Pago registrado correctamente.');
}


// Exportar a Excel
public function pagosExcel(Request $request)
{
    $pagos = Pago::with('empleado')
        ->where('tienda_id', session('tienda_id', 1)); // <--- FILTRO POR TIENDA

    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $pagos->whereHas('empleado', function($q) use ($buscar) {
            $q->where('name', 'like', "%$buscar%");
        })->orWhere('concepto', 'like', "%$buscar%");
    }
    if ($request->filled('desde')) {
        $pagos->where('fecha', '>=', $request->desde);
    }
    if ($request->filled('hasta')) {
        $pagos->where('fecha', '<=', $request->hasta);
    }

    $result = $pagos->orderBy('fecha', 'desc')->get();

    return Excel::download(new PagosExport($result), 'pagos_empleados.xlsx');
}


// Exportar a PDF
public function pagosPdf(Request $request)
{
    $pagos = Pago::with('empleado')
        ->where('tienda_id', session('tienda_id', 1)); // <--- AGREGADO

    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $pagos->whereHas('empleado', function($q) use ($buscar) {
            $q->where('name', 'like', "%$buscar%");
        })->orWhere('concepto', 'like', "%$buscar%");
    }
    if ($request->filled('desde')) {
        $pagos->where('fecha', '>=', $request->desde);
    }
    if ($request->filled('hasta')) {
        $pagos->where('fecha', '<=', $request->hasta);
    }

    $result = $pagos->orderBy('fecha', 'desc')->get();

    $pdf = PDF::loadView('admin.pagos_pdf', ['pagos' => $result]);
    return $pdf->download('pagos_empleados.pdf');
}



public function datos(Request $request)
{
    $query = \App\Models\Movimiento::query()
        ->with('usuario')
        ->where('tienda_id', session('tienda_id', 1)); // ← FILTRO POR TIENDA

    // Filtros de búsqueda
    if ($request->filled('empleado')) $query->where('usuario_id', $request->empleado);
    if ($request->filled('fecha'))    $query->whereDate('created_at', $request->fecha);
    if ($request->filled('tipo')) {
        if ($request->tipo == 'ingreso') $query->where('egreso', 0);
        if ($request->tipo == 'egreso')  $query->where('egreso', '>', 0);
    }
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $query->where(function($q) use ($buscar) {
            $q->where('concepto', 'like', "%$buscar%")
              ->orWhere('egreso_nota', 'like', "%$buscar%");
        });
    }

    // Paginar resultados
    $movimientos = $query->latest()->paginate(30);

    // Totales
    $totalIngresos = (clone $query)->where('egreso', 0)->sum(DB::raw('efectivo + tarjeta + caldes + pagos_clientes + venta_transferencia + otros'));
    $totalEgresos  = (clone $query)->where('egreso', '>', 0)->sum('egreso');

    // *** Aquí cargas los empleados ***
    $empleados = \App\Models\Usuario::whereIn('rol', ['empleado', 'admin'])->get();

    // Mandar todas las variables a la vista:
    return view('admin.datos', compact('movimientos', 'totalIngresos', 'totalEgresos', 'empleados'));
}

// Exportar todos los movimientos a Excel
public function movimientosExcel(Request $request)
{
    $query = \App\Models\Movimiento::with('usuario');

    // Aplica los mismos filtros que en 'datos()'
    if ($request->filled('empleado')) $query->where('usuario_id', $request->empleado);
    if ($request->filled('fecha'))    $query->whereDate('created_at', $request->fecha);
    if ($request->filled('tipo')) {
        if ($request->tipo == 'ingreso') $query->where('egreso', 0);
        if ($request->tipo == 'egreso')  $query->where('egreso', '>', 0);
    }
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $query->where(function($q) use ($buscar) {
            $q->where('concepto', 'like', "%$buscar%")
              ->orWhere('egreso_nota', 'like', "%$buscar%");
        });
    }

    $movimientos = $query->orderBy('created_at', 'desc')->get();

    // Usa tu propio export si ya tienes uno, si no, crea uno:
    return Excel::download(new \App\Exports\MovimientosExport($movimientos), 'todos_los_movimientos.xlsx');
}

// Exportar todos los movimientos a PDF
public function movimientosPdf(Request $request)
{
    $query = \App\Models\Movimiento::with('usuario');

    // Filtros
    if ($request->filled('empleado')) $query->where('usuario_id', $request->empleado);
    if ($request->filled('fecha'))    $query->whereDate('created_at', $request->fecha);
    if ($request->filled('tipo')) {
        if ($request->tipo == 'ingreso') $query->where('egreso', 0);
        if ($request->tipo == 'egreso')  $query->where('egreso', '>', 0);
    }
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $query->where(function($q) use ($buscar) {
            $q->where('concepto', 'like', "%$buscar%")
              ->orWhere('egreso_nota', 'like', "%$buscar%");
        });
    }

    $movimientos = $query->orderBy('created_at', 'desc')->get();

    $pdf = PDF::loadView('admin.datos_pdf', ['movimientos' => $movimientos]);
    return $pdf->download('todos_los_movimientos.pdf');
}

public function pagarCredito($id)
{
    $egreso = \App\Models\Movimiento::findOrFail($id);
    $egreso->pagado = true; // Asume que tienes el campo 'pagado'
    $egreso->save();

    return redirect()->route('admin.dashboard')->with('success', '¡Crédito pagado correctamente!');
}


// app/Http/Controllers/AdminController.php

public function ventas()
{
    // Todos los movimientos para los TOTALES
    $all = \App\Models\Movimiento::where('tienda_id', session('tienda_id', 1))->get();

    // Solo los de batch manual para la tabla
    $movimientos = \App\Models\Movimiento::where('tienda_id', session('tienda_id', 1))
        ->where('batch', 'manual')
        ->orderByDesc('created_at')
        ->take(20)
        ->get();

    // Totales usando TODOS los movimientos
    $ingresosEfectivo       = $all->where('egreso', 0)->sum('efectivo');
    $ingresosTarjeta        = $all->where('egreso', 0)->sum('tarjeta');
    $ingresosVales          = $all->where('egreso', 0)->sum('caldes');
    $ingresosPagosClientes  = $all->where('egreso', 0)->sum('pagos_clientes');
    $ingresosTransferencia  = $all->where('egreso', 0)->sum('venta_transferencia');
    $ingresosOtros          = $all->where('egreso', 0)->sum('otros');
    $totalIngresos = $ingresosEfectivo + $ingresosTarjeta + $ingresosVales +
                     $ingresosPagosClientes + $ingresosTransferencia + $ingresosOtros;

    $egresosEfectivo      = $all->where('egreso', '>', 0)->sum('efectivo');
    $egresosTransferencia = $all->where('egreso', '>', 0)->sum('venta_transferencia');
    $egresosCredito       = $all->where('egreso', '>', 0)->sum('egreso');
    $egresosOtros         = $all->where('egreso', '>', 0)->sum('otros');
    $totalEgresos = $egresosEfectivo + $egresosTransferencia + $egresosCredito + $egresosOtros;

    return view('admin.ventas.index', compact(
        'totalIngresos', 'totalEgresos',
        'ingresosEfectivo', 'ingresosTarjeta', 'ingresosVales', 'ingresosPagosClientes',
        'ingresosTransferencia', 'ingresosOtros',
        'egresosEfectivo', 'egresosTransferencia', 'egresosCredito', 'egresosOtros',
        'movimientos'
    ));
}

// MÉTODO PARA GUARDAR DESCUENTOS (puedes dejarlo igual, solo asegúrate de registrar en el campo correcto)
public function descuento(Request $request)
{
    $tipo   = $request->input('tipo');   // 'ingreso' o 'egreso'
    $monto  = $request->input('monto');
    $motivo = $request->input('motivo');
    $forma  = $request->input('forma');

    $campos = [
        'efectivo'            => 0,
        'tarjeta'             => 0,
        'caldes'              => 0,
        'pagos_clientes'      => 0,
        'venta_transferencia' => 0,
        'otros'               => 0,
        'egreso'              => 0,
        'egreso_tipo'         => null,
        'usuario_id'          => auth()->id(),
        'batch'               => 'manual',
        'motivo'              => $motivo,
        'forma'               => $forma,
        // Aquí el concepto lo formateamos bonito:
        'concepto'            => $request->input('concepto'),
        // AGREGA EL CAMPO TIENDA
        'tienda_id'           => session('tienda_id', 1), // <-- ¡Aquí!
    ];

    // Según forma, ponemos el monto negativo en el campo correspondiente
    switch ($forma) {
        case 'Efectivo':            $campos['efectivo'] = -abs($monto); break;
        case 'Tarjeta':             $campos['tarjeta'] = -abs($monto); break;
        case 'Vales':               $campos['caldes'] = -abs($monto); break;
        case 'Pagos Clientes':      $campos['pagos_clientes'] = -abs($monto); break;
        case 'Transferencia':       $campos['venta_transferencia'] = -abs($monto); break;
        case 'Otros':               $campos['otros'] = -abs($monto); break;
        case 'Crédito':
            if ($tipo == 'egreso') {
                $campos['egreso'] = abs($monto);
                $campos['egreso_tipo'] = 'Crédito';
            } else {
                $campos['otros'] = -abs($monto);
            }
            break;
        default: $campos['otros'] = -abs($monto); break;
    }

    $campos['tipo'] = $tipo;

    \App\Models\Movimiento::create($campos);

    return back()->with('ok', 'Descuento registrado correctamente.');
}



public function cambiarTienda(Request $request)
{
    session(['tienda_id' => $request->tienda_id]);
    \Log::info('TIENDA ACTUAL EN VISTA:', [session('tienda_id')]);
    // ¡SIEMPRE REDIRIGE A UN GET!
    return redirect()->route('admin.dashboard');
}





}
