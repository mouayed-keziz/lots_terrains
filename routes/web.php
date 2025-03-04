<?php

use App\Http\Controllers\PropertyController;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PropertyController::class, 'index'])->name('dashboard');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/fill-form', [PropertyController::class, 'fillForm'])->name('properties.fill-form');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get("test", function (Request $request) {
    try {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.dev',
            'password' => bcrypt('admin'),
        ]);
    } catch (\Throwable $th) {
        return response()->json($th);
    }
    return response()->json($user);
});

require __DIR__ . '/auth.php';
