<?php

use App\Http\Controllers\EntryController;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::post('/items/{item}/update_last_checked_at', [ItemController::class, 'updateLastCheckedAt'])->name('items.update_last_checked_at');
    Route::post('/items/{item}/delete', [ItemController::class, 'destroy'])->name('items.destroy');
    
    Route::post('/entries/store', [EntryController::class, 'store'])->name('entries.store');
    Route::post('/entries/{entry}/update', [EntryController::class, 'update'])->name('entries.update');
    Route::post('/entries/{entry}/delete', [EntryController::class, 'destroy'])->name('entries.destroy');
    
});