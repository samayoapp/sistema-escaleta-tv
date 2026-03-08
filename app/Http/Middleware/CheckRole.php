<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Roles permitidos en orden jerárquico.
     * Un admin puede hacer todo lo que un editor puede, etc.
     */
    private array $hierarchy = ['viewer', 'editor', 'admin'];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Si el usuario tiene alguno de los roles requeridos, pasa
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Admin siempre pasa
        if ($user->role === 'admin') {
            return $next($request);
        }

        abort(403, 'No tienes permisos para realizar esta acción.');
    }
}
