<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;


// Route::resource('contacts', ContactController::class);
Route::redirect('/', '/contacts');

Route::prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/{id}', [ContactController::class, 'show'])->whereNumber('id')->name('contacts.show');
    Route::get('/{id}/edit', [ContactController::class, 'edit'])->whereNumber('id')->name('contacts.edit');
    Route::put('/{id}', [ContactController::class, 'update'])->whereNumber('id')->name('contacts.update');
    Route::delete('/{id}', [ContactController::class, 'destroy'])->whereNumber('id')->name('contacts.destroy');

    Route::post('/import-xml', [ContactController::class, 'importXML'])->name('contacts.importXML');
});
