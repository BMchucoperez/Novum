<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteWord extends Model
{
    protected $table = 'reporte_words';

    protected $fillable = [
        'user_id',
        'owner_id',
        'vessel_id',
        'inspector_name',
        'inspection_date',
        'filters',
        'file_path',
    ];

    protected $casts = [
        'filters' => 'array',
        'inspection_date' => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    public function vessel2(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel2_id');
    }

    public function vessel3(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel3_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
