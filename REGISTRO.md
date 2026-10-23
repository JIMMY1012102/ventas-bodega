# Registro de verificaciones

## 23 de octubre de 2026

### Verificaciones realizadas

- Se ejecutó `php tests/run.php`.
- Resultado observado: `6/6 pruebas aprobadas`.
- Pasaron P1 venta válida, P2 cantidad no positiva, P3 stock insuficiente sin cambios, P4 producto inexistente, P5 precio negativo y P6 stock bajo.
- La comprobación estática de los archivos PHP no reportó errores.

### Auditoría breve de seguridad

- **SQL injection:** los repositorios usan `prepare()` y `execute()` para valores recibidos. La consulta de listado usa `query()` porque no recibe datos del usuario ni concatena valores.
- **XSS:** las vistas escapan los valores mostrados mediante `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **CSRF:** los formularios incluyen un token de sesión y los POST lo validan mediante `Csrf::verify()`.
- **Credenciales:** [config/database.php](config/database.php) lee las variables desde `.env`; `.env` está excluido mediante `.gitignore`. No se verificó un despliegue real ni la existencia de credenciales reales.
- **Transacciones:** `VentaService` ejecuta `beginTransaction()`, guarda la venta, actualiza el stock y ejecuta `commit()`; ante cualquier excepción ejecuta `rollBack()`. El repositorio de producto usa `SELECT ... FOR UPDATE` para la venta.

### Correcciones verdaderas aplicadas durante la implementación

- Se corrigió el import de la función de conexión en `public/index.php` para usar la sintaxis `use function` de PHP.
- Se dejó un doble de transacción para que las pruebas no dependan de un motor de base de datos.

### Pendientes verificados

- No se ejecutó una prueba de integración web contra MySQL/MariaDB porque no se creó un `.env` con credenciales reales.
- El reporte de ventas muestra `producto_id`; no se modificó esa funcionalidad para evitar cambios fuera del alcance final.
