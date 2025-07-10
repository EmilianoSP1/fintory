{{-- resources/views/partials/nav.blade.php --}}
<div 
  x-data="{ openMenu: false }" 
  class="fixed top-0 left-0 w-full bg-white shadow z-50 transition-colors dark:bg-gray-900"
>
  <div class="flex items-center h-14 px-4 md:px-6">
    <button @click="openMenu = !openMenu" class="p-2 rounded hover:bg-gray-100 focus:ring-2 focus:ring-indigo-500 dark:hover:bg-gray-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
    <span class="ml-4 text-xl font-semibold text-gray-800 dark:text-gray-100">Fintory</span>
    <div class="flex-1 flex justify-center space-x-4">
      {{-- Aquí van tus enlaces: --}}
      <a href="{{ route('admin.ventas') }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Venta</a>
      <a href="{{ route('admin.compras') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Compra</a>
    </div>
    {{-- botón modo oscuro igual que en empleado --}}
    <div x-data="{ dark: localStorage.getItem('modoOscuro')==='true', toggle(){ this.dark = !this.dark; document.documentElement.classList.toggle('dark',this.dark); localStorage.setItem('modoOscuro',this.dark)}, init(){ document.documentElement.classList.toggle('dark',this.dark) } }" x-init="init" class="ml-2">
      <button @click="toggle" class="p-2 rounded-full border bg-white dark:bg-gray-800 dark:border-gray-700">
        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"/>
        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-100" fill="currentColor" viewBox="0 0 24 24"/>
      </button>
    </div>
  </div>

  {{-- menú lateral, si quieres dejarlo también --}}
  <div x-show="openMenu" class="fixed inset-0 z-40 flex">
    <div @click="openMenu=false" class="fixed inset-0 bg-black/40"></div>
    <aside class="relative bg-white w-64 h-full shadow dark:bg-gray-900">
      <nav class="py-4">
        <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-800">Dashboard</a>
        <a href="{{ route('admin.ventas') }}" class="block px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-800">Ventas</a>
        <a href="{{ route('admin.compras') }}" class="block px-6 py-3 hover:bg-gray-100 dark:hover:bg-gray-800">Compras</a>
      </nav>
    </aside>
  </div>
</div>
