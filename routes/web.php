<?php

use App\Http\Controllers\PromptGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PromptGeneratorController::class, 'index']);
Route::post('/', [PromptGeneratorController::class, 'generate']);