<?php

use App\Http\Controllers\EmpresaController;
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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
