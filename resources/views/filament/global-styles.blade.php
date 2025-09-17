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
</style>