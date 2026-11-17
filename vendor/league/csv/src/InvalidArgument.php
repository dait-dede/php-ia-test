<?php

declare(strict_types=1);

namespace League\Csv;

class InvalidArgument extends Exception
{
    public static function dueToInvalidDelimiterCharacter(string $delimiter, string $method): self
    {
        return new self($method . '() expects delimiter to be a single character; `' . $delimiter . '` given.');
    }

    public static function dueToInvalidEnclosureCharacter(string $enclosure, string $method): self
    {
        return new self($method . '() expects enclosure to be a single character; `' . $enclosure . '` given.');
    }

    public static function dueToInvalidHeaderOffset(int $offset, string $method): self
    {
        return new self($method . '() expects header offset to be greater or equal to 0; `' . $offset . '` given.');
    }
}
