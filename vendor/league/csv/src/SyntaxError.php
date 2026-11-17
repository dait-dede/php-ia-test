<?php

declare(strict_types=1);

namespace League\Csv;

class SyntaxError extends Exception
{
    public static function dueToHeaderNotFound(int $offset): self
    {
        return new self('The header record does not exist or is empty at offset: `' . $offset . '`');
    }
}
