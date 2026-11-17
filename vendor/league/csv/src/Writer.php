<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Csv;

/**
 * A class to insert records into a CSV Document.
 */
class Writer extends AbstractCsv
{
    protected const ENCLOSE_ALL = 1;
    protected const ENCLOSE_NECESSARY = 0;
    protected const ENCLOSE_NONE = -1;

    /** @var callable[] */
    protected array $formatters = [];

    /** @var callable[] */
    protected array $validators = [];

    protected string $newline = "\n";
    protected int $enclose_all = self::ENCLOSE_NECESSARY;
    protected bool $relaxed = false;
    protected bool $bom_written = false;
    protected ?int $flush_threshold = null;

    public static function createFromPath(string $path, string $open_mode = 'r+', $context = null): static
    {
        return parent::createFromPath($path, $open_mode, $context);
    }

    public function getEndOfLine(): string
    {
        return $this->newline;
    }

    public function setEndOfLine(string $endOfLine): static
    {
        $this->newline = $endOfLine;

        return $this;
    }

    /** @deprecated since 9.10.0 use setEndOfLine() */
    public function setNewline(string $newline): static
    {
        return $this->setEndOfLine($newline);
    }

    public function getNewline(): string
    {
        return $this->newline;
    }

    public function forceEnclosure(): static
    {
        $this->enclose_all = self::ENCLOSE_ALL;

        return $this;
    }

    public function relaxEnclosure(): static
    {
        $this->enclose_all = self::ENCLOSE_NECESSARY;
        $this->relaxed = true;

        return $this;
    }

    public function noEnclosure(): static
    {
        $this->enclose_all = self::ENCLOSE_NONE;

        return $this;
    }

    public function encloseAll(): bool
    {
        return $this->enclose_all === self::ENCLOSE_ALL;
    }

    public function setFlushThreshold(?int $threshold): static
    {
        $this->flush_threshold = $threshold;

        return $this;
    }

    public function addFormatter(callable $formatter): static
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    public function addValidator(callable $validator, string $validator_name): static
    {
        $this->validators[$validator_name] = $validator;

        return $this;
    }

    /**
     * Adds multiple records to the CSV document.
     */
    public function insertAll(iterable $records): int
    {
        $bytes = 0;
        foreach ($records as $record) {
            $bytes += $this->insertOne($record);
        }

        return $bytes;
    }

    /**
     * Adds a single record to a CSV document.
     */
    public function insertOne(array $record): int
    {
        foreach ($this->formatters as $formatter) {
            $record = $formatter($record);
        }
        foreach ($this->validators as $name => $validator) {
            if (true !== $validator($record)) {
                throw CannotInsertRecord::triggerOnValidation($name, $record);
            }
        }

        $line = implode($this->delimiter, array_map(fn ($field) => $this->field($field), $record)) . $this->newline;
        if (!$this->bom_written && $this->output_bom !== '') {
            $line = $this->output_bom . $line;
        }
        $this->bom_written = true;

        $bytes = fwrite($this->stream, $line);
        if ($bytes === false) {
            throw CannotInsertRecord::triggerOnInsertion($record);
        }

        return $bytes;
    }

    protected function field(mixed $field): string
    {
        $value = Encoding::normalize($this->stringify($field));

        if ($this->enclose_all === self::ENCLOSE_NONE) {
            return $value;
        }

        $special = $this->relaxed
            ? [$this->delimiter, $this->enclosure, "\n", "\r"]
            : [$this->delimiter, $this->enclosure, $this->escape, "\n", "\r", "\t", ' '];

        $needs = $this->enclose_all === self::ENCLOSE_ALL;
        foreach ($special as $char) {
            if ($char !== '' && str_contains($value, $char)) {
                $needs = true;
                break;
            }
        }

        if (!$needs) {
            return $value;
        }

        return $this->enclosure . str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value) . $this->enclosure;
    }

    protected function stringify(mixed $field): string
    {
        return match (true) {
            $field === null => '',
            is_bool($field) => $field ? '1' : '0',
            $field instanceof \Stringable, is_scalar($field) => (string) $field,
            default => throw CannotInsertRecord::triggerOnInsertion([$field]),
        };
    }
}
