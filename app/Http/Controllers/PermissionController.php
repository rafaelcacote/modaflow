<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): Response
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('name', 'ilike', "%{$search}%");
        }

        $permissions = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('permissoes/Index', [
            'permissoes' => $permissions,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): Response
    {
        return Inertia::render('permissoes/Create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(PermissionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return to_route('permissoes.index')
            ->with('success', 'Permissão criada com sucesso!');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permiso): Response
    {
        // Note: Route model binding with parameter name {permiso} for resource 'permissoes'
        return Inertia::render('permissoes/Edit', [
            'permissao' => $permiso,
        ]);
    }

    /**
     * Update the specified permission.
     */
    public function update(PermissionUpdateRequest $request, Permission $permiso): RedirectResponse
    {
        $data = $request->validated();
        $permiso->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $permiso->guard_name,
        ]);

        return to_route('permissoes.index')
            ->with('success', 'Permissão atualizada com sucesso!');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permiso): RedirectResponse
    {
        $permiso->delete();

        return to_route('permissoes.index')
            ->with('success', 'Permissão excluída com sucesso!');
    }
}



