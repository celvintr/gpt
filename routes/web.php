<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatGPTController;

Route::get('/chat', [ChatGPTController::class, 'index']);
Route::post('/chat', [ChatGPTController::class, 'chat'])->name('chat');
