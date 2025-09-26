{{-- SOLUCION KISS: Botón simple de traducción CON PERSISTENCIA --}}
<script>
// PERSISTIR IDIOMA ENTRE PÁGINAS
let currentLanguage = localStorage.getItem('novum_language') || 'es';
let translateButtonExists = false;

// CREAR BOTÓN SIMPLE
function createSimpleTranslateButton() {
    if (translateButtonExists) return; // Evitar duplicados

    const header = document.querySelector('header') || document.querySelector('[data-topbar]') || document.querySelector('.fi-header');

    if (header) {
        const translateBtn = document.createElement('div');
        translateBtn.innerHTML = `
            <button id="simple-translate-btn" onclick="simpleTranslate()" style="
                position: fixed;
                top: 20px;
                right: 80px;
                z-index: 9999;
                padding: 8px 16px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-family: 'Inter', sans-serif;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 6px;
            ">
                🌍 <span id="lang-text">${currentLanguage.toUpperCase()}</span>
            </button>
        `;
        document.body.appendChild(translateBtn);
        translateButtonExists = true;
    }
}

// FUNCIÓN SIMPLE DE TRADUCCIÓN CON PERSISTENCIA
function simpleTranslate() {
    // Cambiar idioma
    currentLanguage = currentLanguage === 'es' ? 'pt' : 'es';

    // GUARDAR EN LOCALSTORAGE
    localStorage.setItem('novum_language', currentLanguage);

    // Actualizar texto del botón
    const langText = document.getElementById('lang-text');
    if (langText) {
        langText.textContent = currentLanguage.toUpperCase();
    }

    // MÉTODO DIRECTO: Usar Google Translate API sin widget
    if (currentLanguage === 'pt') {
        // Traducir todo a portugués
        translateAllText('es', 'pt');
    } else {
        // Volver a español (recarga página pero mantiene preferencia)
        location.reload();
    }
}

// TRADUCIR TODO EL TEXTO DE LA PÁGINA
async function translateAllText(from, to) {
    const textElements = document.querySelectorAll('*:not(script):not(style)');

    textElements.forEach(async (element) => {
        // Solo traducir nodos de texto directos
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    return node.nodeValue.trim() &&
                           !node.parentElement.tagName.match(/SCRIPT|STYLE|CODE|PRE/)
                           ? NodeFilter.FILTER_ACCEPT
                           : NodeFilter.FILTER_REJECT;
                }
            }
        );

        let textNode;
        while (textNode = walker.nextNode()) {
            const originalText = textNode.nodeValue.trim();
            if (originalText.length > 1) {
                try {
                    const translatedText = await translateText(originalText, from, to);
                    textNode.nodeValue = translatedText;
                } catch (error) {
                    console.log('Error traduciendo:', originalText);
                }
            }
        }
    });
}

// API DIRECTA DE GOOGLE TRANSLATE (sin widget)
async function translateText(text, from, to) {
    try {
        const response = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=${from}&tl=${to}&dt=t&q=${encodeURIComponent(text)}`);
        const data = await response.json();
        return data[0][0][0] || text;
    } catch (error) {
        // Fallback: traducciones básicas hardcodeadas
        const basicTranslations = {
            'Embarcaciones': 'Embarcações',
            'Inspecciones': 'Inspeções',
            'Configuración': 'Configuração',
            'Usuario': 'Usuário',
            'Buscar': 'Pesquisar',
            'Guardar': 'Salvar',
            'Cancelar': 'Cancelar',
            'Editar': 'Editar',
            'Eliminar': 'Eliminar',
            'Crear': 'Criar',
            'Ver': 'Ver',
            'Documentos': 'Documentos',
            'Nombre': 'Nome',
            'Descripción': 'Descrição'
        };
        return basicTranslations[text] || text;
    }
}

// AUTO-TRADUCIR AL CARGAR SI ESTÁ EN PORTUGUÉS
function autoTranslateOnLoad() {
    const savedLanguage = localStorage.getItem('novum_language');

    if (savedLanguage === 'pt') {
        // Esperar a que cargue la página y traducir automáticamente
        setTimeout(() => {
            translateAllText('es', 'pt');
        }, 2000);
    }
}

// INICIALIZAR
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(createSimpleTranslateButton, 1000);
    autoTranslateOnLoad();
});

// Crear botón si la página ya está cargada
if (document.readyState === 'complete') {
    setTimeout(createSimpleTranslateButton, 500);
    setTimeout(autoTranslateOnLoad, 1000);
}
</script>