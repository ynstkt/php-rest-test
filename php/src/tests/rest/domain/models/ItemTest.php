<?php
use PHPUnit\Framework\TestCase;

use rest\domain\models\Item;

final class ItemTest extends TestCase
{
    /**
     * @covers rest\domain\models\Item::__construct
     * @covers rest\domain\models\Item::getId
     * @covers rest\domain\models\Item::getName
     */
    public function testItem(): void
    {
        $item =new Item('1', 'hoge');
        $this->assertSame('1', $item->getId());
        $this->assertSame('hoge', $item->getName());
    }

    /**
     * @covers rest\domain\models\Item::__construct
     * @dataProvider invalidItemProvider
     */
    public function testInvalidId(string $id): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid id');
        $item =new Item($id, 'hoge');
    }

    public function invalidItemProvider(): array
    {
        return [
            'empty' => [''],
            'char' => ['xxx'],
            'symbol' => ['?<>\/&'],
            'number & char' => ['4xx'],
            'char & number' => ['xx4']
        ];
    }
}