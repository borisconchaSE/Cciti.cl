<?php

namespace Intouch\Framework\Dao\Entity;

use Intouch\Framework\Annotation\AnnotationHelper;
use Intouch\Framework\Collection\GenericCollection;
use Karriere\JsonDecoder\Property;

class PropertyDefinition {

    public $Name = "";
    public $IsKey = false;
    public $IsAutoIncrement = false;
    public $ColumnName = "";
    public $ColumnType = "";
    public $IsNullable = false;
    public $DefaultWhenNull = null;
    public $Ignore = false;

    public static function GetInstance(GenericCollection | null $atributosPropiedades, String $propiedad, \ReflectionProperty $property) {

        // Instanciar el resultado
        $result = new PropertyDefinition();
        $result->Name = $propiedad;

        if (isset($atributosPropiedades)) {
            $definicion = $atributosPropiedades->FirstWhere("propertyName == '" . $propiedad . "'");

            if (isset($definicion)) {
                $result->IsKey = $definicion->attribute->PrimaryKey;
                $result->IsAutoIncrement = $definicion->attribute->AutoIncrement;
                $result->ColumnName = ($definicion->attribute->ColumnName != '') ? $definicion->attribute->ColumnName : $propiedad;
                $result->ColumnType = $definicion->attribute->DataType;
                $result->IsNullable = $definicion->attribute->Nullable;
                $result->DefaultWhenNull = $definicion->attribute->DefaultWhenNull;
                $result->Ignore = $definicion->attribute->Ignore;
            }
        }

        /*
        // Obtener la información respecto de la Propiedad
        $annotations = DocComment::GetPropertyAnnotations($property);

        foreach($annotations as $annotation) {

            switch(strtolower($annotation->Name)) {
                case "key" : $result->IsKey = true; break;
                case "autoincrement" : $result->IsAutoIncrement = true;
                case "column" : if (isset($annotation->Value)) $result->ColumnName = $annotation->Value; break;
                case "columntype" : if (isset($annotation->Value)) $result->ColumnType = $annotation->Value; break;
                case "isnullable" : $result->IsNullable = true;
                case "defaultwhennull" : if (isset($annotation->Value)) $result->DefaultWhenNull = $annotation->Value; break;
                case "ignore" : $result->Ignore = true;
            }
            
        }
        */

        if (!isset($result->ColumnName) || $result->ColumnName == "") {
            $result->ColumnName = $result->Name;
        }
        
        return $result;
    }

}