<?php

use App\Http\Controllers\PropertyController;
use App\Models\Property;
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
    $newProperty = Property::create([
        'title'       => 'Sample Title',
        'description' => 'Sample Description',
        'content'     => 'Sample Content',
        'sections'    => "",
    ]);

    return response()->json($newProperty);
});

require __DIR__ . '/auth.php';
