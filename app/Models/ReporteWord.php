<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChecklistInspection;
use App\Models\Owner;
use App\Models\Vessel;
use App\Models\User;

class ReporteWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_inspection_id',
        'user_id',
        'owner_id',
        'vessel_id',
        'vessel2_id',
        'vessel3_id',
        'inspector_name',
        'inspection_date',
        'filters',
        'file_path',
        'report_path',
        'pdf_path',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'inspection_date' => 'date',
        'filters' => 'array',
    ];

    public function checklistInspection()
    {
        return $this->belongsTo(ChecklistInspection::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }
    
    public function vessel2()
    {
        return $this->belongsTo(Vessel::class, 'vessel2_id');
    }
    
    public function vessel3()
    {
        return $this->belongsTo(Vessel::class, 'vessel3_id');
    }
}
