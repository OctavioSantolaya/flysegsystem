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

Route::get('/log-viewer-temp', function () {
    $out = "=== DIAGNOSTICO DE SERVIDOR Y SESION ===\n";
    $out .= "APP_ENV: " . config('app.env') . "\n";
    $out .= "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
    $out .= "APP_URL: " . config('app.url') . "\n";
    $out .= "APP_KEY set: " . (!empty(config('app.key')) ? 'YES' : 'NO') . "\n";
    $out .= "HTTPS header: " . ($_SERVER['HTTPS'] ?? 'NOT SET') . "\n";
    $out .= "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'NOT SET') . "\n";
    $out .= "Request secure(): " . (request()->secure() ? 'true' : 'false') . "\n";
    $out .= "Session Driver: " . config('session.driver') . "\n";
    $out .= "Session Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
    $out .= "Session Domain: " . var_export(config('session.domain'), true) . "\n";
    $out .= "Session Cookie Name: " . config('session.cookie') . "\n";
    $out .= "Session ID: " . request()->session()->getId() . "\n";
    $out .= "Session Token: " . session('_token') . "\n";
    
    // Probar conexión a DB y usuario admin
    try {
        $user = \App\Models\User::where('email', 'octaviosantolaya@gmail.com')->first();
        if ($user) {
            $out .= "\nUser Octavio ID: {$user->id}\n";
            $out .= "User Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
            $out .= "Can access admin panel: " . ($user->canAccessPanel(filament()->getPanel('admin')) ? 'YES' : 'NO') . "\n";
        } else {
            $out .= "\nUser Octavio NOT FOUND in DB!\n";
        }
    } catch (\Throwable $e) {
        $out .= "\nDB ERROR: " . $e->getMessage() . "\n";
    }

    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $out .= "\n=== ULTIMAS LINEAS DE LARAVEL.LOG ===\n";
        $out .= substr(file_get_contents($logFile), -10000);
    } else {
        $out .= "\nNo laravel.log file at $logFile\n";
    }

    return response('<pre>' . e($out) . '</pre>');
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
