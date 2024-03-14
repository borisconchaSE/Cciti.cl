<?php

namespace Intouch\Framework\Cache;

use Intouch\Framework\Configuration\RedisConfig;

class RedisSvc {

    private function __construct() {}

    static public function Init() {

        // Inicializa una nueva instancia de REDIS y lo conecta al servidor    
        $redis = new \Redis();

        // Configuracion de redis
        $redisConfig = RedisConfig::Instance();
        $redis->connect($redisConfig->Endpoint, $redisConfig->Port);
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);

        // Probar la conexion
        if (self::Test($redis))
            return $redis;
        else
            return null;
    }

    static public function Test($redis) {
        return ($redis->ping() == "+PONG");
    }

    public static function FlushAll($includeSessions = false) {

        $redisSvc = RedisSvc::Init();

        if (isset($redisSvc)) {

            $allKeys = $redisSvc->keys('*');

            foreach($allKeys as $key) {
                $encontrada         = strpos($key, 'PHPREDIS_SESSION');
                $DataTableCahce     = strpos($key, 'RPHPDATATABLE_');
                $GlobalCache        = strpos($key, 'GlobalAppCache_'); 
                if ($encontrada === false && $DataTableCahce == false && $GlobalCache == false) {
                    $redisSvc->unlink($key);
                }
            }
        }

    }

}