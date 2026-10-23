# Minimarket La Esquina

Aplicación PHP 8 para registrar productos, controlar stock, registrar ventas y consultar las ventas del día.

## Requisitos

- XAMPP con Apache, PHP 8.2 o superior y MySQL/MariaDB.
- Extensión PHP `pdo_mysql` habilitada.
- Terminal con el comando `php` disponible para ejecutar las pruebas.

## Instalación

1. Coloca el proyecto dentro de `htdocs`, por ejemplo:

   ```text
   /opt/lampp/htdocs/ventas-bodega
   ```

2. Inicia Apache y MySQL desde XAMPP.

3. Crea la configuración local a partir de la plantilla:

   ```bash
   cp .env.example .env
   ```

4. Edita `.env` con los datos reales de la instalación local. Ejemplo habitual de XAMPP:

   ```dotenv
   DB_HOST=localhost
   DB_NAME=bodega_ventas
   DB_USER=root
   DB_PASS=
   ```

   No subas `.env` al repositorio. El archivo está excluido mediante `.gitignore`.

## Base de datos

### Opción A: consola MySQL/MariaDB

Desde la raíz del proyecto:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p bodega_ventas < database/seed.sql
```

Si el usuario local no tiene contraseña, omite `-p`.

### Opción B: phpMyAdmin

1. Abre `http://localhost/phpmyadmin`.
2. Importa `database/schema.sql`.
3. Selecciona la base `bodega_ventas`.
4. Importa `database/seed.sql`.

El esquema crea las tablas `producto` y `venta`. El seed agrega datos iniciales, incluido un producto útil para comprobar el stock bajo.

## Acceso web

Con Apache activo, abre:

```text
http://localhost/ventas-bodega/public/
```

La aplicación permite acceder desde el menú a:

- Registrar productos.
- Listar productos y detectar stock bajo.
- Registrar ventas.
- Consultar las ventas del día.

## Pruebas

Las pruebas usan repositorios falsos en memoria y no necesitan MySQL, SQLite, Composer ni PHPUnit:

```bash
php tests/run.php
```

El resultado esperado del estado verificado es `6/6 pruebas aprobadas`.

## Estructura relevante

- `public/index.php`: punto de entrada y enrutamiento.
- `app/Services`: reglas de negocio.
- `app/Repositories`: acceso PDO mediante consultas preparadas.
- `app/Models`: entidades tipadas.
- `views`: HTML escapado y formularios con CSRF.
- `database`: esquema y datos iniciales.
- `tests/run.php`: pruebas unitarias obligatorias.

