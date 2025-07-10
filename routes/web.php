<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerificarRol;
use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\UsuarioController;

// Controladores adicionales para el área de Empleado
use App\Http\Controllers\Empleado\InventarioController;
use App\Http\Controllers\Empleado\ProveedoresController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/login',    [AutenticacionController::class, 'mostrarFormularioLogin'])
     ->name('login');
Route::post('/login',   [AutenticacionController::class, 'login'])
     ->name('login.procesar');
Route::post('/logout',  [AutenticacionController::class, 'logout'])
     ->name('logout');

Route::get('/registro', [AutenticacionController::class, 'mostrarFormularioRegistro'])
     ->name('register.form');
Route::post('/registro',[AutenticacionController::class, 'registro'])
     ->name('register.procesar');


/*
|--------------------------------------------------------------------------
| Rutas del Administrador (rol = admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', VerificarRol::class . ':admin'])
     ->group(function () {
         // Panel principal
         Route::get('dashboard', [AdminController::class, 'dashboard'])
              ->name('dashboard');
              // Guardar cierre (ingresos + egresos) para ADMIN
Route::post('cierre', [AdminController::class, 'guardarCierre'])
    ->name('cierre');
// Rutas para editar y eliminar movimientos desde el admin
Route::get('movimiento/{mov}/edit', [AdminController::class, 'editMovimiento'])
    ->name('movimiento.edit');

Route::delete('movimiento/{mov}', [AdminController::class, 'destroyMovimiento'])
    ->name('movimiento.destroy');
// CRUD de INGRESOS (panel admin)
Route::get('movimientos/{movimiento}/editar', [AdminController::class, 'formEditarIngreso'])
    ->name('movimientos.ingreso.editar');
Route::put('movimientos/{movimiento}', [AdminController::class, 'actualizarIngreso'])
    ->name('movimientos.ingreso.actualizar');
Route::delete('movimientos/{movimiento}', [AdminController::class, 'eliminarIngreso'])
    ->name('movimientos.ingreso.eliminar');
    //EGRESOS FORMULARIO EDITAR
    Route::put('movimientos/egreso/{movimiento}', [AdminController::class, 'actualizarEgreso'])
    ->name('movimientos.egreso.actualizar');
    Route::get('estadisticas', [AdminController::class, 'estadisticas'])
     ->name('estadisticas');
     //Pagos del menu
Route::get('pagos', [AdminController::class, 'pagos'])->name('pagos');
Route::post('pagos', [AdminController::class, 'guardarPago'])->name('pagos.guardar');
Route::get('pagos/excel', [AdminController::class, 'pagosExcel'])->name('pagos.excel');
Route::get('pagos/pdf', [AdminController::class, 'pagosPdf'])->name('pagos.pdf');
//Boton de datos alado de fintory
Route::get('datos', [AdminController::class, 'datos'])->name('datos');
//PDF y EXCEL de Datos
Route::get('datos/excel', [AdminController::class, 'movimientosExcel'])->name('datos.excel');
Route::get('datos/pdf', [AdminController::class, 'movimientosPdf'])->name('datos.pdf');
//creditos egreso
Route::post('credito/{id}/pagar', [AdminController::class, 'pagarCredito'])
    ->name('credito.pagar');
//descuento
Route::post('descuento', [AdminController::class, 'descuento'])->name('descuento');
//Accion eliminar en ventas 
Route::delete('movimiento/{id}/eliminar', [AdminController::class, 'destroyMovimiento'])
    ->name('movimiento.eliminar');



    

         // Ventas / Compras/ Estadisticas/ Pagos
         Route::get('ventas',  [AdminController::class, 'ventas'])
              ->name('ventas.index');
         Route::get('compras', [AdminController::class, 'compras'])
              ->name('compras.index');
         Route::get('estadisticas', [AdminController::class, 'estadisticas'])
              ->name('estadisticas');
         Route::get('pagos', [App\Http\Controllers\AdminController::class, 'pagos'])
              ->name('pagos');



         // CRUD Empleados
         Route::get('empleados',                   [AdminController::class, 'listaEmpleados'])
              ->name('empleados');
         Route::get('empleados/crear',             [AdminController::class, 'formCrearEmpleado'])
              ->name('empleados.crear');
         Route::post('empleados',                  [AdminController::class, 'crearEmpleado'])
              ->name('empleados.store');
         Route::get('empleados/{empleado}',        [AdminController::class, 'verEmpleado'])
              ->name('empleados.ver');
         Route::get('empleados/{empleado}/editar', [AdminController::class, 'formEditarEmpleado'])
              ->name('empleados.editar');
         Route::put('empleados/{empleado}',        [AdminController::class, 'actualizarEmpleado'])
              ->name('empleados.actualizar');
         Route::delete('empleados/{empleado}',     [AdminController::class, 'eliminarEmpleado'])
              ->name('empleados.eliminar');

         // CRUD Usuarios normales
         Route::get('usuarios',                  [AdminController::class, 'listaUsuarios'])
              ->name('usuarios');
         Route::get('usuarios/{usuario}',        [AdminController::class, 'verUsuario'])
              ->name('usuarios.ver');
         Route::get('usuarios/{usuario}/editar', [AdminController::class, 'formEditarUsuario'])
              ->name('usuarios.editar');
         Route::put('usuarios/{usuario}',        [AdminController::class, 'actualizarUsuario'])
              ->name('usuarios.actualizar');
         Route::delete('usuarios/{usuario}',     [AdminController::class, 'eliminarUsuario'])
              ->name('usuarios.eliminar');
     });


/*
|--------------------------------------------------------------------------
| Rutas del Empleado (rol = empleado)
|--------------------------------------------------------------------------
*/
Route::prefix('empleado')
     ->name('empleado.')
     ->middleware(['auth', VerificarRol::class . ':empleado'])
     ->group(function () {
         // Panel de empleado
         Route::get('dashboard', [EmpleadoController::class, 'dashboard'])
              ->name('dashboard');

         // Guardar cierre (ingresos + egresos)
         Route::post('cierre', [EmpleadoController::class, 'guardarCierre'])
              ->name('cierre');

         // Eliminar todos los movimientos de un mismo batch
         Route::delete('movimientos/{movimiento}', [EmpleadoController::class, 'eliminarMovimiento'])
              ->name('movimientos.eliminar');

         // Eliminar un movimiento individual
         Route::delete('movimiento/{movimiento}', [EmpleadoController::class, 'destroy'])
              ->name('movimiento.destroy');

         // Marcar crédito como pagado
         Route::patch('movimiento/{movimiento}/pagado', [EmpleadoController::class, 'marcarPagado'])
              ->name('movimiento.pagado');

         // Nuevas rutas para Inventario y Proveedores
         Route::get('inventario', [InventarioController::class, 'index'])
              ->name('inventario');
         Route::get('proveedores', [ProveedoresController::class, 'index'])
              ->name('proveedores');

         // —— RUTA QUE NOS FALTA: Estadísticas semanales ——
         Route::get('estadisticas', [EmpleadoController::class, 'estadisticas'])
              ->name('estadisticas');
     });


/*
|--------------------------------------------------------------------------
| Rutas del Usuario Normal (rol = usuario)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', VerificarRol::class . ':usuario'])
     ->group(function () {
         Route::get('/', [UsuarioController::class, 'inicio'])
              ->name('usuario.inicio');
     });
