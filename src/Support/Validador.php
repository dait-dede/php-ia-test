<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Invoca el validador oficial del sistema de Compras (binario entregado por Compras).
 */
final class Validador
{
    /** @return array{0: bool, 1: string} [esValido, mensaje] */
    public static function validar(string $archivo): array
    {
        $binario = match (PHP_OS_FAMILY) {
            'Windows' => 'validador.exe',
            'Darwin' => 'validador-macos',
            default => 'validador-linux',
        };
        $ruta = dirname(__DIR__, 2) . '/tools/' . $binario;
        if (!is_executable($ruta)) {
            @chmod($ruta, 0755);
        }

        exec(escapeshellarg($ruta) . ' ' . escapeshellarg($archivo) . ' 2>&1', $salida, $codigo);

        return [$codigo === 0, trim(implode("\n", $salida))];
    }
}
