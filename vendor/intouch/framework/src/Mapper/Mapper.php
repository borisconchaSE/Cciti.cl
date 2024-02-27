<?php
namespace Intouch\Framework\Mapper;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Dao\Entity\EntityDefinition;
use Intouch\Framework\Dates\Date;
use Intouch\Framework\Utils;

class Mapper
{
    private static $Instance = null;
    private static $GlobalMapping = null;

    private $Mapping = null;
    private $Escaping = false;

    public $ObjectExecutions = 0;
    public $ObjectTime = 0;
    public $PropertyExecutions = 0;
    public $PropertyTime = 0;
    public $CollectionExecutions = 0;
    public $CollectionTime = 0;

    public $FindMappingExecutions = 0;
    public $FindMappingTime = 0;

    public function ResetMonitor() {
        $this->ObjectExecutions = 0;
        $this->ObjectTime = 0;
        $this->PropertyExecutions = 0;
        $this->PropertyTime = 0;
        $this->CollectionExecutions = 0;
        $this->CollectionTime = 0;  

        $this->FindMappingExecutions = 0;
        $this->FindMappingTime = 0;
    }

    public static function GetInstance() {

        // Implementa Singleton
        if (!isset(self::$Instance)) {
            self::$Instance = new Mapper();
        }

        return self::$Instance;
    }

    public static function GetMapping() {
        if (!isset(self::$GlobalMapping)) {
            self::$GlobalMapping = Map::ObtenerMappings();
        }

        return self::$GlobalMapping;
    }

    private function findMapping($sourceClass) {

        // Monitorear
        $this->FindMappingExecutions++;

        $dtStart = microtime(true);

        $mapping = $this->Mapping;

        // Buscar el mapping en forma normal desde el sourceclass
        $map = $mapping->Where("SourceClass = '$sourceClass'")->First();
        
        if (isset($map)) {
            // Monitorear
            $dtEnd = microtime(true);
            $this->FindMappingTime += round( ($dtEnd - $dtStart) * 1000, 8)/1000;

            // si no encontramos un mapping, buscamos la referencia en forma 
            // inversa, si es que el mapping soporta swap
            return $map->TargetClass;
        }
        else {
            $map = $mapping->Where("Swappable == true")->Where("TargetClass = '$sourceClass'")->First();

            if (isset($map)) {                
                // Monitorear
                $dtEnd = microtime(true);
                $this->FindMappingTime += round( ($dtEnd - $dtStart) * 1000, 8)/1000;

                // el nuevo objeto lo define el sourceclass (la referencia está invertida en el mapping)
                return $map->SourceClass;
            }
        }

        // Monitorear
        $dtEnd = microtime(true);
        $this->FindMappingTime += round( ($dtEnd - $dtStart) * 1000, 8)/1000;
        return null;
    }

    private function newObjectFromMapping($sourceClass) {

        $map = $this->findMapping($sourceClass);

        if (isset($map)) {
            return new $map();
        }
        else {
            return null;
        }
    }

    private function mapProperty($propValue) {

        if (is_object($propValue)) {
            return null;

            // Revisar si está en el mapping
            // $propClass = get_class($propValue);
            // $newObject = $this->newObjectFromMapping($propClass, $mapping);

            // if (isset($newObject)) {
            //     $this->mapObject($propValue, $newObject, $mapping, $createProperties);
            //     return $newObject;
            // }
            // else {
            //     return null;
            // }

        }
        else if (is_array($propValue)) {

            return null;

            // $objects = array();

            // foreach($propValue as $object) {
            //     $newObject = $this->mapProperty($object, $mapping, true);
            //     array_push($objects, $newObject);
            // }

            // return $objects;
        }
        else {
            return Utils::sanearVariables($propValue);
        }

        return null;
    }

    private function mapArray(array & $propValues, $createProperties = true) {

        if (!is_array($propValues)) {
            return null;
        }
        
        $result = array();

        foreach($propValues as $prop) {
            // Atachar la propiedad (la funcion map decidirá a qué mapeador llamar según el tipo de la propiedad)
            $newProp = $this->map($prop, null, $createProperties);
            array_push($result, $newProp);
        }

        return $result;
    }

    private function mapObject(& $source, $createProperties = true) {

        if (!is_object($source)) {
            return null;
        }

        // Revisar si está en el mapping
        $propClass = get_class($source);
        $newObject = $this->newObjectFromMapping($propClass);

        // Si logramos generar el objeto de destino, mapeamos todas las propiedades que
        // podamos encontrar desde el objeto de origen
        if (isset($newObject)) {
            
            $sourceFields = get_object_vars($source);
            $targetFields = get_object_vars($newObject);

            // Mapeamos todas las propiedades del objeto
            foreach ($sourceFields as $propName => $propValue) {
                // Ver si existe la propiedad en el objeto destino 
                // o si se ha solicitado crear propiedades inexistentes en el destino
                if ($createProperties || isset($targetFields[$propName])) {
                   $newObject->$propName = $this->map($propValue, null, $createProperties);
                }
            }
            
            return $newObject;
        }
        else {
            return null;
        }
        /*
            // Obtener las propiedades del objeto de origen
            $sourceClass = get_class($source);
            $sourceFields = get_object_vars($source);

            // Buscar el objeto de destino 

            // Obtener las propiedades del objeto de destino
            $targetClass = get_class($target);
            $targetProps = get_class_vars($targetClass);

            foreach ($sourceFields as $propName => $propValue) {
                // Ver si existe la propiedad en el objeto destino 
                // o si se ha solicitado crear propiedades inexistentes en el destino
                if ($createProperties || isset($targetProps[$propName])) {
                    // Atachar la propiedad
                    $target->$propName = $this->mapProperty($propValue, $mapping, $createProperties);
                }
            }

            return $target;
        */
    }

    private function mapCollection(GenericCollection & $source, $createProperties = true, $key = "") {
        
        if (!isset($source)) {
            return null;
        }

        // Si no se especifica la llave de la collecion de destino,
        // se debe utilizar la llave de la coleccion de origen
        if ($key == "") {
            $key = $source->GetKey();
        }

        // Obtener el mapeo del arreglo de objetos
        $result = $this->mapArray($source->Values, $createProperties);

        if (isset($result)) {
            // Obtener la clase de destino para generar la colección
            $targetClass = $this->findMapping($source->GetDtoName());

            return new GenericCollection(
                Key       : $key,
                DtoName   : $targetClass,
                Values    : $result
            );
        }
        else {
            return null;
        }
    }

    public function map(& $source, $mapping = null, $createProperties = true, $key="", $scapeStrings = false) {

        if (!isset($source)) {
            return null;
        }

        if (!isset($this->Mapping))
            $this->Mapping = self::GetMapping();

        if (isset($mapping)) {
            $this->Mapping->AddRange($mapping);
        }

        if (is_array($source)) {
            return $this->mapArray($source, $createProperties);            
        }
        else if ($source instanceof GenericCollection) {
            return $this->mapCollection($source, $createProperties, $key);
        }
        else if (is_object($source)) {
            return $this->mapObject($source, $createProperties);
        }
        else {
            //$dtPropertyStart = microtime(true);

            if ($scapeStrings)
                return Utils::sanearVariables($source);
            else
                return $source;
            //$var = $this->mapProperty($source);
            //$dtPropertyEnd = microtime(true);

            // $this->PropertyExecutions++;
            // $this->PropertyTime += round( ($dtPropertyEnd - $dtPropertyStart) * 1000, 8)/1000;
        }

    }


    private static function autoMapper($sourceObject, $targetObject, $nosanear = false, $honorPrefix = '', $useTargetProperties = false, array $innerMappings = [])
    {
        if ($useTargetProperties) {
            $vars_clase = get_class_vars(get_class($targetObject));
        }
        else {
            $vars_clase = get_object_vars($sourceObject);
        }

        $prefixLen = 0;
        if ($honorPrefix != '') {
            $prefixLen = strlen($honorPrefix);
        }

        foreach ($vars_clase as $sKey => $value) {  

            $key = $sKey;
            $destKey = $sKey;

            $useProp = true;
            if ($honorPrefix != '') {
                // Se deben utilizar solamente aquellas propiedades con el prefix indicado y quitarlo
                if ($useTargetProperties || substr($key, 0, $prefixLen) == $honorPrefix) {
                    $useProp = true;

                    if ($useTargetProperties) {
                        $key = $honorPrefix . '.' . $sKey;
                    }
                    else {
                        $destKey = self::removePrefix($sKey);
                    }
                }
                else {
                    $useProp = false;
                }
            }

            // if ($removePrefix) {
            //     $key = self::removePrefix($sKey);
            // }
            
            if ($useProp) {

                // ver si tenemos que mapear la propiedad como objeto
                $mapInner = null;

                if (isset($innerMappings[$destKey])) {
                    $mapInner = $innerMappings[$destKey];
                }

                if (property_exists($sourceObject, $key)) {

                    if (isset($sourceObject->{$key})) {

                        if (isset($mapInner) && is_object($sourceObject->{$key})) {
                            $source = $sourceObject->{$key};
                            $target = new $mapInner;
                            $targetObject->{$destKey} = self::autoMapper($source, $target, $nosanear);
                        }
                        else if (isset($mapInner) && is_array($sourceObject->{$key})) {
                            $propArray = [];
                            foreach($sourceObject->{$key} as $source) {
                                $target = new $mapInner;
                                array_push($propArray, self::autoMapper($source, $target, $nosanear));
                            }
                            $targetObject->{$destKey} = $propArray;
                        }
                        else {
                            $targetObject->{$destKey} = Utils::sanearVariables($sourceObject->{$key}, $nosanear, $mapInner);
                        }
                    }
                }
                else if (isset($sourceObject->{$key})) {

                    if (isset($mapInner) && is_object($sourceObject->{$key})) {
                        $source = $sourceObject->{$key};
                        $target = new $mapInner;
                        $targetObject->$destKey = self::autoMapper($source, $target, $nosanear);
                    }
                    else if (isset($mapInner) && is_array($sourceObject->{$key})) {
                        $propArray = [];
                        foreach($sourceObject->{$key} as $source) {
                            $target = new $mapInner;
                            array_push($propArray, self::autoMapper($source, $target, $nosanear));
                        }
                        $targetObject->$destKey = $propArray;
                    }
                    else {
                        $targetObject->$destKey = Utils::sanearVariables($sourceObject->{$key}, $nosanear, $mapInner);
                    }                    
                }
            }        
        }

        return $targetObject;
    }
    
    private static function removePrefix($key) {

        // encontrar la posición del punto de separacion
        $parts = explode('.', $key);

        if (count($parts) > 1)
            return $parts[count($parts)-1];
        else
            return $key;
    }

    public static function ToDto($entity, $dtoName, $innerMappings = [])
    {
        if ($entity === null)
            return null;

        return self::autoMapper(
            sourceObject: $entity, 
            targetObject: new $dtoName, 
            innerMappings: $innerMappings)
        ;
    }

    public static function ToEntity($dto, $entityName, $removePrefix = false)
    {
        if ($dto === null)
            return null;

        return self::autoMapper($dto, new $entityName);
    }

    public static function ToDtos($entityArray, $dtoName, $innerMappings = [], $avoidNull = false) 
    {
        if ($entityArray == null || count($entityArray) == 0) {
            if (!($avoidNull)) {
                return null;
            }
        }

        $dtos = array();

        if (isset($entityArray) && count($entityArray) > 0) {
            foreach($entityArray as $entity) {
                array_push($dtos, self::autoMapper(
                    sourceObject: $entity, 
                    targetObject: new $dtoName, 
                    innerMappings: $innerMappings));
            }
        }
        else {
            if (!($avoidNull)) {
                return null;
            }
            else {
                $dtos = [];
            }
        }

        //$entityDefinition = EntityDefinition::GetInstance(new \ReflectionClass($entity));
        if (!isset($entity)) {
            // crear un objeto
            $entity = new $dtoName();
        }
        
        $entityDefinition = EntityDefinition::Instance(get_class($entity));

        return new GenericCollection(
            Values : $dtos, 
            Key : $entityDefinition->Key, 
            DtoName : $dtoName
        );
    }

    public static function ToEntities($dtoArray, $entityName, $honorPrefix = '', $useTargetProperties = false) 
    {
        if ($dtoArray == null)
            return null;

        $entities = array();

        foreach($dtoArray as $dto) {
            array_push($entities, self::autoMapper($dto, new $entityName, false, $honorPrefix, $useTargetProperties));
        }

        return $entities;
    }

    public static function EvaluateKeyMultilevel($value, $key) {

        // Ver la cantidad de niveles
        $existePunto = strpos($key, '.');
        $existeFlecha = strpos($key, '->');

        if ($existePunto === false && $existeFlecha === false) {
            //no hay mas niveles, devolver la evaluación directa
            return $value->$key;
        }
        else {
            $separador = ($existePunto === false) ? '->' : '.';

            // Obtener el objeto principal
            $parts = explode($separador, $key);
            $mainPart = $parts[0];

            $mainSeccion = $value->$mainPart;

            // Obtener el siguiente nivel
            $levelKey = self::EvaluateKeyMultilevel($mainSeccion, $parts[1]);

            return $levelKey;
        }
        
    }

    public static function Convert($sourceObject, $className, array $dictionary, array $convert = [], string $inputFormat = 'd/m/Y', $outputFormat = 'Y-m-d') {

        $targetObject = new $className;
        $vars = get_object_vars($sourceObject);

        foreach ($vars as $sKey => $value) {  

            $key = $sKey;

            if (!isset($dictionary[$key])) {
                $destKey = $key;
            }
            else {
                $destKey = $dictionary[$key];
            }

            if (in_array($key, $convert)) {

                if (!isset($value) || $value == '') {
                    $value = null;
                }
                else {
                    $fec = Date::createFromFormat($inputFormat, $value);
                    $value = $fec->format($outputFormat);
                }
            }

            $targetObject->$destKey = $value;
        }

        return $targetObject;
    }

    public static function ConvertMultiple(array $sourceObjects, $className, array $dictionary, array $convert = [], string $inputFormat = 'd/m/Y', $outputFormat = 'Y-m-d') {

        $result = [];
        
        foreach($sourceObjects as $object) {
            $result[] = self::Convert($object, $className, $dictionary, $convert, $inputFormat, $outputFormat);
        }

        return $result;
        
    }

    

}
