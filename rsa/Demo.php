<?php
/**
 * Created by PhpStorm.
 * User: 渊虹
 * Date: 2021/1/6
 * Time: 4:37 PM
 */

$config = array(
    'public_key'  => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5wW7P06pfvImsDmvPm33
6g298agFrNshL/x+E1YvVkahK44uiCOp7GX4G0mE+d6G2CqtALaD1X+nDg6owYOx
amyvhkbvogxHncmeJ977kMfTgcBDntkfwMc+cyFDvl/zlZzzWzgrETjo8UKIVcCt
14GCJ4kmihbAOkoPJPb2KOEP2eR7bhCj/h3N/iUWi9/rnYyFXPhGAqLSqJ0wDvIV
Djx6kXXMN2XpXxXt1GRlyJvY5Jk5il12FMgAhxuKCzZ0xmmj03/RHWr9r84GFAc7
XxjIqrk/HLECuqP8HA36WEd62EX7n+/XGOvX0m8hJlEKK78O6TV3zkxc7K7FdQuU
5wIDAQAB
-----END PUBLIC KEY-----',
    'private_key' => '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDnBbs/Tql+8iaw
Oa8+bffqDb3xqAWs2yEv/H4TVi9WRqErji6II6nsZfgbSYT53obYKq0AtoPVf6cO
DqjBg7FqbK+GRu+iDEedyZ4n3vuQx9OBwEOe2R/Axz5zIUO+X/OVnPNbOCsROOjx
QohVwK3XgYIniSaKFsA6Sg8k9vYo4Q/Z5HtuEKP+Hc3+JRaL3+udjIVc+EYCotKo
nTAO8hUOPHqRdcw3ZelfFe3UZGXIm9jkmTmKXXYUyACHG4oLNnTGaaPTf9Edav2v
zgYUBztfGMiquT8csQK6o/wcDfpYR3rYRfuf79cY69fSbyEmUQorvw7pNXfOTFzs
rsV1C5TnAgMBAAECggEBALLedyn/B7DoYBgVsZ+OmWK4nRZb13kMeNekkSD7m88b
BHKqtVBm/IuyL7VA6RplttXrxONGbTCCk5+IrhSWmGkkGYrHpYY8J779edy5AuII
NbTCXhjBV5p1Kd8OJFtkAz8VtD4ucPn2rDUQJGrwsiind1SRFivYjaET8kHWjKT8
lCXGBY5kTFX+hRsP+Jlq4jdDV2Sdwy5suWRORl83Cf3XyachDlMnj0FFjj3NfhMn
Mwe33KXUWtOQsYNTh79vqEx0ODsSme5ENvGyj7U3koo6SHZT6fvn0zygkLXRnjsl
5qa9fjVe00knXMkMZPBBOwW+gYIGVPaS+bMjlj1VJ+ECgYEA9T1JvZYScbRlazAU
9PHID00vTltlxyW3lp8XtdTa4fcAgENZOBndc8g07MIUNAioSaVnW6E11F47v4lB
6dhBhxFJQh/54/4apaJl2ww/uBavqCFV8rNfl7vbxIBzw2LpfY2ogM1XyRo3jyTZ
hCeDtlnm6ndUYT8+ag8c3NTKTX8CgYEA8Si/SrjLaye7YJEnG8XY4QkRjgZW0Axa
ioYtm6iU+opdgp6y77GJnh4lFevXJvXGycQHUHollH7ICxbeYgnFdIh0TIPpwRWD
9amloYBSPCXEQkPstMF1GffZ4OZ4cTxm49nGaULfh+ATt8ekQRmcbG9doVRONZTv
+q9Uc7nGvJkCgYBjnaKfhR822r07ngtVOAU42fR2Ur/z9gkuAK/D55OFqCym7TSH
ilIfHtsItQk53a5mQ+7JFKHmAuUoN1vz3ik38TidyJlcGDLAx0eyg7Y6U9TEac4c
yOnym5d4qWjcnAgWPP2OXFrlUGXzGlfUy6w/3SLkaNZ4rhTvRfZGaZVHhwKBgQDc
wFby+FvEt/cO+3AYPTA9NobqQiU/hr9upEqlfVj6SqQ0iD71qMd3hNKf0WX+VObm
FNo5Mcaquq81b3abuaQU+z+yNuJIDADiZKuoPYWJZ2zS18ia4afm4HmCJJZF2Wu5
0MSN4Fgr0dNa3JYfvWjx3bOQlVKOO8q857ffw0QHqQKBgBlTLxJRqG2vbtOiDuBO
XBWiDa4FOJPn8jHiv+xbfFbSuFcG/emYCG6+wQ8ItAL6g4WoqkLNXilKk6GhQKGp
wksyrGWaBp1Ep2iCsWXE3bIMALdevu0FOe0JUQJzYSjEhyVW5IgqOwq+UUWBmSEv
KLwq3rGGzXriwMY+RCDBtkRl
-----END PRIVATE KEY-----'
);

require_once './Rsa.php';

try {
    $rsa = new Rsa($config);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit();
}

$ori_str = 'hello,world';

echo '########### 公钥加密，私钥解密 ###########' . PHP_EOL;
// 公钥加密，私钥解密
try {
    $encrypt_str = $rsa->encode_with_public_key($ori_str);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit();
}
try {
    $decrypt_str = $rsa->decode_with_private_key($encrypt_str);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit();
}

echo '原始字符串: ' . $ori_str . PHP_EOL;
echo '加密后的字符串: ' . $encrypt_str . PHP_EOL;
echo '解密后的字符串: ' . $decrypt_str . PHP_EOL;

// 私钥加密，公钥解密
echo '########### 私钥加密，公钥解密 ###########' . PHP_EOL;
try {
    $encrypt_str = $rsa->encode_with_public_key($ori_str);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit();
}
try {
    $decrypt_str = $rsa->decode_with_private_key($encrypt_str);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit();
}

echo '原始字符串: ' . $ori_str . PHP_EOL;
echo '加密后的字符串: ' . $encrypt_str . PHP_EOL;
echo '解密后的字符串: ' . $decrypt_str . PHP_EOL;
