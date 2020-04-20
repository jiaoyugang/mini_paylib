<?php
namespace Kongflower\Pay\Gateway\tt;

class Ttiao
{
    /**
     * @author kongflower <18838952961@163.com>
     * 
     * Const url.
     */
    const URL = 'https://api.mch.weixin.qq.com/';

     /**
     * 生成字节跳动小程序sign签名
     * @author kongflower <18838952961@163.com>
     * 
     * @param $params
     * @param $charset
     * @return string
     */
    public static function createSign($params,$app_secret, $charset='utf-8')
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            // 空值，risk_info, sign 不参与签名
            if (!$v || in_array($k, ["risk_info", "sign"])) {
                continue;
            }
            if (false === self::checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = self::characet($v, $charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        $stringToBeSigned .= $app_secret;
        return md5($stringToBeSigned);
    }

    /**
     * 校验$value是否非空
     *
     * @param $value
     * @return  boolean;
     *  if not set ,return true;
     *  if is null , return true;
     **/
    private static function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }
    
    /**
     * 转换字符集编码
     *
     * @param $data
     * @param $targetCharset
     * @return string
     */
    private static function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = "UTF-8";
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
}
