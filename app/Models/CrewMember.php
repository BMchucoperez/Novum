<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewMember extends Model
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
        'inspection_date',
        'tripulantes',
        'general_observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inspection_date' => 'date',
        'tripulantes' => 'array',
    ];

    /**
     * Get the vessel that owns the crew member record.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the second vessel for the crew member record.
     */
    public function vessel2(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_2_id');
    }

    /**
     * Get the third vessel for the crew member record.
     */
    public function vessel3(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_3_id');
    }

    /**
     * Get the owner that owns the crew member record.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the default structure for crew members
     */
    public static function getDefaultStructure(): array
    {
        return [
            [
                'cargo' => '',
                'nombre' => '',
                'matricula' => '',
                'comentarios' => '',
            ]
        ];
    }

    /**
     * Get common cargo options
     */
    public static function getCargoOptions(): array
    {
        return [
            'Capitán' => 'Capitán',
            'Primer Oficial' => 'Primer Oficial',
            'Segundo Oficial' => 'Segundo Oficial',
            'Tercer Oficial' => 'Tercer Oficial',
            'Jefe de Máquinas' => 'Jefe de Máquinas',
            'Primer Maquinista' => 'Primer Maquinista',
            'Segundo Maquinista' => 'Segundo Maquinista',
            'Tercer Maquinista' => 'Tercer Maquinista',
            'Contramaestre' => 'Contramaestre',
            'Marinero' => 'Marinero',
            'Cocinero' => 'Cocinero',
            'Camarero' => 'Camarero',
            'Electricista' => 'Electricista',
            'Soldador' => 'Soldador',
            'Mecánico' => 'Mecánico',
            'Engrasador' => 'Engrasador',
            'Mozo de Cubierta' => 'Mozo de Cubierta',
            'Timonel' => 'Timonel',
            'Radiotelegrafista' => 'Radiotelegrafista',
            'Médico' => 'Médico',
            'Enfermero' => 'Enfermero',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Get total crew count
     */
    public function getTotalCrewAttribute(): int
    {
        return is_array($this->tripulantes) ? count($this->tripulantes) : 0;
    }

    /**
     * Get crew by cargo
     */
    public function getCrewByCargo(string $cargo): array
    {
        if (!is_array($this->tripulantes)) {
            return [];
        }

        return array_filter($this->tripulantes, function ($tripulante) use ($cargo) {
            return isset($tripulante['cargo']) && $tripulante['cargo'] === $cargo;
        });
    }

    /**
     * Get officers count (Capitán, Oficiales, Jefe de Máquinas, Maquinistas)
     */
    public function getOfficersCountAttribute(): int
    {
        if (!is_array($this->tripulantes)) {
            return 0;
        }

        $officerRanks = [
            'Capitán',
            'Primer Oficial',
            'Segundo Oficial', 
            'Tercer Oficial',
            'Jefe de Máquinas',
            'Primer Maquinista',
            'Segundo Maquinista',
            'Tercer Maquinista'
        ];

        return count(array_filter($this->tripulantes, function ($tripulante) use ($officerRanks) {
            return isset($tripulante['cargo']) && in_array($tripulante['cargo'], $officerRanks);
        }));
    }

    /**
     * Get crew count (excluding officers)
     */
    public function getCrewCountAttribute(): int
    {
        return $this->total_crew - $this->officers_count;
    }
}
