<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">

<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-6">
        <div class="flex items-center gap-4">
            <a href="/" class="text-gray-500 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ $users->count() }} usuario{{ $users->count() !== 1 ? 's' : '' }} registrado{{ $users->count() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <button onclick="document.getElementById('modal-nuevo-usuario').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </button>
    </header>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="mb-6 bg-green-900/40 border border-green-700 text-green-300 px-4 py-3 rounded-lg text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-900/40 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- TABLA DE USUARIOS --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700 text-xs uppercase text-gray-500 tracking-widest bg-gray-700/30">
                    <th class="px-5 py-3 text-left">Usuario</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Rol</th>
                    <th class="px-5 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-700/20 transition {{ $user->id === auth()->id() ? 'bg-blue-900/10' : '' }}">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-300">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-white text-sm">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] text-blue-400 ml-1">(tú)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-sm">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded {{ $user->rolColor() }}">
                            {{ $user->rolLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button onclick="abrirEditar(
                                {{ $user->id }},
                                '{{ addslashes($user->name) }}',
                                '{{ $user->email }}',
                                '{{ $user->role }}'
                            )"
                                class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-300">
                                ✏️ Editar
                            </button>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="/admin/usuarios/{{ $user->id }}"
                                onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-900/40 hover:bg-red-700 px-3 py-1 rounded text-xs font-bold uppercase transition text-red-400">
                                    🗑
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- LEYENDA DE ROLES --}}
    <div class="mt-6 grid grid-cols-3 gap-3">
        <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-3">
            <div class="text-[10px] font-bold uppercase text-yellow-400 mb-1">👑 Admin</div>
            <p class="text-xs text-gray-500">Acceso total. Gestiona usuarios, shows, y todas las escaletas.</p>
        </div>
        <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-3">
            <div class="text-[10px] font-bold uppercase text-blue-400 mb-1">✏️ Editor</div>
            <p class="text-xs text-gray-500">Puede crear y editar escaletas. No puede gestionar usuarios ni aprobar.</p>
        </div>
        <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-3">
            <div class="text-[10px] font-bold uppercase text-gray-400 mb-1">👁 Viewer</div>
            <p class="text-xs text-gray-500">Solo lectura. Puede ver escaletas, teleprompter y exportar PDF.</p>
        </div>
    </div>

</div>

{{-- MODAL NUEVO USUARIO --}}
<div id="modal-nuevo-usuario" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-5">Nuevo Usuario</h2>
        <form method="POST" action="/admin/usuarios">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre *</label>
                    <input type="text" name="name" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Email *</label>
                    <input type="email" name="email" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Contraseña *</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Rol *</label>
                    <select name="role" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                        <option value="viewer">👁 Viewer — Solo lectura</option>
                        <option value="editor">✏️ Editor — Puede editar</option>
                        <option value="admin">👑 Admin — Acceso total</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-nuevo-usuario').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR USUARIO --}}
<div id="modal-editar-usuario" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-5">Editar Usuario</h2>
        <form method="POST" id="form-editar-usuario" action="">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre *</label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Email *</label>
                    <input type="email" name="email" id="edit-email" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nueva Contraseña <span class="text-gray-600 normal-case">(dejar en blanco para no cambiar)</span></label>
                    <input type="password" name="password" minlength="8"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Rol *</label>
                    <select name="role" id="edit-role" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                        <option value="viewer">👁 Viewer — Solo lectura</option>
                        <option value="editor">✏️ Editor — Puede editar</option>
                        <option value="admin">👑 Admin — Acceso total</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-editar-usuario').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirEditar(id, name, email, role) {
        document.getElementById('form-editar-usuario').action = '/admin/usuarios/' + id;
        document.getElementById('edit-name').value  = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value  = role;
        document.getElementById('modal-editar-usuario').classList.remove('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('modal-nuevo-usuario').classList.add('hidden');
            document.getElementById('modal-editar-usuario').classList.add('hidden');
        }
    });
</script>

</body>
</html>
