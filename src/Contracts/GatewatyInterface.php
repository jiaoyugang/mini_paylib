<?php
namespace Kongflower\Pay;

interface GatewatyInterface
{
    /**
     * To payl
     */
    public function pay($gateway, $params);
    
}