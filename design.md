# Design — Minimarket La Esquina

**Versión:** 1.3
**Basado en:** D0.md v1.1 y requirements.md v1.3
**Fecha:** 23 de octubre de 2026

---

## 1. Stack tecnológico

| Componente | Tecnología | Justificación |
|------------|-----------|---------------|
| Lenguaje | PHP 8.2+ | Propiedades tipadas, promoción de constructor, tipos de retorno, `declare(strict_types=1)` |
| Base de datos | MySQL / MariaDB (InnoDB, utf8mb4) | InnoDB para transacciones; utf8mb4 para caracteres especiales |
| Acceso a datos | PDO | Sentencias preparadas, transacciones, prevención de inyección SQL (RNF1) |
| Plantillas | PHP nativo | Sin motor de plantillas; `htmlspecialchars` para escapar salida (RNF1) |
| Pruebas | PHP nativo (`tests/run.php`) | Sin PHPUnit ni Composer; repositorios falsos en memoria, sin motor de BD |
| Dependencias | Ninguna | Sin frameworks ni librerías externas; principio KISS |

**Convenciones de código:**
- Todos los archivos PHP inician con `declare(strict_types=1);`.
- PHPDoc en español en todas las clases y métodos públicos.
- Namespace `App\` mapeado al directorio `app/` mediante un autoloader simple en `public/index.php`.

---

## 2. Arquitectura

### 2.1 Capas

```
HTTP Request
    │
    ▼
┌────────────┐
│ Controller │  ← valida CSRF, extrae datos de $_POST/$_GET
└────┬───────┘
     │
     ▼
┌────────────┐
│  Service   │  ← valida reglas de negocio (RN1–RN5), gestiona transacciones
└────┬───────┘
     │
     ▼
┌──────────────┐
│ Repository  │  ← ejecuta SQL con PDO y sentencias preparadas
└────┬─────────┘
     │
     ▼
┌────────────┐
│   Model    │  ← objetos de datos tipados (Producto, Venta)
└────────────┘

     │
     ▼
┌────────────┐
│   Views    │  ← HTML con htmlspecialchars
└────────────┘
```

### 2.2 Responsabilidades por capa

| Capa | Sí (qué hace) | No (qué no debe hacer) |
|------|---------------|------------------------|
| **Model** | Contener datos tipados de entidades (Producto, Venta) | Acceder a la BD, contener lógica de negocio, renderizar HTML |
| **Repository** | Implementar una interfaz; ejecutar SQL con PDO y sentencias preparadas; mapear filas a objetos Model | Contener reglas de negocio o validaciones, renderizar HTML, acceder a `$_POST`/`$_GET` |
| **Service** | Ejecutar reglas de negocio (RN1–RN5), validar datos, gestionar transacciones mediante `TransactionManagerInterface` (`beginTransaction`/`commit`/`rollBack`), orquestar repositorios mediante interfaces | Ejecutar SQL directamente, renderizar HTML, acceder a `$_POST`/`$_GET` |
| **Controller** | Recibir peticiones HTTP, validar token CSRF, extraer datos de entrada, llamar al Service, seleccionar y preparar datos para la vista | Contener reglas de negocio, ejecutar SQL, renderizar HTML directamente |
| **Views** | Renderizar HTML, mostrar datos escapados con `htmlspecialchars`, incluir campos ocultos de CSRF en formularios | Acceder a la BD, contener lógica de negocio, llamar a Services o Repositories |

### 2.3 Estructura de directorios

```
ventas-bodega/
├── .env                              # Credenciales reales (excluido de Git)
├── .env.example                      # Plantilla de variables (committed, sin secretos)
├── .gitignore                        # Excluye .env del repositorio
├── config/
│   └── database.php                  # Lee .env, crea y devuelve instancia PDO
├── app/
│   ├── Models/
│   │   ├── Producto.php
│   │   └── Venta.php
│   ├── Repositories/
│   │   ├── ProductoRepositoryInterface.php
│   │   ├── ProductoRepository.php
│   │   ├── VentaRepositoryInterface.php
│   │   └── VentaRepository.php
│   ├── Services/
│   │   ├── ProductoService.php
│   │   └── VentaService.php
│   ├── Exceptions/
│   │   ├── AppException.php
│   │   ├── CantidadNoPositivaException.php
│   │   ├── StockInsuficienteException.php
│   │   ├── PrecioNegativoException.php
│   │   ├── CodigoDuplicadoException.php
│   │   ├── ProductoNoEncontradoException.php
│   │   └── DatabaseException.php
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ProductoController.php
│   │   └── VentaController.php
│   └── Support/
│       ├── Csrf.php                  # Generación y verificación de tokens CSRF
│       ├── TransactionManagerInterface.php  # Contrato para begin/commit/rollback
│       └── PdoTransactionManager.php       # Implementación con PDO (producción)
├── views/
│   ├── layout.php                    # Estructura HTML común (header, nav, footer)
│   ├── home/
│   │   └── index.php
│   ├── productos/
│   │   ├── formulario.php
│   │   └── lista.php
│   └── ventas/
│       ├── formulario.php
│       └── dia.php
├── public/
│   └── index.php                     # Front controller (punto de entrada, autoloader, rutas)
├── database/
│   ├── schema.sql                    # Creación de tablas (InnoDB, utf8mb4)
│   └── seed.sql                      # Datos iniciales de productos
└── tests/
    └── run.php                       # Ejecutor de las seis pruebas obligatorias
```

**Cambios respecto a la versión anterior (v1.2 → v1.3):**
- `.env` + `.env.example` + `.gitignore` agregados. Las credenciales se cargan desde `.env`; `config/database.php` las lee y crea PDO. No hay credenciales escritas directamente en `config/database.php`.
- `app/Support/TransactionManagerInterface.php` y `app/Support/PdoTransactionManager.php` agregados. El Service gestiona transacciones mediante una interfaz, no mediante PDO directo. En producción se usa `PdoTransactionManager` (delegado a PDO); en pruebas se usa un doble en memoria (sin PDO, sin SQLite, sin motor de BD).
- Eliminada la dependencia de `PDO('sqlite::memory:')` en las pruebas. Las seis pruebas se ejecutan sin ningún motor de base de datos.

**Cambios de la versión anterior (v1.1 → v1.2), conservados:**
- `app/` reemplaza a `src/`. Estructura exigida por el Prompt P2 del examen.
- `public/index.php` como front controller; `config/`, `app/` y `database/` fuera del alcance web.
- `database/schema.sql` y `database/seed.sql` contienen el DDL y los datos iniciales.
- `tests/run.php` ejecuta las seis pruebas sin PHPUnit ni Composer.

### 2.4 Front controller y enrutamiento

El punto de entrada es `public/index.php`. Carga `config/database.php` para obtener PDO, registra un autoloader simple (mapea `App\` → `app/`), inicia sesión y enruta según `?route=controlador/accion`:

| Ruta | Controller | Método | Vista |
|------|------------|--------|-------|
| `home/index` | HomeController | index() | home/index.php |
| `productos/registrar` | ProductoController | mostrarFormulario() | productos/formulario.php |
| `productos/guardar` | ProductoController | registrar() | redirección a lista |
| `productos/listar` | ProductoController | listar() | productos/lista.php |
| `ventas/registrar` | VentaController | mostrarFormulario() | ventas/formulario.php |
| `ventas/guardar` | VentaController | registrar() | redirección o error |
| `ventas/dia` | VentaController | consultarDia() | ventas/dia.php |

---

## 3. Esquema de base de datos

Motor: **InnoDB**. Charset: **utf8mb4**. Collation: `utf8mb4_unicode_ci`.

### 3.1 Tabla `producto`

| Campo | Tipo | Clave | Restricciones |
|-------|------|-------|---------------|
| id | INT | PRIMARY KEY | AUTO_INCREMENT |
| codigo | VARCHAR(50) | UNIQUE | NOT NULL |
| nombre | VARCHAR(150) | — | NOT NULL |
| categoria | VARCHAR(100) | — | NOT NULL |
| precio | DECIMAL(10,2) | — | NOT NULL, CHECK (precio >= 0) |
| stock_actual | INT | — | NOT NULL, DEFAULT 0, CHECK (stock_actual >= 0) |
| stock_minimo | INT | — | NOT NULL, DEFAULT 0, CHECK (stock_minimo >= 0) |

**Índices:**
- UNIQUE INDEX `uk_producto_codigo` ON `producto(codigo)` — garantiza RN4 a nivel de BD.

### 3.2 Tabla `venta`

| Campo | Tipo | Clave | Restricciones |
|-------|------|-------|---------------|
| id | INT | PRIMARY KEY | AUTO_INCREMENT |
| producto_id | INT | FOREIGN KEY → producto(id) | NOT NULL |
| cantidad | INT | — | NOT NULL, CHECK (cantidad > 0) |
| precio_unitario | DECIMAL(10,2) | — | NOT NULL, CHECK (precio_unitario >= 0) |
| total | DECIMAL(10,2) | — | NOT NULL |
| fecha_hora | DATETIME | — | NOT NULL, DEFAULT CURRENT_TIMESTAMP |

**Clave foránea:**
- `fk_venta_producto`: `venta(producto_id)` → `producto(id)` con `ON DELETE RESTRICT` (protege la integridad referencial; la eliminación de productos está fuera de alcance).

**Notas de diseño:**
- `precio_unitario` se almacena en la venta para conservar el precio vigente al momento de la venta, independientemente de cambios posteriores en el producto.
- `total` se calcula como `cantidad × precio_unitario` y se almacena para evitar recálculos.
- `fecha_hora` usa `DEFAULT CURRENT_TIMESTAMP` para que la BD registre el momento exacto de la inserción.

### 3.3 Relación

```
producto (1) ──── (N) venta
  id  ◄────────────  producto_id
```

Un producto puede tener cero o muchas ventas. Cada venta corresponde a un único producto (SUPUESTO 3).

### 3.4 Limitaciones del esquema

El sistema **no implementa** edición ni eliminación de productos (fuera de alcance):

- **No editar:** Una vez registrado, un producto no puede modificarse desde la interfaz (ni código, nombre, categoría, precio ni stock mínimo). El único cambio sobre `stock_actual` es el descuento automático al registrar una venta (RF3, RN5).
- **No eliminar:** No existe operación de borrado de productos. La restricción `ON DELETE RESTRICT` protege la integridad referencial como medida de seguridad para futuras extensiones.

### 3.5 Scripts de base de datos

| Archivo | Descripción |
|---------|-------------|
| `database/schema.sql` | Crea la base de datos `bodega_ventas` con charset `utf8mb4` y las tablas `producto` y `venta` con sus restricciones, claves e índices. |
| `database/seed.sql` | Inserta productos de ejemplo para probar el sistema localmente (al menos un producto con stock bajo para validar RF2). |

---

## 4. Contratos de clases

Se muestran firmas tipadas (PHP 8) con `declare(strict_types=1)` y PHPDoc en español. No incluyen implementación, solo la interfaz de cada clase.

### 4.1 Interfaces de repositorios

Los Services dependen de interfaces, no de implementaciones concretas. Esto permite inyectar repositorios falsos en memoria durante las pruebas (sección 9), sin conexión a base de datos.

```php
<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;

interface ProductoRepositoryInterface
{
    /**
     * Guarda un producto en el repositorio.
     */
    public function save(Producto $producto): Producto;

    /**
     * Busca un producto por su ID.
     * En la implementación concreta usa SELECT ... FOR UPDATE dentro de la transacción.
     */
    public function findById(int $id): ?Producto;

    /**
     * Busca un producto por su código.
     */
    public function findByCodigo(string $codigo): ?Producto;

    /**
     * Devuelve todos los productos.
     * @return array<Producto>
     */
    public function findAll(): array;

    /**
     * Verifica si ya existe un producto con el código indicado.
     */
    public function existsByCodigo(string $codigo): bool;

    /**
     * Actualiza el stock actual de un producto.
     * @return bool true si la actualización afectó al menos una fila.
     */
    public function updateStock(int $id, int $nuevoStock): bool;
}
```

```php
<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venta;

interface VentaRepositoryInterface
{
    /**
     * Guarda una venta en el repositorio.
     */
    public function save(Venta $venta): Venta;

    /**
     * Devuelve las ventas de una fecha específica.
     * @return array<Venta>
     */
    public function findByFecha(string $fecha): array;

    /**
     * Devuelve la suma de los totales de las ventas de una fecha.
     */
    public function getTotalByFecha(string $fecha): float;
}
```

### 4.2 Models

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Producto
{
    public function __construct(
        public readonly int $id,           // 0 si es nuevo, asignado por la BD al guardar
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly string $categoria,
        public readonly float $precio,
        public readonly int $stockActual,
        public readonly int $stockMinimo
    ) {}
}
```

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Venta
{
    public function __construct(
        public readonly int $id,           // 0 si es nueva, asignado por la BD al guardar
        public readonly int $productoId,
        public readonly int $cantidad,
        public readonly float $precioUnitario,
        public readonly float $total,
        public readonly string $fechaHora  // formato Y-m-d H:i:s
    ) {}
}
```

### 4.3 Repositories

Las implementaciones concretas usan PDO con sentencias preparadas. Implementan las interfaces de la sección 4.1.

**ProductoRepository** (implementa `ProductoRepositoryInterface`)

```php
class ProductoRepository implements ProductoRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(Producto $producto): Producto;
    // INSERT con sentencia preparada; devuelve Producto con el ID asignado.

    public function findById(int $id): ?Producto;
    // SELECT ... WHERE id = :id FOR UPDATE
    // El bloqueo FOR UPDATE protege contra ventas concurrentes (sección 5.4).
    // Solo se usa dentro de una transacción.

    public function findByCodigo(string $codigo): ?Producto;
    // SELECT ... WHERE codigo = :codigo (sin FOR UPDATE).

    public function findAll(): array;
    // SELECT * ORDER BY nombre.

    public function existsByCodigo(string $codigo): bool;
    // SELECT COUNT(*) WHERE codigo = :codigo.

    public function updateStock(int $id, int $nuevoStock): bool;
    // UPDATE ... SET stock_actual = :nuevo WHERE id = :id
    // Devuelve true si se actualizó al menos una fila.
    // Único método de modificación: se usa exclusivamente en el flujo de
    // venta (RF3). No existe update() ni delete() por diseño (fuera de alcance).
}
```

**VentaRepository** (implementa `VentaRepositoryInterface`)

```php
class VentaRepository implements VentaRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(Venta $venta): Venta;
    // INSERT con sentencia preparada; devuelve Venta con el ID asignado.

    public function findByFecha(string $fecha): array;
    // SELECT ... WHERE DATE(fecha_hora) = :fecha.

    public function getTotalByFecha(string $fecha): float;
    // SELECT COALESCE(SUM(total), 0) WHERE DATE(fecha_hora) = :fecha.
}
```

### 4.4 Services

Los Services dependen de las **interfaces** de repositorio, no de las implementaciones concretas. Esto permite inyectar repositorios falsos en las pruebas.

**ProductoService**

```php
class ProductoService
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function registrar(array $datos): Producto;
    // Valida precio >= 0 (RN3), stock >= 0, código único (RN4),
    // crea el Producto y lo guarda.

    public function listar(): array;
    // Devuelve array<Producto> con todos los productos.

    public function listarConStockBajo(): array;
    // Devuelve array<Producto> donde stock_actual <= stock_minimo.
}
```

**VentaService**

```php
class VentaService
{
    public function __construct(
        private TransactionManagerInterface $transaction,
        private VentaRepositoryInterface $ventaRepository,
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function registrar(int $productoId, int $cantidad): Venta;
    // Abre transacción, valida RN1–RN3, busca producto con FOR UPDATE,
    // calcula total, guarda venta, descuenta stock y verifica el resultado.
    // Ejecuta rollBack ante cualquier error. Ver sección 5.

    public function consultarDia(string $fecha): array;
    // Devuelve ['ventas' => array<Venta>, 'total' => float].
}
```

> **Nota sobre TransactionManagerInterface:** `VentaService` recibe `TransactionManagerInterface` para gestionar la transacción. En producción, se inyecta `PdoTransactionManager` (que delega a PDO). En pruebas, se inyecta un doble en memoria que no hace nada (no requiere PDO, SQLite ni ningún motor de BD). El Service sigue siendo el responsable de llamar `beginTransaction`, `commit` y `rollBack`.

### 4.5 Controllers

```php
class HomeController
{
    public function index(): void;
    // Renderiza la pantalla principal con enlaces a las acciones.
}
```

```php
class ProductoController
{
    public function __construct(private ProductoService $productoService) {}

    public function mostrarFormulario(): void;
    // Muestra el formulario de registro de producto.

    public function registrar(): void;
    // Valida CSRF, extrae datos de $_POST, llama a
    // ProductoService::registrar() y redirige a la lista.

    public function listar(): void;
    // Llama a ProductoService::listar() y renderiza la lista.
}
```

```php
class VentaController
{
    public function __construct(private VentaService $ventaService) {}

    public function mostrarFormulario(): void;
    // Muestra el formulario de registro de venta con lista de productos.

    public function registrar(): void;
    // Valida CSRF, extrae producto_id y cantidad de $_POST,
    // llama a VentaService::registrar() y maneja excepciones.

    public function consultarDia(): void;
    // Llama a VentaService::consultarDia() con la fecha actual
    // y renderiza el reporte del día.
}
```

### 4.6 Exceptions

```php
<?php
declare(strict_types=1);

namespace App\Exceptions;

// Excepción base
class AppException extends Exception {}

// Excepciones de reglas de negocio
class CantidadNoPositivaException extends AppException {}      // RN1
class StockInsuficienteException extends AppException {}       // RN2
class PrecioNegativoException extends AppException {}          // RN3
class CodigoDuplicadoException extends AppException {}         // RN4
class ProductoNoEncontradoException extends AppException {}    // producto inexistente

// Excepción de infraestructura
class DatabaseException extends AppException {}                // errores de PDO
```

| Excepción | Regla | Cuándo se lanza |
|-----------|-------|-----------------|
| CantidadNoPositivaException | RN1 | `cantidad <= 0` en registro de venta |
| StockInsuficienteException | RN2 | `cantidad > stock_actual` del producto |
| PrecioNegativoException | RN3 | `precio < 0` al registrar producto o al validar precio vigente |
| CodigoDuplicadoException | RN4 | `codigo` ya existe al registrar producto |
| ProductoNoEncontradoException | — | `findById` devuelve `null` (producto inexistente) |
| DatabaseException | — | Falla de PDO, `updateStock` no afecta filas, o error de transacción |

### 4.7 Support

**TransactionManagerInterface (abstracción de transacciones)**

Interfaz mínima que abstrae el control transaccional. Permite que el Service gestione transacciones sin depender directamente de PDO. En producción se usa `PdoTransactionManager`; en pruebas se usa un doble en memoria (sin PDO).

```php
<?php
declare(strict_types=1);

namespace App\Support;

interface TransactionManagerInterface
{
    /**
     * Inicia una transacción.
     */
    public function beginTransaction(): void;

    /**
     * Confirma la transacción.
     */
    public function commit(): void;

    /**
     * Revierte la transacción.
     * La implementación debe verificar si hay una transacción activa
     * antes de intentar revertir.
     */
    public function rollBack(): void;
}
```

**PdoTransactionManager (implementación de producción)**

Delega a PDO. `rollBack()` verifica `inTransaction()` antes de ejecutar para evitar errores cuando no hay transacción activa.

```php
<?php
declare(strict_types=1);

namespace App\Support;

class PdoTransactionManager implements TransactionManagerInterface
{
    public function __construct(private PDO $pdo) {}

    /**
     * Inicia una transacción PDO.
     */
    public function beginTransaction(): void;

    /**
     * Confirma la transacción PDO.
     */
    public function commit(): void;

    /**
     * Revierte la transacción PDO si hay una activa.
     */
    public function rollBack(): void;
    // Internamente: if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
}
```

> **Por qué esta abstracción:** El Service necesita iniciar, confirmar y revertir transacciones, pero en las pruebas no puede depender de PDO (no hay MySQL, SQLite ni ningún motor). La interfaz aísla esa dependencia: producción usa `PdoTransactionManager`, pruebas usan un doble que no hace nada. Es la abstracción mínima necesaria; no introduce patrones adicionales (no es Unit of Work ni Transaction Script).

**Csrf (Protección CSRF)**

```php
<?php
declare(strict_types=1);

namespace App\Support;

class Csrf
{
    /**
     * Genera un token aleatorio, lo almacena en $_SESSION y lo devuelve.
     */
    public static function token(): string;

    /**
     * Compara el token recibido con el de la sesión.
     */
    public static function verify(?string $token): bool;
}
```

---

## 5. Flujo de registro de venta (RF3)

Este es el flujo crítico que implementa RN5 (atomicidad). Toda la lógica ocurre en `VentaService::registrar()`.

### 5.1 Secuencia

```
Controller                      Service                            Repository
    │                               │                                   │
    │  POST (producto_id,           │                                   │
    │       cantidad)               │                                   │
    │  verificar CSRF token         │                                   │
    │──────────────────────────────>│                                   │
    │  registrar(productoId,        │                                   │
    │            cantidad)          │                                   │
    │                               │  $this->transaction               │
    │                               │    ->beginTransaction()           │
    │                               │                                   │
    │                               │  productoRepository               │
    │                               │    ->findById(productoId)         │
    │                               │    (SELECT ... FOR UPDATE)        │
    │                               │──────────────────────────────────>│
    │                               │<──────────────────────────────────│ ?Producto
    │                               │                                   │
    │                               │  IF producto === null             │
    │                               │    → rollBack                     │
    │                               │    → throw ProductoNoEncontrado   │
    │                               │      Exception                    │
    │                               │                                   │
    │                               │  IF cantidad <= 0 (RN1)           │
    │                               │    → rollBack                     │
    │                               │    → throw CantidadNoPositiva     │
    │                               │      Exception                    │
    │                               │                                   │
    │                               │  IF stock < cantidad (RN2)        │
    │                               │    → rollBack                     │
    │                               │    → throw StockInsuficiente      │
    │                               │      Exception                    │
    │                               │                                   │
    │                               │  IF precio < 0 (RN3)              │
    │                               │    → rollBack                     │
    │                               │    → throw PrecioNegativo         │
    │                               │      Exception                    │
    │                               │                                   │
    │                               │  total = precio × cantidad        │
    │                               │  fechaHora = now()                │
    │                               │                                   │
    │                               │  ventaRepository->save(Venta)     │
    │                               │──────────────────────────────────>│
    │                               │<──────────────────────────────────│ Venta (con id)
    │                               │                                   │
    │                               │  nuevoStock = stock - cantidad    │
    │                               │  result = productoRepository      │
    │                               │    ->updateStock(productoId,     │
    │                               │       nuevoStock)                  │
    │                               │──────────────────────────────────>│
    │                               │<──────────────────────────────────│ bool
    │                               │                                   │
    │                               │  IF result === false              │
    │                               │    → throw DatabaseException     │
    │                               │      (triggers rollBack en catch) │
    │                               │                                   │
    │                               │  $this->transaction->commit()     │
    │                               │                                   │
    │<──────────────────────────────│  Venta                            │
    │  redirigir con mensaje        │                                   │
    │  de confirmación              │                                   │
```

### 5.2 Manejo de errores en la transacción

Todas las validaciones y operaciones están dentro de un bloque `try`. El `catch` captura `\Throwable` para abarcar tanto excepciones de negocio como errores de infraestructura. La llamada a `rollBack()` delega en la interfaz; la implementación de producción verifica `inTransaction()` internamente, y el doble de prueba es un no-op:

```
try {
    $this->transaction->beginTransaction();

    $producto = $this->productoRepository->findById($productoId);  // FOR UPDATE
    if ($producto === null) {
        throw new ProductoNoEncontradoException("El producto no existe");
    }
    if ($cantidad <= 0) {
        throw new CantidadNoPositivaException("La cantidad debe ser mayor que cero");
    }
    if ($producto->stockActual < $cantidad) {
        throw new StockInsuficienteException("No hay stock suficiente");
    }
    if ($producto->precio < 0) {
        throw new PrecioNegativoException("El precio no es válido");
    }

    $total = $producto->precio * $cantidad;
    $venta = new Venta(0, $productoId, $cantidad, $producto->precio, $total, date('Y-m-d H:i:s'));
    $this->ventaRepository->save($venta);

    $nuevoStock = $producto->stockActual - $cantidad;
    $result = $this->productoRepository->updateStock($productoId, $nuevoStock);
    if ($result === false) {
        throw new DatabaseException("No se pudo actualizar el stock");
    }

    $this->transaction->commit();
    return $venta;

} catch (\Throwable $e) {
    $this->transaction->rollBack();
    throw $e instanceof AppException
        ? $e
        : new DatabaseException("Error en la transacción", 0, $e);
}
```

### 5.3 Garantía de atomicidad (RN5)

- `beginTransaction()` abre la transacción antes de cualquier escritura.
- `save(Venta)` y `updateStock()` se ejecutan dentro de la misma transacción.
- Si `save` tiene éxito pero `updateStock` devuelve `false` o falla, `rollBack()` revierte la inserción de la venta.
- Si `updateStock` tiene éxito pero `save` falla, `rollBack()` revierte el descuento de stock.
- `commit()` solo se ejecuta si todas las operaciones tuvieron éxito.
- `catch (\Throwable)` garantiza que cualquier error, incluido uno de infraestructura (caída de conexión, timeout), ejecute `rollBack`.
- En producción, `PdoTransactionManager::rollBack()` verifica `inTransaction()` antes de revertir. En pruebas, el doble es un no-op seguro.

### 5.4 Protección contra ventas concurrentes

Si dos cajeros (o dos pestañas) intentan vender el mismo producto simultáneamente:

1. La primera transacción ejecuta `SELECT ... FOR UPDATE` y bloquea la fila del producto.
2. La segunda transacción intenta `SELECT ... FOR UPDATE` y queda en espera hasta que la primera haga `commit` o `rollBack`.
3. Al desbloquearse, la segunda transacción lee el `stock_actual` ya descontado por la primera.
4. Si el stock restante no alcanza para la segunda venta, se lanza `StockInsuficienteException` y se ejecuta `rollBack` sin modificar el stock.

Esto previene la condición de carrera en la que dos ventas leen el mismo stock y ambas se aceptan aunque la suma supere el disponible.

> **Nota:** El bloqueo `FOR UPDATE` solo funciona dentro de una transacción InnoDB. En las pruebas unitarias con repositorios falsos, el `findById` del fake simplemente devuelve el producto desde memoria sin bloqueo, lo cual es suficiente porque las pruebas son de un solo hilo.

---

## 6. Manejo de errores y excepciones

### 6.1 Estrategia

1. Las reglas de negocio se validan en el Service dentro de la transacción.
2. Si una validación falla, el Service lanza la excepción correspondiente (sección 4.6); el `catch` ejecuta `rollBack` y propaga la excepción.
3. El Controller captura `AppException` y prepara el mensaje para la vista.
4. Las vistas muestran el mensaje escapándolo con `htmlspecialchars`.
5. Los errores de PDO se capturan en el Repository y se relanzan como `DatabaseException`.
6. Errores inesperados (`\Throwable`) se envuelven en `DatabaseException` en el `catch` del Service.

### 6.2 Manejo en el Controller

```
try {
    $venta = $this->ventaService->registrar($productoId, $cantidad);
    // redirigir con mensaje de éxito
} catch (AppException $e) {
    // preparar $mensaje = $e->getMessage() para la vista
    // volver a mostrar el formulario con el mensaje
}
```

### 6.3 Manejo en el Repository

```
try {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    // ...
} catch (PDOException $e) {
    throw new DatabaseException("Error de base de datos: " . $e->getMessage(), 0, $e);
}
```

---

## 7. Configuración de base de datos

### 7.1 Archivo `.env`

Las credenciales de conexión se almacenan en `.env` en la raíz del proyecto. Este archivo **no se sube al repositorio** (está excluido mediante `.gitignore`).

### 7.2 Archivo `.env.example`

Plantilla con las variables necesarias, sin credenciales sensibles. Se commitea al repositorio como referencia:

| Variable | Descripción | Valor de ejemplo |
|----------|-------------|------------------|
| DB_HOST | Host del servidor de BD | localhost |
| DB_NAME | Nombre de la base de datos | bodega_ventas |
| DB_USER | Usuario de la BD | root |
| DB_PASS | Contraseña de la BD | (vacío) |

### 7.3 `.gitignore`

Debe incluir al menos:

```
.env
```

### 7.4 `config/database.php`

Lee las variables de `.env`, crea y devuelve una instancia PDO:

| Paso | Descripción |
|------|-------------|
| 1 | Lee el archivo `.env` línea por línea y define las variables con `putenv()` o `getenv()`. |
| 2 | Construye el DSN: `mysql:host={DB_HOST};dbname={DB_NAME};charset=utf8mb4`. |
| 3 | Crea `new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC])`. |
| 4 | Devuelve la instancia PDO. |

**Características:**
- No contiene credenciales escritas directamente; todas provienen de `.env`.
- El parsing de `.env` es simple (lectura de líneas `clave=valor`); sin clases auxiliares ni librerías externas.
- `PDO::ERRMODE_EXCEPTION` para que los errores de BD se manejen como excepciones.
- El archivo se carga en `public/index.php` con `require_once` y el resultado se usa para crear `PdoTransactionManager` y los repositorios.

---

## 8. Controles de seguridad

### 8.1 CSRF (Cross-Site Request Forgery)

**Objetivo:** prevenir que un sitio externo envíe peticiones POST falsificadas al sistema.

**Mecanismo:**

1. **Generación:** En cualquier GET que renderice un formulario, `Csrf::token()` genera un string aleatorio (`bin2hex(random_bytes(32))`), lo almacena en `$_SESSION['csrf_token']` y lo devuelve.
2. **Inclusión:** La vista incluye un campo oculto:
   ```html
   <input type="hidden" name="csrf_token" value="...">
   ```
3. **Verificación:** En cualquier POST, el Controller llama a `Csrf::verify($_POST['csrf_token'])`. Si el token no coincide o no existe, la petición se rechaza.

| Ruta | ¿Requiere CSRF? | Motivo |
|------|-----------------|--------|
| `productos/guardar` (POST) | Sí | Modifica datos |
| `ventas/guardar` (POST) | Sí | Modifica datos |
| `home/index` (GET) | No | Solo lectura |
| `productos/listar` (GET) | No | Solo lectura |
| `ventas/dia` (GET) | No | Solo lectura |

### 8.2 Escape HTML (XSS)

**Objetivo:** prevenir que datos de usuario se interpreten como código HTML o JavaScript (RNF1).

**Mecanismo:**

Toda salida de datos en las vistas debe pasar por `htmlspecialchars`:

```php
<?= htmlspecialchars($producto->nombre, ENT_QUOTES, 'UTF-8') ?>
```

| Contexto | ¿Escapar? | Ejemplo |
|----------|-----------|---------|
| Nombre de producto en HTML | Sí | `htmlspecialchars($producto->nombre)` |
| Código de producto en atributo HTML | Sí | `htmlspecialchars($producto->codigo)` |
| Mensajes de error en HTML | Sí | `htmlspecialchars($mensaje)` |
| Total de venta en HTML | Sí | `htmlspecialchars((string) $total)` |

### 8.3 Inyección SQL

**Objetivo:** prevenir que datos de usuario alteren consultas SQL (RNF1).

**Mecanismo:**

Todos los repositorios usan PDO con sentencias preparadas y parámetros enlazados:

```php
$stmt = $this->pdo->prepare("SELECT * FROM producto WHERE codigo = :codigo");
$stmt->execute([':codigo' => $codigo]);
```

Nunca se concatenan valores directamente en una cadena SQL.

### 8.4 Credenciales en `.env`

**Objetivo:** evitar exponer credenciales de base de datos en el código fuente o en el repositorio.

**Mecanismo:**

- Las credenciales se almacenan en `.env`, que está excluido de Git mediante `.gitignore`.
- `config/database.php` las lee en tiempo de ejecución; no las escribe directamente en el código.
- `.env.example` se commitea como plantilla con valores de ejemplo, sin credenciales reales.

---

## 9. Pruebas

### 9.1 Enfoque

Las pruebas unitarias se ejecutan con `tests/run.php` sin PHPUnit, sin Composer y **sin ningún motor de base de datos** (no MySQL, no SQLite, no PDO). El enfoque es:

1. Definir **repositorios falsos** en memoria que implementan las interfaces (`ProductoRepositoryInterface`, `VentaRepositoryInterface`).
2. Definir un **doble de transacción** en memoria que implemente `TransactionManagerInterface` con métodos vacíos (no-op).
3. Inyectar ambos en los Services.
4. Ejecutar las seis pruebas con aserciones simples (`if`/`else` + `echo`).

### 9.2 Dobles de prueba

**Repositorios falsos**

```php
class ProductoRepositoryFake implements ProductoRepositoryInterface
{
    private array $productos = [];
    private int $nextId = 1;

    // Implementa todos los métodos de la interfaz usando $this->productos.
    // save(): asigna ID y almacena en el array.
    // findById(): busca por ID en el array.
    // updateStock(): modifica stock_actual en el array; devuelve true.
    // etc.
}
```

```php
class VentaRepositoryFake implements VentaRepositoryInterface
{
    private array $ventas = [];
    private int $nextId = 1;

    // save(): asigna ID y almacena en el array.
    // findByFecha(): filtra por fecha.
    // getTotalByFecha(): suma los totales.
}
```

**Doble de transacción**

```php
class TransactionManagerFake implements TransactionManagerInterface
{
    // beginTransaction(): no-op (no hace nada).
    // commit(): no-op.
    // rollBack(): no-op.
}
```

> El `TransactionManagerFake` no requiere PDO, SQLite ni ningún motor de BD. Simplemente no hace nada en los tres métodos. Esto es suficiente porque las pruebas validan la lógica de negocio del Service (validaciones, cálculo de total, descuento de stock), no la infraestructura transaccional.

### 9.3 Las seis pruebas obligatorias

| Prueba | Preparación | Acción | Resultado esperado |
|--------|-------------|--------|-------------------- |
| **P1 — Venta válida** | Fake con producto (precio=10, stock=5, mínimo=2) | `registrar(1, 3)` | Devuelve Venta con `total=30`; `stock_actual` queda en 2 |
| **P2 — Cantidad no positiva** | Fake con producto (stock=5) | `registrar(1, 0)` | Lanza `CantidadNoPositivaException` |
| **P3 — Stock insuficiente sin cambios** | Fake con producto (stock=2) | `registrar(1, 5)` | Lanza `StockInsuficienteException`; `stock_actual` sigue en 2 |
| **P4 — Producto inexistente** | Fake sin producto con ID 999 | `registrar(999, 1)` | Lanza `ProductoNoEncontradoException` |
| **P5 — Precio negativo** | Fake con producto (precio=-5, stock=10) | `registrar(1, 1)` | Lanza `PrecioNegativoException` |
| **P6 — Stock bajo** | Fake con 3 productos (uno con stock=1, mínimo=3) | `listarConStockBajo()` | Devuelve únicamente el producto con stock bajo |

### 9.4 Ejecución

```bash
php tests/run.php
```

El script imprime `[OK]` o `[FAIL]` por cada prueba y un resumen final. No requiere servidor web, base de datos, ni conexión de ningún tipo.

---

## 10. Matriz de trazabilidad diseño ← requisitos

| Requisito | Componente(s) de diseño | Reglas |
|-----------|------------------------|--------|
| RF1 | ProductoController, ProductoService, ProductoRepository, Producto (Model), productos/formulario.php | RN3, RN4 |
| RF2 | ProductoController, ProductoService, ProductoRepository, productos/lista.php | — |
| RF3 | VentaController, VentaService, VentaRepository, ProductoRepository, Venta (Model), ventas/formulario.php | RN1, RN2, RN3, RN5 |
| RF4 | VentaController, VentaService, VentaRepository, ventas/dia.php | — |
| RNF1 | config/database.php (PDO), Csrf, htmlspecialchars en views, .env + .gitignore | — |
| RNF2 | VentaService + TransactionManagerInterface (transacción), ProductoRepository (FOR UPDATE) | RN5 |
| RNF3 | HomeController, layout.php (navegación) | — |
| P1–P6 | tests/run.php, ProductoRepositoryFake, VentaRepositoryFake, TransactionManagerFake | RN1–RN5 |
