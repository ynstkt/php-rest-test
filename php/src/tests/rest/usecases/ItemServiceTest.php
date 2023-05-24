<?php
use PHPUnit\Framework\TestCase;

use rest\usecases\ItemService;
use rest\domain\models\Item;
use rest\domain\repositories\IItemRepository;

final class ItemServiceTest extends TestCase
{    
    private $repository;
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(IItemRepository::class);
        $this->service = new ItemService($this->repository);
    }

    /**
     * @covers rest\domain\models\Item
     * @covers rest\usecases\ItemService::__construct
     * @covers rest\usecases\ItemService::getAll
     */
    public function testGetAll(): void
    {
        $items = [
            new Item('1', 'hoge'),
            new Item('2', 'fuga'),
            new Item('3', 'piyo')
        ];

        $this->repository->expects($this->once())
            ->method('getAll')
            ->willReturn($items);

        $this->assertSame($items, $this->service->getAll());
    }

    /**
     * @covers rest\domain\models\Item
     * @covers rest\usecases\ItemService::__construct
     * @covers rest\usecases\ItemService::getById
     */
    public function testGetById(): void
    {
        $item = new Item('1', 'hoge');

        $this->repository->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($item);

        $this->assertSame($item, $this->service->getById('1'));
    }

    /**
     * @covers rest\domain\models\Item
     * @covers rest\usecases\ItemService::__construct
     * @covers rest\usecases\ItemService::create
     */
    public function testCreate(): void
    {
        $item = new Item('1', 'hoge');

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->equalTo($item));

        $this->assertNull($this->service->create($item));
    }

    /**
     * @covers rest\domain\models\Item
     * @covers rest\usecases\ItemService::__construct
     * @covers rest\usecases\ItemService::update
     */
    public function testUpdate(): void
    {
        $item = new Item('1', 'hoge');

        $this->repository->expects($this->once())
            ->method('update')
            ->with($this->equalTo($item));

        $this->assertNull($this->service->update($item));
    }

    /**
     * @covers rest\usecases\ItemService::__construct
     * @covers rest\usecases\ItemService::delete
     */
    public function testDelete(): void
    {
        $this->repository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('1'));

        $this->assertNull($this->service->delete('1'));
    }
}