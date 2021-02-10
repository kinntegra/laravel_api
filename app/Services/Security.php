<?php

namespace App\Services;

class Security
{

    public static function encryptData($data)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'OWswc2x4TmE1VjZRbkdDa3hPMWFXUT09';

        $secret_iv = '29M21a04m06C';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_encrypt($data, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    public static function decryptData($data)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'OWswc2x4TmE1VjZRbkdDa3hPMWFXUT09';

        $secret_iv = '29M21a04m06C';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = openssl_decrypt(base64_decode($data), $encrypt_method, $key, 0, $iv);

        return $output;
    }

}
