<?php

namespace Intouch\Framework\Collection;

use Exception;
use Intouch\Framework\Dao\Entity\DocComment;

class GenericValidator {

    protected $Element = null;
    private $Validations = array();
    private $RequiredFields = array();

    function __construct($element) {
        $this->Element = $element;
        $this->LoadValidations();
    }

    // Obtener las propiedades y los validadores del objeto actual
    private function LoadValidations() {

        if (!isset($this->Element))
            return;

        $entity = new \ReflectionClass(get_class($this->Element));
        $propiedades = get_class_vars($entity->getName());
        
        foreach($propiedades as $propiedad=>$valor) {
            $prop = new \ReflectionProperty($entity->getName(), $propiedad);
            $annotations = DocComment::GetPropertyAnnotations($prop, "\n");

            foreach($annotations as $annotation) {
                switch(strtolower($annotation->Name)) {
                    case "validation" : 
                        array_push($this->Validations, (object) ["Property" => $prop, "Validation" => $annotation->Value]);
                        break;
                    case "required" : 
                        array_push($this->RequiredFields, $propiedad);
                        break;
                }
            }

        }
    }

    public function RunValidations(array $definition = array()) {

        $mensajes = "";

        // Evaluar los campos que no se cargaron en la definicion y que son requeridos
        foreach($this->RequiredFields as $propiedad) {
            if (!isset($definition[$propiedad])) {
                if ($mensajes!="") $mensajes .= "\n";
                    $mensajes .= "Propiedad: \"" . $propiedad . "\", No se ha definido esta propiedad obligatoria";
            }
        }

        foreach($this->Validations as $validation) {

            $sentence = $validation->Validation;
            $property = $validation->Property->name;

            // Reemplazar las ocurrencias de la propiedad
            $sentence = str_replace('@prop', '$this->Element->'.$property, $sentence);

            // Reemplazar llamadas a metodos
            $sentence = str_replace('@', '$this->', $sentence);

            // Ejecutar la validacion
            $result = eval("return (" . $sentence . ");");

            if (!$result) {
                if ($mensajes!="") $mensajes .= "\n";
                    $mensajes .= "Propiedad: \"" . $property . "\", Validacion: [ " . $sentence . " ]";
            }          
        }

        if ($mensajes != "") {
            throw new Exception($mensajes);
        }

        return true;
    }

    // General purpose validators
    // ***************************************************************
    public function is_date($date, $format) {

        $d = \DateTime::createFromFormat($format, $date);
        
        return $d && $d->format($format) === $date;   
    }

    public function is_valid_array($array) {
        return (isset($array) && is_array($array) && count($array) > 0);
    }

    public function is_collection($collection) {
        return ($collection instanceof GenericCollection);
    }

    public function instance_of($property, $className) {

        $plop = ($property instanceof $className);

        return ($property instanceof $className);
    }
}