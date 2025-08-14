<?php

namespace App\Exports;

use App\Models\Contingency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContingencyExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    protected $contingency;

    public function __construct(Contingency $contingency)
    {
        $this->contingency = $contingency;
    }

    /**
     * Helper para formatear fechas de manera segura
     */
    private function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '';
        
        if (is_string($date)) {
            try {
                return \Carbon\Carbon::parse($date)->format($format);
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
            return $date->format($format);
        }
        
        return (string) $date;
    }

    public function collection()
    {
        return $this->contingency->passengers()
            ->with('formResponse')
            ->orderBy('surname')
            ->orderBy('name')
            ->get();
    }

    public function title(): string
    {
        return 'Contingencia ' . $this->contingency->contingency_id;
    }

    public function headings(): array
    {
        return [
            // Información de la Contingencia
            'ID Contingencia',
            'Número de Vuelo',
            'Aerolínea',
            'Tipo de Contingencia',
            'Escala',
            'Fecha',
            
            // Información del Pasajero
            'Nombre Pasajero',
            'Apellido Pasajero',
            'PNR',
            'Edad',
            'Email',
            'Teléfono',
            'Documento',
            
            // Información de la Respuesta
            'Formulario Completado',
            'Necesita Transporte',
            'Dirección de Transporte',
            'Cantidad de Equipaje',
            'Necesita Alojamiento',
            'Cantidad de Menores (Calculado)',
            'Edades de Menores (Calculado)',
            'Tiene Condición Médica',
            'Detalles Condición Médica',
            'Tiene Reprogramación',
            'Vuelo Reprogramado',
            'Fecha Reprogramada',
            'Alojamiento Asignado',
            'Traslado Asignado',
            'Fecha de Respuesta',
        ];
    }

    public function map($passenger): array
    {
        $formResponse = $passenger->formResponse;
        
        return [
            // Información de la Contingencia
            $this->contingency->contingency_id,
            $this->contingency->flight_number,
            $this->contingency->airline->name ?? '',
            $this->contingency->contingency_type,
            $this->contingency->scale,
            $this->formatDate($this->contingency->date),
            
            // Información del Pasajero
            $passenger->name,
            $passenger->surname,
            $passenger->pnr,
            $passenger->age ?? 'N/A',
            $passenger->email ?? '',
            $passenger->phone ?? '',
            $passenger->document_number ?? '',
            
            // Información de la Respuesta
            $formResponse ? 'Sí' : 'No',
            $formResponse ? ($formResponse->needs_transport ? 'Sí' : 'No') : 'N/A',
            $formResponse ? ($formResponse->transport_address ?? '') : 'N/A',
            $formResponse ? $formResponse->luggage_count : 'N/A',
            $formResponse ? ($formResponse->needs_accommodation ? 'Sí' : 'No') : 'N/A',
            $formResponse ? $formResponse->children_count : 'N/A',
            $formResponse && $formResponse->children_ages && count($formResponse->children_ages) > 0 ? implode(', ', $formResponse->children_ages) . ' años' : 'N/A',
            $formResponse ? ($formResponse->has_medical_condition ? 'Sí' : 'No') : 'N/A',
            $formResponse ? ($formResponse->medical_condition_details ?? '') : 'N/A',
            $formResponse ? ($formResponse->has_flight_reprogramming ? 'Sí' : 'No') : 'N/A',
            $formResponse ? ($formResponse->reprogrammed_flight_number ?? '') : 'N/A',
            $formResponse && $formResponse->reprogrammed_flight_date ? $this->formatDate($formResponse->reprogrammed_flight_date) : 'N/A',
            $formResponse ? ($formResponse->assigned_accommodation_info ?? '') : 'N/A',
            $formResponse ? ($formResponse->assigned_transport_info ?? '') : 'N/A',
            $formResponse ? $this->formatDate($formResponse->created_at, 'd/m/Y H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1976D2'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Bordes para todas las celdas con datos
            "A1:U{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
            // Alineación para las columnas de datos
            "A2:U{$lastRow}" => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $worksheet = $sheet->getDelegate();
                $lastRow = $worksheet->getHighestRow();
                
                // Agregar estadísticas al final
                $statsRow = $lastRow + 3;
                $passengers = $this->contingency->passengers()->with('formResponse')->get();
                
                $totalPassengers = $passengers->count();
                $responsesCompleted = $passengers->filter(fn($p) => $p->formResponse !== null)->count();
                $needTransport = $passengers->filter(fn($p) => $p->formResponse && $p->formResponse->needs_transport)->count();
                $needAccommodation = $passengers->filter(fn($p) => $p->formResponse && $p->formResponse->needs_accommodation)->count();
                
                $worksheet->setCellValue('A' . $statsRow, 'RESUMEN ESTADÍSTICO:');
                $worksheet->setCellValue('A' . ($statsRow + 1), 'Total de Pasajeros:');
                $worksheet->setCellValue('B' . ($statsRow + 1), $totalPassengers);
                $worksheet->setCellValue('A' . ($statsRow + 2), 'Formularios Completados:');
                $worksheet->setCellValue('B' . ($statsRow + 2), $responsesCompleted);
                $worksheet->setCellValue('A' . ($statsRow + 3), 'Necesitan Transporte:');
                $worksheet->setCellValue('B' . ($statsRow + 3), $needTransport);
                $worksheet->setCellValue('A' . ($statsRow + 4), 'Necesitan Alojamiento:');
                $worksheet->setCellValue('B' . ($statsRow + 4), $needAccommodation);
                
                // Estilo para las estadísticas
                $worksheet->getStyle('A' . $statsRow . ':B' . ($statsRow + 4))->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            },
        ];
    }
}
