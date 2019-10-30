<?php
/**
 * Created by PhpStorm.
 * User: klinson
 * Date: 19-3-29
 * Time: 下午1:37
 */

namespace App\Handlers;


class DesHandler
{
    /**
     * @var string $method 加解密方法，可通过 openssl_get_cipher_methods() 获得
     */
    protected $method;

    /**
     * @var string $key 加解密的密钥
     */
    protected $key;

    /**
     * @var string $output 输出格式 无、base64、hex
     */
    protected $output;

    /**
     * @var string $iv 加解密的向量
     */
    protected $iv;

    /**
     * @var string $options
     */
    protected $options;

    // output 的类型
    const OUTPUT_NULL = '';
    const OUTPUT_BASE64 = 'base64';
    const OUTPUT_HEX = 'hex';


    const METHOD_OPTIONS = [
        1 => '[ECB] DES-ECB',
        2 => '[ECB] DES-EDE3',
        3 => '[CBC] DES-CBC',
        4 => '[CBC] DES-EDE3-CBC',
        5 => '[CBC] DESX-CBC',
        6 => '[CFB] DES-CFB8',
        7 => '[CFB] DES-EDE3-CFB8',
    ];

    const METHODS = [
        1 => 'DES-ECB',
        2 => 'DES-EDE3',
        3 => 'DES-CBC',
        4 => 'DES-EDE3-CBC',
        5 => 'DESX-CBC',
        6 => 'DES-CFB8',
        7 => 'DES-EDE3-CFB8',
    ];

    /**
     * DES constructor.
     * @param string $key
     * @param string $method
     *      ECB DES-ECB、DES-EDE3 （为 ECB 模式时，$iv 为空即可）
     *      CBC DES-CBC、DES-EDE3-CBC、DESX-CBC
     *      CFB DES-CFB8、DES-EDE3-CFB8
     *      CTR
     *      OFB
     *
     * @param string $output
     *      base64、hex
     *
     * @param string $iv
     * @param int $options
     */
    public function __construct($key, $method = 'DES-ECB', $output = '', $iv = '', $options = OPENSSL_RAW_DATA)
    {
        $this->key = $key;
        $this->method = $method;
        $this->output = $output;
        if (in_array($method, ['DES-ECB', 'DES-EDE3'])) {
            $this->iv = '';
        } else {
            $this->iv = $iv;
        }
        $this->options = $options;
    }

    /**
     * 加密
     *
     * @param $str
     * @return string
     */
    public function encrypt($str)
    {
        $sign = openssl_encrypt($str, $this->method, $this->key, $this->options, $this->iv);

        if ($this->output == self::OUTPUT_BASE64) {
            $sign = base64_encode($sign);
        } else if ($this->output == self::OUTPUT_HEX) {
            $sign = bin2hex($sign);
        }

        return $sign;
    }

    /**
     * 解密
     *
     * @param $encrypted
     * @return string
     */
    public function decrypt($encrypted)
    {
        if ($this->output == self::OUTPUT_BASE64) {
            $encrypted = base64_decode($encrypted);
        } else if ($this->output == self::OUTPUT_HEX) {
            $encrypted = hex2bin($encrypted);
        }

        $sign = @openssl_decrypt($encrypted, $this->method, $this->key, $this->options, $this->iv);
        $sign = rtrim($sign);
        return $sign;
    }
}