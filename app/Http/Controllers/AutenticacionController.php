<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AutenticacionController extends Controller
{
    /**
     * Mostrar el formulario de login.
     */
    public function mostrarFormularioLogin()
    {
        return view('autenticacion.login');
    }

    /**
     * Procesar el login.
     */
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            return match (Auth::user()->rol) {
                'admin'    => redirect()->route('admin.dashboard'),
                'empleado' => redirect()->route('empleado.dashboard'),
                default    => redirect()->route('usuario.inicio'),
            };
        }

        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden'])
            ->withInput(['email' => $request->email]);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Mostrar el formulario de registro de usuario normal.
     */
    public function mostrarFormularioRegistro()
    {
        return view('autenticacion.registro');
    }

    /**
     * Procesar el registro de un usuario normal.
     */
    public function registro(Request $request)
    {
        $datos = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $usuario = Usuario::create([
            'name'     => $datos['name'],
            'email'    => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol'      => 'usuario',
        ]);

        Auth::login($usuario);
        return redirect()->route('usuario.inicio');
    }
}
