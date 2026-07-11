# FarmaLite 💊

**FarmaLite** es un sistema local de Punto de Venta (POS) e Inventario diseñado específicamente para pequeñas y medianas droguerías. Desarrollado con el objetivo de ofrecer máxima velocidad para el cajero, prevención de pérdidas de inventario y total confiabilidad al funcionar 100% offline.

## 🚀 Características Principales

*   **Ventas Rápidas (POS):** Escaneo con lector de código de barras, búsqueda predictiva y cálculos de cambio instantáneos.
*   **Gestión de Inventario (FEFO):** Alertas de stock bajo y control estricto de fechas de caducidad para prevenir pérdidas por medicamentos vencidos.
*   **Kardex y Trazabilidad:** Historial completo de movimientos de entrada y salida por cada producto.
*   **Devoluciones:** Sistema seguro para reintegrar productos al inventario y registrar el flujo de caja negativo.
*   **Control de Caja:** Apertura y Cierre de Caja con resumen de ventas, efectivo esperado y reportes del día.
*   **Seguridad:** Roles de administrador y cajero, registro de auditoría para operaciones críticas y contraseñas encriptadas.
*   **Marca Blanca:** Personalización del logo, nombre de la droguería e información del ticket directamente desde la interfaz.
*   **Backups Seguros:** Respaldos automáticos de la base de datos (SQLite WAL Mode) y herramienta de restauración intuitiva.
*   **Arquitectura Desacoplada:** (v1.2.0+) Datos de usuario seguros en `ProgramData`, manteniendo la aplicación protegida en `Program Files`.

## 🛠️ Tecnologías

*   **Motor Principal:** PHP 8 embebido con PHP Desktop (Chromium 130).
*   **Base de Datos:** SQLite 3 transaccional.
*   **Frontend:** Bootstrap 4, HTML5, CSS3, JavaScript/jQuery.
*   **Distribución:** Empaquetado automático con Inno Setup.

## 📦 Instalación

1.  Descarga la última versión estable desde la pestaña **Releases**.
2.  Ejecuta `FarmaLite_Setup_x.x.exe`.
3.  El instalador colocará automáticamente la aplicación y aislará la base de datos para futuras actualizaciones.
4.  Inicia sesión con las credenciales por defecto.

## 👥 Contribuciones y Soporte
Las incidencias conocidas se documentan en `KNOWN_ISSUES.md`. Si eres instalador o soporte técnico, consulta el menú de **Diagnóstico** dentro del sistema para información vital de depuración.

---
*Desarrollado con ❤️ para farmacias.*
