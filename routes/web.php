<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;


Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('/tasks',[TaskController::class,'index'])
        ->name('tasks.index');

    Route::post('/tasks',[TaskController::class,'store'])
        ->name('tasks.store');

    Route::patch('/tasks/{task}/completion',[TaskController::class,'toggleCompletion'])
        ->name('tasks.completion');
    Route::patch('/tasks/{task}',[TaskController::class,'update'])
        ->name('tasks.update');

    Route::delete('/tasks/{task}',[TaskController::class,'destroy'])
        ->name('tasks.destroy');
});

require __DIR__.'/settings.php';
