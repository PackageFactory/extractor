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
use PHPUnit\Framework\TestCase;

final class ExtractorForNullTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function canBeCreatedForNull(): void
    {
        $extractor = Extractor::for(null);

        $this->assertInstanceOf(Extractor::class, $extractor);
    }

    /**
     * @test
     * @return void
     */
    public function canExtractBooleanOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->boolOrNull()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractIntegerOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->intOrNull()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractFloatOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->floatOrNull()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractIntegerOrFloatOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->intOrfloatOrNull()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractStringOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->stringOrNull()
        );
    }

    /**
     * @test
     * @return void
     */
    public function canExtractArrayOrNull(): void
    {
        $this->assertNull(
            Extractor::for(null)->arrayOrNull()
        );
    }
}
