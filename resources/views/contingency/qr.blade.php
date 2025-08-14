<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR - {{ $contingency->name }}</title>
    <style>
        /* Reseteo básico y estilos para el body */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%; /* Asegura que el body ocupe toda la altura de la ventana */
            font-family: Arial, sans-serif;
            background-color: #f0f2f5; /* Un color de fondo suave */
        }

        /* Contenedor principal que usa Flexbox para centrar el contenido */
        .flex-container {
            display: flex;
            justify-content: center; /* Centra horizontalmente */
            align-items: center;     /* Centra verticalmente */
            width: 100%;
            height: 100%;
        }

        /* Tarjeta que contiene el QR y el texto */
        .content-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center; /* Centra el texto dentro de la tarjeta */
            max-width: 95%;
            width: 500px;
            min-width: 320px;
        }

        /* Contenedor para el QR */
        .qr-container {
            margin-bottom: 24px; /* Espacio entre el QR y el título */
        }

        /* Estilos para la imagen del código QR */
        .qr-code svg {
            width: 200px !important;
            height: 200px !important;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        /* Estilos para el título */
        h1 {
            margin: 10px 0 12px 0;
            font-size: 24px;
            color: #333;
        }

        /* Estilos para el párrafo */
        p {
            margin: 0 0 16px 0;
            font-size: 16px;
            color: #666;
            line-height: 1.5; /* Mejora la legibilidad del párrafo */
        }

        /* Estilos para la información de la URL */
        .url-info {
            margin-top: 36px;
            font-size: 14px;
            color: #555;
            background-color: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            word-break: break-all;
            text-align: center;
        }

        .url-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        /* Responsiveness para pantallas pequeñas */
        @media (max-width: 600px) {
            .content-card {
                width: 90%;
                padding: 30px 20px;
            }
            
            .url-info {
                font-size: 12px;
                padding: 12px;
            }
            
            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="flex-container">
        <div class="content-card">            
            <div class="qr-container">
                <div class="qr-code">
                    {!! QrCode::size(200)->generate($qrTargetUrl) !!}
                </div>
            </div>
            
            <p>
                Escanea el código QR para ir al formulario y completar tus datos
            </p>
            
            <div class="url-info">
                <div class="url-label">Si tenés inconvenientes con el código QR:</div>
                {{ $qrTargetUrl }}
            </div>

        </div>
    </div>

</body>
</html>
