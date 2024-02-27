<?php

namespace Intouch\Framework\Dao;

use Intouch\Framework\Configuration\ConnectionConfig;
use PDO;

class DataConfig
{
    protected static $host = '';
    protected static $user = '';
    protected static $psw = '';
    protected static $db = '';
    protected static $port = '';

    function __construct()
    {
    }

    public static function GetPDOConnection($domain)
    {
        // Buscar el dominio
        
        $dominios = ConnectionConfig::Instance(); // $GLOBALS['conn_domains'][$domain];
        
        $dom = $dominios[$domain];

        if ($dom) {
            
            if ($dom->Type == 'mysql') { 
                
                $conn = new PDO(
                    "mysql:dbname=" . $dom->Database . ";host=" . $dom->Host . ";charset=utf8",
                    $dom->User,
                    $dom->Password
                ); 
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $conn;
            }
            else if ($dom->Type == 'sqlserver') {
                $conn = new PDO(
                    "sqlsrv:Server=" . $dom->Host . ";Database=" . $dom->Database, 
                    $dom->User, 
                    $dom->Password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $conn;
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }    
}
