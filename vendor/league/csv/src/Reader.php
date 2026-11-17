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
 * A class to parse and read records from a CSV document.
 */
class Reader extends AbstractCsv implements \IteratorAggregate, \Countable
{
    protected ?int $header_offset = null;

    public static function createFromPath(string $path, string $open_mode = 'r', $context = null): static
    {
        return parent::createFromPath($path, $open_mode, $context);
    }

    public function setHeaderOffset(?int $offset): static
    {
        if ($offset !== null && $offset < 0) {
            throw InvalidArgument::dueToInvalidHeaderOffset($offset, __METHOD__);
        }
        $this->header_offset = $offset;

        return $this;
    }

    public function getHeaderOffset(): ?int
    {
        return $this->header_offset;
    }

    /** @return list<array<int, string>> */
    private function rows(): array
    {
        rewind($this->stream);
        $rows = [];
        while (($row = fgetcsv($this->stream, null, $this->delimiter, $this->enclosure, '')) !== false) {
            if ($row === [null]) {
                continue;
            }
            $rows[] = $row;
        }
        if (isset($rows[0][0])) {
            $rows[0][0] = (string) preg_replace('/^\xEF\xBB\xBF/', '', $rows[0][0]);
        }

        return $rows;
    }

    /** @return list<string> */
    public function getHeader(): array
    {
        if ($this->header_offset === null) {
            return [];
        }

        return $this->rows()[$this->header_offset] ?? throw SyntaxError::dueToHeaderNotFound($this->header_offset);
    }

    public function getRecords(array $header = []): \Iterator
    {
        $rows = $this->rows();
        if ($header === [] && $this->header_offset !== null) {
            $header = $rows[$this->header_offset] ?? throw SyntaxError::dueToHeaderNotFound($this->header_offset);
        }

        foreach ($rows as $offset => $row) {
            if ($offset === $this->header_offset) {
                continue;
            }
            yield $offset => $header === [] ? $row : array_combine($header, array_pad(array_slice($row, 0, count($header)), count($header), null));
        }
    }

    public function fetchOne(int $nth_record = 0): array
    {
        foreach ($this->getRecords() as $i => $record) {
            if ($nth_record-- === 0) {
                return $record;
            }
        }

        return [];
    }

    public function getIterator(): \Iterator
    {
        return $this->getRecords();
    }

    public function count(): int
    {
        return iterator_count($this->getRecords());
    }
}
