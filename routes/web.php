<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Landing');
})->name('landing');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/mail-test', function () {
    Mail::raw('Teste SMTP OK', function ($m) {
        $m->to('seu-email@gmail.com')->subject('Portal T20 - Teste SMTP');
    });
    return 'Enviado.';
});

Route::get('/mail-debug', function () {
    return [
        'scheme' => config('mail.mailers.smtp.scheme'),
        'host'   => config('mail.mailers.smtp.host'),
        'port'   => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'from'   => config('mail.from'),
    ];
});

require __DIR__ . '/auth.php';
