<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Support\Validador;

$raiz = dirname(__DIR__);
$archivo = "{$raiz}/reportes/COMPRAS_INV_20261031.txt";
@unlink($archivo);

exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg("{$raiz}/bin/interfaz.php") . ' 2>&1', $salida, $codigo);
if ($codigo !== 0) {
    echo "FALLA: el comando terminó con código {$codigo}: " . implode(' ', $salida) . "\n";
    exit(1);
}
if (!is_file($archivo)) {
    echo "FALLA: no se generó reportes/COMPRAS_INV_20261031.txt\n";
    exit(1);
}

[$ok, $mensaje] = Validador::validar($archivo);
echo $ok ? "OK: el validador de Compras aceptó el archivo\n" : "FALLA: {$mensaje}\n";
exit($ok ? 0 : 1);
