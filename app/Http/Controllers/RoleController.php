<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): Response
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('name', 'ilike', "%{$search}%");
        }

        $roles = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('perfis/Index', [
            'perfis' => $roles,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        return Inertia::render('perfis/Create');
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return to_route('perfis.index')
            ->with('success', 'Perfil criado com sucesso!');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $perfi): Response
    {
        // Note: Route model binding with parameter name {perfi} for resource 'perfis'
        return Inertia::render('perfis/Edit', [
            'perfil' => $perfi,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(RoleUpdateRequest $request, Role $perfi): RedirectResponse
    {
        $data = $request->validated();
        $perfi->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $perfi->guard_name,
        ]);

        return to_route('perfis.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $perfi): RedirectResponse
    {
        $perfi->delete();

        return to_route('perfis.index')
            ->with('success', 'Perfil excluído com sucesso!');
    }
}



