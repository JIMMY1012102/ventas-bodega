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

## Prompt 4 — Corrección de trazabilidad
Actúa como analista de sistemas y revisa únicamente la tabla de trazabilidad de D0.md.

Detecté una inconsistencia: la regla RN3 (precio de venta no negativo) está relacionada con RF1 y RF3, pero en la tabla de trazabilidad RF3 no incluye RN3.

Corrige exclusivamente la fila de RF3 para que quede así:

| RF3 | RN1, RN2, RN3, RN5 | El registro de ventas valida cantidad positiva, stock suficiente, precio válido y atomicidad. |

Aplica el principio KISS. No modifiques otras secciones, requisitos ni reglas de negocio.

No generes código. Explica brevemente la corrección realizada.
