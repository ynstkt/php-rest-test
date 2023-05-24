<?php
use PHPUnit\Framework\TestCase;

use rest\api\items\ItemController;
use rest\domain\models\Item;
use rest\usecases\{IItemService, ItemNotFoundException, DatabaseException};

final class ItemControllerTest extends TestCase
{
    public static $backup;
    public static $capture;

    public static function setUpBeforeClass(): void
    {
        self::$capture = tmpfile();
        self::$backup = ini_set('error_log', stream_get_meta_data(self::$capture)['uri']);
    }

    public static function tearDownAfterClass(): void
    {
        ini_set('error_log', self::$backup);
    }

    private $service;
    private $controller;

    protected function setUp(): void
    {
        $this->service = $this->createMock(IItemService::class);
        $this->controller = new ItemController($this->service);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetAll(): void
    {
        $itemsArr = [
            'items'=>[
                [
                    'id' => '1',
                    'name' => 'hoge'
                ],
                [
                    'id' => '2',
                    'name' => 'fuga'
                ],
                [
                    'id' => '3',
                    'name' => 'piyo'
                ]
            ]
        ];
        $items = [
            new Item('1', 'hoge'),
            new Item('2', 'fuga'),
            new Item('3', 'piyo')
        ];

        $this->service->expects($this->once())
            ->method('getAll')
            ->willReturn($items);

        ob_start();

        $this->controller->getAll();
        $output = ob_get_contents();

        $this->assertEquals(200, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals(json_encode($itemsArr, JSON_PRETTY_PRINT), $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetAllFailure(): void
    {
        $this->service->expects($this->once())
            ->method('getAll')
            ->will($this->throwException(new DatabaseException('hoge')));

        ob_start();
        
        $this->controller->getAll();
        $output = ob_get_contents();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/接続失敗: rest\\\\usecases\\\\DatabaseException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetById(): void
    {
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $item = new Item('1', 'hoge');

        $this->service->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($item);

        ob_start();

        $this->controller->getById('1');
        $output = ob_get_contents();

        $this->assertEquals(200, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals(json_encode($itemArr, JSON_PRETTY_PRINT), $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetByIdFailure(): void
    {
        $this->service->expects($this->once())
            ->method('getById')
            ->will($this->throwException(new DatabaseException('hoge')));

        ob_start();
        
        $this->controller->getById('1');
        $output = ob_get_contents();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/接続失敗: rest\\\\usecases\\\\DatabaseException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetByIdNotFound(): void
    {
        $this->service->expects($this->once())
            ->method('getById')
            ->will($this->throwException(new ItemNotFoundException('hoge')));

        ob_start();
        
        $this->controller->getById('1');
        $output = ob_get_contents();

        $this->assertEquals(404, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPost(): void
    {
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $item = new Item('1', 'hoge');

        $this->service->expects($this->once())
            ->method('create')
            ->with($this->equalTo($item));

        ob_start();

        $this->controller->post($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(201, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPostFailure(): void
    {
        $this->service->expects($this->once())
            ->method('create')
            ->will($this->throwException(new DatabaseException('hoge')));

        ob_start();
        
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->controller->post($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/接続失敗: rest\\\\usecases\\\\DatabaseException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPostInvalid(): void
    {
        $this->service->expects($this->once())
            ->method('create')
            ->will($this->throwException(new InvalidArgumentException('hoge')));

        ob_start();
        
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->controller->post($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(400, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/ID不正: InvalidArgumentException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPut(): void
    {
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $item = new Item('1', 'hoge');

        $this->service->expects($this->once())
            ->method('update')
            ->with($this->equalTo($item));

        ob_start();

        $this->controller->put($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(204, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPutFailure(): void
    {
        $this->service->expects($this->once())
            ->method('update')
            ->will($this->throwException(new DatabaseException('hoge')));

        ob_start();
        
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->controller->put($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/接続失敗: rest\\\\usecases\\\\DatabaseException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPutNotFound(): void
    {
        $this->service->expects($this->once())
            ->method('update')
            ->will($this->throwException(new ItemNotFoundException('hoge')));

        ob_start();
        
        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->controller->put($itemArr);
        $output = ob_get_contents();

        $this->assertEquals(404, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDelete(): void
    {
        $this->service->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('1'));

        ob_start();

        $this->controller->delete('1');
        $output = ob_get_contents();

        $this->assertEquals(204, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDeleteFailure(): void
    {
        $this->service->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new DatabaseException('hoge')));

        ob_start();
        
        $this->controller->delete('1');
        $output = ob_get_contents();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        $this->assertMatchesRegularExpression(
            '/接続失敗: rest\\\\usecases\\\\DatabaseException: hoge/',
            stream_get_contents(self::$capture)
        );

        ob_end_clean();
    }
}