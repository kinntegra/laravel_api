<?php

namespace App\Services;

use Config;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Config as FacadesConfig;
use Illuminate\Support\Facades\Crypt;

class MyServices
{

    /*
    * Used to capture client IP Address
    */
    public static function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }


    /**
     * This method handels Encrypted code with AES Algorythm Custom Method
     * @return  Object
     * @since   2018-07-25
     * @author  NetQuick
     */

    public static function getEncryptedString($plaintext, $getAppKeyFromEnv = false)
    {
        $encryptedSring = '';
        if (!empty($plaintext)) {

            //$envKey = Config::get('Constant.ENV_APP_KEY');
            $envKey = FacadesConfig::get('app.key');
            if ($getAppKeyFromEnv) {
                $envKey = env('APP_KEY');
            }
            $method = 'aes-256-cbc';

            // Must be exact 32 chars (256 bit)
            $secureEnvKey = substr(hash('sha256', $envKey, true), 0, 32);

            // IV must be exact 16 chars (128 bit)
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

            $encryptedSring = base64_encode(openssl_encrypt($plaintext, $method, $secureEnvKey, OPENSSL_RAW_DATA, $iv));
        }
        return $encryptedSring;
    }

    /**
     * This method handels Decrypted code with AES Algorythm Custom Method
     * @return  Object
     * @since   2018-07-25
     * @author  NetQuick
     */

    public static function getDecryptedString($encrypted, $getAppKeyFromEnv = false)
    {
        $decryptedSring = '';
        if (!empty($encrypted)) {

            //$envKey = Config::get('app.key');
            $envKey = FacadesConfig::get('app.key');
            if ($getAppKeyFromEnv) {
                $envKey = env('APP_KEY');
            }
            $method = 'aes-256-cbc';

            // Must be exact 32 chars (256 bit)
            $secureEnvKey = substr(hash('sha256', $envKey, true), 0, 32);

            // IV must be exact 16 chars (128 bit)
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

            $decryptedSring = openssl_decrypt(base64_decode($encrypted), $method, $secureEnvKey, OPENSSL_RAW_DATA, $iv);
        }
        return $decryptedSring;
    }

    public static function getLaravelEncryptedString($plaintext)
    {
        $encryptedSring = '';
        if (!empty($plaintext)) {
            $encryptedSring = Crypt::encrypt($plaintext);
        }
        return $encryptedSring;
    }

    public static function getLaravelDecryptedString($encrypted)
    {
        $decryptedSring = '';
        if (!empty($encrypted)) {
            try {
                $decryptedSring = Crypt::decrypt($encrypted);
            } catch (DecryptException $e) {
                $decryptedSring = '';
            }
        }
        return $decryptedSring;
    }

    public static function getencryptNo($plaintext)
    {
        $encryptedSring = '';
        $no = env('APP_NO');

        if (!empty($plaintext)) {
            $encrypt = ($plaintext*$no);

            $encryptedSring = urlencode(base64_encode($encrypt));
        }
        return $encryptedSring;
    }

    public static function getdecryptNo($encrypted)
    {
        $decryptedSring = '';
        if (!empty($encrypted)) {
            try {
                $decrypt = base64_decode(urldecode($encrypted));
                $no = env('APP_NO');
                $decryptedSring = ($decrypt/$no);

            } catch (DecryptException $e) {
                $decryptedSring = '';
            }
        }
        return $decryptedSring;
    }
}

