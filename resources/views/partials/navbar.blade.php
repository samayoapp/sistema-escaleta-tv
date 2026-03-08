{{-- resources/views/partials/navbar.blade.php --}}
<nav class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between sticky top-0 z-40">

    {{-- LOGO --}}
    <a href="/" class="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition">
        <span class="text-xl">🥃</span>
        <span class="font-bold text-xl tracking-widest">RONUP</span>
        <span class="text-gray-600 text-sm hidden sm:block">by Publimatec</span>
    </a>

    {{-- LADO DERECHO --}}
    @auth
    <div class="flex items-center gap-3">

        @if(auth()->user()->isAdmin())
            <a href="/admin/usuarios"
               class="text-gray-500 hover:text-white text-xs uppercase tracking-widest transition hidden sm:block">
                👥 Usuarios
            </a>
        @endif

        <div class="flex items-center gap-2 bg-gray-800 border border-gray-700 rounded px-3 py-1.5">
            <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-300">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="text-xs text-gray-400 hidden sm:block">{{ auth()->user()->name }}</span>
            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ auth()->user()->rolColor() }}">
                {{ auth()->user()->rolLabel() }}
            </span>
        </div>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit"
                class="text-gray-500 hover:text-red-400 text-xs uppercase tracking-widest transition">
                ⎋ Salir
            </button>
        </form>

    </div>
    @endauth
</nav>
