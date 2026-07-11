# Changelog
Todos los cambios notables de este proyecto se documentarán en este archivo.

## [1.2.0 LTS] - 2026-07-11

### Añadido
- Arquitectura inmutable de sistema con separación estricta de datos (Windows `ProgramData`).
- Script del instalador corporativo `FarmaLite.iss` (Inno Setup) con lógica de retención de datos.
- Sincronización inteligente de Logos de Marca Blanca.
- Módulo de diagnóstico e información en pantalla (`info.php`).
- Módulo avanzado de Devoluciones y Registro de Caja.
- Historial de Inventario (Kardex) completo.
- Sistema de Migraciones de Base de Datos para actualizaciones incrementales.

### Modificado
- Migración completa de almacenamiento de la BD a SQLite WAL-Mode para prevenir cuellos de botella y mejorar concurrencia.
- Refactorización de todos los directorios temporales, de caché y logs hacia el entorno aislado.
- Optimización masiva de rendimiento de consultas SQL mediante inyección de índices y relaciones de llaves foráneas.
- Eliminación de scripts temporales y archivos dev residuales, consolidando el tamaño del empaquetado.

### Arreglado
- Bug visual en el escalado del logo en el sidebar (ahora servido desde cache local y con CSS responsivo).
- Consistencia del inventario al realizar devoluciones (previene doble entrada).
