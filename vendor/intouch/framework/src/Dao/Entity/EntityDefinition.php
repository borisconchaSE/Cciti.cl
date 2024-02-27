<?php

namespace Intouch\Framework\Dao\Entity;

use Intouch\Framework\Annotation\AnnotationHelper;
use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Cache\RedisSvc;
use Intouch\Framework\Collection\GenericCollection;

class EntityDefinition {

    public $Schema = "";
    public $Table = "";
    public $TablePrefix = "TB";    
    public $NamedTable = "";

    public $Properties = array();    
    public $KeyProperty = null;
    public $Key = ""; // the "key" name in the database
    public $KeyName = ""; // the "key" in the entity
    public $AutoIncrement = true;

    public $Fields = "";
    public $NotIgnoredFields = "";
    public $NamedNotIgnoredFields = "";
    public $NamedRenamedNotIgnoredFields = "";
    public $NamedListedFields = "";
    public $ClassFields = "";
    public $NamedFields = "";

    public $Entity = "";

    private static $EntityColletion = null;

    private static function AddEntityDefinitionToCollection($entityDefinition) {

        self::$EntityColletion->Add($entityDefinition);

        // Actualizar REDIS
        $redis = RedisSvc::Init();

        // Si tenemos redis, actualizamos el valor de la coleccion
        if (isset($redis)) {
           $redis->set("gps-cache-entity-collection", serialize(self::$EntityColletion));
           $redis->close();
        }
    }

    private static function GetEntityCollection() {

        if (!isset(self::$EntityColletion)) {

            // Buscar las definiciones en Redis
            $redis = RedisSvc::Init();
            
            // Si tenemos redis, ver si podemos obtener la configuracion desde ahí
            if (isset($redis)) {
                $definition = $redis->get("gps-cache-entity-collection");

                if (isset($definition) && $definition) {
                    $info = unserialize($definition);
                    $redis->close();
                }                

                if (isset($info) && ($info)) {
                    self::$EntityColletion = $info;
                }
            }

            // Si no fue posible obtener la coleccion, deberemos crear una nueva
            if (!isset(self::$EntityColletion)) {
                // Creamos una coleccion nueva
                self::$EntityColletion = new GenericCollection(
                    Key: "Entity", // << la propiedad "Entity" de la clase "EntityDefinition" que contiene el nombre de la entidad
                    DtoName: EntityDefinition::class,
                    Values: null,
                    ReflectDto: false // No se debe realizar REFLECTION del DTO, se produciría un bucle infinito
                );
            }
        }

        return self::$EntityColletion;
    }
    
    public static function Instance($entityName) {

        // Buscamos la entidad en la colección
        $col = self::GetEntityCollection();

        $entidad = $col->FirstWhere("Entity == '$entityName'");

        if (!isset($entidad) && $entidad == null) {
            // Si no encontramos la entidad, deberemos obtenerla mediante Reflection
            //$entidad = EntityDefinition::GetInstance(new \ReflectionClass($entityName));
            $entidad = EntityDefinition::GetInstance($entityName);
            
            // Debemos agregar la entidad a la coleccion (y actualizar redis)
            self::AddEntityDefinitionToCollection($entidad);
        }
        
        return $entidad;
    }

    //public static function GetInstance(\ReflectionClass $entity) {
    public static function GetInstance(String $entity) {

        //$reflected = new \ReflectionClass($entity);

        // Instanciar el resultado
        $result = new EntityDefinition();

        $annotations = AnnotationHelper::FromClass($entity);
        $reflected   = $annotations->Reflected;

        // Buscar atributo de entidad de la clase
        $entidad = $annotations->FindAttributeClass(Entity::class);

        $result->Entity = $reflected->getName();

        // Prefijo de tabla       << TODO
        $result->TablePrefix = $reflected->getShortName();

        // Obtener la información respecto de la Entidad        
        //$annotations = DocComment::GetClassAnnotations($entity);
        if (!isset($entidad->attribute->Schema) || $entidad->attribute->Schema == '') {
            //$result->Schema = 'dbo';
            $result->Schema = '';
        }
        else {
            $result->Schema = $entidad->attribute->Schema;
        }

        if (!isset($entidad->attribute->TableName) || $entidad->attribute->TableName == '') {
            $result->Table = (($result->Schema!='') ? $result->Schema . '.' : '') . $reflected->getShortName();
        }
        else {
            $result->Table = (($result->Schema!='') ? $result->Schema . '.' : '') . $entidad->attribute->TableName;
        }

        if (!isset($entidad->attribute->TablePrefix) || $entidad->attribute->TablePrefix == '') {
            $result->TablePrefix = $reflected->getShortName();
        }
        else {
            $result->TablePrefix = $entidad->attribute->TablePrefix;
        }

        if ($result->Table != $result->TablePrefix)
            $result->NamedTable = $result->Table . " " . $result->TablePrefix;
        else
            $result->NamedTable = $result->Table;

        // Obtener la información respecto de las propiedades
        $propiedades = get_class_vars($reflected->getName());
        $atributosPropiedades   = $annotations->GetAttributeProperties(EntityField::class);

        foreach($propiedades as $propiedad=>$valor) {

            $property = PropertyDefinition::GetInstance($atributosPropiedades, $propiedad, new \ReflectionProperty($reflected->getName(), $propiedad));

            if ($property->IsKey && !isset($result->KeyProperty)) {
                $result->KeyProperty = $property;
                $result->AutoIncrement = $property->IsAutoIncrement;

                // Asignar el nombre de la propiedad "primary key" que tiene en la base de datos
                if (isset($property->ColumnName)) {
                    $result->Key = $property->ColumnName;
                }
                else{
                    $result->Key = $property->Name;
                }

                $result->KeyName = $property->Name;
            }

            // Si no se especifica el nombre de columna de la BD, sobreescribimos con
            // el nombre de la propiedad en la clase (se asume entonces que son iguales)
            if (!isset($property->ColumnName) || $property->ColumnName == "") {
                $property->ColumnName = $property->Name;
            }

            // Agregar a la lista de columnas
            if ($result->Fields != "")
                $result->Fields .= ", ";

            $result->Fields .= $property->ColumnName . ' AS ' . $property->Name;

            // Agregar a la lista de columnas no ignoradas (para usar en inserts y updates)            
            if (!$property->Ignore) {

                // NOT IGNORED
                if ($result->NotIgnoredFields != "")
                    $result->NotIgnoredFields .= ", ";

                $result->NotIgnoredFields .= $property->ColumnName;    

                // NAMED NOT IGNORED
                if ($result->NamedNotIgnoredFields != "")
                    $result->NamedNotIgnoredFields .= ", ";

                $result->NamedNotIgnoredFields .= $result->TablePrefix . "." . $property->ColumnName . ' AS "' . $property->Name . '"';
           
                // NAMED RENAMED NOT IGNORED
                if ($result->NamedRenamedNotIgnoredFields != "")
                    $result->NamedRenamedNotIgnoredFields .= ", ";

                $result->NamedRenamedNotIgnoredFields .= $result->TablePrefix . "." . $property->ColumnName . ' AS "' . $result->TablePrefix . "." . $property->Name . '"'; 

                // NAMED
                // Agregar a la lista de columnas con nombre (solo las no ignoradas)
                if ($result->NamedFields != "")
                    $result->NamedFields .= ", ";

                $result->NamedFields .= $result->TablePrefix . "." . $property->ColumnName . ' AS ' . $property->Name;

                // NAMED LISTED
                // Agregar a la lista de columnas sin nombre (solo las no ignoradas)
                if ($result->NamedListedFields != "")
                    $result->NamedListedFields .= ", ";

                $result->NamedListedFields .= $result->TablePrefix . "." . $property->ColumnName;                
            }

            // Agregar a la lista de campos de clase
            if ($result->ClassFields != "")
                $result->ClassFields .= ", ";

            $result->ClassFields .= $property->Name;

            // Agregar la propiedad a la colección
            $result->Properties[$property->Name] = $property;
        }
        
        return $result;
    }

    function __construct() {
    }
}