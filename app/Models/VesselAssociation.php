<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VesselAssociation extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_vessel_id',
        'associated_vessel_id',
    ];

    /**
     * Get the main vessel.
     */
    public function mainVessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'main_vessel_id');
    }

    /**
     * Get the associated vessel.
     */
    public function associatedVessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'associated_vessel_id');
    }
}
