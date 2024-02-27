<?php

namespace Intouch\Framework\Annotation;

use Intouch\Framework\Annotation\MethodParameter;

class AttributeContainer {

    private int $__Type;
    private string $__Name = '';
    private string $__ReturnType = '';
    private string $__DataType = '';
    private array $__Attributes = [];
    private array $__Parameters = []; // parametros (sólo se utiliza para los métodos)
    
    function __get($name) {

        switch($name) {
            case "Type": return $this->__Type;
            case "Name": return $this->__Name;
            case "ReturnType": return $this->__ReturnType;
            case "DataType": return $this->__DataType;
            case "Attributes": return $this->__Attributes;
            case "Parameters": return $this->__Parameters;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
                return null;
        }

    }

    public function AddAttribute($name, $attribute) {
        $this->__Attributes[$name] = $attribute;
    }

    public function AddParameter(string $name, MethodParameter $parameter) {
        $this->__Parameters[$name] = $parameter;
    }

    public function __construct(
        int $type, string $name, string $returnType = '', string $dataType = ''
    ) {
        $this->__Type = $type;
        $this->__Name = $name;
        $this->__ReturnType = $returnType;
        $this->__DataType = $dataType;
    }

    public function HasAttribute(string $fullAtrributeClassname) {

        foreach($this->__Attributes as $attribueName => $attribute) {
            if ($attribueName == $fullAtrributeClassname)
                return true;
        }

        return false;
    }
}