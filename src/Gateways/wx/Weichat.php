<?php
namespace Kongflower\Pay\Gateway\wx;

use Kongflower\Pay\Exceptions\GatewaysException;

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
     * Instance
     */
    private static $instance;


    /**
     *
     * @author kongflower <18838952961@qq.com>
     *
     * @throws GatewaysException
     *
     * @var array
     */
    protected $config;

    public function __construct($config){

        if (is_null(self::$instance) || !self::$instance instanceof self) {
            throw new GatewaysException('You Should [Create] First Before Using');
        }else{
            self::$instance = new self($config);
        }

        return self::$instance;
    } 

    /**
     * array to xml string
     */
    public static function toXml($data): string
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new GatewaysException('Convert To Xml Error! Invalid Array!');
        }

        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<'.$key.'>'.$val.'</'.$key.'>' :
                                       '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * xml string to array
     */
    public static function toArray($data): array
    {
        if (!$data) {
            throw new GatewaysException('Convert To Array Error! Invalid Xml!');
        }

        if (!$data) {
            throw new GatewaysException('Convert To Array Error! Invalid Xml!');
        }

        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);

    }

}