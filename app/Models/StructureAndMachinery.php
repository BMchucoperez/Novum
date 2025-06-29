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
        'overall_status', // <-- Aseguramos que este campo sea fillable
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
     * Calcula el estado general automáticamente según los estados de todos los ítems de todas las partes.
     */
    public function calculateOverallStatus(): string
    {
        $allEstados = [];
        for ($i = 1; $i <= 13; $i++) {
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
