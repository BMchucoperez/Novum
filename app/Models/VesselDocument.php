<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VesselDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vessel_id',
        'document_type',
        'document_category',
        'document_name',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_at',
        'is_valid',
        'expiry_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
        'expiry_date' => 'date',
        'is_valid' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the vessel that owns the document.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Verificar si el documento está vencido
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isPast();
    }

    /**
     * Verificar si el documento está próximo a vencer (30 días)
     */
    public function isExpiringPoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isBefore(now()->addDays(30));
    }

    /**
     * Obtener color del estado para badges
     */
    public function getStatusColor(): string
    {
        if (!$this->is_valid) {
            return 'danger';
        }
        
        if ($this->isExpired()) {
            return 'danger';
        }
        
        if ($this->isExpiringPoon()) {
            return 'warning';
        }
        
        return 'success';
    }

    /**
     * Obtener texto del estado
     */
    public function getStatusText(): string
    {
        if (!$this->is_valid) {
            return 'Inválido';
        }
        
        if ($this->isExpired()) {
            return 'Vencido';
        }
        
        if ($this->isExpiringPoon()) {
            return 'Por vencer';
        }
        
        return 'Vigente';
    }

    /**
     * Obtener URL segura para descargar el archivo
     */
    public function getFileUrl(): string
    {
        return Storage::disk('local')->url($this->file_path);
    }

    /**
     * Obtener nombre legible del documento
     */
    public function getDisplayName(): string
    {
        $allDocuments = VesselDocumentType::getAllDocuments();
        return $allDocuments[$this->document_type] ?? $this->document_name;
    }

    /**
     * Scope: Filtrar por categoría
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('document_category', $category);
    }

    /**
     * Scope: Solo documentos válidos
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope: Documentos próximos a vencer
     */
    public function scopeExpiring($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>=', now());
    }

    /**
     * Scope: Documentos vencidos
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Boot method para manejar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uploaded_at = now();
            
            // Asignar categoría automáticamente basada en el tipo
            if (!$model->document_category && $model->document_type) {
                $model->document_category = VesselDocumentType::getCategoryByType($model->document_type);
            }
        });

        static::deleting(function ($model) {
            // Eliminar archivo físico cuando se elimina el registro
            if ($model->file_path && Storage::disk('local')->exists($model->file_path)) {
                Storage::disk('local')->delete($model->file_path);
            }
        });
    }
}