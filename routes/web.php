<?php

use App\Http\Controllers\CepController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\MunicipioController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de Empresas (protegidas por autenticação)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('empresas', EmpresaController::class);
    
    // Rotas aninhadas de Lojas
    Route::prefix('empresas/{empresa}')->group(function () {
        Route::get('lojas', [LojaController::class, 'index'])->name('empresas.lojas.index');
        Route::get('lojas/create', [LojaController::class, 'create'])->name('empresas.lojas.create');
        Route::post('lojas', [LojaController::class, 'store'])->name('empresas.lojas.store');
        Route::get('lojas/{loja}/edit', [LojaController::class, 'edit'])->name('empresas.lojas.edit');
        Route::put('lojas/{loja}', [LojaController::class, 'update'])->name('empresas.lojas.update');
        Route::delete('lojas/{loja}', [LojaController::class, 'destroy'])->name('empresas.lojas.destroy');
    });
});

// API Routes para Estados, Municípios e CEP (protegidas por autenticação)
Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::get('estados', [EstadoController::class, 'index'])->name('api.estados.index');
    Route::get('estados/{id}', [EstadoController::class, 'show'])->name('api.estados.show');
    
    Route::get('municipios', [MunicipioController::class, 'index'])->name('api.municipios.index');
    Route::get('municipios/{id}', [MunicipioController::class, 'show'])->name('api.municipios.show');
    
    Route::get('cep/{cep}', [CepController::class, 'show'])->name('api.cep.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
