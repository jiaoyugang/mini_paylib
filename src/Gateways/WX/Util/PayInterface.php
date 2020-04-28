<?php

abstract class PayInterface
{
    /**统一下单接口 */
    abstract public function unifiedorder();


    /**订单查询接口 */
    abstract public function orderquery();

    
}