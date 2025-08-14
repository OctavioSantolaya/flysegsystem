<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contingencia - {{ $contingency->contingency_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: #1e293b;
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
            padding: 30px 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container img {
            max-height: 80px;
            max-width: 300px;
            height: auto;
            width: auto;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            padding: 40px 30px;
            margin-bottom: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
        }
        
        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #13649c;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-row:last-of-type {
            border-bottom: none;
            margin-bottom: 30px;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
            font-size: 16px;
        }
        
        .info-value {
            color: #1e293b;
            font-size: 16px;
            font-weight: 500;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            background: #13649c;
            color: white;
            text-align: center;
        }
        
        .btn:hover {
            background: #0f4c7a;
            text-decoration: none;
            color: white;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }
            
            .card {
                padding: 30px 20px;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ asset('storage/logo.webp') }}" alt="FlySeg Logo" />
        </div>
        
        <!-- Contenido Principal -->
        <div class="card">
            <h1>Nueva Contingencia</h1>
            
            <div class="info-row">
                <span class="info-label">ID de Contingencia:</span>
                <span class="info-value"><strong>{{ $contingency->contingency_id }}</strong></span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Aerolínea:</span>
                <span class="info-value">{{ $contingency->airline ? $contingency->airline->name : 'No especificada' }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Base:</span>
                <span class="info-value">{{ $contingency->base ? $contingency->base->name : 'No especificada' }}</span>
            </div>
            
            @if($contingency->flight_number ?? false)
            <div class="info-row">
                <span class="info-label">Vuelo:</span>
                <span class="info-value">{{ $contingency->flight_number }}</span>
            </div>
            @endif
            
            @if($contingency->contingency_type ?? false)
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value">{{ $contingency->contingency_type }}</span>
            </div>
            @endif
            
            @if($contingency->scale ?? false)
            <div class="info-row">
                <span class="info-label">Escala:</span>
                <span class="info-value">{{ $contingency->scale }}</span>
            </div>
            @endif
            
            @if($contingency->date ?? false)
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($contingency->date)->format('d/m/Y') }}</span>
            </div>
            @endif
            
            <div class="info-row">
                <span class="info-label">Creada por:</span>
                <span class="info-value">{{ $contingency->user ? $contingency->user->name : 'Usuario no disponible' }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Fecha de Creación:</span>
                <span class="info-value">{{ $contingency->created_at ? $contingency->created_at->format('d/m/Y H:i') : 'No disponible' }}</span>
            </div>
            
            <div class="btn-container">
                <a href="{{ $contingencyUrl }}" class="btn">
                    @if(str_contains($contingencyUrl, '/admin/'))
                        Gestionar en Panel Admin
                    @elseif(str_contains($contingencyUrl, '/operator/'))
                        Gestionar en Panel Operador
                    @elseif(str_contains($contingencyUrl, '/manager'))
                        Gestionar en Panel Gestor
                    @else
                        Ver Contingencia Completa
                    @endif
                </a>
            </div>
        </div>
    </div>
</body>
</html>
