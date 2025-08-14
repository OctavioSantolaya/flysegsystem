<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario - {{ $contingency->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px;
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

        .passenger-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #f8f9fa;
            border-radius: 6px;
            margin-top: 8px;
        }
        
        .passenger-details.show {
            max-height: 200px;
            padding: 15px;
        }
        
        .passenger-details .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .passenger-details .form-group {
            flex: 1;
        }
        
        .passenger-details .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }
        
        .passenger-details .form-group input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
        }

        .card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 15px;
            letter-spacing: -0.025em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 0.95rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #13649c;
            box-shadow: 0 0 0 4px rgba(19, 100, 156, 0.1);
            background: rgba(255, 255, 255, 1);
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
            gap: 8px;
            min-height: 48px;
        }

        .btn-primary {
            background: #13649c;
            color: white;
        }

        .btn-primary:hover {
            background: #0f4c7a;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #13649c;
            color: white;
        }

        .btn-success:hover {
            background: #0f4c7a;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fecaca;
            color: #b91c1c;
        }

        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
            color: #166534;
        }

        .hidden {
            display: none;
        }

        .passenger-item, .passenger-selection-item, .selected-passenger-card {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .passenger-selection-item.selected {
            border-color: #13649c;
            background: rgba(255, 255, 255, 0.95);
            transform: translateX(4px);
        }

        .passenger-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(248, 250, 252, 0.8);
            border-radius: 6px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .checkbox-group:hover {
            border-color: #cbd5e1;
            background: rgba(248, 250, 252, 1);
        }

        .passenger-selection-item {
            cursor: pointer;
        }

        .passenger-selection-item label {
            cursor: pointer;
            width: 100%;
            display: block;
        }

        .passenger-selection-item span {
            cursor: pointer;
            width: 100%;
            display: block;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #13649c;
        }

        .checkbox-group input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #13649c;
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
        }

        .checkbox-group span {
            margin-bottom: 0;
            cursor: pointer;
        }

        .required {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
            background-color: rgba(239, 68, 68, 0.05);
        }

        .required-label::after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }

        .validation-error {
            color: #ef4444;
            font-size: 14px;
            margin-top: 8px;
            display: none;
            font-weight: 500;
        }

        .validation-error.show {
            display: block;
        }

        .passenger-selection-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .passenger-selection-header input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #13649c;
        }

        .passenger-details {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .passenger-details.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .edit-button {
            background: #13649c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-left: 10px;
            transition: background 0.3s ease;
        }

        .edit-button:hover {
            background: #0f4c7a;
        }

        /* Modal moderno */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            padding: 30px;
            border-radius: 8px;
            max-width: 700px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s ease-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin: auto;
        }

        .modal-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .confirmation-summary {
            background: linear-gradient(135deg, #f8fafc 0%, #e3f2fd 100%);
            border-radius: 8px;
            padding: 25px;
            border: 1px solid rgba(19, 100, 156, 0.2);
        }

        .confirmation-summary h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            text-align: center;
        }

        .summary-section {
            background: rgba(255, 255, 255, 0.7);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #13649c;
        }

        .summary-section:last-child {
            margin-bottom: 0;
        }

        .summary-section strong {
            color: #0f172a;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-30px) scale(0.95); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .card {
                padding: 25px 20px;
            }
            
            .form-row, .passenger-info {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                padding: 25px 20px;
                max-height: 90vh;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('logo.webp') }}" alt="Logo" />
        </div>

        <!-- Paso 1: Buscar PNR -->
        <div class="card" id="pnr-search">
            <h2>Ingresa tu PNR y Apellido</h2>
            <p style="color: #64748b; margin-bottom: 25px;">Por favor, ingresa tu código PNR y apellido para comenzar:</p>
            
            <div id="error-message" class="alert alert-error hidden"></div>
            <div id="success-message" class="alert alert-success hidden"></div>
            
            <div class="form-group">
                <label for="pnr">Código PNR:</label>
                <input type="text" id="pnr" placeholder="Ejemplo: AB1234" maxlength="20" style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label for="last-name">Apellido:</label>
                <input type="text" id="last-name" placeholder="Ingresa tu apellido" maxlength="50" style="text-transform: capitalize;">
            </div>
            
            <button type="button" class="btn btn-primary" onclick="searchPassenger()">
                Buscar Pasajero
            </button>
        </div>

        <!-- Paso 2: Selección y Agregado de Pasajeros -->
        <div class="card hidden" id="passenger-selection">
            <h2>Selecciona y Agrega Pasajeros</h2>
            <p style="color: #64748b; margin-bottom: 25px;">Selecciona los pasajeros de este PNR y agrega más PNRs si es necesario:</p>
            
            <div id="passenger-selection-list">
                <!-- Los pasajeros para seleccionar se mostrarán aquí -->
            </div>
            
            <!-- Sección para agregar más PNRs -->
            <div id="add-more-pnrs" class="hidden" style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #e2e8f0;">
                <h3 style="font-size: 1.2rem; margin-bottom: 15px;">Agregar más pasajeros</h3>
                <div class="form-group">
                    <label for="additional-pnr">PNR adicional:</label>
                    <input type="text" id="additional-pnr" placeholder="Ejemplo: CD5678" maxlength="20" style="text-transform: uppercase;">
                </div>
                <div class="form-group">
                    <label for="additional-last-name">Apellido:</label>
                    <input type="text" id="additional-last-name" placeholder="Apellido del pasajero" maxlength="50" style="text-transform: capitalize;">
                </div>
                <button type="button" class="btn btn-secondary" onclick="addMorePassengers()" style="margin-top: 15px;">
                    Buscar y Agregar
                </button>
            </div>
            
            <div style="margin-top: 25px;">
                <button type="button" class="btn btn-primary" onclick="proceedToFormResponse()">
                    Continuar al Formulario
                </button>
            </div>
        </div>

        <!-- Paso 3: Datos de Pasajeros para Edición -->
        <div class="card hidden" id="all-passengers-data">
            <h2>Completa los Datos de los Pasajeros</h2>
            <p style="color: #64748b; margin-bottom: 25px;">Ahora puedes editar los datos de los pasajeros seleccionados:</p>
            
            <div id="all-passengers-info">
                <!-- Datos de los pasajeros seleccionados -->
            </div>
            
            <div style="margin-top: 25px; display: flex; gap: 15px;">
                <button type="button" class="btn btn-secondary" onclick="goBackToPassengerSelection()">
                    ← Cambiar Selección
                </button>
                <button type="button" class="btn btn-primary" onclick="proceedToFormOnly()">
                    Continuar al Formulario
                </button>
            </div>
        </div>

        <!-- Paso 4: Formulario de Respuesta -->
        <div class="card hidden" id="form-response">
            <h2>Información del Formulario</h2>
            <div style="margin-bottom: 20px;">
                <button type="button" class="btn btn-secondary" onclick="goBackToPassengerData()" style="padding: 8px 16px; font-size: 0.9rem;">
                    ← Editar Datos de Pasajeros
                </button>
            </div>
            <form id="response-form">
                <div class="form-group">
                    <label class="required-label">¿Necesitas transporte al domicilio?</label>
                    <div class="form-row">
                        <div class="checkbox-group" onclick="selectTransportOption('yes')">
                            <input type="radio" id="transport_yes" name="needs_transport" value="yes" onchange="toggleTransportFields()" onclick="event.stopPropagation()">
                            <label for="transport_yes">Sí</label>
                        </div>
                        <div class="checkbox-group" onclick="selectTransportOption('no')">
                            <input type="radio" id="transport_no" name="needs_transport" value="no" onchange="toggleTransportFields()" onclick="event.stopPropagation()">
                            <label for="transport_no">No</label>
                        </div>
                    </div>
                    <div class="validation-error" id="needs_transport_error">
                        Debes responder si necesitas transporte al domicilio o no
                    </div>
                    
                    <div class="form-group" id="transport_address_group" style="display: none; margin-top: 15px;">
                        <label for="transport_address" class="required-label">Dirección de destino:</label>
                        <input type="text" id="transport_address" name="transport_address" placeholder="Ej: Av. Principal 123, Ciudad" maxlength="255">
                        <div class="validation-error" id="transport_address_error">
                            La dirección de destino es obligatoria cuando necesitas transporte al domicilio
                        </div>
                    </div>
                    
                    <div class="form-group" id="luggage_count_group" style="display: none; margin-top: 15px;">
                        <label for="luggage_count" class="required-label">Cantidad de equipaje:</label>
                        <input type="number" id="luggage_count" name="luggage_count" min="0" value="0">
                        <div class="validation-error" id="luggage_count_error">
                            La cantidad de equipaje es obligatoria cuando necesitas transporte al domicilio
                        </div>
                    </div>
                </div>

                @if($contingency->contingency_type === 'cancelacion')
                <div class="form-group" id="accommodation_section">
                    <label class="required-label">¿Necesitas alojamiento?</label>
                    <div class="form-row">
                        <div class="checkbox-group" onclick="selectAccommodationOption('yes')">
                            <input type="radio" id="accommodation_yes" name="needs_accommodation" value="yes" onchange="toggleAccommodationFields()" onclick="event.stopPropagation()">
                            <label for="accommodation_yes">Sí</label>
                        </div>
                        <div class="checkbox-group" onclick="selectAccommodationOption('no')">
                            <input type="radio" id="accommodation_no" name="needs_accommodation" value="no" onchange="toggleAccommodationFields()" onclick="event.stopPropagation()">
                            <label for="accommodation_no">No</label>
                        </div>
                    </div>
                    <div class="validation-error" id="needs_accommodation_error">
                        Debes responder si necesitas alojamiento o no
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <div class="checkbox-group" onclick="toggleMedicalCheckbox()">
                        <input type="checkbox" id="has_medical_condition" name="has_medical_condition" onchange="toggleMedicalDetails()" onclick="event.stopPropagation()">
                        <label for="has_medical_condition" id="medical-condition-label">¿Tienes alguna condición médica?</label>
                    </div>
                </div>

                <div class="form-group hidden" id="medical-details">
                    <label for="medical_condition_details" class="required-label">Detalles de la condición médica:</label>
                    <textarea id="medical_condition_details" name="medical_condition_details" rows="3" placeholder="Describe tu condición médica..."></textarea>
                    <div class="validation-error" id="medical_condition_details_error">
                        Los detalles de la condición médica son obligatorios
                    </div>
                </div>

                <!-- Nueva sección de reprogramación de vuelo -->
                <div class="form-group">
                    <label class="required-label">¿Has recibido reprogramación de vuelo?</label>
                    <div class="form-row">
                        <div class="checkbox-group" onclick="selectReprogrammingOption('yes')">
                            <input type="radio" id="reprogramming_yes" name="has_flight_reprogramming" value="yes" onchange="toggleReprogrammingFields()" onclick="event.stopPropagation()">
                            <label for="reprogramming_yes">Sí</label>
                        </div>
                        <div class="checkbox-group" onclick="selectReprogrammingOption('no')">
                            <input type="radio" id="reprogramming_no" name="has_flight_reprogramming" value="no" onchange="toggleReprogrammingFields()" onclick="event.stopPropagation()">
                            <label for="reprogramming_no">No</label>
                        </div>
                    </div>
                    <div class="validation-error" id="needs_reprogramming_error">
                        Debes responder si has recibido reprogramación de vuelo o no
                    </div>
                    
                    <div class="form-group" id="flight_number_group" style="display: none; margin-top: 15px;">
                        <label for="reprogrammed_flight_number" class="required-label">Número de vuelo reprogramado:</label>
                        <input type="text" id="reprogrammed_flight_number" name="reprogrammed_flight_number" placeholder="Ej: LA123" maxlength="10">
                        <div class="validation-error" id="flight_number_error">
                            El número de vuelo reprogramado es obligatorio
                        </div>
                    </div>
                    
                    <div class="form-group" id="flight_date_group" style="display: none; margin-top: 15px;">
                        <label for="reprogrammed_flight_date" class="required-label">Fecha del vuelo reprogramado:</label>
                        <input type="date" id="reprogrammed_flight_date" name="reprogrammed_flight_date" min="">
                        <div class="validation-error" id="flight_date_error">
                            La fecha del vuelo reprogramado es obligatoria y no puede ser anterior a hoy
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Botón Guardar -->
        <div class="card hidden" id="save-section">
            <button type="button" class="btn btn-success" onclick="handleSubmit()" style="width: 100%; font-size: 1.1rem; padding: 18px;">
                Revisar y Guardar Formulario
            </button>
        </div>

        <!-- Mensaje de Éxito Final -->
        <div class="card hidden" id="success-section">
            <div class="alert alert-success" style="text-align: center; padding: 30px;">
                <h3 style="margin-bottom: 15px; font-size: 1.5rem;">¡Formulario enviado exitosamente!</h3>
                <p id="success-message-text" style="font-size: 1.1rem; margin-bottom: 20px;">Gracias por completar la información. Hemos registrado tus datos correctamente.</p>
                <div style="background: #f8f9fa; border-left: 4px solid #13649c; padding: 20px; margin: 20px 0; text-align: left;">
                    <h4 style="margin: 0 0 10px 0; color: #13649c; font-size: 1.1rem;">¿Qué sigue ahora?</h4>
                    <p style="margin: 5px 0; color: #495057;">• Un operador revisará tu respuesta a la brevedad</p>
                    <p style="margin: 5px 0; color: #495057;">• Te contactaremos si necesitamos información adicional</p>
                    <p style="margin: 5px 0; color: #495057;">• Ya puedes cerrar esta pestaña con seguridad</p>
                </div>
                <p style="font-size: 0.9rem; color: #6c757d; margin-top: 15px;">
                    Esta pestaña se puede cerrar. Conserva tu PNR para futuras consultas.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmationModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3>Revisar Información</h3>
            <p style="color: #64748b; margin-bottom: 25px;">Por favor, revisa los datos antes de enviar el formulario:</p>
            
            <div id="confirmationSummary">
                <!-- El resumen se generará aquí -->
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="cancelSubmission()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="confirmSubmission()">
                    Confirmar y Enviar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        const contingencySlug = '{{ $contingency->slug }}';
        const contingencyType = '{{ $contingency->contingency_type }}';
        
        let allPassengersFromPnr = [];
        let selectedPassengers = [];
        let allSelectedPassengers = []; // Todos los pasajeros seleccionados
        let allAvailablePassengers = []; // Todos los pasajeros disponibles
        let transportAnswered = false; // Para validar respuesta obligatoria
        let accommodationAnswered = contingencyType !== 'cancelacion'; // Solo obligatorio para cancelaciones
        
        // Configurar CSRF token para las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showError(message, containerId = 'error-message') {
            const errorDiv = document.getElementById(containerId);
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function hideError(containerId = 'error-message') {
            document.getElementById(containerId).classList.add('hidden');
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            successDiv.textContent = message;
            successDiv.classList.remove('hidden');
        }

        function resetForm() {
            // Ocultar todas las secciones excepto la búsqueda
            ['passenger-selection', 'all-passengers-data', 'form-response', 'save-section', 'success-section'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            
            // Resetear variables
            allPassengersFromPnr = [];
            selectedPassengers = [];
            allSelectedPassengers = [];
            allAvailablePassengers = [];
            transportAnswered = false;
            accommodationAnswered = contingencyType !== 'cancelacion'; // Solo obligatorio para cancelaciones
            
            // Resetear formulario de respuesta
            document.getElementById('transport_yes').checked = false;
            document.getElementById('transport_no').checked = false;
            if (contingencyType === 'cancelacion') {
                document.getElementById('accommodation_yes').checked = false;
                document.getElementById('accommodation_no').checked = false;
            }
            document.getElementById('has_medical_condition').checked = false;
            document.getElementById('luggage_count').value = 0;
            document.getElementById('medical_condition_details').value = '';
            document.getElementById('reprogramming_yes').checked = false;
            document.getElementById('reprogramming_no').checked = false;
            document.getElementById('reprogrammed_flight_number').value = '';
            document.getElementById('reprogrammed_flight_date').value = '';
            
            // Ocultar campos condicionales
            const luggageGroup = document.getElementById('luggage_count_group');
            const medicalDetails = document.getElementById('medical-details');
            const flightNumberGroup = document.getElementById('flight_number_group');
            const flightDateGroup = document.getElementById('flight_date_group');
            
            if (luggageGroup) luggageGroup.style.display = 'none';
            if (medicalDetails) medicalDetails.classList.add('hidden');
            if (flightNumberGroup) flightNumberGroup.style.display = 'none';
            if (flightDateGroup) flightDateGroup.style.display = 'none';
            
            // Limpiar errores de validación
            clearValidationErrors();
        }

        function toggleTransportFields() {
            const yesRadio = document.getElementById('transport_yes');
            const noRadio = document.getElementById('transport_no');
            const addressGroup = document.getElementById('transport_address_group');
            const luggageGroup = document.getElementById('luggage_count_group');
            const accommodationSection = document.getElementById('accommodation_section');
            
            if (yesRadio.checked || noRadio.checked) {
                transportAnswered = true;
                hideValidationError('needs_transport_error');
            }
            
            if (yesRadio.checked) {
                // Si necesita transporte, mostrar campos relacionados
                addressGroup.style.display = 'block';
                luggageGroup.style.display = 'block';
                
                // OCULTAR la sección de alojamiento cuando necesita transporte
                if (accommodationSection) {
                    accommodationSection.style.display = 'none';
                    // Limpiar selección de alojamiento
                    document.getElementById('accommodation_yes').checked = false;
                    document.getElementById('accommodation_no').checked = false;
                    accommodationAnswered = contingencyType !== 'cancelacion'; // Solo marcar como respondido si no es cancelación
                    hideValidationError('needs_accommodation_error');
                }
            } else {
                // Si no necesita transporte, ocultar campos relacionados
                addressGroup.style.display = 'none';
                luggageGroup.style.display = 'none';
                document.getElementById('transport_address').value = '';
                document.getElementById('luggage_count').value = 0;
                hideValidationError('transport_address_error');
                hideValidationError('luggage_count_error');
                
                // MOSTRAR la sección de alojamiento solo para cancelaciones
                if (accommodationSection && contingencyType === 'cancelacion') {
                    accommodationSection.style.display = 'block';
                    accommodationAnswered = false; // Resetear porque ahora necesita responder
                }
            }
        }

        function toggleAccommodationFields() {
            const yesRadio = document.getElementById('accommodation_yes');
            const noRadio = document.getElementById('accommodation_no');
            
            if (yesRadio && noRadio && (yesRadio.checked || noRadio.checked)) {
                accommodationAnswered = true;
                hideValidationError('needs_accommodation_error');
            }
        }

        function selectReprogrammingOption(option) {
            if (option === 'yes') {
                document.getElementById('reprogramming_yes').checked = true;
            } else {
                document.getElementById('reprogramming_no').checked = true;
            }
            toggleReprogrammingFields();
        }

        function toggleReprogrammingFields() {
            const yesRadio = document.getElementById('reprogramming_yes');
            const noRadio = document.getElementById('reprogramming_no');
            const flightNumberGroup = document.getElementById('flight_number_group');
            const flightDateGroup = document.getElementById('flight_date_group');
            
            if (yesRadio.checked || noRadio.checked) {
                hideValidationError('needs_reprogramming_error');
            }
            
            if (yesRadio.checked) {
                flightNumberGroup.style.display = 'block';
                flightDateGroup.style.display = 'block';
                // Establecer fecha mínima cada vez que se muestre el campo
                setMinimumFlightDate();
            } else {
                flightNumberGroup.style.display = 'none';
                flightDateGroup.style.display = 'none';
                document.getElementById('reprogrammed_flight_number').value = '';
                document.getElementById('reprogrammed_flight_date').value = '';
                hideValidationError('flight_number_error');
                hideValidationError('flight_date_error');
            }
        }

        function updateMaxChildren() {
            // Esta función ya no es necesaria pero se mantiene para evitar errores
            // Actualizar texto de condición médica según cantidad de pasajeros
            updateMedicalConditionText();
        }

        function updateMedicalConditionText() {
            const medicalLabel = document.getElementById('medical-condition-label');
            if (medicalLabel) {
                if (allSelectedPassengers.length > 1) {
                    medicalLabel.textContent = '¿Algún pasajero tiene alguna condición médica?';
                } else {
                    medicalLabel.textContent = '¿Tienes alguna condición médica?';
                }
            }
        }

        function updateSuccessMessage() {
            const successText = document.getElementById('success-message-text');
            if (successText) {
                if (allSelectedPassengers.length > 1) {
                    successText.textContent = 'Gracias por completar la información. Hemos registrado los datos correctamente.';
                } else {
                    successText.textContent = 'Gracias por completar la información. Hemos registrado tus datos correctamente.';
                }
            }
        }

        function setMinimumFlightDate() {
            const today = new Date();
            
            // Fecha mínima: hoy
            const minYear = today.getFullYear();
            const minMonth = String(today.getMonth() + 1).padStart(2, '0');
            const minDay = String(today.getDate()).padStart(2, '0');
            const todayString = `${minYear}-${minMonth}-${minDay}`;
            
            // Fecha máxima: 2 años desde hoy
            const maxDate = new Date();
            maxDate.setFullYear(today.getFullYear() + 2);
            const maxYear = maxDate.getFullYear();
            const maxMonth = String(maxDate.getMonth() + 1).padStart(2, '0');
            const maxDay = String(maxDate.getDate()).padStart(2, '0');
            const maxDateString = `${maxYear}-${maxMonth}-${maxDay}`;
            
            const flightDateInput = document.getElementById('reprogrammed_flight_date');
            if (flightDateInput) {
                flightDateInput.setAttribute('min', todayString);
                flightDateInput.setAttribute('max', maxDateString);
            }
        }

        function validateFlightDate(selectedDate) {
            const today = new Date();
            const selected = new Date(selectedDate);
            
            // Normalizar las fechas para comparar solo día, mes y año
            today.setHours(0, 0, 0, 0);
            selected.setHours(0, 0, 0, 0);
            
            // Fecha máxima: 2 años desde hoy
            const maxDate = new Date();
            maxDate.setFullYear(today.getFullYear() + 2);
            maxDate.setHours(0, 0, 0, 0);
            
            // Validar que esté entre hoy y 2 años desde hoy
            return selected >= today && selected <= maxDate;
        }

        function selectTransportOption(option) {
            if (option === 'yes') {
                document.getElementById('transport_yes').checked = true;
            } else {
                document.getElementById('transport_no').checked = true;
            }
            toggleTransportFields();
        }

        function selectAccommodationOption(option) {
            if (option === 'yes') {
                document.getElementById('accommodation_yes').checked = true;
            } else {
                document.getElementById('accommodation_no').checked = true;
            }
            toggleAccommodationFields();
        }

        function toggleMedicalCheckbox() {
            const checkbox = document.getElementById('has_medical_condition');
            checkbox.checked = !checkbox.checked;
            toggleMedicalDetails();
        }

        function showValidationError(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('show');
            }
        }

        function hideValidationError(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.remove('show');
            }
        }

        function clearValidationErrors() {
            const errorElements = document.querySelectorAll('.validation-error');
            errorElements.forEach(element => {
                element.classList.remove('show');
            });
            
            const requiredElements = document.querySelectorAll('.required');
            requiredElements.forEach(element => {
                element.classList.remove('required');
            });
        }

        function validateForm() {
            let isValid = true;
            clearValidationErrors();

            // Validar respuesta obligatoria de transporte
            if (!transportAnswered) {
                showValidationError('needs_transport_error');
                isValid = false;
            }

            // Validar respuesta obligatoria de alojamiento (solo para cancelaciones cuando no se necesita transporte)
            if (contingencyType === 'cancelacion' && !accommodationAnswered && !document.getElementById('transport_yes').checked) {
                showValidationError('needs_accommodation_error');
                isValid = false;
            }

            // Validar respuesta obligatoria de reprogramación de vuelo
            const reprogrammingYes = document.getElementById('reprogramming_yes');
            const reprogrammingNo = document.getElementById('reprogramming_no');
            if (!reprogrammingYes.checked && !reprogrammingNo.checked) {
                showValidationError('needs_reprogramming_error');
                isValid = false;
            }

            // Validar campos condicionales
            const needsTransport = document.getElementById('transport_yes').checked;
            const needsAccommodation = contingencyType === 'cancelacion' ? document.getElementById('accommodation_yes').checked : false;
            const hasMedicalCondition = document.getElementById('has_medical_condition').checked;
            const hasReprogramming = document.getElementById('reprogramming_yes').checked;

            // Si necesita transporte, validar dirección y cantidad de equipaje
            if (needsTransport) {
                const transportAddress = document.getElementById('transport_address').value.trim();
                if (transportAddress === '') {
                    showValidationError('transport_address_error');
                    document.getElementById('transport_address').classList.add('required');
                    isValid = false;
                }
                
                const luggageCount = document.getElementById('luggage_count').value.trim();
                if (luggageCount === '' || parseInt(luggageCount) < 0) {
                    showValidationError('luggage_count_error');
                    document.getElementById('luggage_count').classList.add('required');
                    isValid = false;
                }
            }

            // Si necesita alojamiento (solo en cancelaciones), no hay validaciones adicionales necesarias
            // ya que cada pasajero carga su propia edad individualmente

            // Si tiene condición médica, validar detalles
            if (hasMedicalCondition) {
                const medicalDetails = document.getElementById('medical_condition_details').value.trim();
                if (medicalDetails === '') {
                    showValidationError('medical_condition_details_error');
                    document.getElementById('medical_condition_details').classList.add('required');
                    isValid = false;
                }
            }

            // Si tiene reprogramación, validar número de vuelo y fecha
            if (hasReprogramming) {
                const flightNumber = document.getElementById('reprogrammed_flight_number').value.trim();
                const flightDate = document.getElementById('reprogrammed_flight_date').value.trim();
                
                if (flightNumber === '') {
                    showValidationError('flight_number_error');
                    document.getElementById('reprogrammed_flight_number').classList.add('required');
                    isValid = false;
                }
                
                if (flightDate === '') {
                    showValidationError('flight_date_error');
                    document.getElementById('reprogrammed_flight_date').classList.add('required');
                    isValid = false;
                } else if (!validateFlightDate(flightDate)) {
                    showValidationError('flight_date_error');
                    document.getElementById('reprogrammed_flight_date').classList.add('required');
                    isValid = false;
                }
            }

            // Validar información de contacto del primer pasajero
            if (allSelectedPassengers.length > 0) {
                const firstPassenger = allSelectedPassengers[0];
                const emailInput = document.getElementById(`email-${firstPassenger.id}`);
                const phoneInput = document.getElementById(`phone-${firstPassenger.id}`);
                
                const email = emailInput ? emailInput.value.trim() : (firstPassenger.email || '');
                const phone = phoneInput ? phoneInput.value.trim() : (firstPassenger.phone || '');
                
                // Validar email del primer pasajero
                if (!isValidEmail(email)) {
                    alert(`El primer pasajero (${firstPassenger.name} ${firstPassenger.surname}) debe tener un email válido para enviar el formulario.`);
                    if (emailInput) {
                        emailInput.classList.add('required');
                        emailInput.focus();
                    }
                    isValid = false;
                }
                
                // Validar teléfono del primer pasajero
                if (!isValidPhone(phone)) {
                    alert(`El primer pasajero (${firstPassenger.name} ${firstPassenger.surname}) debe tener un teléfono válido (10-15 dígitos) para enviar el formulario.\n\nEjemplo: +56912345678 o 912345678`);
                    if (phoneInput) {
                        phoneInput.classList.add('required');
                        phoneInput.focus();
                    }
                    isValid = false;
                }
                
                // Validar que todos los pasajeros tengan edad válida
                let missingAge = [];
                allSelectedPassengers.forEach(passenger => {
                    const ageInput = document.getElementById(`age-${passenger.id}`);
                    const age = ageInput ? ageInput.value.trim() : (passenger.age || '');
                    
                    if (!age || isNaN(age) || age < 0 || age > 120) {
                        missingAge.push(`${passenger.name} ${passenger.surname}`);
                    }
                });
                
                if (missingAge.length > 0) {
                    alert(`Los siguientes pasajeros requieren una edad válida (0-120 años) para enviar el formulario:\n\n${missingAge.join('\n')}`);
                    // Enfocar el primer campo de edad problemático
                    const firstMissingPassenger = allSelectedPassengers.find(p => 
                        missingAge.includes(`${p.name} ${p.surname}`)
                    );
                    if (firstMissingPassenger) {
                        const ageInput = document.getElementById(`age-${firstMissingPassenger.id}`);
                        if (ageInput) {
                            ageInput.classList.add('required');
                            ageInput.focus();
                        }
                    }
                    isValid = false;
                }

                // Validar que pasajeros mayores de edad (además del primero) tengan email y teléfono
                let missingAdultContact = [];
                allSelectedPassengers.forEach((passenger, index) => {
                    if (index === 0) return; // El primer pasajero ya se validó arriba
                    
                    const ageInput = document.getElementById(`age-${passenger.id}`);
                    const emailInput = document.getElementById(`email-${passenger.id}`);
                    const phoneInput = document.getElementById(`phone-${passenger.id}`);
                    
                    const age = ageInput ? parseInt(ageInput.value.trim()) : (passenger.age || 0);
                    const email = emailInput ? emailInput.value.trim() : (passenger.email || '');
                    const phone = phoneInput ? phoneInput.value.trim() : (passenger.phone || '');
                    
                    if (age >= 18 && (!email || !phone)) {
                        missingAdultContact.push(`${passenger.name} ${passenger.surname} (${age} años)`);
                    }
                });
                
                if (missingAdultContact.length > 0) {
                    alert(`Los siguientes pasajeros mayores de edad requieren email y teléfono para enviar el formulario:\n\n${missingAdultContact.join('\n')}\n\nNota: Los menores de edad no requieren datos de contacto.`);
                    isValid = false;
                }
            }

            return isValid;
        }

        function searchPassenger() {
            const pnr = document.getElementById('pnr').value.trim().toUpperCase();
            const lastName = document.getElementById('last-name').value.trim();
            
            if (!pnr) {
                showError('Por favor ingresa un PNR válido');
                return;
            }
            
            if (!lastName) {
                showError('Por favor ingresa tu apellido');
                return;
            }

            hideError();

            const url = `/contingencias/${contingencySlug}/buscar-pasajero`;
            const requestData = { 
                pnr: pnr,
                last_name: lastName
            };
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                // Para cualquier respuesta, intentar leer como JSON
                return response.json().then(data => {
                    if (!response.ok) {
                        // Si no es ok, lanzar error con el mensaje del servidor
                        throw new Error(data.error || data.message || `Error ${response.status}: ${response.statusText}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.error) {
                    showError(data.error);
                } else {
                    allPassengersFromPnr = data.passengers;
                    // Agregar estos pasajeros a la lista global de disponibles
                    allAvailablePassengers = [...data.passengers];
                    
                    // Ocultar la sección de búsqueda PNR después del primer resultado
                    document.getElementById('pnr-search').classList.add('hidden');
                    
                    if (data.isSharedPnr) {
                        // PNR compartido - mostrar selección
                        showPassengerSelection(data.passengers);
                    } else {
                        // Un solo pasajero - agregar directamente y mostrar opción de más PNRs
                        selectedPassengers = [data.passengers[0]];
                        allSelectedPassengers = [...selectedPassengers];
                        showPassengerSelectionWithAddMore([data.passengers[0]]);
                    }
                }
            })
            .catch(error => {
                showError(error.message || 'Error al buscar el pasajero');
            });
        }

        function searchFormResponse() {
            const responseId = document.getElementById('response-id').value.trim();
            
            if (!responseId || responseId < 1) {
                showError('Por favor ingresa un número de respuesta válido', 'modify-error-message');
                return;
            }

            hideError('modify-error-message');

            fetch(`/contingencias/${contingencySlug}/buscar-respuesta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ response_id: parseInt(responseId) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showError(data.error, 'modify-error-message');
                } else {
                    // Cargar datos existentes para modificar
                    existingFormResponse = data.formResponse;
                    selectedPassengers = [data.mainPassenger];
                    additionalPassengers = data.additionalPassengers.slice(); // Crear copia
                    isEditMode = true;
                    
                    // Ocultar modo modificar y mostrar datos
                    document.getElementById('modify-response').classList.add('hidden');
                    showSelectedPassengersData();
                    showSuccess(`Formulario #${responseId} cargado para modificación`);
                }
            })
            .catch(error => {
                showError('Error al cargar el formulario', 'modify-error-message');
            });
        }

        function showPassengerSelection(passengers) {
            const selectionList = document.getElementById('passenger-selection-list');
            selectionList.innerHTML = '';

            passengers.forEach((passenger, index) => {
                const passengerDiv = document.createElement('div');
                passengerDiv.className = 'passenger-selection-item';
                passengerDiv.id = `selection-item-${passenger.id}`;
                passengerDiv.onclick = () => togglePassengerByClick(passenger.id);
                
                passengerDiv.innerHTML = `
                    <div class="passenger-selection-header">
                        <input type="checkbox" id="select-${passenger.id}"
                               onchange="togglePassengerSelection(${passenger.id}, this.checked)"
                               onclick="event.stopPropagation()">
                        <span>
                            <strong>${passenger.name} ${passenger.surname}</strong>
                        </span>
                    </div>
                `;
                
                selectionList.appendChild(passengerDiv);
            });

            document.getElementById('passenger-selection').classList.remove('hidden');
            // Mostrar inmediatamente la opción de agregar más PNRs
            document.getElementById('add-more-pnrs').classList.remove('hidden');
        }

        function showPassengerSelectionWithAddMore(passengers) {
            // Para un solo pasajero, mostrarlo como ya seleccionado
            const selectionList = document.getElementById('passenger-selection-list');
            selectionList.innerHTML = '';

            const passenger = passengers[0];
            const passengerDiv = document.createElement('div');
            passengerDiv.className = 'passenger-selection-item selected';
            passengerDiv.innerHTML = `
                <div class="passenger-selection-header">
                    <input type="checkbox" id="select-${passenger.id}" checked disabled>
                    <span>
                        <strong>${passenger.name} ${passenger.surname}</strong>
                    </span>
                </div>
            `;
            selectionList.appendChild(passengerDiv);

            document.getElementById('passenger-selection').classList.remove('hidden');
            document.getElementById('add-more-pnrs').classList.remove('hidden');
        }

        function togglePassengerSelection(passengerId, isSelected) {
            // Buscar el pasajero en todas las listas disponibles
            let passenger = allAvailablePassengers.find(p => p.id === passengerId);
            
            const itemDiv = document.getElementById(`selection-item-${passengerId}`);
            
            if (isSelected && passenger) {
                if (!selectedPassengers.find(p => p.id === passengerId)) {
                    selectedPassengers.push(passenger);
                }
                if (itemDiv) itemDiv.classList.add('selected');
            } else {
                selectedPassengers = selectedPassengers.filter(p => p.id !== passengerId);
                if (itemDiv) itemDiv.classList.remove('selected');
            }
            
            // Actualizar la lista de pasajeros seleccionados
            updateAllSelectedPassengers();
        }

        function updateAllSelectedPassengers() {
            // Combinar pasajeros seleccionados del PNR actual con los de PNRs adicionales
            allSelectedPassengers = [...selectedPassengers];
            // Solo actualizar texto de condición médica
            updateMedicalConditionText();
        }

        function togglePassengerDetails(passengerId) {
            const detailsDiv = document.getElementById(`details-${passengerId}`);
            // Buscar el botón de edición en el contenedor correcto
            let button = document.querySelector(`#selection-item-${passengerId} .edit-button`);
            if (!button) {
                // Si no se encuentra en selection-item, buscar en la sección de datos de pasajeros
                button = document.querySelector(`[onclick="togglePassengerDetails(${passengerId})"]`);
            }
            
            if (detailsDiv && detailsDiv.classList.contains('show')) {
                detailsDiv.classList.remove('show');
                if (button) button.textContent = 'Editar Datos';
            } else if (detailsDiv) {
                detailsDiv.classList.add('show');
                if (button) button.textContent = 'Ocultar Datos';
            }
        }

        function addMorePassengers() {
            const pnr = document.getElementById('additional-pnr').value.trim().toUpperCase();
            const lastName = document.getElementById('additional-last-name').value.trim();
            
            if (!pnr) {
                alert('Por favor ingresa un PNR válido');
                return;
            }
            
            if (!lastName) {
                alert('Por favor ingresa el apellido');
                return;
            }

            const url = `/contingencias/${contingencySlug}/buscar-pasajero-adicional`;
            const requestData = { 
                pnr: pnr,
                last_name: lastName
            };

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                // Para cualquier respuesta, intentar leer como JSON
                return response.json().then(data => {
                    if (!response.ok) {
                        // Si no es ok, lanzar error con el mensaje del servidor
                        throw new Error(data.error || data.message || `Error ${response.status}: ${response.statusText}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    if (data.isMultiple) {
                        // Múltiples pasajeros - mostrar selector y agregar a la vista actual
                        showAdditionalPassengerSelection(data.passengers, pnr);
                    } else {
                        // Un solo pasajero - agregar directamente
                        const passenger = data.passengers[0];
                        addPassengerToSelection(passenger);
                        document.getElementById('additional-pnr').value = '';
                        document.getElementById('additional-last-name').value = '';
                    }
                }
            })
            .catch(error => {
                alert('Error al buscar el pasajero: ' + error.message);
            });
        }

        function addPassengerToSelection(passenger) {
            // Agregar pasajero a la lista de selección actual
            const selectionList = document.getElementById('passenger-selection-list');
            
            const passengerDiv = document.createElement('div');
            passengerDiv.className = 'passenger-selection-item selected';
            passengerDiv.id = `selection-item-${passenger.id}`;
            passengerDiv.onclick = () => togglePassengerByClick(passenger.id);
            passengerDiv.innerHTML = `
                <div class="passenger-selection-header">
                    <input type="checkbox" id="select-${passenger.id}" checked 
                           onchange="togglePassengerSelection(${passenger.id}, this.checked)"
                           onclick="event.stopPropagation()">
                    <span>
                        <strong>${passenger.name} ${passenger.surname} (${passenger.pnr})</strong>
                    </span>
                </div>
            `;
            
            selectionList.appendChild(passengerDiv);
            
            // Agregar a las listas de pasajeros
            selectedPassengers.push(passenger);
            // Agregar a la lista de disponibles si no está ya
            if (!allAvailablePassengers.find(p => p.id === passenger.id)) {
                allAvailablePassengers.push(passenger);
            }
            updateAllSelectedPassengers();
        }

        function togglePassengerByClick(passengerId) {
            const checkbox = document.getElementById(`select-${passengerId}`);
            checkbox.checked = !checkbox.checked;
            togglePassengerSelection(passengerId, checkbox.checked);
        }

        function proceedToFormResponse() {
            if (selectedPassengers.length === 0) {
                alert('Por favor selecciona al menos un pasajero');
                return;
            }

            // Ocultar la sección de selección de pasajeros
            document.getElementById('passenger-selection').classList.add('hidden');
            
            // Mostrar SOLO la sección de datos de pasajeros para edición
            showAllPassengersData();
            
            // Actualizar máximo de niños
            updateMaxChildren();
        }

        /**
         * Validar formato de email
         */
        function isValidEmail(email) {
            if (!email || email.trim() === '') {
                return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email.trim());
        }

        /**
         * Validar formato de teléfono
         */
        function isValidPhone(phone) {
            if (!phone || phone.trim() === '') {
                return false;
            }
            
            // Remover espacios, guiones y otros caracteres permitidos para limpieza
            const cleanPhone = phone.replace(/[\s\-()]/g, '');
            
            // Validar que solo contenga números y opcionalmente un + al inicio
            if (!/^[\+]?[0-9]+$/.test(cleanPhone)) {
                return false;
            }
            
            // Remover el + para contar solo los dígitos
            const digitsOnly = cleanPhone.replace(/\+/g, '');
            
            // Validar que tenga entre 10 y 15 dígitos
            return digitsOnly.length >= 10 && digitsOnly.length <= 15;
        }

        /**
         * Validar entrada de teléfono en tiempo real (mientras se escribe)
         */
        function validatePhoneInput(input) {
            const value = input.value;
            const passengerId = input.id.replace('phone-', '');
            const errorDiv = document.getElementById(`phone-error-${passengerId}`);
            
            // Permitir solo números, + al inicio, espacios y guiones
            let cleanValue = value.replace(/[^0-9+\s\-()]/g, '');
            
            // Solo permitir + al inicio
            if (cleanValue.includes('+')) {
                const parts = cleanValue.split('+');
                if (parts[0] === '') {
                    // + está al inicio, está bien
                    cleanValue = '+' + parts.slice(1).join('').replace(/\+/g, '');
                } else {
                    // + no está al inicio, removerlo
                    cleanValue = cleanValue.replace(/\+/g, '');
                }
            }
            
            // Si el valor cambió, actualizarlo
            if (cleanValue !== value) {
                input.value = cleanValue;
            }
            
            // Limpiar errores previos si el campo está vacío
            if (cleanValue === '') {
                hidePhoneError(passengerId);
                input.classList.remove('error');
                return;
            }
            
            // Validar que no contenga caracteres no permitidos
            if (/[^0-9+\s\-()]/.test(cleanValue)) {
                showPhoneError(passengerId, 'Solo se permiten números, +, espacios, guiones y paréntesis');
                input.classList.add('error');
            } else {
                hidePhoneError(passengerId);
                input.classList.remove('error');
            }
        }

        /**
         * Validar formato completo de teléfono (al perder el foco)
         */
        function validatePhoneFormat(input) {
            const value = input.value.trim();
            const passengerId = input.id.replace('phone-', '');
            
            if (value === '') {
                hidePhoneError(passengerId);
                input.classList.remove('error');
                return;
            }
            
            if (!isValidPhone(value)) {
                showPhoneError(passengerId, 'Debe tener entre 10-15 dígitos. Ej: +56912345678');
                input.classList.add('error');
            } else {
                hidePhoneError(passengerId);
                input.classList.remove('error');
            }
        }

        /**
         * Mostrar error de teléfono
         */
        function showPhoneError(passengerId, message) {
            const errorDiv = document.getElementById(`phone-error-${passengerId}`);
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                errorDiv.style.color = '#dc3545';
                errorDiv.style.fontSize = '0.875rem';
                errorDiv.style.marginTop = '5px';
            }
        }

        /**
         * Ocultar error de teléfono
         */
        function hidePhoneError(passengerId) {
            const errorDiv = document.getElementById(`phone-error-${passengerId}`);
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        }

        /**
         * Actualizar requerimientos de email y teléfono basado en la edad
         */
        function updatePassengerAgeRequirements(passengerId, passengerIndex) {
            const ageInput = document.getElementById(`age-${passengerId}`);
            const emailInput = document.getElementById(`email-${passengerId}`);
            const phoneInput = document.getElementById(`phone-${passengerId}`);
            const emailLabel = document.getElementById(`email-label-${passengerId}`);
            const phoneLabel = document.getElementById(`phone-label-${passengerId}`);
            const ageIndicator = document.getElementById(`age-indicator-${passengerId}`);
            
            if (!ageInput || !emailInput || !phoneInput || !emailLabel || !phoneLabel || !ageIndicator) {
                return;
            }

            const age = parseInt(ageInput.value) || 0;
            const isFirstPassenger = passengerIndex === 0;
            const isMinor = age < 18 && age > 0;
            
            // Actualizar indicador de edad en el header
            if (isMinor && !isFirstPassenger) {
                ageIndicator.innerHTML = ' <span style="color: #666; font-size: 0.9em;">(Menor de edad)</span>';
            } else {
                ageIndicator.innerHTML = '';
            }
            
            // Actualizar requerimientos para email y teléfono
            if (isFirstPassenger) {
                // El primer pasajero siempre tiene campos obligatorios
                emailLabel.innerHTML = 'Email: <span style="color: red;">*</span>';
                phoneLabel.innerHTML = 'Teléfono: <span style="color: red;">*</span>';
                emailInput.required = true;
                phoneInput.required = true;
            } else if (isMinor) {
                // Menor de edad: campos opcionales
                emailLabel.innerHTML = 'Email: <span style="color: #666;">(Opcional para menores)</span>';
                phoneLabel.innerHTML = 'Teléfono: <span style="color: #666;">(Opcional para menores)</span>';
                emailInput.required = false;
                phoneInput.required = false;
                
                // Limpiar errores de validación si los había
                const emailError = document.getElementById(`email-error-${passengerId}`);
                const phoneError = document.getElementById(`phone-error-${passengerId}`);
                if (emailError) {
                    emailError.textContent = '';
                    emailError.style.display = 'none';
                }
                if (phoneError) {
                    phoneError.textContent = '';
                    phoneError.style.display = 'none';
                }
                
                // Remover clases de error
                emailInput.classList.remove('error');
                phoneInput.classList.remove('error');
            } else if (age >= 18) {
                // Mayor de edad: campos obligatorios
                emailLabel.innerHTML = 'Email: <span style="color: orange;">*</span>';
                phoneLabel.innerHTML = 'Teléfono: <span style="color: orange;">*</span>';
                emailInput.required = true;
                phoneInput.required = true;
            }
        }

        function updatePassengerDataFromInlineEdits() {
            allSelectedPassengers.forEach(passenger => {
                const documentInput = document.getElementById(`document-${passenger.id}`);
                const emailInput = document.getElementById(`email-${passenger.id}`);
                const phoneInput = document.getElementById(`phone-${passenger.id}`);
                const ageInput = document.getElementById(`age-${passenger.id}`);
                
                if (documentInput) passenger.document_number = documentInput.value;
                if (emailInput) passenger.email = emailInput.value;
                if (phoneInput) passenger.phone = phoneInput.value;
                if (ageInput) passenger.age = ageInput.value;
            });
        }

        function goBackToPassengerSelection() {
            // Ocultar sección de datos de pasajeros
            document.getElementById('all-passengers-data').classList.add('hidden');
            // Mostrar sección de selección de pasajeros
            document.getElementById('passenger-selection').classList.remove('hidden');
        }

        function goBackToPassengerData() {
            // Ocultar formulario de respuesta y botón de guardar
            document.getElementById('form-response').classList.add('hidden');
            document.getElementById('save-section').classList.add('hidden');
            // Mostrar sección de datos de pasajeros
            showAllPassengersData();
        }

        function proceedToFormOnly() {
            // Validar que todos los pasajeros tengan edad válida PRIMERO
            let missingAge = [];
            allSelectedPassengers.forEach(passenger => {
                const ageInput = document.getElementById(`age-${passenger.id}`);
                const age = ageInput ? ageInput.value.trim() : (passenger.age || '');
                
                if (!age || isNaN(age) || age < 0 || age > 120) {
                    missingAge.push(`${passenger.name} ${passenger.surname}`);
                }
            });
            
            if (missingAge.length > 0) {
                alert(`Los siguientes pasajeros requieren una edad válida (0-120 años) para continuar:\n\n${missingAge.join('\n')}\n\nPor favor, completa estos datos obligatorios antes de continuar.`);
                // Enfocar el primer campo de edad problemático
                const firstMissingPassenger = allSelectedPassengers.find(p => 
                    missingAge.includes(`${p.name} ${p.surname}`)
                );
                if (firstMissingPassenger) {
                    const ageInput = document.getElementById(`age-${firstMissingPassenger.id}`);
                    if (ageInput) {
                        ageInput.classList.add('required');
                        ageInput.focus();
                    }
                }
                return;
            }
            
            // Validar que todos los pasajeros tengan email y teléfono (excepto menores de edad, salvo el primer pasajero)
            let missingData = [];
            allSelectedPassengers.forEach((passenger, index) => {
                const emailInput = document.getElementById(`email-${passenger.id}`);
                const phoneInput = document.getElementById(`phone-${passenger.id}`);
                const ageInput = document.getElementById(`age-${passenger.id}`);
                
                const email = emailInput ? emailInput.value.trim() : (passenger.email || '');
                const phone = phoneInput ? phoneInput.value.trim() : (passenger.phone || '');
                const age = ageInput ? parseInt(ageInput.value.trim()) : (passenger.age || 0);
                
                // El primer pasajero SIEMPRE debe tener email y teléfono, independientemente de su edad
                // Los demás pasajeros solo si son mayores de edad (18+)
                const requiresContact = index === 0 || age >= 18;
                
                if (requiresContact && (!email || !phone)) {
                    missingData.push(`${passenger.name} ${passenger.surname}${index === 0 ? ' (pasajero principal)' : ' (mayor de edad)'}`);
                }
                
                // Actualizar los datos del pasajero
                if (emailInput) passenger.email = emailInput.value.trim();
                if (phoneInput) passenger.phone = phoneInput.value.trim();
                if (ageInput) passenger.age = ageInput.value.trim();
            });
            
            if (missingData.length > 0) {
                alert(`Los siguientes pasajeros requieren email y teléfono para continuar:\n\n${missingData.join('\n')}\n\nNota: Los menores de edad no requieren datos de contacto (excepto el pasajero principal).`);
                return;
            }

            // Ocultar sección de datos de pasajeros
            document.getElementById('all-passengers-data').classList.add('hidden');
            // Mostrar SOLO formulario de respuesta y botón de guardar
            document.getElementById('form-response').classList.remove('hidden');
            document.getElementById('save-section').classList.remove('hidden');
            // Actualizar máximo de niños
            updateMaxChildren();
        }

        function showAllPassengersData() {
            const container = document.getElementById('all-passengers-info');
            container.innerHTML = '';

            allSelectedPassengers.forEach((passenger, index) => {
                const passengerCard = document.createElement('div');
                passengerCard.className = 'selected-passenger-card';
                
                // Determinar si este pasajero requiere datos de contacto obligatorios
                const age = parseInt(passenger.age) || 0;
                const requiresContact = index === 0 || age >= 18;
                const isRequired = index === 0; // Solo el primer pasajero tiene * rojo
                const actuallyRequired = requiresContact; // Pero la validación se aplica según edad
                
                passengerCard.innerHTML = `
                    <div class="passenger-selection-header">
                        <h4 id="passenger-header-${passenger.id}">${passenger.name} ${passenger.surname} (${passenger.pnr})<span id="age-indicator-${passenger.id}" style="color: #666; font-size: 0.9em;"></span></h4>
                        <button type="button" class="edit-button" onclick="togglePassengerDetails(${passenger.id})">
                            Editar Datos
                        </button>
                    </div>
                    <div id="details-${passenger.id}" class="passenger-details">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="document-${passenger.id}">Documento:</label>
                                <input type="text" id="document-${passenger.id}" value="${passenger.document_number || ''}" placeholder="Número de documento">
                            </div>
                            <div class="form-group">
                                <label for="age-${passenger.id}">Edad: <span style="color: red;">*</span></label>
                                <input type="number" id="age-${passenger.id}" value="${passenger.age || ''}" placeholder="Edad" min="0" max="120" required onchange="updatePassengerAgeRequirements(${passenger.id}, ${index})">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label id="email-label-${passenger.id}" for="email-${passenger.id}">Email: ${isRequired ? '<span style="color: red;">*</span>' : (requiresContact ? '<span style="color: orange;">*</span>' : '<span style="color: #666;">(Opcional para menores)</span>')}</label>
                                <input type="email" id="email-${passenger.id}" value="${passenger.email || ''}" placeholder="Correo electrónico" ${actuallyRequired ? 'required' : ''}>
                                <div class="validation-error" id="email-error-${passenger.id}"></div>
                            </div>
                            <div class="form-group">
                                <label id="phone-label-${passenger.id}" for="phone-${passenger.id}">Teléfono: ${isRequired ? '<span style="color: red;">*</span>' : (requiresContact ? '<span style="color: orange;">*</span>' : '<span style="color: #666;">(Opcional para menores)</span>')}</label>
                                <input type="tel" id="phone-${passenger.id}" value="${passenger.phone || ''}" 
                                       placeholder="Número de teléfono (ej: +56912345678)" 
                                       pattern="[+]?[0-9]{10,15}" 
                                       title="Ingresa un número de teléfono válido (10-15 dígitos, puede incluir +)"
                                       ${actuallyRequired ? 'required' : ''}
                                       oninput="validatePhoneInput(this)"
                                       onblur="validatePhoneFormat(this)">
                                <div class="validation-error" id="phone-error-${passenger.id}"></div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(passengerCard);
            });

            // Actualizar los requerimientos de edad para todos los pasajeros después de cargar
            setTimeout(() => {
                allSelectedPassengers.forEach((passenger, index) => {
                    updatePassengerAgeRequirements(passenger.id, index);
                });
            }, 100);

            document.getElementById('all-passengers-data').classList.remove('hidden');
        }

        function loadFormResponseData(formResponse) {
            // Cargar datos de transporte
            if (formResponse.needs_transport) {
                document.getElementById('transport_yes').checked = true;
                document.getElementById('transport_address').value = formResponse.transport_address || '';
                document.getElementById('luggage_count').value = formResponse.luggage_count || 0;
                document.getElementById('transport_address_group').style.display = 'block';
                document.getElementById('luggage_count_group').style.display = 'block';
            } else {
                document.getElementById('transport_no').checked = true;
            }
            
            // Cargar datos de alojamiento (solo para cancelaciones)
            if (contingencyType === 'cancelacion') {
                if (formResponse.needs_accommodation) {
                    const accommodationYes = document.getElementById('accommodation_yes');
                    if (accommodationYes) accommodationYes.checked = true;
                } else {
                    const accommodationNo = document.getElementById('accommodation_no');
                    if (accommodationNo) accommodationNo.checked = true;
                }
            }
            
            // Cargar datos de condición médica
            document.getElementById('has_medical_condition').checked = formResponse.has_medical_condition;
            document.getElementById('medical_condition_details').value = formResponse.medical_condition_details || '';
            
            // Marcar respuestas como respondidas para evitar validación obligatoria
            transportAnswered = true;
            accommodationAnswered = contingencyType === 'cancelacion' ? true : true; // Siempre true ya que se está cargando data existente
            
            // Mostrar campos condicionales según las respuestas
            if (formResponse.has_medical_condition) {
                document.getElementById('medical-details').classList.remove('hidden');
            }
        }

        function toggleMedicalDetails() {
            const checkbox = document.getElementById('has_medical_condition');
            const details = document.getElementById('medical-details');
            
            if (checkbox.checked) {
                details.classList.remove('hidden');
            } else {
                details.classList.add('hidden');
                document.getElementById('medical_condition_details').value = '';
            }
        }

        // Variables para la selección de pasajeros adicionales
        let additionalPassengerCandidates = [];
        let selectedAdditionalPassengers = [];

        function showAdditionalPassengerSelection(passengers, pnr) {
            additionalPassengerCandidates = passengers;
            selectedAdditionalPassengers = [];

            // Crear modal o sección para selección
            const modalHTML = `
                <div class="modal-overlay" id="additional-passenger-modal">
                    <div class="modal-content">
                        <h3>Seleccionar Pasajeros para PNR: ${pnr}</h3>
                        <p>Este PNR tiene múltiples pasajeros. Selecciona cuáles agregar al formulario:</p>
                        <div id="additional-passenger-selection-list" style="margin: 20px 0;">
                            <!-- Lista de pasajeros se llenará aquí -->
                        </div>
                        <div style="text-align: right; margin-top: 20px;">
                            <button type="button" class="btn btn-secondary" onclick="closeAdditionalPassengerModal()" style="margin-right: 10px;">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="confirmAdditionalPassengerSelection()">Agregar Seleccionados</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Llenar la lista de pasajeros
            const selectionList = document.getElementById('additional-passenger-selection-list');
            passengers.forEach(passenger => {
                const passengerDiv = document.createElement('div');
                passengerDiv.className = 'passenger-selection-item';
                passengerDiv.style.cssText = 'border: 1px solid #d1d5db; padding: 15px; margin: 10px 0; border-radius: 4px; cursor: pointer;';
                passengerDiv.id = `additional-selection-item-${passenger.id}`;
                passengerDiv.onclick = () => toggleAdditionalPassengerByClick(passenger.id);
                
                passengerDiv.innerHTML = `
                    <div class="passenger-selection-header">
                        <input type="checkbox" id="additional-select-${passenger.id}" 
                               onchange="toggleAdditionalPassengerSelection(${passenger.id}, this.checked)"
                               onclick="event.stopPropagation()">
                        <span style="margin-left: 10px; cursor: pointer;">
                            <strong>${passenger.name} ${passenger.surname}</strong>
                        </span>
                    </div>
                `;
                
                selectionList.appendChild(passengerDiv);
            });
        }

        function toggleAdditionalPassengerSelection(passengerId, isSelected) {
            const passenger = additionalPassengerCandidates.find(p => p.id === passengerId);
            const itemDiv = document.getElementById(`additional-selection-item-${passengerId}`);
            
            if (isSelected) {
                if (!selectedAdditionalPassengers.find(p => p.id === passengerId)) {
                    selectedAdditionalPassengers.push(passenger);
                }
                itemDiv.style.backgroundColor = '#e3f2fd';
                itemDiv.style.borderColor = '#13649c';
            } else {
                selectedAdditionalPassengers = selectedAdditionalPassengers.filter(p => p.id !== passengerId);
                itemDiv.style.backgroundColor = '';
                itemDiv.style.borderColor = '#d1d5db';
            }
        }

        function toggleAdditionalPassengerByClick(passengerId) {
            const checkbox = document.getElementById(`additional-select-${passengerId}`);
            checkbox.checked = !checkbox.checked;
            toggleAdditionalPassengerSelection(passengerId, checkbox.checked);
        }

        function confirmAdditionalPassengerSelection() {
            if (selectedAdditionalPassengers.length === 0) {
                alert('Por favor selecciona al menos un pasajero');
                return;
            }

            // Agregar los pasajeros seleccionados a la vista actual
            selectedAdditionalPassengers.forEach(passenger => {
                addPassengerToSelection(passenger);
            });

            closeAdditionalPassengerModal();
            document.getElementById('additional-pnr').value = '';
        }

        function closeAdditionalPassengerModal() {
            const modal = document.getElementById('additional-passenger-modal');
            if (modal) {
                modal.remove();
            }
            additionalPassengerCandidates = [];
            selectedAdditionalPassengers = [];
        }

        function saveForm() {
            if (allSelectedPassengers.length === 0) {
                alert('Error: No hay pasajeros seleccionados');
                return;
            }

            // Validar el formulario antes de enviar
            if (!validateForm()) {
                alert('Por favor, completa todos los campos obligatorios correctamente');
                return;
            }

            // Preparar datos de todos los pasajeros seleccionados
            const allPassengers = [];

            allSelectedPassengers.forEach(passenger => {
                // Obtener datos actualizados de los inputs
                const documentInput = document.getElementById(`document-${passenger.id}`);
                const emailInput = document.getElementById(`email-${passenger.id}`);
                const phoneInput = document.getElementById(`phone-${passenger.id}`);
                const ageInput = document.getElementById(`age-${passenger.id}`);

                allPassengers.push({
                    id: passenger.id,
                    document_number: documentInput ? documentInput.value : passenger.document_number,
                    email: emailInput ? emailInput.value : passenger.email,
                    phone: phoneInput ? phoneInput.value : passenger.phone,
                    age: ageInput ? parseInt(ageInput.value) : passenger.age
                });
            });

            // Validar que el primer pasajero tenga datos de contacto válidos
            const firstPassenger = allPassengers[0];
            if (!isValidEmail(firstPassenger.email)) {
                alert('El primer pasajero debe tener un email válido');
                return;
            }
            
            if (!isValidPhone(firstPassenger.phone)) {
                alert('El primer pasajero debe tener un teléfono válido (10-15 dígitos)');
                return;
            }

            const formData = {
                main_passenger_id: allSelectedPassengers[0].id,
                needs_transport: document.getElementById('transport_yes').checked,
                transport_address: document.getElementById('transport_address').value,
                luggage_count: parseInt(document.getElementById('luggage_count').value) || 0,
                needs_accommodation: contingencyType === 'cancelacion' ? document.getElementById('accommodation_yes').checked : false,
                has_medical_condition: document.getElementById('has_medical_condition').checked,
                medical_condition_details: document.getElementById('medical_condition_details').value,
                has_flight_reprogramming: document.getElementById('reprogramming_yes').checked,
                reprogrammed_flight_number: document.getElementById('reprogrammed_flight_number').value,
                reprogrammed_flight_date: document.getElementById('reprogrammed_flight_date').value,
                passengers: allPassengers,
                is_edit: false,
                form_response_id: null
            };

            fetch(`/contingencias/${contingencySlug}/guardar-formulario`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json();
                } else {
                    throw new Error("La respuesta del servidor no es JSON válido");
                }
            })
            .then(data => {
                if (data.success) {
                    // Actualizar mensaje de éxito según cantidad de pasajeros
                    updateSuccessMessage();
                    
                    // Ocultar TODAS las secciones anteriores
                    document.getElementById('pnr-search').classList.add('hidden');
                    document.getElementById('passenger-selection').classList.add('hidden');
                    document.getElementById('all-passengers-data').classList.add('hidden');
                    document.getElementById('form-response').classList.add('hidden');
                    document.getElementById('save-section').classList.add('hidden');
                    
                    // Mostrar SOLO el mensaje de éxito final
                    document.getElementById('success-section').classList.remove('hidden');
                    
                    // Scroll hacia el mensaje de éxito
                    document.getElementById('success-section').scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Error al guardar: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                alert('Error al guardar el formulario. Por favor, inténtalo de nuevo.');
            });
        }

        function handleSubmit() {
            if (validateForm()) {
                showConfirmationModal();
            }
        }

        function showConfirmationModal() {
            // Actualizar datos de pasajeros con información editada en línea
            updatePassengerDataFromInlineEdits();
            
            // Construir resumen de la información
            let summary = `<div class="confirmation-summary">`;
            summary += `<h4>Resumen de Información</h4>`;
            
            // Mostrar todos los pasajeros seleccionados
            summary += `<div class="summary-section">`;
            summary += `<strong>Pasajeros Seleccionados:</strong><br><br>`;
            allSelectedPassengers.forEach(passenger => {
                summary += `<strong>• ${passenger.name} ${passenger.surname}</strong> (${passenger.pnr})<br>`;
                if (passenger.email) summary += `&nbsp;&nbsp;&nbsp;&nbsp;Email: ${passenger.email}<br>`;
                if (passenger.phone) summary += `&nbsp;&nbsp;&nbsp;&nbsp;Teléfono: ${passenger.phone}<br>`;
                summary += `<br>`;
            });
            summary += `</div>`;
            
            // Información de transporte
            const needsTransport = document.getElementById('transport_yes').checked;
            summary += `<div class="summary-section">`;
            summary += `<strong>Transporte:</strong> `;
            if (needsTransport) {
                const luggageCount = document.getElementById('luggage_count').value;
                summary += `Sí requiere (${luggageCount} equipaje(s))`;
            } else {
                summary += `No requiere`;
            }
            summary += `</div>`;
            
            // Información de alojamiento (solo para cancelaciones)
            if (contingencyType === 'cancelacion' && !needsTransport) {
                const needsAccommodation = document.getElementById('accommodation_yes').checked;
                summary += `<div class="summary-section">`;
                summary += `<strong>Alojamiento:</strong> `;
                if (needsAccommodation) {
                    summary += `Sí requiere`;
                } else {
                    summary += `No requiere`;
                }
                summary += `</div>`;
            }
            
            // Información médica
            const hasMedicalCondition = document.getElementById('has_medical_condition').checked;
            summary += `<div class="summary-section">`;
            summary += `<strong>Condición Médica:</strong> `;
            if (hasMedicalCondition) {
                const medicalDetails = document.getElementById('medical_condition_details').value;
                summary += `Sí (${medicalDetails})`;
            } else {
                summary += `No`;
            }
            summary += `</div>`;
            
            // Información de reprogramación de vuelo
            const hasReprogramming = document.getElementById('reprogramming_yes').checked;
            summary += `<div class="summary-section">`;
            summary += `<strong>Reprogramación de Vuelo:</strong> `;
            if (hasReprogramming) {
                const flightNumber = document.getElementById('reprogrammed_flight_number').value;
                const flightDate = document.getElementById('reprogrammed_flight_date').value;
                summary += `Sí (Vuelo: ${flightNumber}, Fecha: ${flightDate})`;
            } else {
                summary += `No`;
            }
            summary += `</div>`;
            
            summary += `</div>`;
            
            // Mostrar modal de confirmación
            document.getElementById('confirmationSummary').innerHTML = summary;
            document.getElementById('confirmationModal').style.display = 'flex';
        }

        function confirmSubmission() {
            // Ocultar modal
            document.getElementById('confirmationModal').style.display = 'none';
            
            // Proceder con el guardado normal
            saveForm();
        }

        function cancelSubmission() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('confirmationModal');
            if (e.target === modal) {
                cancelSubmission();
            }
        });

        // Eventos
        document.getElementById('pnr').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPassenger();
            }
        });

        document.getElementById('additional-pnr').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                addMorePassengers();
            }
        });

        // Event listeners para campos obligatorios
        document.getElementById('transport_yes').addEventListener('change', function(e) {
            transportAnswered = true;
            toggleTransportFields();
        });

        document.getElementById('transport_no').addEventListener('change', function(e) {
            transportAnswered = true;
            toggleTransportFields();
        });

        // Event listeners para alojamiento (solo en cancelaciones)
        if (contingencyType === 'cancelacion') {
            document.getElementById('accommodation_yes').addEventListener('change', function(e) {
                accommodationAnswered = true;
                toggleAccommodationFields();
            });

            document.getElementById('accommodation_no').addEventListener('change', function(e) {
                accommodationAnswered = true;
                toggleAccommodationFields();
            });
        }

        // Event listeners para reprogramación de vuelo
        document.getElementById('reprogramming_yes').addEventListener('change', function(e) {
            toggleReprogrammingFields();
        });

        document.getElementById('reprogramming_no').addEventListener('change', function(e) {
            toggleReprogrammingFields();
        });

        // Event listeners para limpiar errores de validación cuando el usuario escriba
        document.getElementById('luggage_count').addEventListener('input', function(e) {
            if (e.target.value.trim() !== '') {
                hideValidationError('luggage_count_error');
                e.target.classList.remove('required');
            }
        });

        document.getElementById('medical_condition_details').addEventListener('input', function(e) {
            if (e.target.value.trim() !== '') {
                hideValidationError('medical_condition_details_error');
                e.target.classList.remove('required');
            }
        });

        // Event listeners para campos de reprogramación
        document.getElementById('reprogrammed_flight_number').addEventListener('input', function(e) {
            if (e.target.value.trim() !== '') {
                hideValidationError('flight_number_error');
                e.target.classList.remove('required');
            }
        });

        document.getElementById('reprogrammed_flight_date').addEventListener('change', function(e) {
            const selectedDate = e.target.value.trim();
            if (selectedDate !== '') {
                if (validateFlightDate(selectedDate)) {
                    hideValidationError('flight_date_error');
                    e.target.classList.remove('required');
                } else {
                    showValidationError('flight_date_error');
                    e.target.classList.add('required');
                    // Limpiar el valor inválido
                    e.target.value = '';
                }
            }
        });

        // Establecer fecha mínima al cargar la página
        setMinimumFlightDate();
    </script>
</body>
</html>
