-- ============================================================
-- Schema: bodega_ventas
-- Proyecto: Minimarket La Esquina
-- Motor: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS bodega_ventas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bodega_ventas;

-- Eliminar tablas en orden inverso (venta primero por la FK)
DROP TABLE IF EXISTS venta;
DROP TABLE IF EXISTS producto;

-- ============================================================
-- Tabla: producto
-- ============================================================
CREATE TABLE producto (
    id            INT           NOT NULL AUTO_INCREMENT,
    codigo        VARCHAR(50)   NOT NULL,
    nombre        VARCHAR(150)  NOT NULL,
    categoria     VARCHAR(100)  NOT NULL,
    precio        DECIMAL(10,2) NOT NULL,
    stock_actual  INT           NOT NULL DEFAULT 0,
    stock_minimo  INT           NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uk_producto_codigo (codigo),
    CHECK (precio >= 0),
    CHECK (stock_actual >= 0),
    CHECK (stock_minimo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabla: venta
-- ============================================================
CREATE TABLE venta (
    id              INT           NOT NULL AUTO_INCREMENT,
    producto_id     INT           NOT NULL,
    cantidad        INT           NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total           DECIMAL(10,2) NOT NULL,
    fecha_hora      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY fk_venta_producto (producto_id)
        REFERENCES producto (id)
        ON DELETE RESTRICT,
    CHECK (cantidad > 0),
    CHECK (precio_unitario >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
