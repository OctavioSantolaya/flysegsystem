<?php

namespace App\Filament\Operator\Resources\ContingencyResource\Pages;

use App\Filament\Operator\Resources\ContingencyResource;
use App\Imports\PassengersImport;
use App\Models\Contingency;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class ImportPassengers extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ContingencyResource::class;

    protected static string $view = 'filament.resources.contingency-resource.pages.import-passengers';

    protected static ?string $title = 'Importar Pasajeros';

    protected static ?string $navigationLabel = 'Importar Pasajeros';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    public ?array $data = [];
    public ?Contingency $contingency = null;

    public function mount(): void
    {
        // Obtener la contingencia desde la URL
        $contingencyId = request()->route('contingency');
        $this->contingency = Contingency::findOrFail($contingencyId);
        
        // Pre-llenar el formulario con la contingencia seleccionada
        $this->form->fill([
            'contingency_id' => $this->contingency->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Importar Pasajeros desde Excel')
                    ->description("Sube un archivo Excel con los pasajeros para la contingencia: {$this->contingency?->name}")
                    ->schema([
                        FileUpload::make('file')
                            ->label('Archivo Excel')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                            ])
                            ->required()
                            ->helperText('Formatos aceptados: .xlsx, .xls, .csv. Descarga el archivo de ejemplo para ver el formato esperado.'),

                        Select::make('contingency_id')
                            ->label('Contingencia')
                            ->options([$this->contingency->id => $this->contingency->name])
                            ->default($this->contingency?->id)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Contingencia seleccionada automáticamente'),
                    ])
                    ->columnSpan(2)
                    ->headerActions([
                        Action::make('download_template')
                            ->label('Descargar Ejemplo')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('gray')
                            ->action(function () {
                                return response()->download(
                                    storage_path('app/templates/ejemplo_pasajeros.csv'),
                                    'ejemplo_pasajeros.csv'
                                );
                            }),
                        Action::make('configure_mapping')
                            ->label('Configurar Mapeo')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->color('gray')
                            ->url(route('filament.admin.resources.import-settings.index'))
                            ->openUrlInNewTab(),
                    ]),
            ])
            ->statePath('data');
    }

    public function import()
    {
        $data = $this->form->getState();
        
        if (!isset($data['file']) || !isset($data['contingency_id'])) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar un archivo.')
                ->danger()
                ->send();
            return;
        }

        try {
            $contingency = Contingency::findOrFail($data['contingency_id']);
            
            $import = new PassengersImport($contingency->id);
            
            Excel::import($import, $data['file']);

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $failures = $import->getFailures();

            // Preparar mensaje de resultado
            $message = "Se importaron {$importedCount} pasajeros correctamente.";
            if ($skippedCount > 0) {
                $message .= " Se omitieron {$skippedCount} registros (duplicados o datos incompletos).";
            }
            if (count($failures) > 0) {
                $message .= " Se encontraron " . count($failures) . " errores de validación.";
            }

            Notification::make()
                ->title('Importación completada')
                ->body($message)
                ->success()
                ->send();

            // Limpiar el formulario
            $this->form->fill(['contingency_id' => $this->contingency->id]);

            // Redirigir a la vista de la contingencia
            $this->redirect(static::getResource()::getUrl('view', ['record' => $this->contingency]));

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en la importación')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('import')
                ->label('Importar Pasajeros')
                ->action('import')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
            Action::make('cancel')
                ->label('Cancelar')
                ->color('gray')
                ->url(static::getResource()::getUrl('view', ['record' => $this->contingency]))
                ->icon('heroicon-o-x-mark'),
        ];
    }
}
