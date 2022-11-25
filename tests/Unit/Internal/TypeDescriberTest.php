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

namespace PackageFactory\Extractor\Tests\Unit\Internal;

use PackageFactory\Extractor\Internal\TypeDescriber;
use PHPUnit\Framework\TestCase;

final class TypeDescriberTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function describesBooleans(): void
    {
        $this->assertEquals(
            'bool(true)',
            TypeDescriber::describeTypeOf(true)
        );
        $this->assertEquals(
            'bool(false)',
            TypeDescriber::describeTypeOf(false)
        );
    }

    /**
     * @test
     * @return void
     */
    public function describesIntegers(): void
    {
        $this->assertEquals(
            'int(42)',
            TypeDescriber::describeTypeOf(42)
        );
    }

    /**
     * @test
     * @return void
     */
    public function describesFloats(): void
    {
        $this->assertEquals(
            'float(47.11)',
            TypeDescriber::describeTypeOf(47.11)
        );
    }

    /**
     * @test
     * @return void
     */
    public function describesStrings(): void
    {
        $this->assertEquals(
            'string("foo")',
            TypeDescriber::describeTypeOf('foo')
        );
        $this->assertEquals(
            'string("waytoolong...")',
            TypeDescriber::describeTypeOf('waytoolongstring')
        );
    }

    /**
     * @test
     * @return void
     */
    public function describesArrays(): void
    {
        $this->assertEquals(
            'array(length=0)',
            TypeDescriber::describeTypeOf([])
        );
        $this->assertEquals(
            'array([int(1)])',
            TypeDescriber::describeTypeOf([1])
        );
        $this->assertEquals(
            'array([string("two")])',
            TypeDescriber::describeTypeOf(['two'])
        );
        $this->assertEquals(
            'array([int(1), ...], length=4)',
            TypeDescriber::describeTypeOf([1, 'two', null, true])
        );
        $this->assertEquals(
            'array(["foo" => int(1234)])',
            TypeDescriber::describeTypeOf(['foo' => 1234])
        );
        $this->assertEquals(
            'array(["foo" => int(1234), ...], length=2)',
            TypeDescriber::describeTypeOf(['foo' => 1234, 'bar' => 5678])
        );
        $this->assertEquals(
            'array(["waytoolong..." => int(1234)])',
            TypeDescriber::describeTypeOf(['waytoolongkey' => 1234])
        );
        $this->assertEquals(
            'array(["deeply" => [...]])',
            TypeDescriber::describeTypeOf([
                'deeply' => [
                    'nested' => [
                        'foo' => 1234
                    ]
                ]
            ])
        );
        $this->assertEquals(
            'array([[...]])',
            TypeDescriber::describeTypeOf([[1, 2, 3, 4]])
        );
    }
}
