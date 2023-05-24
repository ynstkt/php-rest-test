<?php
namespace rest\domain\models;

class AndCheck {

    public static function check(bool $bool1, bool $bool2, bool $bool3, bool $bool4)
    {
        return $bool1 && $bool2
            || $bool3 && $bool4; 
    }
}