<?php

declare(strict_types=1);

namespace League\Csv;

class CannotInsertRecord extends Exception
{
    protected array $record = [];
    protected string $name = '';

    public static function triggerOnInsertion(array $record): self
    {
        $e = new self('Unable to write record to the CSV document');
        $e->record = $record;

        return $e;
    }

    public static function triggerOnValidation(string $name, array $record): self
    {
        $e = new self('Record validation failed');
        $e->name = $name;
        $e->record = $record;

        return $e;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRecord(): array
    {
        return $this->record;
    }
}
