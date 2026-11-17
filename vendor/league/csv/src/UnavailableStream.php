<?php

declare(strict_types=1);

namespace League\Csv;

final class UnavailableStream extends Exception
{
    public static function dueToPathNotFound(string $path): self
    {
        return new self('`' . $path . '`: failed to open stream: No such file or directory.');
    }
}
