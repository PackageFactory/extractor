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

final class ExtractorForBooleanTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canBeCreatedForBoolean(): void
    {
        $extractor = Extractor::for(true);

        $this->assertInstanceOf(Extractor::class, $extractor);

        $extractor = Extractor::for(false);

        $this->assertInstanceOf(Extractor::class, $extractor);
    }

    /**
     * @test
     * @return void
     */
    public function canExtractBoolean(): void
    {
        $this->assertTrue(Extractor::for(true)->bool());
        $this->assertFalse(Extractor::for(false)->bool());
    }

    /**
     * @test
     * @return void
     */
    public function canExtractboolOrNullean(): void
    {
        $this->assertTrue(Extractor::for(true)->boolOrNull());
        $this->assertFalse(Extractor::for(false)->boolOrNull());
    }

    /**
     * @return array<mixed>
     */
    public function requiredNonBooleanExamples(): array
    {
        return [
            'null' => [
                null,
                'Value is required, but was null.'
            ],
            'int' => [
                42,
                'Value was expected to be of type bool, got int(42) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type bool, got float(47.11) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type bool, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type bool, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider requiredNonBooleanExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfBooleanCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->bool();
    }

    /**
     * @return array<mixed>
     */
    public function optionalNonBooleanExamples(): array
    {
        return [
            'int' => [
                42,
                'Value was expected to be of type bool or null, got int(42) instead.'
            ],
            'float' => [
                47.11,
                'Value was expected to be of type bool or null, got float(47.11) instead.'
            ],
            'string' => [
                'foobar',
                'Value was expected to be of type bool or null, got string("foobar") instead.'
            ],
            'array' => [
                [],
                'Value was expected to be of type bool or null, got array(length=0) instead.'
            ],
        ];
    }

    /**
     * @dataProvider optionalNonBooleanExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfBooleanOrNullCannotBeExtracted(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        Extractor::for($data)->boolOrNull();
    }
}
