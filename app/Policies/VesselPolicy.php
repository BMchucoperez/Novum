<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vessel;
use Illuminate\Auth\Access\HandlesAuthorization;

class VesselPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tanto los administradores como los armadores pueden ver el listado de embarcaciones
        // Los armadores verán solo sus embarcaciones asignadas (filtrado en VesselResource::getEloquentQuery)
        return $user->can('view_any_vessel');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede ver sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('view_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('view_vessel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Los usuarios con rol Armador no pueden crear embarcaciones
        if ($user->hasRole('Armador')) {
            return false;
        }

        return $user->can('create_vessel');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede actualizar sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('update_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('update_vessel');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede eliminar sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('delete_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('delete_vessel');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        // Los usuarios con rol Armador no pueden eliminar en masa
        if ($user->hasRole('Armador')) {
            return false;
        }

        return $user->can('delete_any_vessel');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede forzar la eliminación de sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('force_delete_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('force_delete_vessel');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        // Los usuarios con rol Armador no pueden forzar eliminación en masa
        if ($user->hasRole('Armador')) {
            return false;
        }

        return $user->can('force_delete_any_vessel');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede restaurar sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('restore_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('restore_vessel');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        // Los usuarios con rol Armador no pueden restaurar en masa
        if ($user->hasRole('Armador')) {
            return false;
        }

        return $user->can('restore_any_vessel');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Vessel $vessel): bool
    {
        // Si el usuario es Armador, solo puede replicar sus embarcaciones asignadas
        if ($user->hasRole('Armador')) {
            return $user->can('replicate_vessel') && $vessel->user_id === $user->id;
        }

        return $user->can('replicate_vessel');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        // Los usuarios con rol Armador no pueden reordenar
        if ($user->hasRole('Armador')) {
            return false;
        }

        return $user->can('reorder_vessel');
    }
}
