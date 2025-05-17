<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vessel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'service_type_id',
        'navigation_type_id',
        'flag_registry',
        'port_registry',
        'construction_year',
        'shipyard_id',
        'length',
        'beam',
        'depth',
        'gross_tonnage',
        'registration_number',
        'owner_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'construction_year' => 'integer',
        'length' => 'decimal:2',
        'beam' => 'decimal:2',
        'depth' => 'decimal:2',
        'gross_tonnage' => 'decimal:2',
    ];

    /**
     * Get the service type that owns the vessel.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Get the navigation type that owns the vessel.
     */
    public function navigationType(): BelongsTo
    {
        return $this->belongsTo(NavigationType::class);
    }

    /**
     * Get the shipyard that owns the vessel.
     */
    public function shipyard(): BelongsTo
    {
        return $this->belongsTo(Shipyard::class);
    }

    /**
     * Get the owner that owns the vessel.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the user assigned to the vessel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
