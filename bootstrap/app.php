<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Nama pendek untuk middleware role: ->middleware('role:admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\CekRole::class,
        ]);

        // Pengguna yang belum login diarahkan ke halaman login
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Pengguna yang sudah login lalu membuka /login diarahkan ke halaman awal role-nya
        $middleware->redirectUsersTo(
            fn ($request) => \App\Livewire\Auth\Login::halamanAwal($request->user()->role)
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();