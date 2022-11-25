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

final class ExtractorForIntegerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canBeCreatedForInteger(): void
    {
        $extractor = Extractor::for(42);

        $this->assertInstanceOf(Extractor::class, $extractor);
    }

    /**
     * @test
     * @return void
     */
    public function canExtractInteger(): void
    {
        $this->assertEquals(
            42,
            Extractor::for(42)->int()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractintOrNulleger(): void
    {
        $this->assertEquals(
            42,
            Extractor::for(42)->intOrNull()
        );
    }

    /**
     * @return array<mixed>
     */
    public function requiredNonIntegerExamples(): array
    {
        return [
            'null' => [
                null,
                'Value is required, but was null.'
            ],
            'bool' => [
                true,
                'Value was expected to be of type int, got bool(true) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type int, got float(47.11) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type int, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type int, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider requiredNonIntegerExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfIntegerCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->int();
    }

    /**
     * @return array<mixed>
     */
    public function optionalNonIntegerExamples(): array
    {
        return [
            'bool' => [
                true,
                'Value was expected to be of type int or null, got bool(true) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type int or null, got float(47.11) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type int or null, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type int or null, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider optionalNonIntegerExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfIntegerOrNullCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->intOrNull();
    }
}
