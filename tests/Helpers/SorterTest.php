<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DragonCode\LaravelDeployOperations\Helpers\SorterHelper;
use Tests\TestCase;

class SorterTest extends TestCase
{
    public function testByValues(): void
    {
        $expected = [
            '2022_10_13_013321_test1',
            'foo/2022_10_13_013322_test2',
            'bar/2022_10_13_013323_test3',
            'foo/2022_10_13_013324_test4',
            'bar/2022_10_13_013325_test5',
            '2022_10_13_013326_test6',
        ];

        $values = [
            '2022_10_13_013321_test1',
            '2022_10_13_013326_test6',
            'bar/2022_10_13_013323_test3',
            'bar/2022_10_13_013325_test5',
            'foo/2022_10_13_013322_test2',
            'foo/2022_10_13_013324_test4',
        ];

        $this->assertSame($expected, $this->sorter()->byValues($values));
    }

    public function testByKeys(): void
    {
        $expected = [
            '2022_10_13_013321_test1'     => 1,
            'foo/2022_10_13_013322_test2' => 2,
            'bar/2022_10_13_013323_test3' => 3,
            'foo/2022_10_13_013324_test4' => 4,
            'bar/2022_10_13_013325_test5' => 5,
            '2022_10_13_013326_test6'     => 6,
        ];

        $values = [
            '2022_10_13_013321_test1'     => 1,
            '2022_10_13_013326_test6'     => 6,
            'bar/2022_10_13_013323_test3' => 3,
            'bar/2022_10_13_013325_test5' => 5,
            'foo/2022_10_13_013322_test2' => 2,
            'foo/2022_10_13_013324_test4' => 4,
        ];

        $this->assertSame($expected, $this->sorter()->byKeys($values));
    }

    public function testByValuesReindexesKeysAndKeepsEqualBasenamesInOrder(): void
    {
        $values = [
            10    => 'foo/2022_10_13_013322_test.php',
            'bar' => 'bar/2022_10_13_013322_test.php',
            20    => '2022_10_13_013321_test.php',
        ];

        $this->assertSame([
            '2022_10_13_013321_test.php',
            'foo/2022_10_13_013322_test.php',
            'bar/2022_10_13_013322_test.php',
        ], $this->sorter()->byValues($values));
    }

    public function testByKeysSortsNestedArraysAndKeepsEqualBasenamesInOrder(): void
    {
        $values = [
            'foo/2022_10_13_013322_test.php' => [
                '2022_10_13_013322_test.php' => 2,
                '2022_10_13_013321_test.php' => 1,
            ],
            'bar/2022_10_13_013322_test.php' => 3,
            '2022_10_13_013321_test.php'     => 4,
        ];

        $this->assertSame([
            '2022_10_13_013321_test.php'     => 4,
            'foo/2022_10_13_013322_test.php' => [
                '2022_10_13_013321_test.php' => 1,
                '2022_10_13_013322_test.php' => 2,
            ],
            'bar/2022_10_13_013322_test.php' => 3,
        ], $this->sorter()->byKeys($values));
    }

    public function testByValuesSortsNestedArrays(): void
    {
        $this->assertSame([
            ['2022_10_13_013321_test.php', 'foo/2022_10_13_013322_test.php'],
        ], $this->sorter()->byValues([
            [
                20 => 'foo/2022_10_13_013322_test.php',
                10 => '2022_10_13_013321_test.php',
            ],
        ]));
    }

    public function testByRan(): void
    {
        $items = [
            '2022_10_13_013321_test1',
            'foo/2022_10_13_013322_test2',
            'bar/2022_10_13_013323_test3',
            'foo/2022_10_13_013324_test4',
            'bar/2022_10_13_013325_test5',
            '2022_10_13_013326_test6',
        ];

        $completed = [
            '2022_10_13_013321_test1',
            'foo/2022_10_13_013324_test4',
            'bar/2022_10_13_013325_test5',
        ];

        $expected = [
            '2022_10_13_013321_test1',
            'foo/2022_10_13_013324_test4',
            'bar/2022_10_13_013325_test5',
            'foo/2022_10_13_013322_test2',
            'bar/2022_10_13_013323_test3',
            '2022_10_13_013326_test6',
        ];

        $this->assertSame($expected, $this->sorter()->byRan($items, $completed));
    }

    protected function sorter(): SorterHelper
    {
        return new SorterHelper;
    }
}
