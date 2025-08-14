<x-filament-widgets::widget>
    <x-filament::section>
        @if($response)
            {{-- Toda la información en grid de columnas --}}
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Fila 1 --}}
                    <div>
                        <label class="block text-sm font-medium text-white">ID de Contingencia</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->contingency->contingency_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Número de Vuelo</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->contingency->flight_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Base</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->contingency->base->name }}</p>
                    </div>

                    {{-- Fila 2 --}}
                    <div>
                        <label class="block text-sm font-medium text-white">Aerolínea</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->contingency->airline->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Fecha de Creación</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Última Modificación</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $response->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    {{-- Fila 3 --}}
                    <div>
                        <label class="block text-sm font-medium text-white">Necesita Transporte</label>
                        <div class="mt-1 flex items-center">
                            @if($response->needs_transport)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-green-600 dark:text-green-400">Sí</span>
                            @else
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-red-600 dark:text-red-400">No</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Dirección para Transporte</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $response->needs_transport ? ($response->transport_address ?: 'No especificada') : '—' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Cantidad de Equipaje</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($response->needs_transport)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $response->luggage_count }} piezas
                                </span>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    {{-- Fila 4 --}}
                    <div>
                        <label class="block text-sm font-medium text-white">Necesita Alojamiento</label>
                        <div class="mt-1 flex items-center">
                            @if($response->needs_accommodation)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-green-600 dark:text-green-400">Sí</span>
                            @else
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-red-600 dark:text-red-400">No</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Cantidad de Niños</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($response->needs_accommodation)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ $response->children_count }} niños
                                </span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white">Tiene Condición Médica</label>
                        <div class="mt-1 flex items-center">
                            @if($response->has_medical_condition)
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-red-600 dark:text-red-400">Sí</span>
                            @else
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-green-600 dark:text-green-400">No</span>
                            @endif
                        </div>
                    </div>

                    {{-- Fila 5 - Detalles de condición médica si existe --}}
                    @if($response->has_medical_condition && $response->medical_condition_details)
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-white">Detalles de Condición Médica</label>
                            <div class="mt-1 bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800">
                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $response->medical_condition_details }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Asignaciones Actuales --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Asignaciones Actuales
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Asignación de Transporte</label>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded border">
                                @if($response->assigned_transport_info)
                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $response->assigned_transport_info }}</p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No asignada</p>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Asignación de Alojamiento</label>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded border">
                                @if($response->assigned_accommodation_info)
                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $response->assigned_accommodation_info }}</p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No asignada</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay respuesta seleccionada</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona una respuesta de formulario para ver los detalles.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
