# Interfaz de inventario para Compras — Especificación

Versión 1.3 (2019). Documento heredado del proveedor original. **Puede estar desactualizado: la referencia vigente es el validador de Compras.**

## Generalidades

- Archivo de texto plano, un registro por línea.
- Campos separados por `|` (pipe).
- Se entrega un archivo por corte mensual.
- Los caracteres no permitidos se eliminan del texto.

## Estructura

```
H|<emisor>|<interfaz>|<fecha de corte>
<encabezado de columnas>
<registros de detalle>
T|<número de registros>|<suma del valor>
```

### Registro de control (H)

| Campo | Descripción |
|---|---|
| emisor | Clave de la empresa emisora |
| interfaz | Tipo de interfaz: `INVENTARIO` |
| fecha de corte | Último día del periodo |

### Detalle

| Campo | Descripción |
|---|---|
| SKU | Clave del producto |
| DESCRIPCION | Nombre del producto (máx. 30 posiciones) |
| CATEGORIA | Clave de categoría de 3 letras |
| PRECIO | Precio unitario con IVA |
| EXISTENCIA | Unidades en almacén |
| VALOR | PRECIO × EXISTENCIA |

### Registro de cierre (T)

Número de registros de detalle y suma del campo VALOR.
