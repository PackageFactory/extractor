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
 */
final class ExtractorException extends \Exception
{
    /**
     * @param (int|string)[] $path
     * @param string $rawMessage
     */
    private function __construct(
        private readonly array $path,
        private readonly string $rawMessage
    ) {
        parent::__construct(
            $path
            ? sprintf('Extraction failed at path "%s": %s', implode('.', $path), $rawMessage)
            : sprintf('Extraction failed: %s', $rawMessage),
            1669042598
        );
    }

    /**
     * @return (int|string)[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRawMessage(): string
    {
        return $this->rawMessage;
    }

    /**
     * @param (int|string)[] $path
     * @return self
     */
    public static function becauseDataIsRequiredButNullWasPassed(array $path): self
    {
        return new self(
            path: $path,
            rawMessage: 'Value is required, but was null.'
        );
    }

    /**
     * @param (int|string)[] $path
     * @param string $expectedType
     * @param mixed $attemptedData
     * @return self
     */
    public static function becauseDataDidNotMatchExpectedType(
        array $path,
        string $expectedType,
        mixed $attemptedData,
        bool $isKey
    ): self {
        return new self(
            path: $path,
            rawMessage: sprintf(
                '%s was expected to be of type %s, got %s instead.',
                $isKey ? 'Key' : 'Value',
                $expectedType,
                TypeDescriber::describeTypeOf($attemptedData)
            )
        );
    }
}
