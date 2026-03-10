<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RONUP — Sin Permisos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen flex items-center justify-center px-4">

<div class="text-center max-w-sm">

    {{-- Logo --}}
    <a href="/" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 transition mb-10">
        <span class="text-2xl">🥃</span>
        <span class="font-bold text-lg tracking-widest uppercase">RONUP</span>
        <span class="text-gray-600 text-xs">by Publimatec</span>
    </a>

    {{-- Código --}}
    <div class="mb-4">
        <span class="text-8xl font-black text-gray-800 leading-none select-none">403</span>
    </div>

    {{-- Ícono y mensaje --}}
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-900/30 border border-red-700/50 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5V9m0 0V7m0 2h2m-2 0H10M5.05 5.05a7 7 0 119.9 9.9 7 7 0 01-9.9-9.9z"/>
            </svg>
        </div>
        <h1 class="text-lg font-bold text-white mb-1">Sin permisos</h1>
        <p class="text-gray-500 text-sm">
            No tienes permisos para realizar esta acción.<br>
            Contacta al administrador del sistema.
        </p>
    </div>

    {{-- Botón volver --}}
    <a href="javascript:history.back()"
        class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 border border-gray-700
               px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition text-gray-300 mr-2">
        ← Volver
    </a>
    <a href="/"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500
               px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition text-white">
        🏠 Inicio
    </a>

    {{-- Rol actual --}}
    @auth
    <p class="text-gray-700 text-xs mt-8 uppercase tracking-widest">
        Conectado como
        <span class="{{ auth()->user()->rolColor() }} px-1.5 py-0.5 rounded font-bold">
            {{ auth()->user()->rolLabel() }}
        </span>
    </p>
    @endauth

</div>

</body>
</html>
