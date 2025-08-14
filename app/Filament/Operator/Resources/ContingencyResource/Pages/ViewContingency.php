<?php

namespace App\Filament\Operator\Resources\ContingencyResource\Pages;

use App\Exports\ContingencyExport;
use App\Filament\Operator\Resources\ContingencyResource;
use App\Imports\PassengersImport;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Maatwebsite\Excel\Facades\Excel;

class ViewContingency extends ViewRecord
{
    protected static string $resource = ContingencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
              
            Actions\Action::make('qr')
                ->label('Ver QR')
                ->icon('heroicon-o-qr-code')
                ->color('gray')
                ->tooltip('Ver código QR')
                ->url(fn () => route('contingency.qr', ['slug' => $this->record->slug]))
                ->openUrlInNewTab(),
            Actions\Action::make('exportar')
                ->label('Exportar Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->tooltip('Exportar datos completos de la contingencia')
                ->action(function () {
                    try {
                        $filename = 'contingencia-' . $this->record->contingency_id . '-' . now()->format('Y-m-d-H-i') . '.xlsx';
                        return Excel::download(new ContingencyExport($this->record), $filename);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al exportar')
                            ->body('No se pudo generar el archivo de exportación: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Exportar Contingencia')
                ->modalDescription('Se generará un archivo Excel con todos los datos de la contingencia, incluyendo información de pasajeros y sus respuestas de formulario.')
                ->modalSubmitActionLabel('Exportar Excel'),
            Actions\Action::make('importarPasajeros')
                ->label('Importar Pasajeros')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('archivo_pasajeros')
                        ->label('Archivo de Pasajeros')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                        ->maxSize(51200) // 50MB
                        ->helperText('Formatos aceptados: .xlsx, .xls, .csv (máximo 50MB)')
                        ->uploadingMessage('Subiendo archivo...')
                        ->storeFiles(false) // No guardar el archivo en storage
                        ->disk('local'),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('mostrar_ayuda')
                            ->label('📋 Ver formato de archivo y ayuda')
                            ->color('info')
                            ->size('sm')
                            ->outlined()
                            ->modalHeading('Formato de archivo para importación')
                            ->modalContent(function () {
                                return new \Illuminate\Support\HtmlString("
                                    <div class=\"space-y-4\">
                                        <div class=\"bg-blue-50 rounded-lg\">
                                            <p class=\"text-sm font-medium text-blue-800 mb-3\">Detección automática de columnas</p>
                                            <p class=\"text-sm text-blue-700 mb-3\">El sistema detectará automáticamente las columnas del archivo usando nombres predefinidos.</p>
                                            
                                            <div class=\"mt-3 p-4 rounded-xl border border-blue-200\">
                                                <div class=\"space-y-2\">
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">Nombre:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">nombre, name, first_name, firstname, first name, primer_nombre, nombres</div></div>
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">Apellido:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">apellido, surname, last_name, lastname, last name, apellidos</div></div>
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">PNR:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">pnr, localizador, booking, reservation, codigo</div></div>
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">Email:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">email, correo, mail, e-mail, correo_electronico</div></div>
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">Teléfono:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">telefono, phone, celular, movil, tel, numero</div></div>
                                                    <div class=\"mt-1\"><div class=\"font-semibold text-blue-800\">Documento:</div><div class=\"text-blue-600 text-sm mt-1 mb-1\">documento, document, document_number, dni, cedula, pasaporte, id, foid</div></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class=\"bg-amber-50 rounded-lg\">
                                            <p class=\"text-sm font-medium text-amber-800 mb-2\">📝 Detección automática de mayúsculas/minúsculas</p>
                                            <p class=\"text-sm text-amber-700\">El sistema reconoce las columnas independientemente de si están escritas en MAYÚSCULAS, minúsculas o MixTo.</p>
                                            <p class=\"text-xs text-amber-600 mt-1\">Ejemplo: 'FIRST_NAME', 'first_name', 'First Name' funcionan igual.</p>
                                        </div>
                                        
                                        <div class=\"bg-green-50 rounded-lg\">
                                            <p class=\"text-sm font-medium text-green-800 mb-2\">Campos obligatorios</p>
                                            <p class=\"text-sm text-green-700\">Solo son obligatorios: <strong>Nombre, Apellido y PNR</strong></p>
                                            <p class=\"text-xs text-green-600 mt-1\">Los demás campos son opcionales y se completarán automáticamente si están disponibles en el archivo.</p>
                                        </div>

                                        <div class=\"bg-yellow-50 border p-4 rounded-lg\">
                                            <p class=\"text-sm font-medium text-yellow-800 mb-2\">💡 Consejos</p>
                                            <ul class=\"text-xs text-yellow-700 space-y-1\">
                                                <li>• El archivo puede estar en cualquier idioma configurado</li>
                                                <li>• No importa el orden de las columnas</li>
                                                <li>• La primera fila debe contener los nombres de las columnas</li>
                                                <li>• Se omitirán automáticamente las filas con datos incompletos</li>
                                            </ul>
                                        </div>
                                    </div>
                                ");
                            })
                            ->modalButton('Entendido')
                            ->modalWidth('lg'),
                        Forms\Components\Actions\Action::make('download_template')
                            ->label('📥 Descargar Ejemplo')
                            ->color('gray')
                            ->size('sm')
                            ->action(function () {
                                return response()->download(
                                    storage_path('app/templates/ejemplo_pasajeros.csv'),
                                    'ejemplo_pasajeros.csv'
                                );
                            }),
                    ])
                ])
                ->action(function (array $data): void {
                    try {
                        $uploadedFile = $data['archivo_pasajeros'];

                        if (!$uploadedFile) {
                            Notification::make()
                                ->title('Error')
                                ->body('No se pudo procesar el archivo.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Procesar el archivo directamente desde el archivo temporal
                        $import = new PassengersImport($this->record->id);

                        // Si es un UploadedFile, usar getRealPath(), si es string usar directamente
                        if (is_object($uploadedFile) && method_exists($uploadedFile, 'getRealPath')) {
                            $filePath = $uploadedFile->getRealPath();
                        } else {
                            $filePath = $uploadedFile;
                        }

                        Excel::import($import, $filePath);

                        // No es necesario eliminar el archivo ya que no se guarda en storage

                        // Obtener estadísticas de la importación
                        $importedCount = $import->getImportedCount();
                        $skippedCount = $import->getSkippedCount();
                        $failures = $import->getFailures();

                        // Mostrar notificación de éxito
                        $message = "Se importaron {$importedCount} pasajeros exitosamente.";
                        if ($skippedCount > 0) {
                            $message .= " {$skippedCount} filas fueron omitidas por errores.";
                        }

                        Notification::make()
                            ->title('Importación Completada')
                            ->body($message)
                            ->success()
                            ->send();

                        // Si hay errores, mostrar detalles
                        if (!empty($failures)) {
                            $errorMessages = [];
                            foreach ($failures as $failure) {
                                $errorMessages[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
                            }

                            Notification::make()
                                ->title('⚠️ Errores en la importación')
                                ->body('Algunas filas no pudieron ser importadas: ' . implode(' | ', array_slice($errorMessages, 0, 3)))
                                ->warning()
                                ->send();
                        }

                        // Refrescar para mostrar los nuevos pasajeros
                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('❌ Error en la importación')
                            ->body('Ocurrió un error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('📊 Importar Pasajeros')
                ->modalSubheading("Sube un archivo Excel o CSV con los datos de los pasajeros para la contingencia: {$this->record->name}")
                ->modalButton('Importar Ahora')
                ->modalWidth('lg'),  
            ]),

            Actions\EditAction::make(),
        ];
    }
}
