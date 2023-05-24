<?php
use PHPUnit\Framework\TestCase;

use rest\api\RestHandler;
use rest\api\InputReader;
use rest\api\RestController;

final class RestHandlerTest extends TestCase
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

    private $controller;
    private $reader;
    private $handler;

    protected function setUp(): void
    {
        $this->controller = $this->createMock(RestController::class);
        $this->reader = $this->createMock(InputReader::class);
        $this->handler = new RestHandler($this->controller, $this->reader);
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testGetAll(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->controller->expects($this->once())
            ->method('getAll');

        $this->handler->handle();
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testGetById(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->controller->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'));

        $this->handler->handle();
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testPost(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->reader->expects($this->once())
            ->method('getInputStream')
            ->willReturn(json_encode($itemArr));

        $this->controller->expects($this->once())
            ->method('post')
            ->with($this->equalTo($itemArr));

        $this->handler->handle();
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testPostInvalid(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $itemStr = <<<__EOS__
        {
            "id": "4",
            "name": "fugafuga",
        }
        __EOS__;
        $this->reader->expects($this->once())
            ->method('getInputStream')
            ->willReturn($itemStr);
        
        $this->controller->expects($this->once())
            ->method('response')
            ->with($this->equalTo(400));

        $this->handler->handle();
        
        $this->assertMatchesRegularExpression(
            '/JSON不正: JsonException:/',
            stream_get_contents(self::$capture)
        );
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testPut(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/1';
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $itemArr = [
            'id' => '1',
            'name' => 'hoge'
        ];
        $this->reader->expects($this->once())
            ->method('getInputStream')
            ->willReturn(json_encode($itemArr));

        $this->controller->expects($this->once())
            ->method('put')
            ->with($this->equalTo($itemArr));

        $this->handler->handle();
    }

    /**
     * @covers rest\api\RestHandler::__construct
     * @covers rest\api\RestHandler::handle
     */
    public function testDelete(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/api/items/index.php';
        $_SERVER['REQUEST_URI'] = '/api/items/1';
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $this->controller->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('1'));

        $this->handler->handle();
    }
}