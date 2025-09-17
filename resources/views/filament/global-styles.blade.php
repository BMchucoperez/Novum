<style>
    /* Estilos globales para mantener consistencia en todos los módulos */

    /* FORZAR badges de navegación para que NUNCA cambien */
    .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:hover .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item.fi-active .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item[aria-current="page"] .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:focus .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:active .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item.fi-sidebar-nav-item-active .fi-sidebar-nav-badge {
        background-color: #3b82f6 !important;
        color: white !important;
        font-size: 12px !important;
        padding: 2px 6px !important;
        border-radius: 10px !important;
        min-width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 600 !important;
        transform: none !important;
        transition: none !important;
        box-shadow: none !important;
        border: none !important;
        scale: 1 !important;
        opacity: 1 !important;
    }
    
    /* Asegurar que el texto del badge no cambie */
    .fi-sidebar-nav-item .fi-sidebar-nav-badge span,
    .fi-sidebar-nav-item:hover .fi-sidebar-nav-badge span,
    .fi-sidebar-nav-item.fi-active .fi-sidebar-nav-badge span,
    .fi-sidebar-nav-item[aria-current="page"] .fi-sidebar-nav-badge span {
        color: white !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    
    /* Prevenir cualquier cambio de estilo en estados hover o active */
    .fi-sidebar-nav-item:hover .fi-sidebar-nav-badge:hover,
    .fi-sidebar-nav-item.fi-active .fi-sidebar-nav-badge:hover {
        background-color: #3b82f6 !important;
        transform: none !important;
        scale: 1 !important;
    }
    
    /* Estilos específicos para diferentes módulos para mantener consistencia */
    .fi-sidebar-nav-item:has([href*="crew-members"]) .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:has([href*="statutory-certificates"]) .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:has([href*="onboard-management-documents"]) .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:has([href*="vessels"]) .fi-sidebar-nav-badge {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    /* Anular CUALQUIER otro estilo que pueda afectar badges de navegación */
    .fi-sidebar-nav-badge * {
        color: white !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    /* Prevenir transformaciones y transiciones en badges de navegación */
    .fi-sidebar-nav-badge,
    .fi-sidebar-nav-badge:hover,
    .fi-sidebar-nav-badge:focus,
    .fi-sidebar-nav-badge:active {
        transform: none !important;
        transition: none !important;
        animation: none !important;
        scale: 1 !important;
    }

    /* Forzar especificidad máxima para badges de navegación */
    body .fi-sidebar .fi-sidebar-nav .fi-sidebar-nav-item .fi-sidebar-nav-badge {
        background-color: #3b82f6 !important;
        color: white !important;
        font-size: 12px !important;
        padding: 2px 6px !important;
        border-radius: 10px !important;
        min-width: 18px !important;
        height: 18px !important;
        font-weight: 600 !important;
        transform: none !important;
        transition: none !important;
        scale: 1 !important;
    }

    /* ===== SOLUCIÓN PARA ITEMS LARGOS EN CHECKLIST ===== */
    
    /* Forzar que los headers de repeater muestren texto completo */
    .fi-fo-repeater-item-header {
        min-height: auto !important;
        height: auto !important;
        padding: 12px 16px !important;
    }
    
    /* El elemento que contiene el texto del item */
    .fi-fo-repeater-item-header-label {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        text-overflow: unset !important;
        overflow: visible !important;
        max-width: none !important;
        width: auto !important;
        line-height: 1.4 !important;
        display: block !important;
        padding-right: 40px !important;
    }
    
    /* Asegurar que el span interno también se comporte correctamente */
    .fi-fo-repeater-item-header-label span {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        text-overflow: unset !important;
        overflow: visible !important;
        display: block !important;
        line-height: 1.4 !important;
    }
    
    /* Ajustar el layout del header para acomodar texto largo */
    .fi-fo-repeater-item-header {
        display: flex !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    /* El contenedor del texto debe tomar el espacio disponible */
    .fi-fo-repeater-item-header-label {
        flex: 1 !important;
        min-width: 0 !important;
    }
    
    /* Los botones de acción deben mantenerse a la derecha */
    .fi-fo-repeater-item-header-actions {
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }
    
    /* Responsive: En pantallas pequeñas */
    @media (max-width: 768px) {
        .fi-fo-repeater-item-header {
            padding: 10px 12px !important;
        }
        
        .fi-fo-repeater-item-header-label {
            font-size: 14px !important;
            padding-right: 35px !important;
        }
    }
    
    /* Mejorar la legibilidad con mejor tipografía */
    .fi-fo-repeater-item-header-label {
        font-size: 15px !important;
        font-weight: 500 !important;
        color: #374151 !important;
    }
    
    /* Asegurar que el contenedor padre no limite la altura */
    .fi-fo-repeater-item {
        overflow: visible !important;
    }
    
    /* Forzar que todos los elementos de texto en el header se muestren completos */
    .fi-fo-repeater-item-header * {
        text-overflow: unset !important;
        overflow: visible !important;
        white-space: normal !important;
    }
</style>