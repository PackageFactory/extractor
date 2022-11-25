<?php

/**
 * PackageFactory.Extractor - A fluent interface that allows to validate
 * primitive PHP data structures while also reading them
 *   Copyright (C) 2022 Contributors of PackageFactory.Extractor
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PackageFactory\Extractor;

use PackageFactory\Extractor\Internal\TypeDescriber;

/**
 * @api
 * @implements \ArrayAccess<int|string,Extractor>
 * @implements \IteratorAggregate<Extractor,Extractor>
 */
final class Extractor implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @param null|boolean|integer|float|string|array<mixed> $data
     * @param (int|string)[] $pathUntilFirstNullEncounter
     * @param (int|string)[] $entireAccessPath
     * @param bool $isKey
     */
    private function __construct(
        private readonly null|bool|int|float|string|array $data,
        private readonly array $pathUntilFirstNullEncounter,
        private readonly array $entireAccessPath,
        private readonly bool $isKey
    ) {
    }

    /**
     * @api
     * @param null|boolean|integer|float|string|array<mixed> $data
     */
    public static function for(null|bool|int|float|string|array $data): self
    {
        return new self($data, [], [], false);
    }

    /**
     * @param int|string $key
     */
    private function forKey(int|string $key): self
    {
        return new self(
            data: $key,
            pathUntilFirstNullEncounter: [...$this->entireAccessPath, $key],
            entireAccessPath: [...$this->entireAccessPath, $key],
            isKey: true
        );
    }

    /**
     * @api
     * @return (int|string)[]
     */
    public function getPath(): array
    {
        return $this->entireAccessPath;
    }

    /**
     * @api
     * @return boolean
     */
    public function bool(): bool
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_bool($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'bool',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return boolean|null
     */
    public function boolOrNull(): bool|null
    {
        if ($this->data === null || is_bool($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'bool or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return integer
     */
    public function int(): int
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_int($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'int',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return integer|null
     */
    public function intOrNull(): int|null
    {
        if ($this->data === null || is_int($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'int or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return float
     */
    public function float(): float
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_float($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'float',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return float|null
     */
    public function floatOrNull(): float|null
    {
        if ($this->data === null || is_float($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'float or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return int|float
     */
    public function intOrFloat(): int|float
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_int($this->data) || is_float($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'int or float',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return int|float|null
     */
    public function intOrFloatOrNull(): int|float|null
    {
        if ($this->data === null || is_int($this->data) || is_float($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'int or float or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return string
     */
    public function string(): string
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_string($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'string',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return string|null
     */
    public function stringOrNull(): string|null
    {
        if ($this->data === null || is_string($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'string or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return array<mixed>
     */
    public function array(): array
    {
        if ($this->data === null) {
            throw ExtractorException::becauseDataIsRequiredButNullWasPassed($this->pathUntilFirstNullEncounter);
        }

        if (is_array($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'array',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @return array<mixed>|null
     */
    public function arrayOrNull(): null|array
    {
        if ($this->data === null || is_array($this->data)) {
            return $this->data;
        }

        throw ExtractorException::becauseDataDidNotMatchExpectedType(
            path: $this->pathUntilFirstNullEncounter,
            expectedType: 'array or null',
            attemptedData: $this->data,
            isKey: $this->isKey
        );
    }

    /**
     * @api
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_array($this->data) && (is_string($offset) || is_int($offset) || is_float($offset));
    }

    /**
     * @api
     * @param mixed $offset
     * @return self
     */
    public function offsetGet(mixed $offset): mixed
    {
        if ($this->data === null) {
            return new self(
                data: null,
                pathUntilFirstNullEncounter: $this->pathUntilFirstNullEncounter,
                entireAccessPath: [...$this->entireAccessPath, $offset],
                isKey: false
            );
        }

        $data = $this->array();

        return array_key_exists($offset, $data)
            ? new self(
                data: $data[$offset],
                pathUntilFirstNullEncounter: [...$this->entireAccessPath, $offset],
                entireAccessPath: [...$this->entireAccessPath, $offset],
                isKey: false
            )
            : new self(
                data: null,
                pathUntilFirstNullEncounter: [...$this->entireAccessPath, $offset],
                entireAccessPath: [...$this->entireAccessPath, $offset],
                isKey: false
            );
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException(
            sprintf(
                'Extractor is read-only! Tried to set: "%s"',
                is_string($offset) || is_int($offset) || is_float($offset)
                    ? $offset
                    : TypeDescriber::describeTypeOf($offset)
            )
        );
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException(
            sprintf(
                'Extractor is read-only! Tried to unset: "%s"',
                is_string($offset) || is_int($offset) || is_float($offset)
                    ? $offset
                    : TypeDescriber::describeTypeOf($offset)
            )
        );
    }

    /**
     * @api
     * @return \Traversable<Extractor,Extractor>
     */
    public function getIterator(): \Traversable
    {
        if ($this->data !== null) {
            foreach ($this->array() as $key => $value) {
                yield $this->forKey($key) => new self(
                    data: $value,
                    pathUntilFirstNullEncounter: [...$this->entireAccessPath, $key],
                    entireAccessPath: [...$this->entireAccessPath, $key],
                    isKey: false
                );
            }
        }
    }
}
