<?php

namespace App\Console\Commands;

use App\Models\Vessel;
use App\Models\VesselAssociation;
use Illuminate\Console\Command;

class ManageVesselAssociations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vessel:associations {action} {--main=} {--associated=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage vessel associations (list, create, delete)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listAssociations();
                break;
            case 'create':
                $this->createAssociation();
                break;
            case 'delete':
                $this->deleteAssociation();
                break;
            default:
                $this->error('Invalid action. Use: list, create, or delete');
                return 1;
        }

        return 0;
    }

    private function listAssociations()
    {
        $associations = VesselAssociation::with(['mainVessel', 'associatedVessel'])->get();

        if ($associations->isEmpty()) {
            $this->info('No vessel associations found.');
            return;
        }

        $this->table(
            ['Main Vessel', 'Associated Vessel', 'Created At'],
            $associations->map(function ($association) {
                return [
                    $association->mainVessel->name ?? 'N/A',
                    $association->associatedVessel->name ?? 'N/A',
                    $association->created_at->format('Y-m-d H:i:s'),
                ];
            })
        );
    }

    private function createAssociation()
    {
        $mainId = $this->option('main');
        $associatedId = $this->option('associated');

        if (!$mainId || !$associatedId) {
            $this->error('Both --main and --associated options are required.');
            return;
        }

        $mainVessel = Vessel::find($mainId);
        $associatedVessel = Vessel::find($associatedId);

        if (!$mainVessel) {
            $this->error("Main vessel with ID {$mainId} not found.");
            return;
        }

        if (!$associatedVessel) {
            $this->error("Associated vessel with ID {$associatedId} not found.");
            return;
        }

        if ($mainId == $associatedId) {
            $this->error('A vessel cannot be associated with itself.');
            return;
        }

        $existing = VesselAssociation::where('main_vessel_id', $mainId)
            ->where('associated_vessel_id', $associatedId)
            ->first();

        if ($existing) {
            $this->error('This association already exists.');
            return;
        }

        VesselAssociation::create([
            'main_vessel_id' => $mainId,
            'associated_vessel_id' => $associatedId,
        ]);

        $this->info("Association created: {$mainVessel->name} -> {$associatedVessel->name}");
    }

    private function deleteAssociation()
    {
        $mainId = $this->option('main');
        $associatedId = $this->option('associated');

        if (!$mainId || !$associatedId) {
            $this->error('Both --main and --associated options are required.');
            return;
        }

        $association = VesselAssociation::where('main_vessel_id', $mainId)
            ->where('associated_vessel_id', $associatedId)
            ->first();

        if (!$association) {
            $this->error('Association not found.');
            return;
        }

        $mainVessel = $association->mainVessel;
        $associatedVessel = $association->associatedVessel;

        $association->delete();

        $this->info("Association deleted: {$mainVessel->name} -> {$associatedVessel->name}");
    }
}
