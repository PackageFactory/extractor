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

final class ExtractorArrayAccessTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function allowsForFirstLevelArrayAccess(): void
    {
        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        $this->assertInstanceOf(Extractor::class, $extractor['some']);
        $this->assertEquals(['deep' => ['path' => '1234']], $extractor['some']->array());
    }

    /**
     * @test
     * @return void
     */
    public function allowsForFirstLevelArrayAccessWithUnknownKeys(): void
    {
        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        $this->assertInstanceOf(Extractor::class, $extractor['foo']);
        $this->assertNull($extractor['foo']->arrayOrNull());
    }

    /**
     * @test
     * @return void
     */
    public function allowsForNthLevelArrayAccess(): void
    {
        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        $this->assertInstanceOf(Extractor::class, $extractor['some']['deep']);
        $this->assertEquals(['path' => '1234'], $extractor['some']['deep']->array());

        $this->assertInstanceOf(Extractor::class, $extractor['some']['deep']['path']);
        $this->assertEquals('1234', $extractor['some']['deep']['path']->string());
    }

    /**
     * @test
     * @return void
     */
    public function allowsForNthLevelArrayAccessWithUnknownKeys(): void
    {
        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        $this->assertInstanceOf(Extractor::class, $extractor['foo']['bar']);
        $this->assertNull($extractor['foo']['bar']->stringOrNull());

        $this->assertInstanceOf(Extractor::class, $extractor['foo']['bar']['baz']);
        $this->assertNull($extractor['foo']['bar']['baz']->stringOrNull());

        $this->assertInstanceOf(Extractor::class, $extractor['some']['foo']);
        $this->assertNull($extractor['some']['foo']->stringOrNull());

        $this->assertInstanceOf(Extractor::class, $extractor['some']['deep']['foo']);
        $this->assertNull($extractor['some']['deep']['foo']->stringOrNull());
    }

    /**
     * @test
     * @return void
     */
    public function valuesCannotBeSetWithArrayAccess(): void
    {
        $this->expectException(\LogicException::class);

        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        $extractor['some']['deep'] = 'something';
    }

    /**
     * @test
     * @return void
     */
    public function valuesCannotBeUnsetWithArrayAccess(): void
    {
        $this->expectException(\LogicException::class);

        $extractor = Extractor::for([
            'some' => [
                'deep' => [
                    'path' => '1234'
                ]
            ]
        ]);

        unset($extractor['some']['deep']);
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
     * @dataProvider nonArrayExamples
     * @test
     * @param mixed $data
     * @param string $expectedErrorMessage
     * @return void
     */
    public function throwsIfArrayAccessIsAttemptedOnNonArrayValue(mixed $data, string $expectedErrorMessage): void
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $extractor = Extractor::for($data);
        print_r($extractor['foo']);
    }

    /**
     * @test
     * @return void
     */
    public function keepsTrackOfPathWhenExtractorExceptionHappensAtADeeperLevel(): void
    {
        $this->expectException(ExtractorException::class);

        try {
            $extractor = Extractor::for(['foo' => ['bar' => ['baz' => 42]]]);
            $extractor['foo']['bar']['baz']->string();
        } catch (ExtractorException $e) {
            $this->assertEquals(
                ['foo', 'bar', 'baz'],
                $e->getPath()
            );

            throw $e;
        }
    }

    /**
     * @test
     * @return void
     */
    public function onlyKeepsTrackOfPathUntilFirstNullValueWasEncountered(): void
    {
        $this->expectException(ExtractorException::class);

        try {
            $extractor = Extractor::for(['foo' => ['bar' => null]]);
            $extractor['foo']['bar']['baz']->array();
        } catch (ExtractorException $e) {
            $this->assertEquals(
                ['foo', 'bar'],
                $e->getPath()
            );

            throw $e;
        }
    }

    /**
     * @test
     * @return void
     */
    public function exposesCurrentPathForOutsideConsumption(): void
    {
        $extractor = Extractor::for([]);

        $this->assertEquals([], $extractor->getPath());
        $this->assertEquals(['foo'], $extractor['foo']->getPath());
        $this->assertEquals(['foo', 'bar'], $extractor['foo']['bar']->getPath());
        $this->assertEquals(['foo', 'bar', 'baz'], $extractor['foo']['bar']['baz']->getPath());
    }
}
