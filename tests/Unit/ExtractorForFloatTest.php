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

final class ExtractorForFloatTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canBeCreatedForFloat(): void
    {
        $extractor = Extractor::for(47.11);

        $this->assertInstanceOf(Extractor::class, $extractor);
    }

    /**
     * @test
     * @return void
     */
    public function canExtractFloat(): void
    {
        $this->assertEquals(
            47.11,
            Extractor::for(47.11)->float()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractfloatOrNull(): void
    {
        $this->assertEquals(
            47.11,
            Extractor::for(47.11)->floatOrNull()
        );
    }

    /**
     * @return array<mixed>
     */
    public function requiredNonFloatExamples(): array
    {
        return [
            'null' => [
                null,
                'Value is required, but was null.'
            ],
            'bool' => [
                true,
                'Value was expected to be of type float, got bool(true) instead.'
            ],
            'int' => [
                42,
                'Value was expected to be of type float, got int(42) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type float, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type float, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider requiredNonFloatExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfFloatCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->float();
    }

    /**
     * @return array<mixed>
     */
    public function optionalNonFloatExamples(): array
    {
        return [
            'bool' => [
                true,
                'Value was expected to be of type float or null, got bool(true) instead.'
            ],
            'int' => [
                42,
                'Value was expected to be of type float or null, got int(42) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type float or null, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type float or null, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider optionalNonFloatExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfFloatOrNullCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->floatOrNull();
    }
}
