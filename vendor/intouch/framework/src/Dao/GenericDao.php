<?php

namespace Intouch\Framework\Dao {
    date_default_timezone_set('America/Santiago');
    use \PDO;

    class GenericDao extends BasicDao
    {
        private $ReflectedEntity = null;
        function __construct($entity, $domain) {
            parent::__construct($entity, $domain);
        }

        private function Cast($property) {

            // Cast Datetime
            if (strtolower($property->ColumnType) == 'datetime') {
                if ($this->Domain->Type == 'oracle' && strtolower($property->ColumnType) == 'datetime') {
                    return "TO_DATE(:" . $property->ColumnName . ", 'yyyy-mm-dd hh24:mi:ss')";
                }
                else if ($this->Domain->Type == 'sqlserver' && strtolower($property->ColumnType) == 'datetime') {
                    return "convert(datetime, :" . $property->ColumnName . ", 20)";
                }
            }

            // cualquier otro caso
            return ':' . $property->ColumnName; 
        }

        private function Value($property, $entity) {

            $value = $this->ReflectedEntity
                        ->getProperty($property->Name)
                        ->getValue($entity);

            if (strtolower($property->ColumnType) == 'datetime') {

                if (isset($value))
                    return $date = new \DateTime($value);
                else
                    return null;
            }

            // cualquier otro caso
            return $value;

        }
        
        public function Update($entity, $transaction = null) {

            if ($this->ReflectedEntity == null)
                $this->ReflectedEntity = new \ReflectionClass($this->EntityDefinition->Entity);

            $fields = array();
            $bindings = array();
            $values = array();
            $bindingCasts = array(); // para casos especiales en los cuales se debe interponer algun cast o formateador al campo
            $types = array();

            // Construir la sentencia SQL de actualización
            $qry = 'UPDATE '. $this->EntityDefinition->Table.' SET ';

            $idx = 0;
            foreach($this->EntityDefinition->Properties as $property) {       

                if (!$property->Ignore && !$property->IsKey) {   

                    $fields[$idx] = $property->ColumnName;
                    $bindings[$idx] = ':' . $property->ColumnName;

                    if ($property->ColumnType == 'int')
                        $types[$idx] = 'int';
                    else
                        $types[$idx] = '';

                    $bindingCasts[$idx] = $this->Cast($property);
                    // if ($this->Domain->Type == 'oracle' && strtolower($property->ColumnType) == 'datetime') {
                    //     $bindingCasts[$idx] = "TO_DATE(:" . $property->ColumnName . ", 'yyyy-mm-dd hh24:mi:ss')";
                    // }
                    // else if ($this->Domain->Type == 'sqlserver' && strtolower($property->ColumnType) == 'datetime') {
                    //     $bindingCasts[$idx] = "convert(datetime, :" . $property->ColumnName . ", 20)";
                    // }
                    // else {
                    //     $bindingCasts[$idx] = ':' . $property->ColumnName;
                    // }
                    
                    $values[$idx] = $this->Value($property, $entity);
                    // $values[$idx] = $this->ReflectedEntity
                    //                     ->getProperty($property->Name)
                    //                     ->getValue($entity);

                    // Agregar instrucción SET a la sentencia
                    if ($idx > 0)
                        $qry .= ', ';

                    $qry .= $property->ColumnName . ' = ' . $bindingCasts[$idx];

                    $idx++;
                }                
            }

            // Agregar la condición de selección
            $qry .= ' WHERE ' . $this->EntityDefinition->Key . ' = :' . $this->EntityDefinition->Key;

            // Preparar la sentencia
            if (!isset($transaction)) {
                $transaction = $this->Connection;
            }
            $stmt = $transaction->prepare($qry);

            // Asignar el enlazamiento (bindings)
            for( $idxb = 0; $idxb < count($bindings); $idxb++) {
                $value = $values[$idxb];
                $bind = $bindings[$idxb];

                if(is_int($value) || $types[$idxb] == 'int') {
                    $param = PDO::PARAM_INT;
                }
                elseif(is_double($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif(is_float($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif(is_bool($value)) {
                    $param = PDO::PARAM_BOOL;
                }
                elseif(is_null($value)) {
                    $param = PDO::PARAM_NULL;
                }
                elseif(is_string($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif($value instanceof \DateTime) {
                    $param = PDO::PARAM_STR;
                    $value = $value->format('Y-m-d H:i:s');
                }
                else
                    $param = FALSE; 
                    
                $stmt->bindValue("$bind", $value, $param);
            }
            

            // Asignar el enlazamiento de la KEY
            $keyParam = PDO::PARAM_INT;
            $keyBind = ':'.$this->EntityDefinition->Key;

            $keyValue = $this->ReflectedEntity
                            ->getProperty($this->EntityDefinition->KeyProperty->Name)
                            ->getValue($entity);

            $stmt->bindValue("$keyBind", $keyValue, $keyParam);
            
            // Ejecutar la sentencia            
            $stmt->execute();
        }

        public function Insert($entity, $transaction = null) {

            $oracleNewId = 0;

            if ($this->ReflectedEntity == null)
                $this->ReflectedEntity = new \ReflectionClass($this->EntityDefinition->Entity);

            $fields = array();
            $bindings = array();
            $values = array();
            $bindingCasts = array(); // para casos especiales en los cuales se debe interponer algun cast o formateador al campo
            $types = array();

            $idx = 0;

            if ($this->Domain->Type == 'oracle') {
                // Se debe obtener el siguiente elemento en la secuencia
                $oracleNewId = $this->Connection->sequenceNextVal($this->EntityDefinition->Table . "_SEQ");
                
                $fields[0] = $this->EntityDefinition->KeyProperty->ColumnName;
                $bindings[0] = ':' . $this->EntityDefinition->KeyProperty->ColumnName;
                $bindingCasts[0] = ':' . $this->EntityDefinition->KeyProperty->ColumnName;
                $values[0] = $oracleNewId;
                //$types[0] = 'sequence';
                $types[0] = '';
                $idx++;
            }

            foreach($this->EntityDefinition->Properties as $property) {       

                if (!$property->Ignore && !$property->IsKey) {

                    $fields[$idx] = $property->ColumnName;
                    $bindings[$idx] = ':' . $property->ColumnName;

                    // if ($property->ColumnType == 'int')
                    //     $types[$idx] = 'int';
                    // else if ($property->ColumnType == 'datetime')
                    //     $types[$idx] = 'datetime';
                    // else
                        $types[$idx] = '';   
                        
                    $bindingCasts[$idx] = $this->Cast($property);
                    // if ($this->Domain->Type == 'oracle' && strtolower($property->ColumnType) == 'datetime') {
                    //     $bindingCasts[$idx] = "TO_DATE(:" . $property->ColumnName . ", 'yyyy-mm-dd hh24:mi:ss')";
                    // }
                    // else if ($this->Domain->Type == 'sqlserver' && strtolower($property->ColumnType) == 'datetime') {
                    //     $bindingCasts[$idx] = "convert(datetime, :" . $property->ColumnName . ", 20)";
                    // }
                    // else {
                    //     $bindingCasts[$idx] = ':' . $property->ColumnName;
                    // }
                    
                    $values[$idx] = $this->Value($property, $entity);
                    // $values[$idx] = $this->ReflectedEntity
                    //                     ->getProperty($property->Name)
                    //                     ->getValue($entity);                 

                    $idx++;
                }                
            }

            // Construir la sentencia SQL de inserción
            $qry = 'INSERT INTO '. $this->EntityDefinition->Table . ' ('.join(",", $fields).') VALUES ('.join(",", $bindingCasts).')';

            // Preparar la sentencia
            if (!isset($transaction)) {
                $transaction = $this->Connection;
            }
            $stmt = $transaction->prepare($qry);

            // Asignar el enlazamiento (bindings)
            for( $idxb = 0; $idxb < count($bindings); $idxb++) {
                $value = $values[$idxb];
                $bind = $bindings[$idxb];

                if(is_int($value) || $types[$idxb] == 'sequence' || $types[$idxb] == 'int') {
                    $param = PDO::PARAM_INT;
                }
                elseif(is_double($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif(is_float($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif(is_bool($value)) {
                    $param = PDO::PARAM_BOOL;
                }
                elseif(is_null($value)) {
                    $param = PDO::PARAM_NULL;
                }
                elseif(is_string($value)) {
                    $param = PDO::PARAM_STR;
                }
                elseif($value instanceof \DateTime) {
                    $param = PDO::PARAM_STR;
                    $value = $value->format('Y-m-d H:i:s');
                }
                else
                    $param = FALSE;     
                    
                $stmt->bindValue("$bind", $value, $param);
            }
            
            // Ejecutar la sentencia y obtener el ID del nuevo registro
            /*
            if (!self::IsTransactionOpen()) {
                $this->Connection->beginTransaction();
            }
            */
            $result = $stmt->execute();

            if ($this->Domain->Type == 'oracle') {
                $newID = $oracleNewId;
            }
            else if ($this->Domain->Type == 'mysql') {
                $newID = $transaction->lastInsertId();
            }
            else {
                $newID = $transaction->lastInsertId();
            }

            /*
            if (!self::IsTransactionOpen()) {
                $this->Connection->commit();
            }
            */

            if ($newID <= 0)
                return null;

            // Asignar el nuevo ID a la entidad
            $reflectionClass = new \ReflectionClass($this->EntityDefinition->Entity);
            $reflectionClass->getProperty($this->EntityDefinition->KeyName)->setValue($entity, intval($newID));

            // Retornar la entidad con el nuevo ID asignado a la llave
            return $entity;
        }

        public function Delete($keyValue, $transaction = null) 
        {
            $qry = 'DELETE FROM '. $this->EntityDefinition->Table .' WHERE '. $this->EntityDefinition->Key. ' = :' . $this->EntityDefinition->Key;

            if (!isset($transaction)) {
                $transaction = $this->Connection;
            }
            $stmt = $transaction->prepare($qry);

            // Asignar la condicion de selección
            $keyParam = PDO::PARAM_INT;
            $keyBind = ':'.$this->EntityDefinition->Key;

            $stmt->bindValue("$keyBind", $keyValue, $keyParam);            

            $stmt->execute();
        }

        public function DeleteBy($bindings, $transaction = null)
        {
            $qry = 'DELETE FROM ' . $this->EntityDefinition->Table;
            
            /* @var $var BindVariable */
            $count = 0;
            $where = '';
            $bind = array();
            foreach ($bindings as $key => $var) {

                if ($count > 0) {
                    $where .= '
                        AND
                        ';
                }

                if ($var->Operator == 'BETWEEN') {
                    $where .= $var->Field . ' BETWEEN :var' . $count . ' AND :var' . ($count + 1) . ' ';

                    $bind['var' . $count] = $var->Operand1;
                    $count++;
                    $bind['var' . $count] = $var->Operand2;
                } else {
                    $where .= $var->Field . ' ' . $var->Operator . ' :var' . $count . ' ';
                    $bind['var' . $count] = $var->Operand1;
                }

                $count++;
            }

            if ($where != '') {
                $qry .= ' WHERE ' . $where;
            } else {
                throw new \Exception("NO PUDE REALIZAR UNA ELIMINACION DESDE UNA TABLA SIN CONDICIONES DE EXCLUSION -WHERE-");
                return;
            }
            
            // preparar sentencia
            if (!isset($transaction)) {
                $transaction = $this->Connection;
            }
            $stmt = $transaction->prepare($qry);
            
            // ligar variables
            foreach ($bind as $v => $value) {
                if(is_int($value))
                    $param = PDO::PARAM_INT;
                elseif(is_bool($value))
                    $param = PDO::PARAM_BOOL;
                elseif(is_null($value))
                    $param = PDO::PARAM_NULL;
                elseif(is_string($value))
                    $param = PDO::PARAM_STR;
                else
                    $param = FALSE;
                    
                if($param)
                    $stmt->bindValue(":$v", $value, $param);
            }

            $stmt->execute();
        }
    }
}
