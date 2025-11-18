<?php

use App\Http\Controllers\Player\CharacterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Landing');
})->name('landing');

Route::get('/dashboard', function () {
    // return redirect()->route('dashboard.player.characters.index');
    return redirect()->route('dashboard.player.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')->name('dashboard.')
    ->group(function () {
        Route::prefix('player')->name('player.')->group(function () {
            Route::get('/', fn() => Inertia::render('Player/Index'))->name('index');

            Route::get('/characters', fn() => Inertia::render('Player/Characters/Index'))
                ->name('characters.index');
            // Route::get('/characters/create', fn() => Inertia::render('Player/Characters/Create'))
            // ->name('characters.create');
            Route::get('/characters/create', [CharacterController::class, 'create'])
                ->name('characters.create');
            Route::get('/campaign', fn() => Inertia::render('Player/Campaign/Index'))
                ->name('campaign.index');
        });

        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/', fn() => Inertia::render('Master/Index'))->name('index');
        });
    });

require __DIR__ . '/auth.php';
