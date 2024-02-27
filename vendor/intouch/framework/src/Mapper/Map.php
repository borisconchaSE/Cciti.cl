<?php

namespace Intouch\Framework\Mapper;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\RedisAccess\RedisSvc;
use Karriere\JsonDecoder\JsonDecoder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Map {

    public $IdMapping = 0;
    public $SourceClass = "";
    public $TargetClass = "";
    public $Swappable = true;

    public function __construct() {
        
    }

    private static function ClearPath($path, $startingPoint) {
        $pos = strpos($path, $startingPoint);
        return substr($path, $pos + strlen($startingPoint));
    }
    
    /// Devuelve una colección de bundles para el sistema
    public static function ObtenerMappings() {

        $mapping = null;
        $redis = RedisSvc::Init();

        // Intentar obtener la configuracion desde redis
        if (isset($redis)) {
            $info = unserialize($redis->get("cache-mapping"));            

            if (isset($info) && ($info)) {
                $mapping = $info;
            }
        }

        if ($mapping == null) {
            $mappingFilePath = __DIR__."/../../mapping.config.json";

            // Leer el archivo de configuracion
            if (file_exists($mappingFilePath)) {
                $jsonData = file_get_contents($mappingFilePath);

                // Eliminar los comentarios del archivo json
                $jsonData = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $jsonData);

                if (!isset($jsonData) || $jsonData == null) {
                }
                else {
                    $jsonDecoder = new JsonDecoder();
                    $info = $jsonDecoder->decodeMultiple($jsonData, Map::class);

                    if (isset($info)) {
                        $mapping = new GenericCollection(
                            Key     : "IdMapping",
                            DtoName : Map::class,
                            Values  : $info
                        );
                    }
                }
            }

            // Intentar obtener mapeos automáticos por CONVENCION (Entities > Dtos)
            $DTOfolder = __DIR__."/../../BLL/DataTransferObjects";
            $EntityFolder = __DIR__."/../../Dao/Entities";

            $idxMap = 0;
            if (isset($mapping)) {
                $idxMap = $mapping->Max('IdMapping') + 1;
            }

            if (file_exists($DTOfolder) && file_exists($EntityFolder)) {                
                $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($EntityFolder));
                $files = array();

                foreach ($rii as $file) {
                    if ($file->isDir()){ 
                        continue;
                    }
                    
                    $entityName = str_replace(".php", "", $file->getFilename());
                    $entityRoute = str_replace($file->getFilename(), "", self::ClearPath($file->getPathname(), "Entities/"));
                    $entityPath = str_replace("/", "\\", $entityRoute);

                    // Ver si existe una DTO con el nombre de la entity
                    $dtoName = $entityName . "Dto";

                    if (file_exists($DTOfolder . "/" . $entityRoute . $dtoName . ".php")) {
                        $entityClass = "Dao\\Entities\\" . $entityPath . $entityName;
                        $dtoClass = "BLL\\DataTransferObjects\\" . $entityPath . $dtoName;
                    }

                    if (isset($entityClass) && $entityClass != "" && isset($dtoClass) && $dtoClass != "") {
                        // Ver si la definicion ya está cargada
                        if (isset($mapping)) {
                            $ent = $mapping->Where("SourceClass = '$entityClass'")->First();
                            $dto = $mapping->Where("TargetClass = '$dtoClass'")->First();
                        }
                        else {
                            $ent = null;
                            $dto = null;
                        }

                        // Si no existe, lo agregamos al mapeo
                        if (!isset($ent) && !isset($dto)) {
                            $mp = new Map();
                            $mp->IdMapping = $idxMap;
                            $mp->SourceClass = $entityClass;
                            $mp->TargetClass = $dtoClass;
                            $mp->Swappable = true;

                            $mapping->Add($mp, false);
                            $idxMap++;
                        }
                    }
                }
                
                $mapping->Reindex();
            }

            // Si obtuvimos el mapping, lo guardamos en REDIS
            if (isset($mapping) && ($mapping->Count() > 0)) {
                $redis->set("cache-mapping", serialize($mapping));
                $redis->close();
            }
        }    
        else {
            $redis->close();
        }

        // Es obligatorio lograr obtener la configuracion
        if (!isset($mapping)) {
            http_response_code(500);
            echo "No se ha encontrado el mapping en redis ni en el archivo de mappings: mapping.config.json";
            die();
        }

        return $mapping;
    }
}