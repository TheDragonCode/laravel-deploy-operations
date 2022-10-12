<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DragonCode\LaravelActions\Helpers\Sorter;
use Tests\TestCase;

class SorterTest extends TestCase
{
    public function testByValues(): void
    {
        $expected = [
            '2022_10_13_013321_test1',
            'foo/2022_10_13_013321_test2',
            'bar/2022_10_13_013321_test3',
            'foo/2022_10_13_013321_test4',
            'bar/2022_10_13_013321_test5',
            '2022_10_13_013321_test6',
        ];

        $values = [
            '2022_10_13_013321_test1',
            '2022_10_13_013321_test6',
            'bar/2022_10_13_013321_test3',
            'bar/2022_10_13_013321_test5',
            'foo/2022_10_13_013321_test2',
            'foo/2022_10_13_013321_test4',
        ];

        $this->assertSame($expected, $this->sorter()->byValues($values));
    }

    public function testByKeys(): void
    {
        $expected = [
            '2022_10_13_013321_test1'     => 1,
            'foo/2022_10_13_013321_test2' => 2,
            'bar/2022_10_13_013321_test3' => 3,
            'foo/2022_10_13_013321_test4' => 4,
            'bar/2022_10_13_013321_test5' => 5,
            '2022_10_13_013321_test6'     => 6,
        ];

        $values = [
            '2022_10_13_013321_test1'     => 1,
            '2022_10_13_013321_test6'     => 6,
            'bar/2022_10_13_013321_test3' => 3,
            'bar/2022_10_13_013321_test5' => 5,
            'foo/2022_10_13_013321_test2' => 2,
            'foo/2022_10_13_013321_test4' => 4,
        ];

        $this->assertSame($expected, $this->sorter()->byKeys($values));
    }

    protected function sorter(): Sorter
    {
        return new Sorter();
    }
}
