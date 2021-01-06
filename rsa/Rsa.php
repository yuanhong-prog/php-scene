<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2020/12/24
 * Time: 4:40 PM
 *
 * 加密规则说明:
 * 先将需要加密的字符串整体做base64编码，然后做RSA分组加密，针对加密数组再进行序列化，序列化后再进行base64编码得到最终加密字符串
 */

class Rsa {

    private $private_key;

    private $public_key;

    private $per = 96; // 分组字符上限

    /**
     * Rsa constructor.
     * @param $config => array (
     *      'public_key'  => '',
     *      'private_key' => ''
     * )
     * @throws Exception
     */
    public function __construct($config) {
        if (!isset($config['private_key']) && !isset($config['public_key'])) {
            throw new Exception('init ras fail, lose key files');
        }
        if (empty($config['private_key']) && empty($config['public_key'])) {
            throw new Exception('init ras fail, all key is empty');
        }

        $this->private_key = $config['private_key'];
        $this->public_key  = $config['public_key'];
    }

    /**
     * 私钥加密
     * @param $data string 要加密的数据
     * @return string      加密后的字符串
     * @throws Exception
     */
    public function encode_with_private_key($data) {
        $encrypted = '';
        $this->is_key_exists(2);
        $private_key = openssl_pkey_get_private($this->private_key);
        $fstr = array();
        $array_data = $this->split_encode($data);
        foreach ($array_data as $value) {
            openssl_private_encrypt($value, $encrypted, $private_key);
            $fstr[] = $encrypted;
        }
        return base64_encode(serialize($fstr));
    }

    /**
     * 公钥加密
     * @param $data string 要加密的数据
     * @return string      加密后的字符串
     * @throws Exception
     */
    public function encode_with_public_key($data) {
        $encrypted = '';
        $this->is_key_exists(1);
        $public_key = openssl_pkey_get_public($this->public_key);
        $fstr = array();
        $array_data = $this->split_encode($data);
        foreach ($array_data as $value) {
            openssl_public_encrypt($value, $encrypted, $public_key);
            $fstr[] = $encrypted;
        }
        return base64_encode(serialize($fstr));
    }

    /**
     * 用公钥解密私钥加密内容
     * @param $data string  要解密的数据
     * @return bool|string  解密后的字符串
     * @throws Exception
     */
    public function decode_with_public_key($data) {
        $decrypted = '';
        $this->is_key_exists(1);
        $public_key = openssl_pkey_get_public($this->public_key);
        $array_data = $this->_toArray($data);
        $str = '';
        foreach ($array_data as $value) {
            openssl_public_decrypt($value, $decrypted, $public_key);
            $str .= $decrypted;
        }
        return base64_decode($str);
    }

    /**
     * 用私钥解密公钥加密内容
     * @param $data string 要解密的数据
     * @return bool|string 解密后的字符串
     * @throws Exception
     */
    public function decode_with_private_key($data) {
        $decrypted = '';
        $this->is_key_exists(2);
        $private_key = openssl_pkey_get_private($this->private_key);
        $array_data = $this->_toArray($data);
        $str = '';
        foreach ($array_data as $value) {
            openssl_private_decrypt($value, $decrypted, $private_key);
            $str .= $decrypted;
        }
        return base64_decode($str);
    }

    /**
     * 检查是否 含有所需配置文件
     * @param int 1 公钥 2 私钥
     * @throws Exception
     */
    private function is_key_exists($type) {
        switch ($type) {
            case 1:
                if (empty($this->public_key)) {
                    throw new \Exception('请配置公钥');
                }
                break;
            case 2:
                if (empty($this->private_key)) {
                    throw new \Exception('请配置私钥');
                }
                break;
            default:
        }
    }

    /**
     * 分组, rsa每次加密的个数不得超过100个
     *
     * @param $data
     * @return array
     */
    private function split_encode($data) {
        $return = array();
        $data = base64_encode($data);
        $total_len = strlen($data);
        $dy = $total_len % $this->per;
        $total_block = $dy ? ($total_len / $this->per) : ($total_len / $this->per - 1);
        $total_block = intval($total_block + 1);
        for ($i = 0; $i < $total_block; $i++) {
            $return[] = substr($data, $i * $this->per, $this->per);
        }
        return $return;
    }

    /**
     * 公钥加密并用 base64 serialize 过的 data
     *
     * @param $data = base64 serialize 过的 data
     * @return mixed
     * @throws Exception
     */
    private function _toArray($data) {
        $data = base64_decode($data);
        $array_data = unserialize($data);
        if (!is_array($array_data)) {
            throw new \Exception('数据加密不符');
        }
        return $array_data;
    }
}