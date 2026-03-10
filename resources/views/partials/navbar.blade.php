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

{{-- ── TOAST DE SIN PERMISOS ─────────────────────────────────────────────── --}}
<div id="toast-sin-permisos"
     class="fixed bottom-6 right-6 z-50 pointer-events-none"
     aria-live="assertive">
    <div id="toast-inner"
         class="flex items-start gap-3 bg-gray-900 border border-red-700/70 rounded-lg px-4 py-3 shadow-2xl
                max-w-xs w-full
                translate-y-4 opacity-0 transition-all duration-300 ease-out pointer-events-auto"
         style="will-change: transform, opacity;">

        {{-- Ícono --}}
        <div class="mt-0.5 text-red-500 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>

        {{-- Texto --}}
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold uppercase tracking-widest text-red-400">Sin permisos</p>
            <p id="toast-mensaje" class="text-xs text-gray-400 mt-0.5 leading-snug">
                No tienes permisos para realizar esta acción.
            </p>
        </div>

        {{-- Cerrar --}}
        <button onclick="cerrarToast()"
                class="text-gray-600 hover:text-white transition shrink-0 mt-0.5 leading-none text-sm">
            ✕
        </button>
    </div>
</div>

<script>
// ── Toast de sin permisos ──────────────────────────────────────────────────
let _toastTimer = null;

function mostrarToast(mensaje) {
    const inner = document.getElementById('toast-inner');
    const msg   = document.getElementById('toast-mensaje');
    if (!inner) return;

    if (mensaje) msg.textContent = mensaje;

    // Mostrar
    inner.classList.remove('translate-y-4', 'opacity-0');
    inner.classList.add('translate-y-0', 'opacity-100');

    // Auto-cerrar en 4s
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(cerrarToast, 4000);
}

function cerrarToast() {
    const inner = document.getElementById('toast-inner');
    if (!inner) return;
    inner.classList.add('translate-y-4', 'opacity-0');
    inner.classList.remove('translate-y-0', 'opacity-100');
}

// Función global para llamar desde onclick en botones bloqueados
function sinPermiso(msg) {
    mostrarToast(msg || 'No tienes permisos para realizar esta acción.');
}

// ── Interceptar respuestas 403 de HTMX ────────────────────────────────────
document.addEventListener('htmx:responseError', function(e) {
    if (e.detail.xhr.status === 403) {
        try {
            const data = JSON.parse(e.detail.xhr.responseText);
            mostrarToast(data.message || null);
        } catch {
            mostrarToast();
        }
        e.preventDefault(); // No mostrar el error en el target de HTMX
    }
});

// ── Interceptar fetch() manual con 403 ───────────────────────────────────
(function() {
    const _fetch = window.fetch;
    window.fetch = async function(...args) {
        const res = await _fetch(...args);
        if (res.status === 403) {
            const clone = res.clone();
            try {
                const data = await clone.json();
                mostrarToast(data.message || null);
            } catch {
                mostrarToast();
            }
        }
        return res;
    };
})();
</script>
