<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContingencyQrController;

Route::get('/', function () {
    // Si el usuario ya está logueado, redirigir al panel admin
    if (Auth::check()) {
        return redirect('/admin');
    }
    
    // Si no está logueado, redirigir al login del panel admin
    return redirect('/admin/login');
});

Route::get('/download-template/{type?}', function ($type = 'csv') {
    $files = [
        'csv' => ['path' => 'ejemplo_pasajeros.csv', 'name' => 'ejemplo_pasajeros.csv'],
        'tsv' => ['path' => 'ejemplo_pasajeros.tsv', 'name' => 'ejemplo_pasajeros.tsv'],
        'xml' => ['path' => 'ejemplo_pasajeros.xml', 'name' => 'ejemplo_pasajeros.xml'],
        'html' => ['path' => 'ejemplo_pasajeros.html', 'name' => 'ejemplo_pasajeros.html'],
    ];
    
    if (!isset($files[$type])) {
        abort(404, 'Tipo de archivo no encontrado');
    }
    
    $file = $files[$type];
    $fullPath = storage_path("app/templates/{$file['path']}");
    
    if (!file_exists($fullPath)) {
        abort(404, 'Archivo de ejemplo no encontrado');
    }
    
    return response()->download($fullPath, $file['name']);
})->name('download.template');

// Rutas para códigos QR de contingencias
Route::get('/contingencias/{slug}/qr', [ContingencyQrController::class, 'showQr'])->name('contingency.qr');
Route::get('/contingencias/{slug}/formulario', [ContingencyQrController::class, 'showForm'])->name('contingency.form');

// Rutas AJAX para el formulario
Route::post('/contingencias/{slug}/buscar-pasajero', [ContingencyQrController::class, 'searchPassenger'])->name('contingency.search-passenger');
Route::post('/contingencias/{slug}/buscar-pasajero-adicional', [ContingencyQrController::class, 'searchAdditionalPassenger'])->name('contingency.search-additional-passenger');
Route::post('/contingencias/{slug}/guardar-formulario', [ContingencyQrController::class, 'saveForm'])->name('contingency.save-form');
Route::post('/contingencias/{slug}/buscar-respuesta', [ContingencyQrController::class, 'searchFormResponse'])->name('contingency.search-response');

// Rutas de redirección inteligente para paneles
Route::get('/acceso-denegado', function () {
    return view('errors.403');
})->name('access.denied');

// Redirección automática según roles del usuario
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/admin/login');
    }
    
    $user = Auth::user();
    $userRoles = $user->roles->pluck('name')->toArray();
    
    // Verificar roles y redirigir al panel correspondiente
    if (in_array('super_admin', $userRoles) || in_array('administrador', $userRoles)) {
        return redirect('/admin');
    } elseif (in_array('operador', $userRoles)) {
        return redirect('/operator');
    } elseif (in_array('gestor', $userRoles)) {
        return redirect('/manager');
    }
    
    // Si no tiene roles apropiados, mostrar página de acceso denegado
    return view('errors.403');
})->name('dashboard.redirect');

// Ruta de logout general
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
