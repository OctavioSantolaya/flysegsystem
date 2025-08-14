<?php

namespace App\Filament\Resources\ContingencyResource\Pages;

use App\Exports\ContingencyExport;
use App\Filament\Resources\ContingencyResource;
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
                    ->url(fn() => route('contingency.qr', ['slug' => $this->record->slug]))
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
                    ->slideOver()
                    ->color('success')
                    ->form([
                        FileUpload::make('archivo_pasajeros')
                            ->label('Archivo de Pasajeros')
                            ->required()
                            ->acceptedFileTypes([
                                // Excel formats
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                                'application/vnd.ms-excel', // .xls
                                'application/vnd.ms-excel.sheet.macroEnabled.12', // .xlsm
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.template', // .xltx
                                'application/vnd.ms-excel.template.macroEnabled.12', // .xltm
                                'application/vnd.ms-excel.sheet.binary.macroEnabled.12', // .xlsb

                                // CSV and TSV
                                'text/csv',
                                'application/csv',
                                'text/tab-separated-values',
                                'text/tsv',

                                // OpenDocument
                                'application/vnd.oasis.opendocument.spreadsheet', // .ods
                                'application/vnd.oasis.opendocument.spreadsheet-template', // .ots

                                // Other formats
                                'application/vnd.ms-excel.sheet.4', // .slk
                                'text/xml',
                                'application/xml',
                                'application/x-gnumeric', // .gnumeric
                                'text/html',
                                'application/html',

                                // Plain text fallbacks
                                'text/plain',
                                'application/octet-stream'
                            ])
                            ->maxSize(51200) // 50MB
                            ->helperText('Formatos aceptados: .xlsx, .xls, .xlsm, .csv, .tsv, .ods, .slk, .xml, .gnumeric, .html (máximo 50MB)')
                            ->uploadingMessage('Subiendo archivo...')
                            ->storeFiles(false) // No guardar el archivo en storage
                            ->disk('local'),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('mostrar_ayuda')
                                ->label('📋 Ver formato de archivo y ayuda')
                                ->color('info')
                                ->size('sm')
                                ->outlined()
                                ->slideOver()
                                ->modalHeading('Formato de archivo para importación')
                                ->modalContent(function () {
                                    return new \Illuminate\Support\HtmlString("
                                    <div class=\"space-y-4\">
                                        <div class=\"bg-gray-50 rounded-lg p-4 border-2 border-dashed border-gray-300\">
                                            <p class=\"text-sm font-medium text-gray-800 mb-3\">📥 Descargar archivos de ejemplo</p>
                                            <div class=\"flex flex-wrap gap-2\">
                                                <a href=\"" . route('download.template', ['type' => 'csv']) . "\" 
                                                   class=\"inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500\">
                                                    📄 Ejemplo CSV
                                                </a>
                                                <a href=\"" . route('download.template', ['type' => 'tsv']) . "\" 
                                                   class=\"inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500\">
                                                    📄 Ejemplo TSV
                                                </a>
                                                <a href=\"" . route('download.template', ['type' => 'xml']) . "\" 
                                                   class=\"inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500\">
                                                    📄 Ejemplo XML
                                                </a>
                                                <a href=\"" . route('download.template', ['type' => 'html']) . "\" 
                                                   class=\"inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500\">
                                                    📄 Ejemplo HTML
                                                </a>
                                            </div>
                                            <p class=\"text-xs text-gray-600 mt-2\">💡 Descarga estos archivos para ver el formato correcto y usar como plantilla</p>
                                            <div class=\"mt-2 p-2 bg-blue-50 rounded text-xs text-blue-700\">
                                                <strong>Nota:</strong> Los archivos XML y HTML usan formatos compatibles con Excel. Para otros formatos (CSV, TSV) recomendamos usarlos preferentemente.
                                            </div>
                                        </div>
                                        
                                        <div class=\"bg-blue-50 rounded-lg p-4\">
                                            <p class=\"text-sm font-medium text-blue-800 mb-3\">📂 Formatos de archivo soportados</p>
                                            <div class=\"grid grid-cols-2 gap-3 text-sm text-blue-700\">
                                                <div>
                                                    <p class=\"font-semibold\">Microsoft Excel:</p>
                                                    <p class=\"text-xs\">• XLSX, XLS, XLSM, XLTX, XLTM</p>
                                                </div>
                                                <div>
                                                    <p class=\"font-semibold\">Texto separado:</p>
                                                    <p class=\"text-xs\">• CSV, TSV</p>
                                                </div>
                                                <div>
                                                    <p class=\"font-semibold\">OpenDocument:</p>
                                                    <p class=\"text-xs\">• ODS</p>
                                                </div>
                                                <div>
                                                    <p class=\"font-semibold\">Otros:</p>
                                                    <p class=\"text-xs\">• SLK, XML, GNUMERIC, HTML</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class=\"bg-blue-50 rounded-lg p-4\">
                                            <p class=\"text-sm font-medium text-blue-800 mb-3\">🔍 Detección automática de columnas</p>
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
                                        
                                        <div class=\"bg-amber-50 rounded-lg p-4\">
                                            <p class=\"text-sm font-medium text-amber-800 mb-2\">📝 Detección automática de mayúsculas/minúsculas</p>
                                            <p class=\"text-sm text-amber-700\">El sistema reconoce las columnas independientemente de si están escritas en MAYÚSCULAS, minúsculas o MixTo.</p>
                                            <p class=\"text-xs text-amber-600 mt-1\">Ejemplo: 'FIRST_NAME', 'first_name', 'First Name' funcionan igual.</p>
                                        </div>
                                        
                                        <div class=\"bg-green-50 rounded-lg p-4\">
                                            <p class=\"text-sm font-medium text-green-800 mb-2\">✅ Campos obligatorios</p>
                                            <p class=\"text-sm text-green-700\">Solo son obligatorios: <strong>Nombre, Apellido y PNR</strong></p>
                                            <p class=\"text-xs text-green-600 mt-1\">Los demás campos son opcionales y se completarán automáticamente si están disponibles en el archivo.</p>
                                        </div>

                                        <div class=\"bg-yellow-50 border p-4 rounded-lg\">
                                            <p class=\"text-sm font-medium text-yellow-800 mb-2\">💡 Consejos</p>
                                            <ul class=\"text-xs text-yellow-700 space-y-1\">
                                                <li>• <strong>Recomendado:</strong> Use CSV, TSV o XLSX para mejor compatibilidad</li>
                                                <li>• Compatible con archivos de Excel, LibreOffice, Google Sheets y más</li>
                                                <li>• No importa el orden de las columnas</li>
                                                <li>• La primera fila debe contener los nombres de las columnas</li>
                                                <li>• Se omitirán automáticamente las filas con datos incompletos</li>
                                                <li>• Tamaño máximo: 50MB</li>
                                                <li>• Para XML/HTML: descargue los ejemplos para ver el formato exacto</li>
                                            </ul>
                                        </div>
                                    </div>
                                ");
                                })
                                ->modalButton('Entendido')
                                ->modalWidth('lg'),
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

                            // Verificar que el archivo existe y es legible
                            if (!file_exists($filePath) || !is_readable($filePath)) {
                                throw new \Exception('El archivo no pudo ser leído. Verifique que el formato sea compatible.');
                            }

                            Excel::import($import, $filePath);

                            // Obtener estadísticas de la importación
                            $importedCount = $import->getImportedCount();
                            $skippedCount = $import->getSkippedCount();
                            $failures = $import->getFailures();

                            // Mostrar notificación de éxito
                            $message = "Se importaron {$importedCount} pasajeros exitosamente.";
                            if ($skippedCount > 0) {
                                $message .= " ⚠️ {$skippedCount} filas fueron omitidas por errores o datos incompletos.";
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
                        } catch (\Maatwebsite\Excel\Exceptions\NoTypeDetectedException $e) {
                            Notification::make()
                                ->title('❌ Formato de archivo no soportado')
                                ->body('No se pudo detectar el tipo de archivo. Asegúrese de usar uno de los formatos soportados: XLSX, XLS, CSV, TSV, ODS, SLK, XML, GNUMERIC, HTML.')
                                ->danger()
                                ->send();
                        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                            $errorMessage = 'Error al leer el archivo: ';
                            
                            // Mensajes específicos según el tipo de error
                            if (strpos($e->getMessage(), 'XML') !== false) {
                                $errorMessage .= 'El archivo XML no tiene el formato correcto. Use el archivo de ejemplo XML como referencia.';
                            } elseif (strpos($e->getMessage(), 'HTML') !== false) {
                                $errorMessage .= 'El archivo HTML debe contener una tabla válida con encabezados.';
                            } else {
                                $errorMessage .= 'Formato no válido. Verifique que el archivo se pueda abrir en Excel o LibreOffice.';
                            }
                            
                            Notification::make()
                                ->title('❌ Error al leer el archivo')
                                ->body($errorMessage)
                                ->danger()
                                ->send();
                        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                            Notification::make()
                                ->title('❌ Error al procesar el archivo')
                                ->body('El archivo está corrupto o tiene un formato inválido. Intente con un formato más simple como CSV o descargue uno de los archivos de ejemplo.')
                                ->danger()
                                ->send();
                        } catch (\Exception $e) {
                            $errorMessage = $e->getMessage();

                            // Personalizar mensajes para errores comunes
                            if (strpos($errorMessage, 'encoding') !== false) {
                                $errorMessage = 'Error de codificación de caracteres. Guarde el archivo con codificación UTF-8.';
                            } elseif (strpos($errorMessage, 'permission') !== false) {
                                $errorMessage = 'No se tienen permisos para leer el archivo.';
                            } elseif (strpos($errorMessage, 'memory') !== false) {
                                $errorMessage = 'El archivo es demasiado grande. Intente con un archivo más pequeño o divídalo en partes.';
                            }

                            Notification::make()
                                ->title('❌ Error en la importación')
                                ->body('Ocurrió un error: ' . $errorMessage)
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
