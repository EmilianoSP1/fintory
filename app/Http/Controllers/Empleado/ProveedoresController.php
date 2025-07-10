<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;

class ProveedoresController extends Controller
{
    public function index()
    {
        return view('empleado.proveedores');
    }
}
