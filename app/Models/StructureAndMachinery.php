<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StructureAndMachinery extends Model
{
    use HasFactory;

    protected $table = 'structure_and_machineries';

    protected $fillable = [
        'owner_id',
        'vessel_id',
        'vessel_2_id',
        'vessel_3_id',
        'inspector_name',
        'inspector_license',
        'inspection_date',
        // Partes 1 a 13
        'parte_1_items',
        'parte_2_items',
        'parte_3_items',
        'parte_4_items',
        'parte_5_items',
        'parte_6_items',
        'parte_7_items',
        'parte_8_items',
        'parte_9_items',
        'parte_10_items',
        'parte_11_items',
        'parte_12_items',
        'parte_13_items',
        // Estados por parte
        'parte_1_estado',
        'parte_2_estado',
        'parte_3_estado',
        'parte_4_estado',
        'parte_5_estado',
        'parte_6_estado',
        'parte_7_estado',
        'parte_8_estado',
        'parte_9_estado',
        'parte_10_estado',
        'parte_11_estado',
        'parte_12_estado',
        'parte_13_estado',
        'overall_status',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'parte_1_items' => 'array',
        'parte_2_items' => 'array',
        'parte_3_items' => 'array',
        'parte_4_items' => 'array',
        'parte_5_items' => 'array',
        'parte_6_items' => 'array',
        'parte_7_items' => 'array',
        'parte_8_items' => 'array',
        'parte_9_items' => 'array',
        'parte_10_items' => 'array',
        'parte_11_items' => 'array',
        'parte_12_items' => 'array',
        'parte_13_items' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(\App\Models\Owner::class, 'owner_id');
    }

    public function vessel()
    {
        return $this->belongsTo(\App\Models\Vessel::class, 'vessel_id');
    }

    public function vessel2()
    {
        return $this->belongsTo(\App\Models\Vessel::class, 'vessel_2_id');
    }

    public function vessel3()
    {
        return $this->belongsTo(\App\Models\Vessel::class, 'vessel_3_id');
    }

    /**
     * Calcula el estado de una parte específica según los estados de sus ítems.
     * Solo será "V" cuando TODOS los ítems sean "V"
     */
    public function calculatePartStatus(int $partNumber): string
    {
        $items = $this->getAttribute('parte_' . $partNumber . '_items') ?? [];
        $estados = [];
        
        foreach ($items as $item) {
            if (!empty($item['estado'])) {
                $estados[] = $item['estado'];
            }
        }
        
        // Si no hay ítems o no hay estados definidos, retornar V por defecto
        if (empty($items) || empty($estados)) {
            return 'V';
        }
        
        // Solo será "V" si TODOS los ítems son "V"
        $todosConformes = true;
        foreach ($estados as $estado) {
            if ($estado !== 'V') {
                $todosConformes = false;
                break;
            }
        }
        
        if ($todosConformes) {
            return 'V';
        } else {
            // Si no todos son conformes, tomar el peor estado
            if (in_array('R', $estados, true)) {
                return 'R';
            }
            if (in_array('N', $estados, true)) {
                return 'N';
            }
            return 'A';
        }
    }

    /**
     * Calcula el estado general automáticamente según los estados de todas las partes.
     * Solo será "V" cuando TODAS las partes sean "V"
     */
    public function calculateOverallStatus(): string
    {
        $estadosPartes = [];
        
        // Calcular estado de cada parte
        for ($i = 1; $i <= 13; $i++) {
            $estadoParte = $this->calculatePartStatus($i);
            $estadosPartes[] = $estadoParte;
            // Actualizar el estado de la parte
            $this->setAttribute('parte_' . $i . '_estado', $estadoParte);
        }
        
        if (empty($estadosPartes)) {
            return 'V'; // Por defecto si no hay estados será V
        }
        
        // Solo será "V" si TODAS las partes son "V"
        $todasConformes = true;
        foreach ($estadosPartes as $estado) {
            if ($estado !== 'V') {
                $todasConformes = false;
                break;
            }
        }
        
        if ($todasConformes) {
            return 'V';
        } else {
            // Si no todas son conformes, tomar el peor estado
            if (in_array('R', $estadosPartes, true)) {
                return 'R';
            }
            if (in_array('N', $estadosPartes, true)) {
                return 'N';
            }
            return 'A';
        }
    }

    /**
     * Obtiene las partes que tienen observaciones (estado diferente a "V")
     * Recalcula los estados en tiempo real para asegurar precisión
     */
    public function getPartesConObservaciones(): array
    {
        $partNames = [
            1 => 'Casco y Estructura',
            2 => 'Sistema de Propulsión',
            3 => 'Sistema de Gobierno',
            4 => 'Luces de Navegación y Equipos de Comunicación y Navegación',
            5 => 'Sistema Eléctrico',
            6 => 'Sistema de Combustible',
            7 => 'Sistema de Contraincendios',
            8 => 'Sistema de Achique y Sentina',
            9 => 'Sistema de Aguas Negras',
            10 => 'Sistema de Amarre',
            11 => 'Sistema de Agua Dulce y de Servicios Generales',
            12 => 'Seguridad y Salvamento',
            13 => 'Aspectos Generales',
        ];
        
        $partesConObservaciones = [];
        
        for ($i = 1; $i <= 13; $i++) {
            // Recalcular el estado de la parte en tiempo real
            $estadoParte = $this->calculatePartStatus($i);
            if ($estadoParte && $estadoParte !== 'V') {
                $partesConObservaciones[] = $partNames[$i];
            }
        }
        
        return $partesConObservaciones;
    }

    /**
     * Obtiene el texto de las partes con observaciones para mostrar en la tabla
     */
    public function getPartesConObservacionesTexto(): string
    {
        $partes = $this->getPartesConObservaciones();
        
        if (empty($partes)) {
            return '—';
        }
        
        if (count($partes) <= 3) {
            return implode(', ', $partes);
        }
        
        return implode(', ', array_slice($partes, 0, 2)) . ' y ' . (count($partes) - 2) . ' más';
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            // Calcular estados de cada parte y estado general
            $model->overall_status = $model->calculateOverallStatus();
        });
    }
}
