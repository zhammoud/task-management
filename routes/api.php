<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([],
    function () {
        Route::post('auth/login', [UserController::class, 'login'])->name('auth.login');
        Route::post('auth/register', [UserController::class, 'register'])->name('auth.register');
        Route::group(
            [
                'middleware' => 'auth:sanctum',
            ], function () {
            Route::post('auth/logout', [UserController::class, 'logout'])->name('auth.logout');
            Route::get('auth/current', [UserController::class, 'currentUser'])->name('auth.current');

            Route::group([
                'prefix' => 'tasks',
                'as' => 'tasks.'
            ], function () {
                Route::get('/', [\App\Http\Controllers\TaskController::class, 'index'])->name('list');
                Route::post('/', [\App\Http\Controllers\TaskController::class, 'store'])->name('create');
                Route::put('/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('update');
                Route::delete('/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('delete');
                Route::get('/{task}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.list');
                Route::post('/{task}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.create');
            });
        });
    }
);
