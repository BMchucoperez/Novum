<style>
    /* Estilos específicos para el módulo de Tripulantes */

    /* Mejorar la visualización de los repeaters de tripulantes */
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
    
    /* Estilos para los badges de conteo (NO navegación) */
    .fi-ta-text .fi-badge,
    .fi-in-text .fi-badge,
    .fi-fo-field-wrp .fi-badge {
        font-weight: 600 !important;
        padding: 6px 12px !important;
        border-radius: 6px !important;
        font-size: 12px !important;
    }
    
    /* Colores específicos para badges de tripulantes (NO navegación) */
    .fi-ta-text .fi-badge.fi-color-primary,
    .fi-in-text .fi-badge.fi-color-primary,
    .fi-fo-field-wrp .fi-badge.fi-color-primary {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    .fi-ta-text .fi-badge.fi-color-success,
    .fi-in-text .fi-badge.fi-color-success,
    .fi-fo-field-wrp .fi-badge.fi-color-success {
        background-color: #10b981 !important;
        color: white !important;
    }

    .fi-ta-text .fi-badge.fi-color-warning,
    .fi-in-text .fi-badge.fi-color-warning,
    .fi-fo-field-wrp .fi-badge.fi-color-warning {
        background-color: #f59e0b !important;
        color: white !important;
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

    /* Estilos para los selects de cargo */
    .fi-select-input[name*="cargo"] {
        font-weight: 600 !important;
        color: #3b82f6 !important;
    }

    /* Estilos específicos para la página de visualización */
    .fi-in-repeatable-entry {
        width: 100% !important;
        max-width: none !important;
    }

    .fi-in-repeatable-entry .fi-in-entry-wrp {
        width: 100% !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        margin-bottom: 12px !important;
        padding: 16px !important;
        background-color: #fafafa !important;
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

    /* Estilos específicos para cargos de tripulantes */
    .fi-in-text:has-text("Capitán") {
        color: #dc2626 !important;
        font-weight: bold !important;
    }
    
    .fi-in-text:has-text("Oficial") {
        color: #2563eb !important;
        font-weight: 600 !important;
    }
    
    .fi-in-text:has-text("Jefe de Máquinas") {
        color: #7c3aed !important;
        font-weight: 600 !important;
    }
    
    .fi-in-text:has-text("Maquinista") {
        color: #059669 !important;
        font-weight: 500 !important;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .fi-fo-repeater-item,
        .fi-in-repeatable-entry .fi-in-entry-wrp {
            padding: 12px !important;
            margin-bottom: 8px !important;
        }
        
        .fi-section-content {
            padding: 16px !important;
        }
    }

    /* Animaciones suaves solo para badges de contenido, NO para navegación */
    .fi-ta-text .fi-badge,
    .fi-in-text .fi-badge,
    .fi-fo-field-wrp .fi-badge {
        transition: all 0.2s ease !important;
    }

    .fi-ta-text .fi-badge:hover,
    .fi-in-text .fi-badge:hover,
    .fi-fo-field-wrp .fi-badge:hover {
        transform: scale(1.05) !important;
    }

    /* Específicamente PREVENIR cambios en badges de navegación */
    .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item:hover .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item.fi-active .fi-sidebar-nav-badge,
    .fi-sidebar-nav-item[aria-current="page"] .fi-sidebar-nav-badge {
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
    }

    /* Mejorar la legibilidad de los labels */
    .fi-fo-field-wrp-label {
        font-weight: 600 !important;
        color: #374151 !important;
    }

    /* Estilos para el botón "Añadir tripulante" */
    .fi-fo-repeater-add-btn {
        background-color: #3b82f6 !important;
        color: white !important;
        border: none !important;
        padding: 12px 20px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }
    
    .fi-fo-repeater-add-btn:hover {
        background-color: #2563eb !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3) !important;
    }

    /* Mejorar la visualización de los items del repeater */
    .fi-fo-repeater-item-header {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
        border-radius: 6px 6px 0 0 !important;
        font-weight: 600 !important;
        color: #475569 !important;
    }

    /* Estilos para campos requeridos */
    .fi-fo-field-wrp.fi-required .fi-fo-field-wrp-label::after {
        content: " *" !important;
        color: #ef4444 !important;
        font-weight: bold !important;
    }

    /* Mejorar la visualización de placeholders */
    .fi-input::placeholder,
    .fi-textarea::placeholder {
        color: #9ca3af !important;
        font-style: italic !important;
    }

    /* Estilos para el resumen de tripulación en la vista */
    .crew-summary .fi-badge {
        font-size: 14px !important;
        padding: 8px 16px !important;
        margin: 4px !important;
    }
</style>

<script>
    // Script para mejorar la experiencia de usuario en el módulo de tripulantes
    document.addEventListener('DOMContentLoaded', function() {
        // Función para aplicar estilos dinámicos a los cargos
        function applyCargosStyles() {
            const cargoInputs = document.querySelectorAll('select[name*="cargo"]');
            
            cargoInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const value = this.value;
                    
                    // Remover clases anteriores
                    this.classList.remove('cargo-captain', 'cargo-officer', 'cargo-engineer', 'cargo-crew');
                    
                    // Aplicar clase según el cargo
                    if (value === 'Capitán') {
                        this.classList.add('cargo-captain');
                        this.style.color = '#dc2626';
                        this.style.fontWeight = 'bold';
                    } else if (value.includes('Oficial')) {
                        this.classList.add('cargo-officer');
                        this.style.color = '#2563eb';
                        this.style.fontWeight = '600';
                    } else if (value.includes('Jefe de Máquinas') || value.includes('Maquinista')) {
                        this.classList.add('cargo-engineer');
                        this.style.color = '#7c3aed';
                        this.style.fontWeight = '600';
                    } else {
                        this.classList.add('cargo-crew');
                        this.style.color = '#059669';
                        this.style.fontWeight = '500';
                    }
                });
                
                // Aplicar estilos iniciales
                if (input.value) {
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
        
        // Aplicar estilos inicialmente
        applyCargosStyles();
        
        // Observar cambios en el DOM para aplicar estilos a nuevos elementos
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    setTimeout(applyCargosStyles, 100);
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Función para actualizar contadores en tiempo real
        function updateCrewCounters() {
            const tripulantesContainer = document.querySelector('[data-field-wrapper="tripulantes"]');
            if (tripulantesContainer) {
                const items = tripulantesContainer.querySelectorAll('.fi-fo-repeater-item');
                const totalCount = items.length;
                
                // Aquí podrías agregar lógica para mostrar contadores en tiempo real
                console.log(`Total tripulantes: ${totalCount}`);
            }
        }
        
        // Actualizar contadores cuando se añaden/eliminan tripulantes
        document.addEventListener('click', function(e) {
            if (e.target.closest('.fi-fo-repeater-add-btn') || e.target.closest('.fi-fo-repeater-delete-btn')) {
                setTimeout(updateCrewCounters, 100);
            }
        });
    });
</script>
