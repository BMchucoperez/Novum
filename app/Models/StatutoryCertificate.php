<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatutoryCertificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vessel_id',
        'vessel_2_id',
        'vessel_3_id',
        'owner_id',
        'inspection_type',
        'inspection_date',
        'inspector_name',
        'inspector_license',
        'parte_1_items',
        'parte_2_items',
        'parte_3_items',
        'parte_4_items',
        'parte_5_items',
        'parte_6_items',
        'overall_status',
        'general_observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inspection_date' => 'date',
        'parte_1_items' => 'array',
        'parte_2_items' => 'array',
        'parte_3_items' => 'array',
        'parte_4_items' => 'array',
        'parte_5_items' => 'array',
        'parte_6_items' => 'array',
    ];

    /**
     * Get the vessel that owns the statutory certificate.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the second vessel for the statutory certificate.
     */
    public function vessel2(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_2_id');
    }

    /**
     * Get the third vessel for the statutory certificate.
     */
    public function vessel3(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_3_id');
    }

    /**
     * Get the owner that owns the statutory certificate.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the default structure for each part
     */
    public static function getDefaultStructure(): array
    {
        return [
            'parte_1' => [
                ['item' => 'Certificado de Arqueo', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado de Línea Máxima de Carga', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado de Matrícula', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado Nacional de Seguridad para naves fluviales', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado de Dotación Mínima', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_2' => [
                ['item' => 'Certificado de Fumigación, Desinfección y Desratización', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_3' => [
                ['item' => 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Permiso para Operar una Estación de Comunicación de Teleservicio Móvil', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_4' => [
                ['item' => 'Póliza de Casco Marítimo P&I', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Póliza de SCTR, salud y pensión', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Póliza de Seguro de Accidentes Personales', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_5' => [
                ['item' => 'Registro para el Control de Bienes Fiscalizados', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_6' => [
                ['item' => 'Registro de la Embarcación', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Registro de la Empresa', 'estado' => '', 'comentarios' => ''],
            ],
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'V' => 'V - Vigente (100% operativo, cumple, buenas condiciones)',
            'A' => 'A - En trámite (operativo con observaciones menores)',
            'N' => 'N - Reparaciones (observaciones que comprometen estanqueidad)',
            'R' => 'R - Vencido (inoperativo, no cumple, observaciones críticas)',
        ];
    }

    /**
     * Get status colors for badges
     */
    public static function getStatusColors(): array
    {
        return [
            'V' => 'success',    // Verde
            'A' => 'warning',    // Amarillo
            'N' => 'danger',     // Naranja (usando danger como aproximación)
            'R' => 'danger',     // Rojo
        ];
    }

    /**
     * Get overall status options
     */
    public static function getOverallStatusOptions(): array
    {
        return [
            'V' => 'V - Conforme General',
            'A' => 'A - Conforme con Observaciones',
            'N' => 'N - No Conforme con Reparaciones',
            'R' => 'R - No Conforme Crítico',
        ];
    }

    /**
     * Calcula el estado general automáticamente según los estados de todos los ítems de todas las partes.
     */
    public function calculateOverallStatus(): string
    {
        $allEstados = [];
        for ($i = 1; $i <= 6; $i++) {
            $items = $this->getAttribute('parte_' . $i . '_items') ?? [];
            foreach ($items as $item) {
                if (!empty($item['estado'])) {
                    $allEstados[] = $item['estado'];
                }
            }
        }
        if (empty($allEstados)) {
            return 'A'; // Por defecto si no hay estados
        }
        if (in_array('R', $allEstados, true)) {
            return 'R';
        }
        if (in_array('N', $allEstados, true)) {
            return 'N';
        }
        if (in_array('A', $allEstados, true)) {
            return 'A';
        }
        return 'V';
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->overall_status = $model->calculateOverallStatus();
        });
    }
}
