<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarRol
{
    /**
     * Filtra el acceso según rol.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed     ...$rolesPermitidos
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$rolesPermitidos)
    {
        // 1) Si no está autenticado, redirige a login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // 2) Comprueba el rol
        $rolUsuario = Auth::user()->rol;
        if (! in_array($rolUsuario, $rolesPermitidos)) {
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
