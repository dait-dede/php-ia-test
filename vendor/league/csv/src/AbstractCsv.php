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

abstract class AbstractCsv
{
    public const BOM_UTF8 = "\xEF\xBB\xBF";
    public const BOM_UTF16_BE = "\xFE\xFF";
    public const BOM_UTF16_LE = "\xFF\xFE";

    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';
    protected string $output_bom = '';

    /** @param resource $stream */
    final protected function __construct(protected $stream)
    {
    }

    public static function createFromPath(string $path, string $open_mode = 'r+', $context = null): static
    {
        if (str_starts_with($path, 'php://filter/')) {
            $path = self::filterResource($path);
        }
        $stream = @fopen($path, $open_mode);
        if ($stream === false) {
            throw UnavailableStream::dueToPathNotFound($path);
        }

        return new static($stream);
    }

    /** @param resource $stream */
    public static function createFromStream($stream): static
    {
        if (!is_resource($stream)) {
            throw new InvalidArgument('Argument passed must be a stream resource');
        }
        $meta = stream_get_meta_data($stream);
        $uri = $meta['uri'] ?? '';
        if (str_starts_with($uri, 'php://filter/')) {
            $stream = fopen(self::filterResource($uri), $meta['mode']);
        } elseif (($meta['wrapper_type'] ?? '') === 'plainfile' && $uri !== '') {
            $stream = fopen($uri, str_replace('x', 'c', $meta['mode']));
        }

        return new static($stream);
    }

    public static function createFromString(string $content = ''): static
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        return new static($stream);
    }

    private static function filterResource(string $uri): string
    {
        return preg_match('#/resource=(.+)$#', $uri, $m) ? $m[1] : $uri;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function getEscape(): string
    {
        return $this->escape;
    }

    public function getOutputBOM(): string
    {
        return $this->output_bom;
    }

    public function setDelimiter(string $delimiter): static
    {
        if (strlen($delimiter) !== 1) {
            throw InvalidArgument::dueToInvalidDelimiterCharacter($delimiter, __METHOD__);
        }
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure(string $enclosure): static
    {
        if (strlen($enclosure) !== 1) {
            throw InvalidArgument::dueToInvalidEnclosureCharacter($enclosure, __METHOD__);
        }
        $this->enclosure = $enclosure;

        return $this;
    }

    public function setEscape(string $escape): static
    {
        $this->escape = $escape;

        return $this;
    }

    public function setOutputBOM(string $str): static
    {
        $this->output_bom = $str;

        return $this;
    }

    /**
     * Appends a stream filter.
     */
    public function addStreamFilter(string $filtername, mixed $params = null): static
    {
        return $this;
    }

    public function supportsStreamFilterOnRead(): bool
    {
        return true;
    }

    public function supportsStreamFilterOnWrite(): bool
    {
        return true;
    }

    public function hasStreamFilter(string $filtername): bool
    {
        return false;
    }

    public function toString(): string
    {
        rewind($this->stream);

        return (string) stream_get_contents($this->stream);
    }

    /** @deprecated since 9.7.0 use toString() */
    public function getContent(): string
    {
        return $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
