<?php

namespace App\Http\Controllers;

use App\Http\Requests\LojaStoreRequest;
use App\Http\Requests\LojaUpdateRequest;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\Loja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LojaController extends Controller
{
    /**
     * Display a listing of lojas for a specific empresa.
     */
    public function index(Request $request, Empresa $empresa): Response
    {
        $query = $empresa->lojas();

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Busca por nome, CNPJ ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                    ->orWhere('cnpj', 'like', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $lojas = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('lojas/Index', [
            'empresa' => $empresa,
            'lojas' => $lojas,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new loja.
     */
    public function create(Empresa $empresa): Response
    {
        return Inertia::render('lojas/Create', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Store a newly created loja.
     */
    public function store(LojaStoreRequest $request, Empresa $empresa): RedirectResponse
    {
        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            // Remove dados de endereço do array principal se existirem
            // (os campos de endereço já estão diretamente na tabela lojas)
            unset($data['endereco']);
            
            // Adiciona empresa_id
            $data['empresa_id'] = $empresa->id;
            
            Loja::create($data);
            
            DB::commit();

            return to_route('empresas.lojas.index', $empresa)
                ->with('success', 'Loja cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao criar loja:', ['error' => $e->getMessage()]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar loja. Tente novamente.']);
        }
    }

    /**
     * Display the specified loja.
     */
    public function show(Empresa $empresa, Loja $loja): Response
    {
        // Verifica se a loja pertence à empresa
        abort_if($loja->empresa_id !== $empresa->id, 404);
        
        return Inertia::render('lojas/Show', [
            'empresa' => $empresa,
            'loja' => $loja,
        ]);
    }

    /**
     * Show the form for editing the specified loja.
     */
    public function edit(Empresa $empresa, Loja $loja): Response
    {
        // Verifica se a loja pertence à empresa
        abort_if($loja->empresa_id !== $empresa->id, 404);
        
        return Inertia::render('lojas/Edit', [
            'empresa' => $empresa,
            'loja' => $loja,
        ]);
    }

    /**
     * Update the specified loja.
     */
    public function update(LojaUpdateRequest $request, Empresa $empresa, Loja $loja): RedirectResponse
    {
        // Verifica se a loja pertence à empresa
        abort_if($loja->empresa_id !== $empresa->id, 404);
        
        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            // Remove dados de endereço do array principal se existirem
            // (os campos de endereço já estão diretamente na tabela lojas)
            unset($data['endereco']);
            
            $loja->update($data);
            
            DB::commit();

            return to_route('empresas.lojas.index', $empresa)
                ->with('success', 'Loja atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar loja:', ['error' => $e->getMessage()]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar loja. Tente novamente.']);
        }
    }

    /**
     * Remove the specified loja (soft delete).
     */
    public function destroy(Empresa $empresa, Loja $loja): RedirectResponse
    {
        // Verifica se a loja pertence à empresa
        abort_if($loja->empresa_id !== $empresa->id, 404);
        
        $loja->delete();

        return to_route('empresas.lojas.index', $empresa)
            ->with('success', 'Loja excluída com sucesso!');
    }
}
