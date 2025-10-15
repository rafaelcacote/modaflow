<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    /**
     * Search CEP using ViaCEP API.
     *
     * @param  string  $cep
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($cep)
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/\D/', '', $cep);
        
        // Valida o CEP
        if (strlen($cep) != 8) {
            return response()->json([
                'error' => 'CEP inválido'
            ], 400);
        }
        
        try {
            // Busca na API do ViaCEP (desabilitando verificação SSL em desenvolvimento)
            $response = Http::withOptions([
                'verify' => false, // Desabilita verificação SSL em desenvolvimento
            ])->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Erro ao buscar CEP'
                ], 500);
            }
            
            $data = $response->json();
            
            // Verifica se o CEP foi encontrado
            if (isset($data['erro']) && $data['erro']) {
                return response()->json([
                    'error' => 'CEP não encontrado'
                ], 404);
            }
            
            // Busca o estado pelo UF
            $estado = Estado::where('uf', $data['uf'])->first();
            
            // Busca o município pelo nome e estado
            $municipio = null;
            if ($estado) {
                $municipio = Municipio::where('estado_id', $estado->id)
                    ->where('nome', 'LIKE', '%' . $data['localidade'] . '%')
                    ->first();
            }
            
            // Formata a resposta
            $resultado = [
                'cep' => $data['cep'],
                'endereco' => $data['logradouro'],
                'complemento' => $data['complemento'],
                'bairro' => $data['bairro'],
                'cidade' => $data['localidade'],
                'uf' => $data['uf'],
                'estado_id' => $estado ? $estado->id : null,
                'municipio_id' => $municipio ? $municipio->id : null,
            ];
            
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar CEP: ' . $e->getMessage()
            ], 500);
        }
    }
}
