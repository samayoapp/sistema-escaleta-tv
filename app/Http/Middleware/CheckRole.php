<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    private array $hierarchy = ['viewer', 'editor', 'admin'];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Admin siempre pasa
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Verificar si tiene alguno de los roles requeridos
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Respuesta 403 — si es HTMX o fetch devuelve JSON limpio
        if ($request->header('HX-Request') || $request->expectsJson() || $request->ajax()) {
            return response()->json([
                'error'   => true,
                'message' => 'No tienes permisos para realizar esta acción.',
                'role'    => $user->role,
            ], 403);
        }

        abort(403, 'No tienes permisos para realizar esta acción.');
    }
}
