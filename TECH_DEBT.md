# Deuda técnica

## 1. Parser `.env` propio

- **Qué se simplificó:** `config/database.php` interpreta directamente líneas simples `CLAVE=VALOR`.
- **Por qué:** el examen exige PHP nativo, sin Composer ni dependencias externas.
- **Impacto:** no cubre toda la sintaxis de archivos `.env` complejos, como escapes avanzados o valores multilínea.
- **Cuándo mejorarlo:** antes de usar el sistema fuera del entorno local o cuando se necesiten configuraciones más complejas; entonces conviene usar un parser probado y gestionado como dependencia.

## 2. Vistas PHP sin motor de plantillas ni componentes HTML

- **Qué se simplificó:** las vistas generan HTML directamente y repiten pequeñas funciones de escape.
- **Por qué:** se mantuvo KISS y no se agregó un framework ni una librería de plantillas.
- **Impacto:** puede aumentar la duplicación y hacer más difícil mantener una interfaz grande.
- **Cuándo mejorarlo:** al crecer el número de pantallas o cuando se requiera un sistema visual consistente; convendría centralizar componentes y layouts.

## 3. Pruebas sin motor de base de datos

- **Qué se simplificó:** `tests/run.php` usa repositorios falsos y un doble de transacciones en memoria.
- **Por qué:** es una condición explícita del examen y permite ejecutar las pruebas sin MySQL, SQLite, Composer ni PHPUnit.
- **Impacto:** verifica las reglas del servicio, pero no comprueba la integración real de PDO, InnoDB, `FOR UPDATE` ni el DDL de producción.
- **Cuándo mejorarlo:** antes de publicar o modificar el esquema; se deben agregar pruebas de integración contra una base aislada.

## 4. Modelo de venta con `producto_id` en el reporte

- **Qué se simplificó:** el reporte actual muestra el identificador del producto, no un nombre obtenido mediante `JOIN`.
- **Por qué:** se conservó el modelo tipado aprobado y se evitó ampliar el alcance durante la finalización del examen.
- **Impacto:** el detalle de RF4 es menos amigable para el cajero.
- **Cuándo mejorarlo:** en la siguiente iteración, agregando una consulta preparada con `JOIN` o un dato de presentación separado, sin mezclarlo con las reglas de negocio.

