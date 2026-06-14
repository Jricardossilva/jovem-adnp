<?php

use App\Livewire\AcessoPelada;
use Illuminate\Support\Facades\Route;

/*
| Página pública dos atletas: entrar na lista de uma pelada usando o código.
| Ex.: https://seudominio.com/  ou  https://seudominio.com/p/ABC234
*/
Route::get('/', AcessoPelada::class)->name('home');
Route::get('/p/{codigo?}', AcessoPelada::class)->name('pelada.acesso');

// O painel do organizador é registrado pelo Filament em /organizador.
