<?php
namespace kongflower\pay;

interface GatewatyInterface
{
    /**
     * To payl
     */
    public function pay($gateway, $params);
    
}