# Requirements — Minimarket La Esquina

**Versión:** 1.3
**Basado en:** D0.md v1.1
**Fecha:** 23 de octubre de 2026

---

## 1. Requisitos funcionales

### RF1 — Registrar productos

El sistema debe permitir al cajero registrar un producto con los siguientes datos:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| Código | Texto | Identificador único del producto |
| Nombre | Texto | Nombre descriptivo del producto |
| Categoría | Texto | Clasificación del producto (ej. abarrotes, lácteos, bebidas) |
| Precio | Decimal | Precio unitario de venta |
| Stock actual | Entero | Cantidad de unidades disponibles |
| Stock mínimo | Entero | Cantidad mínima antes de requerir reabastecimiento |

### RF2 — Listar productos e identificar stock bajo

El sistema debe listar todos los productos registrados y mostrar de forma destacada aquellos cuyo stock actual es menor o igual al stock mínimo, para que el cajero identifique rápidamente qué productos requieren reabastecimiento.

### RF3 — Registrar ventas

El sistema debe permitir registrar una venta indicando el producto y la cantidad vendida. Al registrar la venta, el sistema debe:

- Tomar el precio de venta vigente del producto.
- Calcular el total de la venta (precio unitario × cantidad).
- Registrar la fecha y hora de la venta.
- Descontar la cantidad vendida del stock actual del producto.

### RF4 — Consultar ventas del día

El sistema debe permitir consultar las ventas registradas durante el día actual, mostrando:

- El detalle de cada venta (producto, cantidad, total).
- El monto total acumulado vendido en el día.

---

## 2. Requisitos no funcionales

### RNF1 — Seguridad

- El sistema debe usar PDO con sentencias preparadas para prevenir la inyección SQL.
- El sistema debe usar `htmlspecialchars` para escapar la salida HTML y reducir los riesgos de XSS.

### RNF2 — Integridad de datos

- El sistema debe garantizar que el registro de una venta y el descuento de stock se realicen como una operación atómica: o ambas acciones se completan, o ninguna se aplica.
- El sistema debe validar que los datos ingresados cumplan con los tipos y restricciones definidos antes de almacenarlos.

### RNF3 — Facilidad de uso

- La interfaz debe ser clara y sencilla, accesible para usuarios sin conocimientos técnicos avanzados.
- Las acciones principales (registrar producto, registrar venta, consultar ventas) deben ser accesibles en pocos clics desde la pantalla principal.

---

## 3. Criterios de aceptación (formato EARS)

Cada criterio sigue el patrón **CUANDO [condición] EL SISTEMA DEBERÁ [respuesta]**.

### 3.1 RF1 — Registrar productos

| ID | Criterio | Reglas |
|----|----------|--------|
| CA-RF1-01 | CUANDO el cajero complete el formulario de producto con código, nombre, categoría, precio, stock actual y stock mínimo, y envíe el formulario, EL SISTEMA DEBERÁ registrar el producto y mostrar un mensaje de confirmación. | — |
| CA-RF1-02 | CUANDO el cajero ingrese un código de producto que ya existe en el sistema, EL SISTEMA DEBERÁ rechazar el registro y mostrar el mensaje "El código ya está registrado". | RN4 |
| CA-RF1-03 | CUANDO el cajero ingrese un precio negativo, EL SISTEMA DEBERÁ rechazar el registro y mostrar el mensaje "El precio no puede ser negativo". | RN3 |
| CA-RF1-04 | CUANDO el cajero ingrese un stock actual o stock mínimo negativo, EL SISTEMA DEBERÁ rechazar el registro y mostrar el mensaje "El stock no puede ser negativo". | RNF2 |

### 3.2 RF2 — Listar productos e identificar stock bajo

| ID | Criterio | Reglas |
|----|----------|--------|
| CA-RF2-01 | CUANDO el cajero acceda a la lista de productos, EL SISTEMA DEBERÁ mostrar todos los productos registrados con código, nombre, categoría, precio, stock actual y stock mínimo. | — |
| CA-RF2-02 | CUANDO un producto tenga stock actual menor o igual al stock mínimo, EL SISTEMA DEBERÁ resaltarlo visualmente para indicar que requiere reabastecimiento. | — |
| CA-RF2-03 | CUANDO no existan productos registrados, EL SISTEMA DEBERÁ mostrar un mensaje indicando que no hay productos registrados. | — |

### 3.3 RF3 — Registrar ventas

| ID | Criterio | Reglas |
|----|----------|--------|
| CA-RF3-01 | CUANDO el cajero seleccione un producto e ingrese una cantidad válida, y envíe el registro de venta, EL SISTEMA DEBERÁ tomar el precio vigente del producto, calcular el total, registrar la fecha y hora, descontar el stock y mostrar un mensaje de confirmación. | RN5 |
| CA-RF3-02 | CUANDO el cajero ingrese una cantidad menor o igual a cero, EL SISTEMA DEBERÁ rechazar la venta y mostrar el mensaje "La cantidad debe ser mayor que cero". | RN1 |
| CA-RF3-03 | CUANDO el cajero intente vender una cantidad mayor al stock disponible, EL SISTEMA DEBERÁ rechazar la venta y mostrar el mensaje "No hay stock suficiente". | RN2 |
| CA-RF3-04 | CUANDO el precio vigente del producto sea negativo, EL SISTEMA DEBERÁ rechazar la venta y mostrar el mensaje "El precio no es válido". | RN3 |
| CA-RF3-05 | CUANDO el cajero intente registrar una venta de un producto que no existe, EL SISTEMA DEBERÁ rechazar la venta y mostrar el mensaje "El producto no existe". | — |

### 3.4 RF4 — Consultar ventas del día

| ID | Criterio | Reglas |
|----|----------|--------|
| CA-RF4-01 | CUANDO el cajero consulte las ventas del día, EL SISTEMA DEBERÁ mostrar el detalle de cada venta (producto, cantidad, total) y el monto total acumulado del día. | — |
| CA-RF4-02 | CUANDO no existan ventas registradas en el día, EL SISTEMA DEBERÁ mostrar un mensaje indicando que no hay ventas registradas. | — |

### 3.5 RNF1 — Seguridad

| ID | Criterio |
|----|----------|
| CA-RNF1-01 | CUANDO el sistema ejecute una consulta a la base de datos, EL SISTEMA DEBERÁ usar PDO con sentencias preparadas. |
| CA-RNF1-02 | CUANDO el sistema muestre datos en una vista HTML, EL SISTEMA DEBERÁ escapar la salida con `htmlspecialchars`. |

### 3.6 RNF2 — Integridad de datos

| ID | Criterio |
|----|----------|
| CA-RNF2-01 | CUANDO el sistema registre una venta, EL SISTEMA DEBERÁ ejecutar el registro de la venta y el descuento de stock como una sola transacción atómica. |
| CA-RNF2-02 | CUANDO el sistema reciba datos de entrada, EL SISTEMA DEBERÁ validar que cumplan los tipos y restricciones definidos antes de almacenarlos. |

### 3.7 RNF3 — Facilidad de uso

| ID | Criterio |
|----|----------|
| CA-RNF3-01 | CUANDO el cajero acceda al sistema, EL SISTEMA DEBERÁ mostrar las acciones principales (registrar producto, registrar venta, consultar ventas) accesibles desde la pantalla principal. |

---

## 4. Casos de rechazo

Estos son los casos en que el sistema debe rechazar la operación e informar al usuario:

| Caso | Regla | Mensaje esperado | Origen |
|------|-------|------------------|--------|
| Cantidad no positiva (≤ 0) | RN1 | "La cantidad debe ser mayor que cero" | RF3 |
| Stock insuficiente (cantidad > stock disponible) | RN2 | "No hay stock suficiente" | RF3 |
| Precio negativo | RN3 | "El precio no puede ser negativo" / "El precio no es válido" | RF1, RF3 |
| Código duplicado | RN4 | "El código ya está registrado" | RF1 |
| Producto inexistente | — | "El producto no existe" | RF3 |

Cada caso de rechazo debe:
1. Cancelar la operación sin modificar ningún dato.
2. Mostrar el mensaje correspondiente al usuario.
3. Si el rechazo ocurre durante una transacción (RF3), ejecutar `rollBack` antes de propagar el error.

---

## 5. Trazabilidad de reglas de negocio

| Regla | Descripción | Requisitos relacionados | Criterios de aceptación |
|-------|-------------|------------------------|------------------------|
| RN1 | La cantidad vendida debe ser mayor que cero. | RF3 | CA-RF3-02 |
| RN2 | No se permite vender más unidades que el stock disponible del producto. | RF3, RNF2 | CA-RF3-03, CA-RNF2-01 |
| RN3 | El precio de venta no puede ser negativo. | RF1, RF3 | CA-RF1-03, CA-RF3-04 |
| RN4 | El código de cada producto debe ser único; no se permiten códigos duplicados. | RF1 | CA-RF1-02 |
| RN5 | Registrar la venta y descontar el stock deben realizarse como una sola operación atómica (transacción). | RF3, RNF2 | CA-RF3-01, CA-RNF2-01 |

---

## 6. Fuera de alcance

Las siguientes funcionalidades no forman parte de este proyecto:

- Autenticación de usuarios (inicio de sesión, gestión de credenciales).
- Roles y permisos (accesos diferenciados por tipo de usuario).
- Gestión de clientes (registro y administración de datos de clientes).
- Facturación electrónica (comprobantes electrónicos, integración con SUNAT).
- Agentes conversacionales (chatbots, asistentes de voz).
- Despliegue público (el sistema funciona de forma interna en el local).
- Edición y eliminación de productos (no se pueden modificar ni eliminar productos una vez registrados; el único cambio sobre el stock es el descuento automático al registrar ventas).

---

## 7. Pruebas obligatorias

El sistema debe pasar seis pruebas unitarias ejecutables con `tests/run.php`, usando repositorios falsos en memoria (sin conexión a base de datos, sin PHPUnit, sin Composer):

| Prueba | Descripción | Excepción esperada / Resultado |
|--------|-------------|-------------------------------|
| P1 — Venta válida | Registrar venta con producto existente, cantidad positiva y stock suficiente. | Devuelve Venta con total correcto; stock descontado. |
| P2 — Cantidad no positiva | Registrar venta con cantidad ≤ 0. | CantidadNoPositivaException |
| P3 — Stock insuficiente sin cambios | Registrar venta con cantidad mayor al stock disponible. | StockInsuficienteException; el stock no se modifica. |
| P4 — Producto inexistente | Registrar venta con un ID de producto que no existe. | ProductoNoEncontradoException |
| P5 — Precio negativo | Registrar venta de un producto con precio negativo. | PrecioNegativoException |
| P6 — Stock bajo | Listar productos con stock actual ≤ stock mínimo. | Devuelve únicamente los productos con stock bajo. |
