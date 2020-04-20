<?php
namespace Kongflower\Pay\Gateways\WX;

use Kongflower\Pay\Exceptions\GatewaysException;
use Kongflower\Pay\Gateways\Support\Request;

class Weichat {
    
     /**
     * 普通模式.
     */
    const MODE_NORMAL = 'normal';

    /**
     * @author kongflower <18838952961@163.com>
     * 
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
     * 参数初始化
     */
    private function __construct()
    {}

    /**
     * 私有属性的克隆方法 防止被克隆
     */
    private function __clone()
    {}

    /**
     * 单例初始化对象
     */
    public function getInstance()
    {
        if(is_null(self::$instance) || self::$instance instanceof self){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @author kongflower <18838952961@163.com>
     * 
     * 创建weichat pay
     * @param string $appid         微信公众账号ID
     * @param string $mch_id        微信商户号
     * @param string $key           
     * @param string $body
     * @param string $out_order_no  订单号
     * @param int    $total_amount  金额，分为单位(1元=100分)
     * @param string $trade_type    H5支付的交易类型为MWEB
     * @param string $notify_url    微信回调地址
     * @param string $scene_info
     */
    public function createWeiXinUrl(array $data): array
    {
        //参与签名参数，空值不参与签名
        $params = [
            'appid'     => $data['appid'],    //微信公众账号ID
            'body'      => $data['body'],
            'mch_id'    => $data['mch_id'],  //微信商户号
            'nonce_str' => self::generateNonceStr(),    //32位以内随机字符
            'notify_url'    => $data['notify_url'],  //微信回调地址
            'out_trade_no'  => $data['out_order_no'],
            'spbill_create_ip'  => Request::getRealIp(),
            'scene_info'    => $data['scene_info'] ? $data['scene_info'] : '{"h5_info": {"type":"Wap","wap_url": "https://fortune.skinrun.cn","wap_name": "面相分析"}}',
            'total_fee'     => $data['total_amount'],  //金额，分为单位
            'trade_type'    => $data['trade_type'] ? $data['trade_type'] : 'MWEB', //H5支付的交易类型为MWEB
        ];
        // 组装签名
        $params['sign'] = self::generateSign($params,$data['key']);
        //数据转为xml格式
        $pay_param = self::toXml($params);
        // 发送请求
        $result = Request::send(self::URL[self::MODE_NORMAL].'pay/unifiedorder' ,'POST',$pay_param);
        $$result = self::toArray($result);
        if($result['return_code'] == 'SUCCESS' && $result['result_code']){
            $response = ['prepay_id' => $result['prepay_id'],'trade_type' => $result['trade_type'],'mweb_url' =>  $result['mweb_url']]; 
        }else{
            $response = ['error_code' => $resul['err_code']];
        }
        return $response;
        
    }
    /**
     * @author kongflower <18838952961@163.com>
     * 
     * array to xml string
     * @param   array   $data
     * @return  string  $xml
     */
    public static function toXml( array $data): string
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new GatewaysException('Convert To Xml Error! Invalid Array!');
        }

        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<'.$key.'>'.$val.'</'.$key.'>' : '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * @author kongflower <18838952961@163.com>
     * 
     * xml string to array
     * @param string $xmlString
     * @return array $data
     */
    public static function toArray(string $xmlString): array
    {
        if (!$xmlString) {
            throw new GatewaysException('Convert To Array Error! Invalid Xml!');
        }

        if (!$xmlString) {
            throw new GatewaysException('Convert To Array Error! Invalid Xml!');
        }

        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);

    }

    
    /**
     * @author kongflower <18838952961@163.com>
     * 
     * 随机字符串
     * @param   int     $length
     * @return  string  $str
     */
    public static function generateNonceStr(int $length = 16): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= mb_substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * @author kongflower <18838952961@163.com>
     * 
     * 签名
     * @param   array   array
     * @param   string  key  key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
     * @return  string  signValue
     */
    public static function generateSign(array $params,string $key) : string
    {
         // 过滤控制（参数的值为空不参与签名）
         foreach ($params as $k => $v) {
            if (!$v) {
                unset($params[$k]);
            }
        }
        // 对参数按照key=value的格式，并按照参数名ASCII字典序排序如下
        ksort($params);
        $stringA = '';
        foreach ($params as $k => $v) {
            $stringA = $stringA . $k . '=' . $v . '&';
        }
        // 拼接API密钥
        $stringSignTemp = $stringA . 'key=' . $key;
        $signValue = mb_strtoupper(md5($stringSignTemp));
        return $signValue;
    }
    

}