<?php

namespace Intouch\Framework\Dao\Entity;

class DocComment {

    public static function GetClassAnnotations(\ReflectionClass $class) {
        
        $classdoc = $class->getDocComment();        
        return self::GetAnnotationsFromDocumentation($classdoc);

    }

    public static function GetPropertyAnnotations(\ReflectionProperty $property, $annotationSeparator = ",") {

        $propdoc = $property->getDocComment();
        return self::GetAnnotationsFromDocumentation($propdoc, $annotationSeparator);    
        
    }

    private static function GetAnnotationsFromDocumentation($propdoc, $annotationSeparator = ",") {

        // Remover los delimitadores
        $propdoc = str_replace('/**', '', $propdoc);
        $propdoc = str_replace('*/', '', $propdoc);
        $propdoc = str_replace("\n     *", "\n", $propdoc);
        
        // Los bloques deben estar separados por comas
        $bloques = explode($annotationSeparator, $propdoc);

        $annotations = array();

        if (isset($bloques)) {
            // Revisar si es un valor constante o una asignacion
            foreach($bloques as $bloque) {

                $bloque = trim($bloque);

                if (strpos($bloque, '=') === false) {
                    // no es una asignacion
                    $annotation = new Annotation();
                    if (trim($bloque) != "") {
                        $annotation->Name = trim($bloque);
                        $annotation->Value = null;
                        $annotations[$annotation->Name] = $annotation;
                    }
                }
                else {
                    // es una asignacion
                    $asignacion = explode('=', $bloque, 2);

                    if (isset($asignacion[0])) {
                        if (trim($asignacion[0]) != "") {
                            $annotation = new Annotation();                    
                            $annotation->Name = trim($asignacion[0]);
                            $annotation->Value = trim($asignacion[1]);
                            $annotations[$annotation->Name] = $annotation;
                        }
                    }
                }
            }
        }

        return $annotations;
    }
}