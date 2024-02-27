<?php

namespace Intouch\Framework\Collection;

use Intouch\Framework\Dao\Entity\DocComment;

abstract class GenericDefinition {

    private $Validator = null;

    public function __construct(array $definition, $validatorClass = "")
    {        
        $properties = get_object_vars($this);
        /*
        if ($validateRequired) {
            $errors = $this->ValidateRequiredFields($definition);

            if (isset($errors)) {
                $mensaje = "";

                foreach($errors as $error) {
                    if ($mensaje != "") $mensaje .= "\n";
                    $mensaje.= $error;
                }

                throw new \Exception("No se han especificado los siguientes parametros obligatorios en la definicion:\n" . $mensaje);
            }
        }
        */
        
        // Escribir las propiedades en el objeto final
        foreach($properties as $propname => $propvalue) {
            // Seteamos la propiedad
            if (isset($definition[$propname])) {
                $this->$propname = $definition[$propname];
            }            
        }

        // Validacion
        if (isset($validatorClass) && $validatorClass != "") {
            
            // Instanciar el validador
            $this->Validator = new $validatorClass($this);
            
            // Ejecutar la validacion
            try {
                $this->Validator->RunValidations($definition);
            }
            catch (\Exception $e) {
                throw ($e);
            }

        }
    }

    // Getters
    // *******************************************
    public function GetValue($property) {
        return $this->$property;
    }
    
    // protected function ValidateRequiredFields($definition) {
    //     // Verificar que todos los campos de la definicion han sido especificados
    //     $errors = array();

    //     $vars = get_object_vars($this);

    //     foreach($vars as $prop => $value) {
    //         if (!isset($definition[$prop])) {
    //             array_push($errors, "El campo $prop no ha sido especificado en la definicion");
    //         }            
    //     }

    //     if (count($errors) > 0) {
    //         return $errors;
    //     }
    //     else {
    //         return null;
    //     }
    // }
    
}