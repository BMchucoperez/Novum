<style>
    /* Badges de estado con colores específicos para certificados estatutarios */
    
    /* Badges V - Verde */
    .fi-badge:has-text("V -"),
    .fi-badge[title*="V -"],
    .fi-ta-text:has-text("V -") .fi-badge,
    span.fi-badge:contains("V -") {
        background-color: #10b981 !important;
        color: white !important;
        font-weight: bold !important;
        border: none !important;
    }
    
    /* Badges A - Amarillo */
    .fi-badge:has-text("A -"),
    .fi-badge[title*="A -"],
    .fi-ta-text:has-text("A -") .fi-badge,
    span.fi-badge:contains("A -") {
        background-color: #f59e0b !important;
        color: white !important;
        font-weight: bold !important;
        border: none !important;
    }
    
    /* Badges N - Naranja */
    .fi-badge:has-text("N -"),
    .fi-badge[title*="N -"],
    .fi-ta-text:has-text("N -") .fi-badge,
    span.fi-badge:contains("N -") {
        background-color: #f97316 !important;
        color: white !important;
        font-weight: bold !important;
        border: none !important;
    }
    
    /* Badges R - Rojo */
    .fi-badge:has-text("R -"),
    .fi-badge[title*="R -"],
    .fi-ta-text:has-text("R -") .fi-badge,
    span.fi-badge:contains("R -") {
        background-color: #ef4444 !important;
        color: white !important;
        font-weight: bold !important;
        border: none !important;
    }

    /* Alternativa usando clases de Filament */
    .fi-badge.fi-color-success:has-text("V") {
        background-color: #10b981 !important;
    }
    
    .fi-badge.fi-color-warning:has-text("A") {
        background-color: #f59e0b !important;
    }
    
    .fi-badge.fi-color-danger:has-text("N"),
    .fi-badge.fi-color-danger:has-text("R") {
        background-color: #ef4444 !important;
    }

    /* Mejorar la visualización de los repeaters */
    .fi-fo-repeater-item {
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        margin-bottom: 12px !important;
        padding: 16px !important;
        background-color: #fafafa !important;
        transition: all 0.2s ease !important;
    }
    
    .fi-fo-repeater-item:hover {
        background-color: #f3f4f6 !important;
        border-color: #d1d5db !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Estilos para los tabs */
    .fi-tabs-tab[aria-selected="true"] {
        font-weight: 600 !important;
        background-color: #f3f4f6 !important;
    }
    
    .fi-tabs-tab {
        transition: all 0.2s ease !important;
    }
    
    .fi-tabs-tab:hover {
        background-color: #f9fafb !important;
    }
    
    /* Mejorar la visualización de las secciones */
    .fi-section {
        margin-bottom: 24px !important;
    }
    
    .fi-section-header {
        margin-bottom: 16px !important;
    }
    
    .fi-section-content {
        padding: 20px !important;
    }

    /* Mejorar los campos del formulario */
    .fi-fo-field-wrp {
        margin-bottom: 16px !important;
    }

    /* Estilos para los selects de estado */
    .fi-select-input[name*="estado"] {
        font-weight: 600 !important;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .fi-fo-repeater-item {
            padding: 12px !important;
            margin-bottom: 8px !important;
        }
        
        .fi-section-content {
            padding: 16px !important;
        }
    }

    /* Animaciones suaves */
    .fi-badge {
        transition: all 0.2s ease !important;
    }
    
    .fi-badge:hover {
        transform: scale(1.05) !important;
    }

    /* Mejorar la legibilidad de los labels */
    .fi-fo-field-wrp-label {
        font-weight: 600 !important;
        color: #374151 !important;
    }

    /* Estilos específicos para el estado general */
    .fi-fo-field-wrp:has([name="overall_status"]) .fi-badge {
        font-size: 14px !important;
        padding: 8px 12px !important;
    }

    /* Estilos específicos para la página de visualización */
    .fi-in-repeatable-entry {
        width: 100% !important;
        max-width: none !important;
    }

    .fi-in-repeatable-entry .fi-in-entry-wrp {
        width: 100% !important;
    }

    /* Mejorar el ancho de los tabs en la vista */
    .fi-tabs {
        width: 100% !important;
    }

    .fi-tabs-content {
        width: 100% !important;
        max-width: none !important;
    }

    /* Asegurar que las secciones ocupen todo el ancho */
    .fi-section,
    .fi-in-section {
        width: 100% !important;
        max-width: none !important;
    }

    .fi-section-content,
    .fi-in-section-content {
        width: 100% !important;
        max-width: none !important;
    }

    /* Mejorar el grid de información */
    .fi-in-grid {
        width: 100% !important;
        gap: 1rem !important;
    }

    /* Estilos para los entries de texto */
    .fi-in-text {
        width: 100% !important;
    }

    /* Mejorar la visualización de los badges en la vista */
    .fi-in-text .fi-badge {
        display: inline-flex !important;
        align-items: center !important;
        font-size: 12px !important;
        font-weight: bold !important;
        padding: 6px 10px !important;
        border-radius: 6px !important;
    }
</style>

<script>
    // Script para aplicar colores dinámicamente a los badges
    document.addEventListener('DOMContentLoaded', function() {
        function applyBadgeColors() {
            // Buscar todos los badges y aplicar colores según su contenido
            const badges = document.querySelectorAll('.fi-badge');
            
            badges.forEach(badge => {
                const text = badge.textContent.trim();
                
                if (text.startsWith('V -') || text === 'V') {
                    badge.style.backgroundColor = '#10b981';
                    badge.style.color = 'white';
                    badge.style.fontWeight = 'bold';
                } else if (text.startsWith('A -') || text === 'A') {
                    badge.style.backgroundColor = '#f59e0b';
                    badge.style.color = 'white';
                    badge.style.fontWeight = 'bold';
                } else if (text.startsWith('N -') || text === 'N') {
                    badge.style.backgroundColor = '#f97316';
                    badge.style.color = 'white';
                    badge.style.fontWeight = 'bold';
                } else if (text.startsWith('R -') || text === 'R') {
                    badge.style.backgroundColor = '#ef4444';
                    badge.style.color = 'white';
                    badge.style.fontWeight = 'bold';
                }
            });
        }
        
        // Aplicar colores inicialmente
        applyBadgeColors();
        
        // Observar cambios en el DOM para aplicar colores a nuevos badges
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    setTimeout(applyBadgeColors, 100);
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
