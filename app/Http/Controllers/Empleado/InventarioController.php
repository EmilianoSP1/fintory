<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;

class InventarioController extends Controller
{
    public function index()
    {
        return view('empleado.inventario');
    }
}
