## Prompt 1 — Documento D0
Actúa como analista de sistemas y ayúdame a elaborar el documento D0.md para el proyecto web **Minimarket La Esquina**, aplicando el principio KISS: la solución más sencilla que cumpla todos los requisitos.

**Contexto:**

Un minimarket de San Vicente de Cañete registra sus ventas manualmente y no controla adecuadamente su inventario. Necesita una aplicación web interna para gestionar productos, controlar el stock y registrar ventas.

**El documento D0.md debe incluir:**

1. **Problema:** describe en una sola frase la situación que se necesita resolver.
2. **Usuarios y proceso actual:** identifica quién utilizará el sistema, qué actividades realiza y cuáles son las dificultades actuales.
3. **Requisitos funcionales (RF1–RF4):**
   - Registrar productos con código, nombre, categoría, precio, stock actual y mínimo.
   - Listar productos e identificar aquellos con stock bajo.
   - Registrar ventas, calcular el total y descontar el stock.
   - Consultar las ventas del día y el total vendido.
4. **Requisitos no funcionales (RNF):** incluye seguridad, integridad de datos y facilidad de uso.
5. **Reglas de negocio (RN1–RN5):**
   - La cantidad vendida debe ser mayor que cero.
   - No se permite vender más unidades que el stock disponible.
   - El precio de venta no puede ser negativo.
   - El código de cada producto debe ser único.
   - Registrar la venta y descontar el stock deben realizarse como una sola operación atómica.
6. **Fuera de alcance:** autenticación, roles, clientes, facturación electrónica, agentes conversacionales y despliegue público.
7. **Supuestos:** identifica explícitamente como SUPUESTO cualquier información no establecida en el caso.

**Condiciones:**

- Utiliza lenguaje técnico claro y comprensible para un estudiante de Ingeniería de Sistemas de sexto ciclo.
- Respeta el alcance del examen y evita funcionalidades adicionales.
- Mantén coherencia entre requisitos y reglas de negocio.
- Genera únicamente D0.md; todavía no implementes código.
- Explica brevemente la estructura del documento para que pueda revisarlo y corregirlo.

## Prompt 2 — Primera corrección de D0.md

Revisa y corrige únicamente D0.md, respetando la guía del examen EPIA y aplicando KISS.

He identificado estas observaciones:

1. Cambia "encargado de tienda" por "cajero", porque es el usuario indicado en el caso.
2. En RF3, especifica que el sistema toma el precio de venta vigente, calcula el total, registra fecha y hora y descuenta el stock.
3. En RNF1, elimina la referencia al almacenamiento de contraseñas y hashing porque la autenticación está fuera del alcance. Mantén seguridad de entradas, consultas y salidas.
4. En SUPUESTO 5, elimina la funcionalidad adicional de modificar posteriormente el stock mínimo.
5. Corrige "reaches" por "alcanza" y aclara que las operaciones básicas del sistema local no requieren internet.

Conserva RF1–RF4, RNF1–RNF3, RN1–RN5 y la tabla de trazabilidad.

No agregues funcionalidades adicionales ni generes código.

Al terminar, explica qué modificaste y por qué.

---

## Prompt 3 — Ajustes finales de seguridad y redacción

Realiza únicamente estos ajustes finales en D0.md:

1. En RNF1, reemplaza la indicación de sanitizar consultas por el uso de PDO con sentencias preparadas para prevenir inyección SQL, y htmlspecialchars para escapar la salida HTML y reducir riesgos de XSS.
2. Corrige "un alerta" por "una alerta".

No modifiques otros requisitos, reglas de negocio, supuestos ni la trazabilidad. No generes código.



## Prompt 4 — Corrección de trazabilidad
Actúa como analista de sistemas y revisa únicamente la tabla de trazabilidad de D0.md.

Detecté una inconsistencia: la regla RN3 (precio de venta no negativo) está relacionada con RF1 y RF3, pero en la tabla de trazabilidad RF3 no incluye RN3.

Corrige exclusivamente la fila de RF3 para que quede así:

| RF3 | RN1, RN2, RN3, RN5 | El registro de ventas valida cantidad positiva, stock suficiente, precio válido y atomicidad. |

Aplica el principio KISS. No modifiques otras secciones, requisitos ni reglas de negocio.

No generes código. Explica brevemente la corrección realizada.

## Prompt 5 — Especificación y diseño
Actúa como analista y arquitecto de software. Lee D0.md y elabora la especificación técnica del proyecto Minimarket La Esquina, aplicando KISS y respetando el alcance del examen EPIA.

Genera dos documentos en la raíz del proyecto:

1. requirements.md:
- RF1 a RF4 y requisitos no funcionales identificados.
- Criterios de aceptación en formato EARS: "CUANDO ... EL SISTEMA DEBERÁ ...".
- Trazabilidad de RN1 a RN5, sin omitir ninguna regla.
- Casos de rechazo: cantidad no positiva, stock insuficiente, precio negativo y código duplicado.
- No agregues funcionalidades fuera del alcance.

2. design.md:
- PHP 8 orientado a objetos, sin frameworks.
- MySQL/MariaDB con PDO y sentencias preparadas.
- Arquitectura Model → Repository → Service → Controller → Views.
- Explica qué hace y qué NO debe hacer cada capa.
- Tablas producto y venta con campos, tipos, claves y restricciones.
- Contratos de clases y métodos con firmas tipadas.
- Flujo de registro de venta con beginTransaction, commit y rollBack.
- Manejo de errores y excepciones propias.
- Variables de conexión almacenadas en .env.
- Controles de seguridad CSRF y escape HTML.

Mantén ambas especificaciones coherentes con D0.md.

Primero explica las decisiones de arquitectura. Luego genera únicamente los documentos de especificación, sin implementar código PHP ni SQL.

Si se genera tasks.md, no ejecutes ninguna tarea. La implementación corresponde a VS Code.

Al finalizar, identifica posibles inconsistencias para que yo las revise y decida las correcciones.

## Prompt 6 — Revisión de especificación
He revisado las siete observaciones de requirements.md y design.md siguiendo la guía EPIA y el principio KISS.

Estas son mis decisiones:

1. Mantener la validación RN3 también en RF3, porque el examen exige una prueba que rechace productos con precio negativo.
2. Mantener CA-RF1-04 para impedir stock negativo y relacionarlo con RNF2 (integridad de datos), sin agregar RN6.
3. No implementar edición ni eliminación de productos. Documentar explícitamente esta limitación para respetar el alcance.
4. Mantener precio_unitario y total en la tabla venta para conservar los importes registrados.
5. Mantener CSRF mediante sesiones PHP, porque es obligatorio en el Prompt P2 del examen.
6. Mantener ON DELETE RESTRICT para proteger la integridad referencial.
7. Mantener listar() y listarConStockBajo(), porque la prueba 6 del examen exige devolver únicamente los productos cuyo stock esté en o por debajo del mínimo.

Aplica únicamente las correcciones documentales necesarias en requirements.md y design.md.

Verifica:
- Criterios EARS para RF1–RF4 y RNF.
- Trazabilidad completa de RN1–RN5.
- Arquitectura Model, Repository, Service, Controller y Views.
- PHP 8 orientado a objetos, sin frameworks.
- PDO preparado y transacciones atómicas.
- Contratos con firmas tipadas.
- Manejo de errores y excepciones.
- Coherencia con D0.md y las seis pruebas obligatorias del examen.

No generes código PHP ni SQL. No ejecutes tareas de Kiro.

Al terminar, indica exactamente qué archivos y secciones modificaste para registrar mis correcciones en REGISTRO.md.

## Prompt 7 — Corrección de diseño según EPIA
Revisa requirements.md y design.md conforme a la guía EPIA. He identificado cuatro inconsistencias que debes corregir aplicando KISS.

1. ESTRUCTURA:
Alinea design.md con la estructura exigida por el Prompt P2 del examen: config/database.php, app/Models, app/Repositories, app/Services, app/Exceptions, app/Controllers, views, public/index.php y tests/run.php. Incluye únicamente archivos auxiliares indispensables. Actualiza el enrutamiento y el recorrido de una venta.

2. BASE DE DATOS:
Corrige DB_NAME a bodega_ventas y especifica utf8mb4, motor InnoDB, database/schema.sql y database/seed.sql.

3. PRUEBAS:
Asegura que ProductoService y VentaService puedan probarse con repositorios falsos en memoria, sin conexión MySQL, PHPUnit ni Composer. Define contratos mínimos tipados para las dependencias, sin agregar patrones innecesarios. Considera las seis pruebas obligatorias: venta válida, cantidad no positiva, stock insuficiente sin cambios, producto inexistente, precio negativo y stock bajo.

4. TRANSACCIONES:
Precisa que la venta y el descuento de stock compartan la misma transacción PDO. Debe comprobarse el resultado del descuento y ejecutarse rollBack ante cualquier error, incluidos errores de infraestructura. Contempla el rechazo de productos inexistentes y la protección contra ventas concurrentes con stock insuficiente.

Conserva PHP 8 orientado a objetos, PDO preparado, strict_types, PHPDoc en español, excepciones propias, CSRF y salida escapada.

No agregues autenticación, roles, clientes ni funcionalidades fuera de alcance.

Modifica únicamente requirements.md y design.md. No generes todavía código PHP ni SQL.

Al finalizar, enumera las secciones corregidas y explica cada decisión para registrarla en REGISTRO.md.

## Prompt 8 — Corrección final de diseño
Revisa únicamente requirements.md y design.md.

Detecté dos inconsistencias con la guía oficial EPIA que debemos corregir antes de implementar.

1. CONFIGURACIÓN:
Conserva config/database.php como responsable de crear la conexión PDO, pero las credenciales deben provenir exclusivamente de variables cargadas desde .env.

El proyecto debe incluir .env.example con DB_HOST, DB_NAME, DB_USER y DB_PASS, sin credenciales sensibles.

.env debe permanecer fuera de GitHub mediante .gitignore.

No escribas credenciales reales directamente en config/database.php.

2. PRUEBAS:
Elimina la dependencia de PDO('sqlite::memory:') en las pruebas unitarias.

Los seis casos de tests/run.php deben poder ejecutarse sin MySQL, SQLite ni otro motor de base de datos.

Conserva las interfaces de repositorios y define una abstracción mínima para gestionar transacciones que permita utilizar PDO en producción y un doble de prueba en memoria en los tests.

El Service debe seguir siendo responsable de iniciar, confirmar y revertir las transacciones.

Aplica KISS: no añadas frameworks, Composer ni abstracciones que no sean necesarias.

Conserva los demás requisitos, decisiones y contratos existentes.

No generes código PHP ni SQL todavía.

Explica qué secciones modificaste y cómo se cumplen las instrucciones P2 y P3 del examen.

## Prompt 9 — Base de datos EPIA
Actúa como diseñador de bases de datos MySQL/MariaDB.

Lee D0.md, requirements.md y design.md.

Genera exclusivamente estos archivos:
1. database/schema.sql
2. database/seed.sql
3. .env.example
4. .gitignore

REQUISITOS OBLIGATORIOS:
- Base de datos bodega_ventas con utf8mb4.
- Motor InnoDB.
- Tablas producto y venta conforme a design.md.
- PRIMARY KEY, FOREIGN KEY y UNIQUE para codigo.
- CHECK para precio >= 0, stock_actual >= 0, stock_minimo >= 0 y cantidad > 0.
- ON DELETE RESTRICT.
- Guardar precio_unitario, cantidad, total y fecha_hora de cada venta.
- No incluir tablas adicionales.
- No implementar autenticación ni roles.

En seed.sql inserta exactamente estos 8 productos:
P001 | Arroz superior 5 kg | Abarrotes | 22.50 | 20 | 5
P002 | Azúcar rubia 1 kg | Abarrotes | 4.20 | 30 | 10
P003 | Aceite vegetal 1 L | Abarrotes | 9.90 | 12 | 4
P004 | Leche evaporada 400 g | Lácteos | 4.00 | 48 | 12
P005 | Fideo spaghetti 500 g | Abarrotes | 3.50 | 25 | 8
P006 | Atún en trozos 170 g | Conservas | 6.50 | 3 | 6
P007 | Gaseosa 1.5 L | Bebidas | 7.00 | 18 | 6
P008 | Detergente 1 kg | Limpieza | 11.80 | 2 | 4

.env.example debe contener DB_HOST, DB_NAME, DB_USER y DB_PASS sin contraseñas sensibles.
.gitignore debe excluir .env.

Aplica KISS y respeta exactamente EPIA.
Genera solamente los archivos de esta fase. No implementes código PHP ni ejecutes tasks.md.

Explícame cómo importar schema.sql y seed.sql en phpMyAdmin y cómo verificar que SELECT COUNT(*) FROM producto devuelva 8.