# Sistema de Embarcaciones Asociadas

## Descripción

Este sistema permite asociar embarcaciones entre sí, de manera que cuando selecciones una embarcación principal en las inspecciones, automáticamente se incluyan las embarcaciones asociadas.

## Características

### 1. Asociación de Embarcaciones
- Cada embarcación puede tener hasta 2 embarcaciones asociadas
- Las asociaciones se configuran desde el formulario de edición de embarcaciones
- Las asociaciones son unidireccionales (A -> B no implica B -> A)

### 2. Integración con Inspecciones
- Al seleccionar una embarcación principal en cualquier formulario de inspección, automáticamente se cargan las embarcaciones asociadas
- Funciona en todos los módulos de inspección:
  - Estructura y Maquinaria
  - Certificados y Documentos Estatutarios
  - Documentos del Sistema de Gestión a Bordo
  - Tripulantes

### 3. Interfaz de Usuario
- Nueva pestaña "Embarcaciones Asociadas" en el formulario de embarcaciones
- Columna adicional en la tabla de embarcaciones mostrando el número de asociaciones
- Vista detallada de asociaciones en la página de visualización de embarcaciones

## Cómo Usar

### Crear Asociaciones de Embarcaciones

1. Ve a **Gestión de Embarcaciones > Embarcaciones**
2. Edita una embarcación existente o crea una nueva
3. Ve a la pestaña **"Embarcaciones Asociadas"**
4. Selecciona hasta 2 embarcaciones que quieras asociar
5. Guarda los cambios

### Crear Inspecciones con Embarcaciones Asociadas

1. Ve a cualquier módulo de inspección (ej: **Inspecciones > Estructura y Maquinaria**)
2. Crea un nuevo registro
3. Selecciona el propietario
4. Selecciona la **Embarcación Principal**
5. Las embarcaciones asociadas se cargarán automáticamente en los campos "Embarcación 2" y "Embarcación 3"
6. Puedes cambiar las embarcaciones asociadas si es necesario

## Comandos de Consola

### Listar todas las asociaciones
```bash
php artisan vessel:associations list
```

### Crear una asociación
```bash
php artisan vessel:associations create --main=1 --associated=2
```

### Eliminar una asociación
```bash
php artisan vessel:associations delete --main=1 --associated=2
```

## Estructura de Base de Datos

### Tabla: vessel_associations
- `id`: ID único
- `main_vessel_id`: ID de la embarcación principal
- `associated_vessel_id`: ID de la embarcación asociada
- `created_at`, `updated_at`: Timestamps

### Restricciones
- Índice único en (`main_vessel_id`, `associated_vessel_id`) para evitar duplicados
- Claves foráneas con eliminación en cascada
- No se permite auto-asociación (una embarcación consigo misma)

## Métodos del Modelo Vessel

### `associatedVessels()`
Obtiene las embarcaciones asociadas a esta embarcación (donde esta es la principal).

### `mainVessels()`
Obtiene las embarcaciones principales donde esta embarcación está asociada.

### `getAllAssociatedVessels()`
Obtiene todas las embarcaciones relacionadas (tanto como principal como asociada).

### `getInspectionVessels()`
Obtiene todas las embarcaciones que deben incluirse en una inspección (máximo 3).

## Ejemplo de Uso

Si tienes las embarcaciones:
- **RODRIGO I** (ID: 1)
- **RODRIGO II** (ID: 2)  
- **RODRIGO III** (ID: 3)

Y creas las asociaciones:
- RODRIGO I -> RODRIGO II
- RODRIGO I -> RODRIGO III

Entonces, cuando crees una inspección y selecciones **RODRIGO I** como embarcación principal:
- Embarcación 1: RODRIGO I (seleccionada manualmente)
- Embarcación 2: RODRIGO II (cargada automáticamente)
- Embarcación 3: RODRIGO III (cargada automáticamente)

## Beneficios

1. **Eficiencia**: No necesitas seleccionar manualmente las mismas embarcaciones en cada inspección
2. **Consistencia**: Garantiza que siempre se incluyan las embarcaciones correctas
3. **Flexibilidad**: Puedes cambiar las embarcaciones asociadas si es necesario
4. **Escalabilidad**: Fácil de mantener y actualizar las asociaciones

## Notas Técnicas

- Las asociaciones se manejan a través del modelo `VesselAssociation`
- La lógica de auto-carga se implementa usando el evento `afterStateUpdated` de Filament
- El sistema es compatible con el filtrado por propietario existente
- Las asociaciones se eliminan automáticamente si se elimina una embarcación