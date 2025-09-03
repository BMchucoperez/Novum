<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the statutory certificates for the vessel.
     */
    public function statutoryCertificates(): HasMany
    {
        return $this->hasMany(StatutoryCertificate::class);
    }

    /**
     * Get the onboard management documents for the vessel.
     */
    public function onboardManagementDocuments(): HasMany
    {
        return $this->hasMany(OnboardManagementDocument::class);
    }

    /**
     * Get the crew members for the vessel.
     */
    public function crewMembers(): HasMany
    {
        return $this->hasMany(CrewMember::class);
    }

    /**
     * Get the vessels associated to this vessel (this vessel is the main one).
     */
    public function associatedVessels(): HasMany
    {
        return $this->hasMany(VesselAssociation::class, 'main_vessel_id');
    }

    /**
     * Get the main vessels where this vessel is associated.
     */
    public function mainVessels(): HasMany
    {
        return $this->hasMany(VesselAssociation::class, 'associated_vessel_id');
    }

    /**
     * Get the inspection schedules for the vessel.
     */
    public function inspectionSchedules(): HasMany
    {
        return $this->hasMany(InspectionSchedule::class);
    }

    /**
     * Get the documents for the vessel.
     */
    public function vesselDocuments(): HasMany
    {
        return $this->hasMany(VesselDocument::class);
    }

    /**
     * Get all associated vessels for this vessel (both directions).
     */
    public function getAllAssociatedVessels()
    {
        $associated = $this->associatedVessels()->with('associatedVessel')->get()->pluck('associatedVessel');
        $mains = $this->mainVessels()->with('mainVessel')->get()->pluck('mainVessel');
        
        return $associated->merge($mains)->unique('id');
    }

    /**
     * Get all vessels that should be included in inspections when this vessel is selected.
     * This includes the vessel itself plus all associated vessels.
     */
    public function getInspectionVessels()
    {
        $vessels = collect([$this]);
        $associated = $this->getAllAssociatedVessels();
        
        return $vessels->merge($associated)->unique('id')->take(3); // Máximo 3 embarcaciones
    }

    /**
     * Get document by type
     */
    public function getDocumentByType(string $documentType): ?VesselDocument
    {
        return $this->vesselDocuments()->where('document_type', $documentType)->first();
    }

    /**
     * Get document completeness percentage
     */
    public function getDocumentCompleteness(): int
    {
        $totalRequired = count(VesselDocumentType::getAllDocuments());
        $uploaded = $this->vesselDocuments()->valid()->count();
        
        return $totalRequired > 0 ? round(($uploaded / $totalRequired) * 100) : 0;
    }

    /**
     * Get missing documents
     */
    public function getMissingDocuments(): array
    {
        $allDocuments = VesselDocumentType::getAllDocuments();
        $uploadedTypes = $this->vesselDocuments()->valid()->pluck('document_type')->toArray();
        
        $missingTypes = array_diff(array_keys($allDocuments), $uploadedTypes);
        
        return array_intersect_key($allDocuments, array_flip($missingTypes));
    }

    /**
     * Check if vessel has all required documents
     */
    public function hasRequiredDocuments(): bool
    {
        return empty($this->getMissingDocuments());
    }

    /**
     * Get documents by category
     */
    public function getDocumentsByCategory(string $category)
    {
        return $this->vesselDocuments()->byCategory($category)->get();
    }
}
