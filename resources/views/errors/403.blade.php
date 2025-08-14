<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - FlySeg</title>
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

        .btn.success {
            background: #22c55e;
        }

        .btn.success:hover {
            background: #16a34a;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn.warning {
            background: #f59e0b;
        }

        .btn.warning:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn.info {
            background: #06b6d4;
        }

        .btn.info:hover {
            background: #0891b2;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
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

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            margin: 0 4px;
        }

        .role-super_admin { background: #fecaca; color: #991b1b; }
        .role-administrador { background: #fed7aa; color: #9a3412; }
        .role-operador { background: #bbf7d0; color: #166534; }
        .role-gestor { background: #bae6fd; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">FlySeg</div>
        
        @guest
            <h1>Acceso Requerido</h1>
            <p>
                Para acceder al sistema FlySeg, necesitas iniciar sesión con una cuenta autorizada.
                <br><br>
                Selecciona el panel al que deseas acceder:
            </p>
            <div class="buttons">
                <a href="/admin/login" class="btn">Panel Administrador</a>
                <a href="/operator/login" class="btn success">Panel Operador</a>
                <a href="/manager/login" class="btn warning">Panel Gestor</a>
            </div>
        @else
            @php
                $user = auth()->user();
                $userRoles = $user->roles->pluck('name')->toArray();
                $hasAnyRole = !empty($userRoles);
                
                // Determinar qué paneles puede acceder según sus roles
                $availablePanels = [];
                if (in_array('super_admin', $userRoles) || in_array('administrador', $userRoles)) {
                    $availablePanels[] = ['url' => '/admin', 'name' => 'Panel Administrador', 'class' => 'btn'];
                }
                if (in_array('operador', $userRoles)) {
                    $availablePanels[] = ['url' => '/operator', 'name' => 'Panel Operador', 'class' => 'btn success'];
                }
                if (in_array('gestor', $userRoles)) {
                    $availablePanels[] = ['url' => '/manager', 'name' => 'Panel Gestor', 'class' => 'btn warning'];
                }
            @endphp
            
            @if(!$hasAnyRole)
                <h1>Sin Roles Asignados</h1>
                <p>
                    Hola <strong>{{ $user->name }}</strong>,
                    <br><br>
                    Tu cuenta no tiene roles asignados o los roles no están configurados correctamente. 
                    <br><br>
                    Por favor, contacta al administrador del sistema para que te asigne los permisos necesarios.
                </p>
                <div class="buttons">
                    <a href="/admin/login" class="btn secondary">Contactar Administrador</a>
                </div>
            @else
                <h1>Panel Incorrecto</h1>
                <p>
                    Hola <strong>{{ $user->name }}</strong>,
                    <br><br>
                    Tienes los siguientes roles asignados:
                    @foreach($userRoles as $role)
                        <span class="role-badge role-{{ $role }}">{{ ucfirst($role) }}</span>
                    @endforeach
                    <br><br>
                    Estás intentando acceder a un panel para el que no tienes permisos. 
                    Selecciona el panel correcto según tus roles:
                </p>
                <div class="buttons">
                    @forelse($availablePanels as $panel)
                        <a href="{{ $panel['url'] }}" class="{{ $panel['class'] }}">{{ $panel['name'] }}</a>
                    @empty
                        <p style="color: #ef4444;">No tienes acceso a ningún panel. Contacta al administrador.</p>
                    @endforelse
                    
                    @if(count($availablePanels) > 0)
                        <a href="/logout" class="btn secondary" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           Cerrar Sesión
                        </a>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endif
                </div>
            @endif
        @endguest
    </div>
</body>
</html>
