<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpresaStoreRequest;
use App\Http\Requests\EmpresaUpdateRequest;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmpresaController extends Controller
{
    /**
     * Display a listing of empresas.
     */
    public function index(Request $request): Response
    {
        $query = Empresa::query();

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Busca por razão social, nome fantasia ou CNPJ
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('razao_social', 'ilike', "%{$search}%")
                    ->orWhere('nome_fantasia', 'ilike', "%{$search}%")
                    ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        $empresas = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('empresas/Index', [
            'empresas' => $empresas,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new empresa.
     */
    public function create(): Response
    {
        return Inertia::render('empresas/Create');
    }

    /**
     * Store a newly created empresa.
     */
    public function store(EmpresaStoreRequest $request): RedirectResponse
    {
        Empresa::create($request->validated());

        return to_route('empresas.index')
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    /**
     * Display the specified empresa.
     */
    public function show(Empresa $empresa): Response
    {
        return Inertia::render('empresas/Show', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Show the form for editing the specified empresa.
     */
    public function edit(Empresa $empresa): Response
    {
        return Inertia::render('empresas/Edit', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Update the specified empresa.
     */
    public function update(EmpresaUpdateRequest $request, Empresa $empresa): RedirectResponse
    {
        $empresa->update($request->validated());

        return to_route('empresas.edit', $empresa)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Remove the specified empresa (soft delete).
     */
    public function destroy(Empresa $empresa): RedirectResponse
    {
        $empresa->delete();

        return to_route('empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }
}
