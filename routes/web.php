<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArmadaController;
use App\Models\Galeri;

Route::get('/', function () {
    return view('landing');
});

// Route::get('/armada', function () {
//     return view('armada');
// });

Route::get('/armada', [ArmadaController::class, 'index'])->name('armada.index');

Route::get('/armada/{id}', [ArmadaController::class, 'show'])->name('armada.show');

Route::post('/armada/{id}/sewa', [ArmadaController::class, 'store'])->name('armada.sewa');

Route::get('/galeri', function () {

    $galeris = Galeri::latest()->get();

    return view('galeri', compact('galeris'));

});

Route::get('/debug', function () {
    return [
        'secure' => request()->secure(),
        'scheme' => request()->getScheme(),
        'host' => request()->getHost(),
        'app_url' => config('app.url'),
    ];
});


// Route::get('/armada/detail', function () {
//     return view('detail');
// });
