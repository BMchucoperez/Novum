<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'inspector_name',
        'status',
        'vessel_id',
        'statutory_certificate_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    /**
     * Get the vessel that owns the inspection schedule.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the statutory certificate associated with the inspection schedule.
     */
    public function statutoryCertificate(): BelongsTo
    {
        return $this->belongsTo(StatutoryCertificate::class);
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'scheduled' => 'Programada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ];
    }

    /**
     * Get status label by key (supports both English and Spanish keys)
     */
    public static function getStatusLabel(string $status): string
    {
        $normalizedStatus = strtolower($status);
        
        return match($normalizedStatus) {
            'scheduled', 'programada' => 'Programada',
            'completed', 'completada' => 'Completada',
            'cancelled', 'cancelada' => 'Cancelada',
            default => $status,
        };
    }
}
