<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('titulo') – Fintory</title>
  {{-- Alpine.js --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  {{-- Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-950 min-h-screen">

@if(optional(Auth::user())->rol === 'admin')
  {{-- HEADER + MENU DRAWER LATERAL --}}
  <div x-data="{ openMenu: false }" class="fixed top-0 left-0 w-full bg-white shadow z-50 transition-colors dark:bg-gray-900">
    <div class="flex items-center h-14 px-4 md:px-6">
{{-- Botón hamburguesa --}}
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

<a href="{{ route('admin.datos') }}"
   class="ml-6 px-4 py-1 rounded-full bg-blue-50 text-blue-700 font-medium hover:bg-blue-100 transition text-base shadow-sm border border-blue-200">
    Datos
</a>

<div class="flex-1"></div>



      
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
    {{-- Menú lateral deslizable --}}
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
        <div class="px-4 py-6 border-b border-gray-200 dark:border-gray-700 flex items-center space-x-4">
          {{-- Foto de perfil circular --}}
          <img
            src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0D8ABC&color=fff' }}"
            alt="Foto de perfil"
            class="h-10 w-10 rounded-full object-cover"
          />
          <div>
            <p class="text-gray-800 dark:text-white font-medium">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-300">Administrador Fintory</p>
          </div>
        </div>








<div class="px-4 pt-4 pb-2 flex flex-col items-center">
<form method="POST" action="{{ route('admin.cambiar.tienda') }}" class="flex gap-2 w-full">
        @csrf
        <button
            type="submit"
            name="tienda_id"
            value="1"
            class="flex-1 max-w-[110px] px-2 py-2 rounded-lg font-semibold text-xs shadow transition-all duration-200 border text-center
                {{ session('tienda_id', 1) == 1
                    ? 'bg-blue-600 text-white border-blue-600 cursor-default ring-2 ring-blue-400'
                    : 'bg-white text-blue-800 border-blue-200 hover:bg-blue-50 hover:border-blue-400 hover:ring-1 hover:ring-blue-300' }}"
            {{ session('tienda_id', 1) == 1 ? 'disabled' : '' }}
        >
            Plastiseo y Más
        </button>
        <span class="self-center text-gray-300 select-none">|</span>
        <button
            type="submit"
            name="tienda_id"
            value="2"
            class="flex-1 max-w-[110px] px-2 py-2 rounded-lg font-semibold text-xs shadow transition-all duration-200 border text-center
                {{ session('tienda_id', 1) == 2
                    ? 'bg-green-600 text-white border-green-600 cursor-default ring-2 ring-green-400'
                    : 'bg-white text-green-800 border-green-200 hover:bg-green-50 hover:border-green-400 hover:ring-1 hover:ring-green-300' }}"
            {{ session('tienda_id', 1) == 2 ? 'disabled' : '' }}
        >
            Autofuchon
        </button>
    </form>
    <div class="mt-2 text-xs text-gray-500 w-full text-center">
        Tienda activa:
        @if(session('tienda_id', 1) == 1)
            <span class="font-bold text-blue-700">Plastiseo y Más</span>
        @else
            <span class="font-bold text-green-700">Autofuchon</span>
        @endif
    </div>
</div>







<nav class="flex flex-col py-2">
  <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor"><path d="M3 12l2-2 4 4 8-8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Panel
  </a>
  <a href="{{ route('admin.usuarios') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
    Usuarios
  </a>
  <a href="{{ route('admin.empleados') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor"><rect width="16" height="16" x="4" y="4" stroke-width="2"/></svg>
    Empleados
  </a>
  <a href="{{ route('admin.ventas.index') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor"><path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Ventas
  </a>
  <a href="{{ route('admin.compras.index') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor"><path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Compras
  </a>
  {{-- NUEVA SECCIÓN: PAGOS --}}
  <a href="{{ route('admin.pagos') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor">
      <path d="M4 7V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2M4 7v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <rect x="8" y="11" width="8" height="4" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Pagos
  </a>
  <a href="{{ route('admin.estadisticas') }}" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor">
      <circle cx="12" cy="12" r="9" stroke-width="2"/>
      <path d="M8 17l4-4 4 4M12 13V7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Estadísticas
  </a>
  <form method="POST" action="{{ route('logout') }}" class="px-6 py-3">
    @csrf
    <button type="submit" class="flex items-center gap-3 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 w-full px-2 py-2 rounded transition font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Cerrar sesión
    </button>
  </form>
</nav>
      </aside>
    </div>
  </div>
@endif

{{-- Espacio debajo del header fijo --}}
<div class="pt-14"></div>

<main class="max-w-7xl mx-auto p-4">
  @yield('contenido')
</main>
</body>
</html>
