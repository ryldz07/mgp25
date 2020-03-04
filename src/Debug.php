<?php

namespace InstagramAPI;

class Debug
{
    public static function printRequest(
        $method,
        $endpoint)
    {
        if (PHP_SAPI === 'cli') {
            $method = Utils::colouredString("{$method}:  ", 'light_blue');
        } else {
            $method = $method.':  ';
        }
        echo $method.$endpoint."\n";
    }

    public static function printUpload(
        $uploadBytes)
    {
        if (PHP_SAPI === 'cli') {
            $dat = Utils::colouredString('→ '.$uploadBytes, 'yellow');
        } else {
            $dat = '→ '.$uploadBytes;
        }
        echo $dat."\n";
    }

    public static function printHttpCode(
        $httpCode,
        $bytes)
    {
        if (PHP_SAPI === 'cli') {
            echo Utils::colouredString("← {$httpCode} \t {$bytes}", 'green')."\n";
        } else {
            echo "← {$httpCode} \t {$bytes}\n";
        }
    }

    public static function printResponse(
        $response,
        $truncated = false)
    {
        if (PHP_SAPI === 'cli') {
            $res = Utils::colouredString('RESPONSE: ', 'cyan');
        } else {
            $res = 'RESPONSE: ';
        }
        if ($truncated && mb_strlen($response, 'utf8') > 1000) {
            $response = mb_substr($response, 0, 1000, 'utf8').'...';
        }
        echo $res.$response."\n\n";
    }

    public static function printPostData(
        $post)
    {
        $gzip = mb_strpos($post, "\x1f"."\x8b"."\x08", 0, 'US-ASCII') === 0;
        if (PHP_SAPI === 'cli') {
            $dat = Utils::colouredString(($gzip ? 'DECODED ' : '').'DATA: ', 'yellow');
        } else {
            $dat = 'DATA: ';
        }
        echo $dat.urldecode(($gzip ? zlib_decode($post) : $post))."\n";
    }
}
