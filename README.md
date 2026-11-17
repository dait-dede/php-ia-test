# Prueba técnica — Interfaz de inventario para Compras

## Contexto

El sistema de Compras (legacy) recibe cada mes un archivo de texto con el inventario. Hoy se arma a mano. Queremos generarlo automáticamente desde `data/productos.csv`.

## Tu tarea

Implementa el comando:

```bash
php bin/interfaz.php
```

Debe generar `reportes/COMPRAS_INV_20261031.txt` (corte del 31 de octubre de 2026), que el sistema de Compras acepte.

- La especificación del formato está en `docs/interfaz-compras.md`.
- **La referencia oficial es el validador de Compras.** El archivo es correcto cuando el validador responde `OK`:

```bash
php bin/validar.php reportes/COMPRAS_INV_20261031.txt
```

## Reglas

- El archivo se genera con `league/csv`, que ya está instalada en `vendor/` (es la librería estándar del equipo para archivos de intercambio).
- No agregues dependencias: el servidor no tiene acceso a internet ni a Composer.
- No modifiques `vendor/`, `data/`, `tools/`, `docs/` ni `tests/`.
- Tiempo sugerido: 2 horas.

## Entrega

Además del código, un `ENTREGA.md` breve con lo que hiciste.

```bash
php bin/interfaz.php
php tests/run.php
```
