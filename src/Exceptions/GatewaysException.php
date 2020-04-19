<?php
namespace Kongflower\Pay\Exceptions;

class GatewaysException extends \Exception
{

    const UNKNOWN_ERROR = 9999;

    public function __construct($message,$code = self::UNKNOWN_ERROR)
    {
        parent::__construct($message, intval($code));
    }
}   