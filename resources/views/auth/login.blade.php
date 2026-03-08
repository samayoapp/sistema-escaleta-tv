<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RONUP — Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-sm">

    {{-- LOGO --}}
    <div class="text-center mb-10">
        <div class="text-5xl mb-3">🥃</div>
        <h1 class="text-3xl font-bold text-white tracking-widest uppercase">RONUP</h1>
        <p class="text-gray-600 text-s uppercase tracking-widest mt-1">by Publimatec</p>
    </div>

    @if (session('status'))
        <div class="mb-4 bg-green-900/40 border border-green-700 text-green-300 px-4 py-3 rounded text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-900/40 border border-red-700 text-red-300 px-4 py-3 rounded text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf

        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-1">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full bg-gray-800 border border-gray-700 rounded px-4 py-3 text-white text-sm focus:border-blue-500 focus:outline-none transition placeholder-gray-600"
                placeholder="usuario@canal.com">
        </div>

        <div>
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-1">Contraseña</label>
            <input type="password" name="password" required autocomplete="current-password"
                class="w-full bg-gray-800 border border-gray-700 rounded px-4 py-3 text-white text-sm focus:border-blue-500 focus:outline-none transition"
                placeholder="••••••••">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="w-3.5 h-3.5 rounded accent-blue-500 cursor-pointer">
            <label for="remember" class="text-xs text-gray-500 cursor-pointer">Recordarme</label>
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-500 active:bg-blue-700 px-4 py-3 rounded font-bold uppercase tracking-widest text-sm transition mt-2">
            Iniciar Sesión
        </button>
    </form>

    <p class="text-center text-gray-700 text-xs mt-10 uppercase tracking-widest">Sistema de Producción TV desarrollado por Inversiones Publimatec S.A. de C.V. - </br>Todos los derechos reservados - 2026</p>

</div>
</body>
</html>
