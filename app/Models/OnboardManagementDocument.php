<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardManagementDocument extends Model
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
    ];

    /**
     * Get the vessel that owns the onboard management document.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the second vessel for the onboard management document.
     */
    public function vessel2(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_2_id');
    }

    /**
     * Get the third vessel for the onboard management document.
     */
    public function vessel3(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_3_id');
    }

    /**
     * Get the owner that owns the onboard management document.
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
                ['item' => 'Hoja de Seguridad (MSDS) de los materiales peligrosos dentro de la nave', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Informe y Planos de Calibración de Casco y Estructuras', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificados de Prueba Hidrostática y Mantenimiento de Extintores', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Manual y/o Programa Mantenimiento: Casco y estructuras, sistemas principales, equipos y maquinarias', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Registro de Mantenimiento: Casco y estructuras, sistemas principales, equipos y maquinarias', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Política de control de la corrosión', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Manual o Acta de Estabilidad y Trimado', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Juego de Planos (Disposición general, seguridad, sistema contraincendio y operaciones)', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Procedimiento de Abastecimiento de Combustible (Diesel)', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Plan Médico de evacuación - MEDEVAC', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Manual de Navegación', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Plan de Gestión de Basura', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Libro de registro de Basura a bordo', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Plan de Gestión de Mezclas Oleosas', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Manual de gestión de seguridad', 'estado' => '', 'comentarios' => ''],
                ['item' => 'IPERC y AST a bordo', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Procedimiento de ingreso a espacios confinados', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Política de Alcohol y Drogas', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Plan de contingencias', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_2' => [
                ['item' => 'Programa de Simulacros en Condiciones Peligrosas', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Estructura Orgánica de los Simulacros', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Registro de Capacitación de la Tripulación', 'estado' => '', 'comentarios' => ''],
            ],
            'parte_3' => [
                ['item' => 'Reglamento Internacional para prevenir abordajes', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Lámina Gráfica del Reglamento Nacional para prevenir abordajes en los ríos', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Diario de Máquinas', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Diario de Navegación', 'estado' => '', 'comentarios' => ''],
                ['item' => 'Derrotero y Cartas de Navegación', 'estado' => '', 'comentarios' => ''],
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
}
