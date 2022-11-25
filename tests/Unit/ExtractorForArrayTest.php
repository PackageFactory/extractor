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

namespace PackageFactory\Extractor\Tests\Unit;

use PackageFactory\Extractor\Extractor;
use PackageFactory\Extractor\ExtractorException;
use PHPUnit\Framework\TestCase;

final class ExtractorForArrayTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canBeCreatedForArray(): void
    {
        $extractor = Extractor::for([]);

        $this->assertInstanceOf(Extractor::class, $extractor);
    }

    /**
     * @return array<mixed>
     */
    public function arrayExamples(): array
    {
        return [
            'empty' => [[]],
            'one-dimensional, numeric index' => [[1, 2, 3]],
            'two-dimensional, numeric index' => [[[1, 2, 3]]],
            'deeply nested, numeric index' => [[[[[[1, 2, 3]]]]]],
            'one-dimensional, string index' => [['foo' => 1, 'bar' => 2]],
            'deeply nested, string index' => [[
                'deeply' => [
                    'nested' => ['foo' => 1, 'bar' => 2]
                ]
            ]],
        ];
    }

    /**
     * @dataProvider arrayExamples
     * @test
     * @param array<mixed> $example
     * @return void
     */
    public function canExtractArray(array $example): void
    {
        $this->assertEquals(
            $example,
            Extractor::for($example)->array()
        );
    }

    /**
     * @dataProvider arrayExamples
     * @test
     * @param array<mixed> $example
     * @return void
     */
    public function canExtractarrayOrNull(array $example): void
    {
        $this->assertEquals(
            $example,
            Extractor::for($example)->arrayOrNull()
        );
    }

    /**
     * @return array<mixed>
     */
    public function requiredNonArrayExamples(): array
    {
        return [
            'null' => [
                null,
                'Value is required, but was null.'
            ],
            'bool' => [
                true,
                'Value was expected to be of type array, got bool(true) instead.'
            ],
            'int' => [
                42,
                'Value was expected to be of type array, got int(42) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type array, got float(47.11) instead.'
            ],
            'string' => [
                'foo',
                'Value was expected to be of type array, got string("foo") instead.'
            ],
        ];
    }

    /**
     * @dataProvider requiredNonArrayExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfArrayCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->array();
    }

    /**
     * @return array<mixed>
     */
    public function optionalNonArrayExamples(): array
    {
        return [
            'bool' => [
                true,
                'Value was expected to be of type array or null, got bool(true) instead.'
            ],
            'int' => [
                42,
                'Value was expected to be of type array or null, got int(42) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type array or null, got float(47.11) instead.'
            ],
            'string' => [
                'foo',
                'Value was expected to be of type array or null, got string("foo") instead.'
            ],
        ];
    }

    /**
     * @dataProvider optionalNonArrayExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfArrayOrNullCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->arrayOrNull();
    }
}
