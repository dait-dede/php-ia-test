<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Support\Validador;

$archivo = $argv[1] ?? null;
if ($archivo === null) {
    fwrite(STDERR, "Uso: php bin/validar.php <archivo>\n");
    exit(2);
}

[$ok, $mensaje] = Validador::validar($archivo);
echo $mensaje . "\n";
exit($ok ? 0 : 1);
