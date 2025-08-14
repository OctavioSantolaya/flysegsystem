<?php

namespace App\Imports;

use App\Models\Passenger;
use App\Models\Contingency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

/**
 * Importador de pasajeros que soporta múltiples formatos de archivo:
 * 
 * Formatos Excel: XLSX, XLS, XLSM, XLTX, XLTM
 * Texto separado: CSV, TSV
 * OpenDocument: ODS
 * Otros formatos: SLK, XML, GNUMERIC, HTML
 * 
 * El sistema detecta automáticamente el formato basándose en la extensión
 * del archivo y utiliza el lector apropiado de PhpSpreadsheet.
 */
class PassengersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $contingencyId;
    protected $importedCount = 0;
    protected $skippedCount = 0;

    public function __construct($contingencyId)
    {
        $this->contingencyId = $contingencyId;
    }

    /**
     * Mapea automáticamente las columnas del archivo a los campos del modelo
     * Soporta detección insensible a mayúsculas/minúsculas y espacios en blanco
     */
    private function mapColumnValue($row, $possibleKeys, $default = null)
    {
        foreach ($possibleKeys as $key) {
            $normalizedKey = strtolower(trim(str_replace([' ', '_', '-'], '', $key)));
            foreach ($row as $columnName => $value) {
                // Normalizar el nombre de la columna removiendo espacios, guiones y guiones bajos
                $normalizedColumnName = strtolower(trim(str_replace([' ', '_', '-'], '', $columnName)));
                if ($normalizedColumnName === $normalizedKey) {
                    return $this->cleanValue($value) ?: $default;
                }
            }
        }
        return $default;
    }

    /**
     * Limpia y normaliza valores de entrada para manejar diferentes codificaciones
     * y formatos que pueden venir de diferentes tipos de archivo
     */
    private function cleanValue($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Convertir a string y limpiar espacios en blanco
        $value = trim((string) $value);
        
        // Manejar posibles problemas de codificación de caracteres
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }
        
        // Remover caracteres de control no deseados
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        return $value ?: null;
    }

    /**
     * Capitaliza correctamente los nombres y apellidos
     * Maneja caracteres especiales y acentos de diferentes codificaciones
     */
    private function capitalizeName($name)
    {
        if (empty($name)) {
            return $name;
        }
        
        // Limpiar el valor primero
        $name = $this->cleanValue($name);
        if (empty($name)) {
            return null;
        }
        
        // Convertir a minúsculas y luego capitalizar cada palabra
        // mb_convert_case maneja correctamente caracteres especiales y acentos
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Mapear automáticamente las columnas con mayor flexibilidad
        $nombre = $this->mapColumnValue($row, [
            'nombre', 'name', 'first_name', 'firstname', 'first name', 'primer_nombre', 'nombres',
            'nom', 'prenom', 'given_name', 'forename'
        ]);
        
        $apellido = $this->mapColumnValue($row, [
            'apellido', 'surname', 'last_name', 'lastname', 'last name', 'apellidos',
            'family_name', 'nom_famille'
        ]);
        
        $pnr = $this->mapColumnValue($row, [
            'pnr', 'localizador', 'booking', 'reservation', 'codigo', 'record_locator',
            'confirmation_code', 'booking_reference'
        ]);
        
        $email = $this->mapColumnValue($row, [
            'email', 'correo', 'mail', 'e-mail', 'correo_electronico', 'email_address'
        ]);
        
        $telefono = $this->mapColumnValue($row, [
            'telefono', 'phone', 'celular', 'movil', 'tel', 'numero', 'mobile',
            'phone_number', 'contact_number'
        ]);
        
        $documento = $this->mapColumnValue($row, [
            'documento', 'document', 'document_number', 'dni', 'cedula', 'pasaporte', 'id', 'foid',
            'passport', 'id_number', 'identification'
        ]);

        // Capitalizar nombres y apellidos para formato consistente
        $nombre = $this->capitalizeName($nombre);
        $apellido = $this->capitalizeName($apellido);
        
        // Normalizar email a minúsculas
        $email = !empty($email) ? strtolower($this->cleanValue($email)) : null;
        
        // Limpiar y normalizar otros campos
        $pnr = !empty($pnr) ? strtoupper($this->cleanValue($pnr)) : null;
        $telefono = $this->cleanValue($telefono);
        $documento = $this->cleanValue($documento);

        // Validar que al menos tengamos nombre, apellido y PNR
        if (empty($nombre) || empty($apellido) || empty($pnr)) {
            $this->skippedCount++;
            return null;
        }

        $this->importedCount++;

        return new Passenger([
            'contingency_id' => $this->contingencyId,
            'name' => $nombre,
            'surname' => $apellido,
            'pnr' => $pnr,
            'document_number' => $documento,
            'email' => $email,
            'phone' => $telefono,
        ]);
    }

    /**
     * Validación más permisiva - solo validamos que haya datos
     */
    public function rules(): array
    {
        return [
            // No validamos columnas específicas ya que pueden variar
        ];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        $this->skippedCount += count($failures);
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getFailures(): array
    {
        return $this->failures()->toArray();
    }
}
