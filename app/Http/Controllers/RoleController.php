<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Role inti: dipakai langsung oleh kode (register, redirectByRole,
     * Gate::before), jadi nama dan keberadaannya tidak boleh diubah lewat UI.
     */
    protected array $protectedRoles = ['admin', 'pengelola', 'user'];

    public function index(Request $request)
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->string('search') . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $protectedRoles = $this->protectedRoles;

        return view('roles.index', compact('roles', 'protectedRoles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
        ]);

        Role::create([
            'name'       => strtolower($validated['name']),
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('crud_success', 'Role berhasil ditambahkan. Atur permission-nya lewat tombol Permission.');
    }

    public function edit(Role $role)
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()
                ->route('roles.index')
                ->with('crud_error', 'Role inti sistem tidak dapat diubah namanya.');
        }

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors(['role' => 'Role inti sistem tidak dapat diubah namanya.']);
        }

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role->id),
            ],
        ]);

        $role->update(['name' => strtolower($validated['name'])]);

        return redirect()
            ->route('roles.index')
            ->with('crud_success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors(['role' => 'Role inti sistem tidak dapat dihapus.']);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors([
                'role' => 'Role tidak dapat dihapus karena masih digunakan oleh user.',
            ]);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('crud_success', 'Role berhasil dihapus.');
    }
}