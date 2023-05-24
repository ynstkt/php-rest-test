<?php
use PHPUnit\Framework\TestCase;

use rest\api\RestController;

final class RestControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = $this->createPartialMock(RestController::class, [
            'getAll',
            'getById',
            'post',
            'put',
            'delete'
        ]);
    }

    /**
     * @covers rest\api\RestController::response
     * @runInSeparateProcess
     */
    public function testResponse(): void
    {
        ob_start();

        $this->controller->response(200);
        $output = ob_get_contents();

        $this->assertEquals(200, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals('', $output);

        ob_end_clean();
    }

    /**
     * @covers rest\api\RestController::response
     * @runInSeparateProcess
     */
    public function testResponseWithData(): void
    {
        ob_start();

        $data = [
            'hoge'=>[
                'a'=>'あ',
                'i'=>'い'
            ]
        ];

        $this->controller->response(200, $data);
        $output = ob_get_contents();

        $this->assertEquals(200, http_response_code());
        $this->assertEquals([
            'Content-Type: application/json; charset=UTF-8'
        ], xdebug_get_headers());
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $output);

        ob_end_clean();
    }
}