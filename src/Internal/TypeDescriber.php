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

namespace PackageFactory\Extractor\Internal;

/**
 * @internal
 */
final class TypeDescriber
{
    private const STRING_LENGTH_LIMIT = 10;

    public static function describeTypeOf(mixed $value): string
    {
        return match (true) {
            is_bool($value) => self::describeBoolean($value),
            is_int($value) => self::describeInteger($value),
            is_float($value) => self::describeFloat($value),
            is_string($value) => self::describeString($value),
            is_array($value) => self::describeArray($value),
            default => 'unknown(???)'
        };
    }

    private static function describeBoolean(bool $value): string
    {
        return $value ? 'bool(true)' : 'bool(false)';
    }

    private static function describeInteger(int $value): string
    {
        return sprintf('int(%s)', $value);
    }

    private static function describeFloat(float $value): string
    {
        return sprintf('float(%s)', $value);
    }

    private static function describeString(string $value): string
    {
        return sprintf('string("%s")', self::truncateStringIfNecessary($value));
    }

    /**
     * @param array<mixed> $value
     * @return string
     */
    private static function describeArray(array $value): string
    {
        $count = count($value);

        if ($count === 0) {
            return 'array(length=0)';
        }

        foreach ($value as $itemKey => $itemValue) {
            $valueDescription = is_array($itemValue)
                ? '[...]'
                : self::describeTypeOf($itemValue);

            if (is_string($itemKey)) {
                $keyDescription = self::truncateStringIfNecessary($itemKey);

                return $count === 1
                    ? sprintf(
                        'array(["%s" => %s])',
                        $keyDescription,
                        $valueDescription
                    )
                    : sprintf(
                        'array(["%s" => %s, ...], length=%s)',
                        $keyDescription,
                        $valueDescription,
                        $count
                    );
            } else {
                return $count === 1
                    ? sprintf('array([%s])', $valueDescription)
                    : sprintf('array([%s, ...], length=%s)', $valueDescription, $count);
            }
        }
    }

    private static function truncateStringIfNecessary(string $string): string
    {
        $length = mb_strlen($string);

        if ($length > self::STRING_LENGTH_LIMIT) {
            return mb_substr($string, 0, self::STRING_LENGTH_LIMIT) . '...';
        } else {
            return $string;
        }
    }
}
