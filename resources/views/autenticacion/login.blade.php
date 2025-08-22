<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login | Fintory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded-xl shadow-lg w-96">
    <h2 class="text-2xl font-bold mb-6 text-center">Iniciar sesión</h2>

    @if($errors->any())
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.procesar') }}">
      @csrf
      <label class="block mb-2">Correo</label>
      <input type="email" name="email" value="{{ old('email') }}"
             class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring"
             required autofocus>

      <label class="block mb-2">Contraseña</label>
      <input type="password" name="password"
             class="w-full px-4 py-2 mb-6 border rounded focus:outline-none focus:ring"
             required>

      <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Entrar
      </button>
    </form>

  </div>
</body>
</html>
