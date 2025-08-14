<?php

namespace App\Http\Controllers;

use App\Models\Contingency;
use App\Models\Passenger;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContingencyQrController extends Controller
{
    /**
     * Mostrar la página QR para una contingencia específica
     */
    public function showQr($slug)
    {
        // Buscar por contingency_id ya que el slug es el ID visible
        $contingency = Contingency::where('contingency_id', $slug)->first();
        
        if (!$contingency) {
            abort(404, 'Contingencia no encontrada');
        }
        
        // URL de destino del QR
        $qrTargetUrl = url("/contingencias/{$slug}/formulario");
        
        return view('contingency.qr', compact('contingency', 'qrTargetUrl'));
    }
    
    /**
     * Mostrar la página de formulario para una contingencia específica
     */
    public function showForm($slug)
    {
        // Buscar por contingency_id ya que el slug es el ID visible
        $contingency = Contingency::where('contingency_id', $slug)->first();
        
        if (!$contingency) {
            abort(404, 'Contingencia no encontrada');
        }
        
        return view('contingency.form', compact('contingency'));
    }

    /**
     * Actualizar búsqueda de pasajero para filtrar solo los que NO tienen FormResponse
     */
    public function searchPassenger(Request $request, $slug)
    {
        Log::info('SearchPassenger called', [
            'slug' => $slug,
            'request_data' => $request->all(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);
        
        $request->validate([
            'pnr' => 'required|string|max:20',
            'last_name' => 'required|string|max:50'
        ]);

        $contingency = Contingency::where('contingency_id', $slug)->first();
        if (!$contingency) {
            Log::error('Contingency not found', ['slug' => $slug]);
            return response()->json(['error' => 'Contingencia no encontrada'], 404);
        }

        // Buscar pasajeros por PNR y apellido en esta contingencia que no tengan FormResponse
        $passengers = Passenger::where('pnr', $request->pnr)
                              ->where('contingency_id', $contingency->id)
                              ->where('surname', 'LIKE', '%' . $request->last_name . '%')
                              ->whereNull('form_response_id')
                              ->get();

        if ($passengers->isEmpty()) {
            return response()->json(['error' => 'No se encontraron pasajeros con el PNR y apellido proporcionados, o todos ya tienen un formulario asociado'], 404);
        }

        return response()->json([
            'passengers' => $passengers,
            'isSharedPnr' => $passengers->count() > 1
        ]);
    }

    /**
     * Buscar pasajero adicional por PNR - Con debugging mejorado
     */
    public function searchAdditionalPassenger(Request $request, $slug)
    {
        try {
            // Log de entrada para debugging
            Log::info('searchAdditionalPassenger called', [
                'slug' => $slug,
                'pnr' => $request->get('pnr'),
                'last_name' => $request->get('last_name')
            ]);

            $request->validate([
                'pnr' => 'required|string|max:20',
                'last_name' => 'required|string|max:50'
            ]);

            $contingency = Contingency::where('contingency_id', $slug)->first();
            if (!$contingency) {
                Log::warning('Contingency not found', ['slug' => $slug]);
                return response()->json(['error' => 'Contingencia no encontrada'], 404);
            }

            Log::info('Contingency found', ['contingency_id' => $contingency->id]);

            // Buscar todos los pasajeros por PNR y apellido en esta contingencia
            $passengers = Passenger::where('pnr', $request->pnr)
                                  ->where('contingency_id', $contingency->id)
                                  ->where('surname', 'LIKE', '%' . $request->last_name . '%')
                                  ->whereNull('form_response_id')
                                  ->get();

            Log::info('Passengers query result', [
                'count' => $passengers->count(),
                'passengers' => $passengers->toArray()
            ]);

            if ($passengers->isEmpty()) {
                return response()->json(['error' => 'No se encontraron pasajeros con el PNR y apellido proporcionados, o todos ya tienen un formulario asociado'], 404);
            }

            $response = [
                'passengers' => $passengers,
                'isMultiple' => $passengers->count() > 1
            ];

            Log::info('Returning successful response', $response);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in searchAdditionalPassenger', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'slug' => $slug,
                'pnr' => $request->get('pnr'),
                'last_name' => $request->get('last_name')
            ]);

            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Guardar el formulario completo
     */
    public function saveForm(Request $request, $slug)
    {
        $request->validate([
            'main_passenger_id' => 'required|exists:passengers,id',
            'needs_transport' => 'boolean',
            'transport_address' => 'nullable|required_if:needs_transport,true|string|max:255',
            'luggage_count' => 'integer|min:0',
            'needs_accommodation' => 'boolean',
            'has_medical_condition' => 'boolean',
            'medical_condition_details' => 'nullable|string',
            'has_flight_reprogramming' => 'boolean',
            'reprogrammed_flight_number' => 'nullable|required_if:has_flight_reprogramming,true|string|max:20',
            'reprogrammed_flight_date' => 'nullable|required_if:has_flight_reprogramming,true|date',
            'passengers' => 'array|min:1',
            'passengers.*.id' => 'required|exists:passengers,id',
            'passengers.*.document_number' => 'nullable|string|max:20',
            'passengers.*.email' => 'nullable|email|max:255',
            'passengers.*.phone' => 'nullable|string|max:20',
            'passengers.*.age' => 'required|integer|min:0|max:120'
        ]);

        try {
            DB::beginTransaction();

            $contingency = Contingency::where('contingency_id', $slug)->first();
            
            if (!$contingency) {
                return response()->json(['error' => 'Contingencia no encontrada'], 404);
            }

            // Validar que el primer pasajero tenga email y teléfono válidos
            $firstPassenger = $request->passengers[0] ?? null;
            if (!$firstPassenger) {
                return response()->json(['error' => 'Debe seleccionar al menos un pasajero'], 422);
            }

            if (!$this->isValidEmail($firstPassenger['email'])) {
                return response()->json(['error' => 'El primer pasajero debe tener un email válido'], 422);
            }

            if (!$this->isValidPhone($firstPassenger['phone'])) {
                return response()->json(['error' => 'El primer pasajero debe tener un teléfono válido (10-15 dígitos)'], 422);
            }
            
            // Crear nuevo FormResponse
            $formResponse = FormResponse::create([
                'contingency_id' => $contingency->id,
                'needs_transport' => $request->needs_transport ?? false,
                'transport_address' => $request->transport_address,
                'luggage_count' => $request->luggage_count ?? 1,
                'needs_accommodation' => $request->needs_accommodation ?? false,
                'has_medical_condition' => $request->has_medical_condition ?? false,
                'medical_condition_details' => $request->medical_condition_details,
                'has_flight_reprogramming' => $request->has_flight_reprogramming ?? false,
                'reprogrammed_flight_number' => $request->reprogrammed_flight_number,
                'reprogrammed_flight_date' => $request->reprogrammed_flight_date,
            ]);

            // Actualizar datos de pasajeros y asociar al FormResponse
            $passengerIds = [];
            foreach ($request->passengers as $passengerData) {
                $passenger = Passenger::findOrFail($passengerData['id']);
                
                // Actualizar datos del pasajero
                $passenger->update([
                    'document_number' => $passengerData['document_number'],
                    'email' => $passengerData['email'],
                    'phone' => $passengerData['phone'],
                    'age' => $passengerData['age'],
                    'form_response_id' => $formResponse->id
                ]);
                
                $passengerIds[] = $passenger->id;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Formulario guardado exitosamente',
                'formResponseId' => $formResponse->id,
                'passengerIds' => $passengerIds
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving form: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al guardar el formulario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar formato de email
     */
    private function isValidEmail($email)
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validar formato de teléfono
     */
    private function isValidPhone($phone)
    {
        if (empty($phone)) {
            return false;
        }
        
        // Remover espacios y caracteres especiales
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Validar que tenga al menos 10 dígitos y máximo 15
        return strlen($cleanPhone) >= 10 && 
               strlen($cleanPhone) <= 15 &&
               preg_match('/^[\+]?[0-9]{10,15}$/', $cleanPhone);
    }
}
