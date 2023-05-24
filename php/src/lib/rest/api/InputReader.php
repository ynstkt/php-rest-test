<?php
namespace rest\api;

/**
 * @codeCoverageIgnore
 */
class InputReader
{
    public function getInputStream()
    {
        return file_get_contents('php://input');
    }
}
