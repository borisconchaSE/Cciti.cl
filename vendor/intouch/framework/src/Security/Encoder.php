<?php

namespace Intouch\Framework\Security;

class Encoder 
{
    private $skey;
    private $salt;
    private $method;

    const BASE64_ENCRYPTION_KEY = 'hxr48j96scXNYAMOSiRmtgR9JP3FDRuXGCUfhFhaGlI';
    const BASE64_IV = 'XbrkZI/bcS4d6v176acNSA';

    // Implementacion de singleton
    //
    private static $_instance = null;
	
    private function __construct()
    {
        $this->method  = 'aes-256-cbc';
        $this->skey = 'hxr48j96scXNYAMOSiRmtgR9JP3FDRuXGCUfhFhaGlI';
        $this->salt = 'XbrkZI/bcS4d6v176acNSA';
        // $this->skey = 'G1fM0aXhguJ5tVaqVMJOVHB+Jk6QFd99FgkfAcEgwjI';
        // $this->salt = 'xIkaHuquZFjtP4SI4mIyOg';
    }

    public static function Instance() {

        if (!isset(self::$_instance)) {
            self::$_instance = new Encoder();
        }

        return self::$_instance;
    }

    //const OPEN_SSL_METHOD = 'aes-256-cbc';
    // Generate a 256-bit encryption key
    //const BASE64_ENCRYPTION_KEY = 'G1fM0aXhguJ5tVaqVMJOVHB+Jk6QFd99FgkfAcEgwjI';//base64_encode(openssl_random_pseudo_bytes(32));
    //const BASE64_IV = 'xIkaHuquZFjtP4SI4mIyOg';//base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC)));
 
    private function base64_url_encode($input) {
        return strtr(base64_encode($input), '+/=', '-_,');
    }
 
    private function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_,', '+/='));
    } 

    function Encode($message) {

        // Encriptar con el metodo configurado
        //
        $encrypted = openssl_encrypt($message, $this->method, base64_decode(self::BASE64_ENCRYPTION_KEY), 0, base64_decode(self::BASE64_IV));

        // Volver a encriptar con Base64
        //
        $base64_encrypted = $this->base64_url_encode($encrypted);

        return $base64_encrypted;
    }
 
    function Decode($encrypted) {

        // Decodificar el base64
        //
        $base64_decrypted = $this->base64_url_decode($encrypted);

        // Desencriptar con el metodo configurado
        //
        $decrypted = openssl_decrypt($base64_decrypted, $this->method, base64_decode(self::BASE64_ENCRYPTION_KEY), 0, base64_decode(self::BASE64_IV));

        return $decrypted;
    }
 
    // function Encode($message) {

    //     // Encriptar con el metodo configurado
    //     //
    //     $encrypted = openssl_encrypt($message, $this->method, base64_decode($this->key), 0, base64_decode($this->salt));

    //     // Volver a encriptar con Base64
    //     //
    //     $base64_encrypted = $this->base64_url_encode($encrypted);

    //     return $base64_encrypted;
    // }
 
    // function Decode($encrypted) {

    //     // Decodificar el base64
    //     //
    //     $base64_decrypted = $this->base64_url_decode($encrypted);

    //     // Desencriptar con el metodo configurado
    //     //
    //     $decrypted = openssl_decrypt($base64_decrypted, $this->method, base64_decode($this->skey), 0, base64_decode($this->salt));

    //     return $decrypted;
    // }
}
