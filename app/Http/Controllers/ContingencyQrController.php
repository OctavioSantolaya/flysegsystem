<?php

namespace App\Http\Controllers;

use App\Models\Contingency;
use App\Models\Passenger;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return $this->lookupPassengers($request, $slug, false);
    }

    /**
     * Buscar pasajero adicional en la misma contingencia.
     */
    public function searchAdditionalPassenger(Request $request, $slug)
    {
        return $this->lookupPassengers($request, $slug, true);
    }

    private function lookupPassengers(Request $request, string $slug, bool $additional)
    {
        $input = $request->validate([
            'pnr' => 'required|string|max:20',
            'last_name' => 'required|string|max:50',
        ]);
        $contingency = Contingency::where('contingency_id', $slug)->first();
        if (!$contingency) {
            return response()->json(['error' => 'Contingencia no encontrada'], 404);
        }

        $sessionKey = 'contingency_verified_passengers';
        $verified = $request->session()->get($sessionKey, []);
        $currentIds = ($verified['contingency'] ?? null) === $contingency->id &&
            ($verified['expires_at'] ?? 0) > time() ? $verified['ids'] : [];

        if ($additional && !$currentIds) {
            return response()->json(['error' => 'Volvé a buscar al pasajero principal.'], 403);
        }

        $passengers = Passenger::where('contingency_id', $contingency->id)
            ->whereRaw('LOWER(TRIM(pnr)) = LOWER(?)', [trim($input['pnr'])])
            ->whereRaw('LOWER(TRIM(surname)) = LOWER(?)', [trim($input['last_name'])])
            ->whereNull('form_response_id')
            ->get(['id', 'name', 'surname', 'pnr', 'age']);
        if ($passengers->isEmpty()) {
            return response()->json(['error' => 'No se encontraron pasajeros disponibles con ese PNR y apellido.'], 404);
        }

        $request->session()->put($sessionKey, [
            'contingency' => $contingency->id,
            'expires_at' => time() + 3600,
            'ids' => array_values(array_unique(array_merge($additional ? $currentIds : [], $passengers->modelKeys()))),
        ]);

        return response()->json([
            'passengers' => $passengers,
            ($additional ? 'isMultiple' : 'isSharedPnr') => $passengers->count() > 1,
        ], 200, ['Cache-Control' => 'no-store']);
    }

    /**
     * Guardar el formulario completo
     */
    public function saveForm(Request $request, $slug)
    {
        $data = $request->validate([
            'main_passenger_id' => 'required|integer',
            'needs_transport' => 'boolean',
            'transport_address' => 'nullable|required_if:needs_transport,true|string|max:255',
            'luggage_count' => 'integer|min:0',
            'needs_accommodation' => 'boolean',
            'has_medical_condition' => 'boolean',
            'medical_condition_details' => 'nullable|string',
            'has_flight_reprogramming' => 'boolean',
            'reprogrammed_flight_number' => 'nullable|required_if:has_flight_reprogramming,true|string|max:20',
            'reprogrammed_flight_date' => 'nullable|required_if:has_flight_reprogramming,true|date',
            'passengers' => 'required|array|min:1|max:50',
            'passengers.*.id' => 'required|integer|distinct',
            'passengers.*.document_number' => 'nullable|string|max:20',
            'passengers.*.email' => 'nullable|email|max:255',
            'passengers.*.phone' => 'nullable|string|max:20',
            'passengers.*.age' => 'required|integer|min:0|max:120'
        ]);

        $contingency = Contingency::where('contingency_id', $slug)->first();
        if (!$contingency) {
            return response()->json(['error' => 'Contingencia no encontrada'], 404);
        }

        $sessionKey = 'contingency_verified_passengers';
        $verified = $request->session()->get($sessionKey, []);
        $passengerIds = array_map('intval', array_column($data['passengers'], 'id'));
        if (($verified['contingency'] ?? null) !== $contingency->id ||
            ($verified['expires_at'] ?? 0) <= time() ||
            array_diff($passengerIds, $verified['ids'] ?? []) ||
            $passengerIds[0] !== (int) $data['main_passenger_id']) {
            return response()->json(['error' => 'Volvé a verificar el PNR y apellido de los pasajeros.'], 403);
        }

        $firstPassenger = reset($data['passengers']);
        if (!$this->isValidEmail($firstPassenger['email'] ?? null)) {
            return response()->json(['error' => 'El primer pasajero debe tener un email válido'], 422);
        }
        if (!$this->isValidPhone($firstPassenger['phone'] ?? null)) {
            return response()->json(['error' => 'El primer pasajero debe tener un teléfono válido (10-15 dígitos)'], 422);
        }

        $formResponse = DB::transaction(function () use ($contingency, $data, $passengerIds) {
            $passengers = Passenger::where('contingency_id', $contingency->id)
                ->whereNull('form_response_id')
                ->whereIn('id', $passengerIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            if ($passengers->count() !== count($passengerIds)) {
                return null;
            }

            $response = FormResponse::create([
                'contingency_id' => $contingency->id,
                'needs_transport' => $data['needs_transport'] ?? false,
                'transport_address' => $data['transport_address'] ?? null,
                'luggage_count' => $data['luggage_count'] ?? 1,
                'needs_accommodation' => $data['needs_accommodation'] ?? false,
                'has_medical_condition' => $data['has_medical_condition'] ?? false,
                'medical_condition_details' => $data['medical_condition_details'] ?? null,
                'has_flight_reprogramming' => $data['has_flight_reprogramming'] ?? false,
                'reprogrammed_flight_number' => $data['reprogrammed_flight_number'] ?? null,
                'reprogrammed_flight_date' => $data['reprogrammed_flight_date'] ?? null,
            ]);

            foreach ($data['passengers'] as $passenger) {
                $passengers[$passenger['id']]->update([
                    'document_number' => $passenger['document_number'] ?? null,
                    'email' => $passenger['email'] ?? null,
                    'phone' => $passenger['phone'] ?? null,
                    'age' => $passenger['age'],
                    'form_response_id' => $response->id,
                ]);
            }

            return $response;
        });
        if (!$formResponse) {
            return response()->json(['error' => 'Alguno de los pasajeros ya no está disponible. Volvé a buscar.'], 409);
        }

        $request->session()->forget($sessionKey);
        return response()->json([
            'success' => true,
            'message' => 'Formulario guardado exitosamente',
            'formResponseId' => $formResponse->id,
            'passengerIds' => $passengerIds,
        ]);
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
