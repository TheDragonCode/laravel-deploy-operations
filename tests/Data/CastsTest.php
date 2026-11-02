<?php

declare(strict_types=1);

namespace Tests\Data;

use DragonCode\LaravelDeployOperations\Data\Casts\BoolCast;
use DragonCode\LaravelDeployOperations\Data\Casts\OperationNameCast;
use PHPUnit\Framework\TestCase;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use TypeError;

class CastsTest extends TestCase
{
    protected DataProperty $property;

    protected CreationContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = $this->createStub(DataProperty::class);
        $this->context  = $this->createStub(CreationContext::class);
    }

    public function testBooleanValues(): void
    {
        $cast = new BoolCast;

        foreach ([true, 1, '1', 'true', 'TRUE', 'on', 'yes', ' yes '] as $value) {
            $this->assertTrue($cast->cast($this->property, $value, [], $this->context));
        }

        foreach ([false, 0, '0', 'false', 'FALSE', 'off', 'no', '', ' no '] as $value) {
            $this->assertFalse($cast->cast($this->property, $value, [], $this->context));
        }
    }

    public function testInvalidBooleanValueIsRejected(): void
    {
        $this->expectException(TypeError::class);

        (new BoolCast)->cast($this->property, 'invalid', [], $this->context);
    }

    public function testNullBooleanValueIsRejected(): void
    {
        $this->expectException(TypeError::class);

        (new BoolCast)->cast($this->property, null, [], $this->context);
    }

    public function testOperationNames(): void
    {
        $cast = new OperationNameCast;

        $this->assertSame('activate_articles', $cast->cast($this->property, 'ActivateArticles.php', [], $this->context));
        $this->assertSame(
            'sub_path' . DIRECTORY_SEPARATOR . 'activate_articles',
            $cast->cast($this->property, 'SubPath/ActivateArticles.php', [], $this->context)
        );
        $this->assertSame(
            'sub_path' . DIRECTORY_SEPARATOR . 'activate_articles',
            $cast->cast($this->property, 'SubPath\\ActivateArticles.php', [], $this->context)
        );
    }

    public function testEmptyOperationNames(): void
    {
        $cast = new OperationNameCast;

        foreach ([null, '', '0', 0, false, []] as $value) {
            $this->assertNull($cast->cast($this->property, $value, [], $this->context));
        }
    }
}
