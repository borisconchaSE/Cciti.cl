<?php

namespace Intouch\Framework\Collection;

use Intouch\Framework\Dao\Entity\EntityDefinition;

class ConditionBuilder {

    private $ConditionsSentence = "";
    private $EntityDefinition = "";
    public $Conditions = array();

    public function __construct(string $conditions = "", $entityDefinition = null) {

        // Verificar la existencia de la condicion
        if ($conditions == "") {
            throw new \Exception("Debe especificar una condicion");
        }

        // Verificar la existencia de la coleccion
        if (!isset($entityDefinition)) {
            throw new \Exception("Debe especificar la definición de entidad asociada");
        }

        // Verificar que la coleccion sea un GenericCollection
        if (!( $entityDefinition instanceof EntityDefinition)) {
            throw new \Exception("La coleccion asociada debe ser una instancia de GenericCollection o una clase derivada de ella");
        }

        // Verificar que la definicion de entidad tenga definido el tipo de objeto "DtoName"
        $dtoName = $entityDefinition->Entity;
        if (!isset($dtoName) || $dtoName == "") {
            throw new \Exception("La definición de entidad debe especificar el tipo de objeto contenido en -Entity-");
        }

        // Verificar que la definicion de entidad tenga definida la llave "Key"        
        $key = $entityDefinition->KeyName;     
        if (!isset($key) || $key == "") {
            throw new \Exception("La definicion de entidad debe especificar la llave principal en -KeyName-");
        }

        $this->ConditionsSentence = $conditions;
        $this->EntityDefinition = $entityDefinition;

        $this->Conditions = $this->PrepareConditions($conditions, $entityDefinition);
    }

    private function PrepareConditions($conditionsSentence, $entityDefinition) {

        // Analizar las condiciones y establecer parametros para la búsqueda en los índices

        // Separadores admitidos son:
        //      &&
        //      ||
        //
        //  El separador && genera 2 condiciones
        //  El separador || genera 2 subcondiciones dentro de 1 condicion

        // Separar las condiciones
        // ************************************************

        // Separador inicial &&
        $condicionesInicio = explode('&&', $conditionsSentence);

        $condicionesAnd = array();
        // Separador secundario ||
        foreach($condicionesInicio as $condicionInicio) {
            $condicionesOr = explode("||", $condicionInicio);
            
            foreach($condicionesOr as $or) {
                $cond = $this->BuildCondition($or, $entityDefinition);
                if (isset($cond)) {
                    array_push($condicionesAnd, $cond);
                }
            }
        }

        return $condicionesAnd;
    }

    private function BuildCondition($condition, $entityDefinition) {

        // Construye una condición unitaria
        // **************************************

        // Obtener el operador
        // ***********************
        // Operadores admitidos son:
        //      ==
        //      >
        //      >=
        //      <
        //      <=
        //      !=
        $operadoresPosibles = ["==", ">", ">=", "<", "<=", "!="];
        $operador = "";

        foreach($operadoresPosibles as $op) {
            if (strpos($condition, $op)) {
                $operador = $op;
                break;
            }
        }

        // si no se encontró el operador, generar excepcion
        if ($operador == "")
            throw new \Exception("No se encontró del operador");

        // Procesar los operandos
        $parts = explode($operador, $condition);

        if (!isset($parts) || count($parts) != 2) {
            throw new \Exception("Condicion con error de sintaxis");
        }

        // Revisar el tipo de dato
        $fieldName = trim($parts[0]);
        $value = trim($parts[1]);

        if ($fieldName == "" || $value == "") {
            throw new \Exception("Condicion con error de sintaxis");
        }

        if (isset($entityDefinition)) {
            // Buscar el tipo de dato de la propiedad actual
            if (isset($entityDefinition->Properties[$fieldName])) {
                $plop = $entityDefinition->Properties[$fieldName];
            }
        }

        // Construir la condicion
        $condicion = new Condition([
            "FieldName" => $fieldName,
            "Operator" => $operador,
            "Value" => $value,
            "FieldType" => "unknown"
        ]);

        return $condicion;
    }
}