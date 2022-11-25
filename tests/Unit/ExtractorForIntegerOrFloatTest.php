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

final class ExtractorForIntegerOrFloatTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canExtractIntegerOrFloat(): void
    {
        $this->assertEquals(
            42,
            Extractor::for(42)->intOrFloat()
        );
        $this->assertEquals(
            47.11,
            Extractor::for(47.11)->intOrFloat()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractintOrNullegerOrFloat(): void
    {
        $this->assertEquals(
            42,
            Extractor::for(42)->intOrfloatOrNull()
        );
        $this->assertEquals(
            47.11,
            Extractor::for(47.11)->intOrfloatOrNull()
        );
    }

    /**
     * @return array<mixed>
     */
    public function requiredNonIntegerOrFloatExamples(): array
    {
        return [
            'null' => [
                null,
                'Value is required, but was null.'
            ],
            'bool' => [
                true,
                'Value was expected to be of type int or float, got bool(true) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type int or float, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type int or float, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider requiredNonIntegerOrFloatExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfIntegerOrFloatCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->intOrFloat();
    }

    /**
     * @return array<mixed>
     */
    public function optionalNonIntOrFloatExamples(): array
    {
        return [
            'bool' => [
                true,
                'Value was expected to be of type int or float or null, got bool(true) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type int or float or null, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type int or float or null, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider optionalNonIntOrFloatExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfIntegerOrNullOrFloatCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->intOrfloatOrNull();
    }
}
