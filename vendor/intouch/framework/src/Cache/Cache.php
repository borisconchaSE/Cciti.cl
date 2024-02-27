<?php

namespace Intouch\Framework\Cache;

use Intouch\Framework\Configuration\RedisConfig;

abstract class Cache {


    abstract protected static function GetInstance();

    //public static function Get($token, $multi = false) {
    public static function Get($token) {

        // Buscamos la configuracion en cache
        $redisSvc = RedisSvc::Init();
        $info = null;

        if (isset($redisSvc)) {
            $data = $redisSvc->get(RedisConfig::Instance()->TokenPrefix . '-' . $token);
            
            if (isset($data) && $data !== false) {

                $info = unserialize($data);
                
            }
            
            $redisSvc->close();
            
            return $info;
        }
        else {
            return null;
        }
    }

    // ---------------------------------------------------------------------
    public static function Set($token, $value) {
        // Buscamos la configuracion en cache
        $redisSvc = RedisSvc::Init(); 

        if (isset($redisSvc)) {
            $redisSvc->set(RedisConfig::Instance()->TokenPrefix . '-' . $token, serialize($value));
            $redisSvc->close();
        }
    }
    // ---------------------------------------------------------------------


    // ---------------------------------------------------------------------
    public static function SetWithTimeout($token, $value) {
        // Buscamos la configuracion en cache
        $redisSvc = RedisSvc::Init(); 

        if (isset($redisSvc)) {
            $RedisKey   =  RedisConfig::Instance()->TokenPrefix . '-' . $token; 
            $redisSvc->set($RedisKey, serialize($value));
            $redisSvc->expire($RedisKey, RedisConfig::Instance()->ExpireTime);
            $redisSvc->close();
        }
    }
    // ---------------------------------------------------------------------


    // ---------------------------------------------------------------------
    public static function GetWithTimeOut($token) {

        // Buscamos la configuracion en cache
        $redisSvc = RedisSvc::Init();
        $info = null;

        if (isset($redisSvc)) {
            $RedisKey   =  RedisConfig::Instance()->TokenPrefix . '-' . $token; 
            $data = $redisSvc->get($RedisKey);
            $redisSvc->expire($RedisKey, RedisConfig::Instance()->ExpireTime);
            
            if (isset($data) && $data !== false) {

                $info = unserialize($data);
                
            }
            
            $redisSvc->close();
            
            return $info;
        }
        else {
            return null;
        }
    }
    // ---------------------------------------------------------------------



    // ---------------------------------------------------------------------
    public static function Delete($token){

        $redisSvc   =   RedisSvc::Init(); 
        if (isset($redisSvc)) {
            $redisSvc->delete(RedisConfig::Instance()->TokenPrefix . '-' . $token);
            $redisSvc->close();
        }  
    }
    // ---------------------------------------------------------------------



    // ---------------------------------------------------------------------
    public static function Exists($token){

        $redisSvc       =   RedisSvc::Init();         
        $tokenPrefix    =   RedisConfig::Instance()->TokenPrefix;
        $status         =   $redisSvc->exists( $tokenPrefix. '-' . $token);
        $redisSvc->close();

        return $status;

    }
    // ---------------------------------------------------------------------

}