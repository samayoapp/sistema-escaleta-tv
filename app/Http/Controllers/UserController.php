<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.usuarios', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', Rule::in(['admin', 'editor', 'viewer'])],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect('/admin/usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // No se puede degradar al último admin
        if ($user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect('/admin/usuarios')
                    ->with('error', 'No puedes cambiar el rol del único administrador.');
            }
        }

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role'  => ['required', Rule::in(['admin', 'editor', 'viewer'])],
        ];

        // Password es opcional en edición
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('/admin/usuarios')->with('success', 'Usuario actualizado.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // No eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return redirect('/admin/usuarios')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // No eliminar el último admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect('/admin/usuarios')
                    ->with('error', 'No puedes eliminar el único administrador.');
            }
        }

        $user->delete();
        return redirect('/admin/usuarios')->with('success', 'Usuario eliminado.');
    }
}
