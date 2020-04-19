<?php
namespace kongflower\weixin;

class Weichat {
    
     /**
     * 普通模式.
     */
    const MODE_NORMAL = 'normal';

     /**
     * Const url.
     */
    const URL = [
        self::MODE_NORMAL => 'https://api.mch.weixin.qq.com/',
    ];

    /**
     * Wechat payload.
     *
     * @var array
     */
    protected $payload;

    private function __construct(){

    } 


}