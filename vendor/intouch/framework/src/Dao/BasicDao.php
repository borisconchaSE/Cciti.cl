<?php

namespace Intouch\Framework\Dao {
    date_default_timezone_set('America/Santiago');

    use Intouch\Framework\Collection\AttachDefinition;
    use Intouch\Framework\Collection\GenericCollection;
    use Intouch\Framework\Collection\MergeDefinition;
    use Intouch\Framework\Configuration\ConnectionConfig;
    use Intouch\Framework\Dao\Entity\EntityDefinition;
    use Intouch\Framework\Mapper\Mapper;
    use \PDO;

    class BasicDao extends ExecuteDao
    {        
        protected $Connection = null;
        protected $EntityDefinition = null;

        public $Domain = null;

        public function GetEntityName() {
            return $this->EntityDefinition->Entity;
        }

        function __construct($entity, $domain) {

            // Obtener la información de la entidad
            //$this->EntityDefinition = EntityDefinition::GetInstance(new \ReflectionClass($entity));
            $this->EntityDefinition = EntityDefinition::Instance($entity); // << version con control de caché y almacenamiento en redis

            // Asignar el dominio para el acceso a datos
            // Buscar el dominio
            $dominios = ConnectionConfig::Instance();

            $this->Domain = $dominios[$domain];
            //$this->Domain = $domain;

            // Obtener una instancia de conexión PDO para utilizar
            // con este DAO
            if ($this->Connection === null) {
                $this->Connection = DataConfig::GetPDOConnection($domain);
            }
        }

        public function Query(Queryable $query, bool $returnFirstRecord = false) {

            $qry = $query->Build();
            $stmt = $this->Connection->prepare($qry->Statement);

            foreach($qry->Bindings as $binding) {

                $param = FALSE;

                if (is_int($binding->BindingValue))
                    $param = PDO::PARAM_INT;
                else if(is_bool($binding->BindingValue))
                    $param = PDO::PARAM_BOOL;
                else if(is_null($binding->BindingValue))
                    $param = PDO::PARAM_NULL;
                else if(is_string($binding->BindingValue))
                    $param = PDO::PARAM_STR;
                else
                    $param = PDO::PARAM_STR;
                    
                if($param)
                    $stmt->bindValue($binding->BindingVariable, $binding->BindingValue, $param);
            }

            $results = new GenericCollection(
                Key: $qry->FromDefinition->Key,
                Values: $this->ExecuteStatementForQueryable($stmt),
                DtoName: $qry->FromDefinition->Entity
            );

            // FROM results
            $fromKey = $qry->FromDefinition->Key;

            // Obtener el DISTINCT de la tabla padre
            $parentDistinct = $results->DistinctItem($fromKey)->Values;
            $parentEntities = Mapper::ToEntities($parentDistinct, $qry->FromDefinition->Entity, '', true);

            $fromResults = new GenericCollection(
                Key: $qry->FromDefinition->Key,
                Values: $parentEntities,
                DtoName: $qry->FromDefinition->Entity
            );

            // JOIN results
            foreach($qry->JoinDefinitions as $joinDefinition) {
                $joinKey = $joinDefinition->TablePrefix . '.' . $joinDefinition->Key;

                $distincts = $results->DistinctItem($joinKey);

                $joinResults = new GenericCollection(
                    Key: $joinDefinition->Key,
                    Values: Mapper::ToEntities(
                            $distincts, 
                            $joinDefinition->Entity,
                            $joinDefinition->TablePrefix, true
                    ),
                    DtoName: $joinDefinition->Entity
                );

                // Incorporarlo al resultado principal como JOIN
                $fromResults->MergeChildren(
                    new MergeDefinition(
                        Values: $joinResults,
                        ParentKey: (isset($joinDefinition->CustomParentKey) && $joinDefinition->CustomParentKey != '') ? $joinDefinition->CustomParentKey : $qry->FromDefinition->Key,
                        ChildReferenceKey: (isset($joinDefinition->CustomForeignKey) && $joinDefinition->CustomForeignKey != '') ? $joinDefinition->CustomForeignKey : $qry->FromDefinition->Key
                    )
                );
            }

            // LEFT JOIN results
            foreach($qry->LeftJoinDefinitions as $joinDefinition) {
                $joinKey = $joinDefinition->TablePrefix . '.' . $joinDefinition->Key;
                $joinResults = new GenericCollection(
                    Key: $joinDefinition->Key,
                    Values: Mapper::ToEntities(
                            $results->DistinctItem($joinKey), 
                            $joinDefinition->Entity,
                            $joinDefinition->TablePrefix, true
                    ),
                    DtoName: $joinDefinition->Entity
                );

                // Incorporarlo al resultado principal como JOIN
                $fromResults->MergeChildren( 
                    new MergeDefinition(
                        Values: $joinResults,
                        ParentKey: (isset($joinDefinition->CustomParentKey) && $joinDefinition->CustomParentKey != '') ? $joinDefinition->CustomParentKey : $qry->FromDefinition->Key,
                        ChildReferenceKey: (isset($joinDefinition->CustomForeignKey) && $joinDefinition->CustomForeignKey != '') ? $joinDefinition->CustomForeignKey : $qry->FromDefinition->Key
                    )
                );
            }

            // WITH results
            foreach($qry->WithDefinitions as $withDefinition) {
                $withKey = $withDefinition->TablePrefix . '.' . $withDefinition->Key;
                $withResults = new GenericCollection(
                    Values: Mapper::ToEntities(
                        $results->DistinctItem($withKey), 
                        $withDefinition->Entity,
                        $withDefinition->TablePrefix, true
                    ),
                    DtoName: $withDefinition->Entity
                );

                $fromResults->Attach( new AttachDefinition(
                        Values: $withResults,
                        AttachPrimaryKey: (isset($withDefinition->CustomParentKey) && $withDefinition->CustomParentKey != '') ? $withDefinition->CustomParentKey : $withDefinition->Key,
                        ForeignKey: (isset($withDefinition->CustomForeignKey) && $withDefinition->CustomForeignKey != '') ? $withDefinition->CustomForeignKey : $withDefinition->Key
                    )
                );
            }

            if ($returnFirstRecord) {            
                if (isset($fromResults) && $fromResults->Count() > 0) {
                    return $fromResults->First();
                }
                else {
                    return null;
                }
            }
            else {
                return $fromResults->Values;
            }
        }

        public function Find($keyValue)
        {
            $stmt = $this->Connection->prepare('
                SELECT ' . $this->EntityDefinition->NotIgnoredFields . '
                FROM ' . $this->EntityDefinition->Table . '
                WHERE ' . $this->EntityDefinition->Key . ' = :keyValue
            ');

            $stmt->bindParam(':keyValue', $keyValue);

            return $this->ExecuteStatementForObject($stmt);
        }

        public function FindByForeign($foreignKey, $foreignKeyValue) {

            $foreignKeyBind = ':'.$foreignKey;

            $qry = '
                SELECT ' . $this->EntityDefinition->NotIgnoredFields . '
                FROM ' . $this->EntityDefinition->Table . '
                WHERE ' . $foreignKey . ' = '.$foreignKeyBind;        

            $stmt = $this->Connection->prepare($qry);

            $stmt->bindValue("$foreignKeyBind", $foreignKeyValue);

            return $this->ExecuteStatementForObject($stmt);
        }

        function GetByCodigoRegistro($codigoRegistro) {
            $stmt = $this->Connection->prepare("
                SELECT 
                    " . $this->EntityDefinition->NotIgnoredFields . "
                FROM " . $this->EntityDefinition->Table . "
                WHERE CodigoRegistro = :keyValue
            ");

            $stmt->bindParam(':keyValue', $codigoRegistro);

            return $this->ExecuteStatementForObjects($stmt);
        }
        
        public function GetByForeign($foreignKey, $foreignKeyValue, $orderFields = '') {

            $foreignKeyBind = ':'.$foreignKey;

            $qry = '
            SELECT ' . $this->EntityDefinition->NotIgnoredFields . '
            FROM ' . $this->EntityDefinition->Table . '
            WHERE ' . $foreignKey . ' = '.$foreignKeyBind;
        
            if ($orderFields != '') {
                $qry .= ' ORDER BY '. $orderFields;
            }

            $stmt = $this->Connection->prepare($qry);

            $stmt->bindValue("$foreignKeyBind", $foreignKeyValue);

            return $this->ExecuteStatementForObjects($stmt);
        }

        public function GetBy($bindings, $orderFields = '')
        {
            $qry = 'SELECT ' . $this->EntityDefinition->NotIgnoredFields . ' FROM ' . $this->EntityDefinition->Table;
            
            /* @var $var BindVariable */
            $count = 0;
            $where = '';
            $bind = array();

            if ($bindings instanceof BindVariable) {
                $bindings = [$bindings];
            }

            foreach ($bindings as $key => $var) {

                if ($count > 0) {
                    $where .= '
                        AND
                        ';
                }

                if (strtoupper($var->Operator) == 'LIKE') {

                    // Ver si es campo fecha
                    if ($this->Domain->Type == "sqlserver") {
                        $where .= $var->Field . " LIKE :var" . $count . " ";
                    }
                    else if ($this->Domain->Type == "mysql"){
                        $where .= $var->Field . " LIKE :var" . $count . " ";
                    }
                    else {
                        $where .= $var->Field . " LIKE :var" . $count . " ";
                    }                    

                    $bind['var' . $count] = $var->Operand1;
                    $count++;

                }
                else if (strtoupper($var->Operator) == 'BETWEEN') {

                    // Ver si es campo fecha
                    if ($this->Domain->Type == "sqlserver" 
                            && isset($this->EntityDefinition->Properties[$var->Field])
                            && $this->EntityDefinition->Properties[$var->Field]->ColumnType == 'datetime') {
                                $where .= $var->Field . ' BETWEEN CONVERT(DATETIME, :var' . $count . ', 121) AND CONVERT(DATETIME, :var' . ($count + 1) . ', 121) ';
                    }
                    else {
                        $where .= $var->Field . ' BETWEEN :var' . $count . ' AND :var' . ($count + 1) . ' ';
                    }

                    

                    $bind['var' . $count] = $var->Operand1;
                    $count++;
                    $bind['var' . $count] = $var->Operand2;  

                    $count++;              
                }
                else if (strtoupper($var->Operator) == 'IN' && is_array($var->Operand1)) {

                    $variables = '';
                    foreach($var->Operand1 as $op) {
                        if ($variables != '')
                            $variables .= ', ';
                        $variables .= ':var' . $count;
                        $bind['var' . $count] = $op;
                        $count++;
                    }

                    $where .= $var->Field . ' ' . $var->Operator . ' (' . $variables . ') ';
                }
                else {

                    if ($this->Domain->Type == "sqlserver" 
                        && isset($this->EntityDefinition->Properties[$var->Field])
                        && $this->EntityDefinition->Properties[$var->Field]->ColumnType == 'datetime') {
                            $where .= $var->Field . ' ' . $var->Operator . ' CONVERT(DATETIME, :var' . $count . ', 121) ';
                    }
                    else {
                        $where .= $var->Field . ' ' . $var->Operator . ' :var' . $count . ' ';
                    }

                    
                    $bind['var' . $count] = $var->Operand1;

                    $count++;
                }
            }

            if ($where != '') {
                $qry .= ' WHERE ' . $where;
            }

            if ($orderFields != '') {
                $qry .= ' ORDER BY '. $orderFields;
            }
            
            // preparar sentencia
            $stmt = $this->Connection->prepare($qry);
            
            // ligar variables
            foreach ($bind as $v => $value) {
                //echo "...binding  :" . $v . " with value [" . $value . "] <br>";
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

            return $this->ExecuteStatementForObjects($stmt);
        }

        public function FindBy($bindings, $orderFields = '')
        {
            $select = "SELECT ";

            if ($this->Domain->Type == 'sqlserver') {
                $select .= 'TOP 1 ';
            }
            $qry = $select . $this->EntityDefinition->NotIgnoredFields . ' FROM ' . $this->EntityDefinition->Table;    
            
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
                }  
                else if (strtoupper($var->Operator) == 'IN' && is_array($var->Operand1)) {

                    $variables = '';
                    foreach($var->Operand1 as $op) {
                        if ($variables == '')
                            $variables .= ', ';
                        $variables .= ':var' . $count;
                        $bind['var' . $count] = $op;
                        $count++;
                    }

                    $where .= $var->Field . ' ' . $var->Operator . $variables . ' ';
                }
                else {
                    $where .= $var->Field . ' ' . $var->Operator . ' :var' . $count . ' ';
                    $bind['var' . $count] = $var->Operand1;
                }

                $count++;
            }

            if ($where != '') {
                $qry .= ' WHERE ' . $where;
            }

            if ($this->Domain->Type == 'oracle') {
                if ($where == '') {
                    $where = ' WHERE ROWNUM <= 1';
                }
                else {
                    $where .= ' AND ROWNUM <= 1';
                }
            }

            if ($orderFields != '') {
                $qry .= ' ORDER BY '. $orderFields;
            }

            if ($this->Domain->Type == 'mysql')
                $qry .= ' LIMIT 1';
            
            // preparar sentencia
            $stmt = $this->Connection->prepare($qry);
            
            // ligar variables
            foreach ($bind as $v => $value) {
                //echo "...binding  :" . $v . " with value [" . $value . "] <br>";
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

            return $this->ExecuteStatementForObject($stmt);
        }

        public function GetAll($order = "")
        {
            $qry = 'SELECT ' . $this->EntityDefinition->NotIgnoredFields . ' FROM ' . $this->EntityDefinition->Table . (($order != '') ? ' ORDER BY '.$order : '');
            $stmt = $this->Connection->prepare($qry);

            return $this->ExecuteStatementForObjects($stmt);
        }

        public function GetByStmt($stmt) {
            return $this->ExecuteStatementForObjects($stmt);
        }

        public function FindByStmt($stmt) {
            return $this->ExecuteStatementForObject($stmt);
        }

        public function GetStatementFromFile($filePath) {            
            // Abrir el archivo y retornar la query
            if (\file_exists(__DIR__ . '/' . $filePath)) {
                return \file_get_contents(__DIR__. '/' . $filePath);
            }
            else {
                throw new \Exception("Archivo no existe");
            }
        }

        function SumFieldByForeign($fieldName, $foreignKeyName, $foreignKeyValue) {
            $stmt = $this->Connection->prepare('
                SELECT SUM(' . $fieldName . ') AS SUMRESULT 
                FROM ' . $this->EntityDefinition->Table . '
                WHERE ' . $foreignKeyName . ' = :foreignKeyValue
            ');

            $stmt->bindParam(':foreignKeyValue', $foreignKeyValue);
            return $this->ExecuteScalar($stmt);
        }

        function CountByForeign($foreignKeyName, $foreignKeyValue) {
            $stmt = $this->Connection->prepare('
                SELECT COUNT(1) AS COUNTRESULT 
                FROM ' . $this->EntityDefinition->Table . '
                WHERE ' . $foreignKeyName . ' = :foreignKeyValue
            ');

            $stmt->bindParam(':foreignKeyValue', $foreignKeyValue);
            return $this->ExecuteScalar($stmt);
        }

        function Count() {
            $stmt = $this->Connection->prepare('
                SELECT COUNT(1) AS COUNTRESULT 
                FROM ' . $this->EntityDefinition->Table);

            return $this->ExecuteScalar($stmt);
        }
        
        public function CountBy($bindings)
        {
            $qry = 'SELECT COUNT(1)  AS COUNTRESULT FROM ' . $this->EntityDefinition->Table;
            
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
                }  
                else if (strtoupper($var->Operator) == 'IN' && is_array($var->Operand1)) {

                    $variables = '';
                    foreach($var->Operand1 as $op) {
                        if ($variables != '')
                            $variables .= ', ';
                        $variables .= ':var' . $count;
                        $bind['var' . $count] = $op;
                        $count++;
                    }

                    $where .= $var->Field . ' ' . $var->Operator . ' (' . $variables . ') ';
                }
                else {
                    $where .= $var->Field . ' ' . $var->Operator . ' :var' . $count . ' ';
                    $bind['var' . $count] = $var->Operand1;
                }

                $count++;
            }

            if ($where != '') {
                $qry .= ' WHERE ' . $where;
            }
            
            // preparar sentencia
            $stmt = $this->Connection->prepare($qry);
            
            // ligar variables
            foreach ($bind as $v => $value) {
                //echo "...binding  :" . $v . " with value [" . $value . "] <br>";
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

            return $this->ExecuteScalar($stmt);
        }

        public function GetIN($foreignKey, $foreignKeyValue, $orderFields = '') {
         
            // Se genera un campo vacio para cargar los KeyValues
            $values = '';

      

            //Verifico que KeyValue se un arreglo
            if(is_array($foreignKeyValue) OR is_object($foreignKeyValue)):
                //Si es numerico uso la función Implode para convertir el Arreglo en String
                if(is_numeric($foreignKeyValue[0])):
                    $values = implode(', ',$foreignKeyValue);
                
                else:
                    //Si no es un Arreglo Calculco el total de valores pasados

                    $t = count($foreignKeyValue) - 1;
                     
                    //Recorro los valores y los incorporo a la variable Values
                    foreach($foreignKeyValue as $k => $vl){

                        //Si la Llave ES IGUAL al total de registros no se agrega una coma al final
                         if($k == $t){
                            
                            $values .= "'".$vl ."'";

                         }else{
                            $values .= "'".$vl ."' , ";
                         }  

                    }
                         
                endif;
            else:
                $values = $foreignKeyValue;            
            endif;
       
            //Se define los arreglos
            $qry = '
            SELECT ' . $this->EntityDefinition->NotIgnoredFields . '
            FROM ' . $this->EntityDefinition->Table . '
            WHERE ' . $foreignKey . ' IN (' . $values . ')';


            if ($orderFields != '') {
                $qry .= ' ORDER BY '. $orderFields;
            }

            $stmt = $this->Connection->prepare($qry);

            return $this->ExecuteStatementForObjects($stmt);


        }

        function Max($field, $ignoreBlanks = false) {

            $qry = 'SELECT Max(' . $field . ') AS MAXRESULT FROM ' . $this->EntityDefinition->Table;

            if ($ignoreBlanks) {
                $qry .= " WHERE " . $field . " IS NOT NULL AND " . $field . " <> ''";
            }

            $stmt = $this->Connection->prepare($qry);
            return $this->ExecuteScalar($stmt);
        }
        
        function Min($field, $ignoreBlanks = false) {
            
            $qry = 'SELECT Min(' . $field . ') AS MINRESULT FROM ' . $this->EntityDefinition->Table;

            if ($ignoreBlanks) {
                $qry .= " WHERE " . $field . " IS NOT NULL AND " . $field . " <> ''";
            }

            $stmt = $this->Connection->prepare($qry);
            return $this->ExecuteScalar($stmt);
        }

        
    }
}
