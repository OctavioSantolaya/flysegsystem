<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - FlySeg</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            margin: 20px;
        }
        
        h1 {
            color: #1e293b;
            margin-bottom: 20px;
            font-size: 48px;
            font-weight: 700;
        }
        
        h2 {
            color: #1976d2;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
        }
        
        p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .btn {
            background: #1976d2;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0 8px;
        }
        
        .btn:hover {
            background: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        .btn.secondary {
            background: #64748b;
        }

        .btn.secondary:hover {
            background: #475569;
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }
        
        .logo {
            color: #1976d2;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .error-code {
            color: #ef4444;
            font-size: 120px;
            font-weight: 900;
            margin-bottom: 20px;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">FlySeg</div>
        <div class="error-code">404</div>
        <h2>Página No Encontrada</h2>
        <p>
            Lo sentimos, la página que estás buscando no existe o ha sido movida.
            <br><br>
            Verifica la URL o regresa al inicio para continuar navegando.
        </p>
        <div class="buttons">
            <a href="/" class="btn">Ir al Inicio</a>
            <a href="/dashboard" class="btn secondary">Mi Panel</a>
        </div>
    </div>
</body>
</html>
