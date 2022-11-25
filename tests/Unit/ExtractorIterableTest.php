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

final class ExtractorIterableTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function allowsIterationIfDataIsArrayAndProvidesExtractorsForKeysAndValues(): void
    {
        $extractor = Extractor::for([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);

        $count = 0;
        foreach ($extractor as $key => $value) {
            $count++;
            $this->assertIsString($key->string());
            $this->assertIsInt($value->int());
        }

        $this->assertEquals(3, $count);
    }

    /**
     * @test
     * @return void
     */
    public function allowsIterationIfDataIsNull(): void
    {
        $this->expectNotToPerformAssertions();

        $extractor = Extractor::for(null);

        foreach ($extractor as $key => $value) {
            $key->string();
            $value->int();
        }
    }

    /**
     * @test
     * @return void
     */
    public function throwsIfKeyIsIntegerButWasAttemptedToBeExtractedAsString(): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage('Key was expected to be of type string, got int(0) instead.');

        $extractor = Extractor::for(['foo', 'bar']);

        foreach ($extractor as $key => $value) {
            $key->string();
        }
    }

    /**
     * @test
     * @return void
     */
    public function throwsIfKeyIsStringButWasAttemptedToBeExtractedAsInteger(): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage('Key was expected to be of type int, got string("foo") instead.');

        $extractor = Extractor::for(['foo' => 'bar']);

        foreach ($extractor as $key => $value) {
            $key->int();
        }
    }

    /**
     * @return array<mixed>
     */
    public function nonArrayExamples(): array
    {
        return [
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
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfIterationIsAttemptedOnNonArrayData(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $extractor = Extractor::for($data);

        foreach ($extractor as $key => $value) {
            $key->string();
            $value->string();
        }
    }
}
