<?php

namespace Intouch\Framework\Collection;

use ArrayIterator;
use Intouch\Framework\Dao\Entity\EntityDefinition;
use Intouch\Framework\Mapper\Mapper;
use IteratorAggregate;
use JsonSerializable;

class Monitor {

    public static $FromTimeExecutions = 0;
    public static $FromTimeExecutionTime = 0;
    public static $MergeChildrenExecutions = 0;
    public static $MergeChildrenExecutionTime = 0;
    public static $KeyIndexExecutions = 0;
    public static $KeyIndexExecutionTime = 0;
    public static $MinExecutions = 0;
    public static $MinExecutionTime = 0;
    public static $IndexedMinExecutions = 0;
    public static $IndexedMinExecutionTime = 0;

}

class Cache {

    public $PrimaryIndex = array();    
    public $SecondaryIndexes = array();

    // Caché implementation
    public $LastOrderedBy = array();
    public $LastOrderedByCondition = "";

    public array|GenericCollection $LastWhere = [];
    public $LastWhereCondition = "";
    
    public $LastFirstWhere = null;
    public $LastFirstWhereCondition = "";

    public $LastAvg = 0;
    public $LastAvgField = "";

    public $LastSum = 0;
    public $LastSumField = "";

    public $LastMax = 0;
    public $LastMaxField = "";

    public $LastMin = 0;
    public $LastMinField = "";

    public $LastItemMax = null;
    public $LastItemMaxField = "";

    public $LastMinItem = null;
    public $LastMinItemField = "";   
    
    public $LastCount = -1;

    public $LastDistinctField = "";
    public $LastDistinct = null;

    public $LastDistinctItemField = "";
    public $LastDistinctItem = null;

}

/**
 * GenericColletion
 * 
 * Clase genérica que permite manipular un arreglo de objetos como una colección
 * 
 * @package Collection
 * @author Claudio González <claudioalfonso@gmail.com>
 */
class GenericCollection implements IteratorAggregate, JsonSerializable {

    public $Type = "Collection";
    public $DtoDefinition = null;    
    public $Cache;
    protected $EntityDefinition;
    
    /** 
     * @param array $definition
     * 
     * Arreglo asociativo con los elementos necesarios para crear la coleccion
     * 
     * Ej:
     * 
     * ["Key" => "Id", "DtoName" => "myClass", "Values" => $miArreglo ]
     * 
     */
    function __construct(
        protected $DtoName    = '',
        protected $Key        = '',
        public    $Values     = [],
        protected $Indexes    = [],
        protected $ClassName  = '',
        public    $ReflectDto = false
    )
    {
        $this->Cache = new Cache();

        if (!isset($this->Values)) {
            $this->Values = [];
        }

        if ($this->DtoName == '' && $this->Key != '') {
            throw new \Exception("Debe especificar el DTO para usar una GenericCollection con designacion de KEY");
        }

        if (isset($this->Indexes) && count($this->Indexes) > 0 && $this->Key == '') {
            throw new \Exception("Debe especificar una llave -Key- cuando desea especificar índices secundarios");
        }

        if ($this->DtoName != '' && $this->ClassName == '') {
            $this->ClassName = $this->GetClassName($this->DtoName);
        }

        $dtStartIndexMonitor = microtime(true);
        if ($this->Key != '' && isset($this->Values)) {
            $this->CreatePrimaryIndex($this->Key);
        }        

        if (isset($this->Indexes) && count($this->Indexes) > 0) {
            $this->CreateSecondaryIndexes($this->Indexes);
        }
        $dtEndIndexMonitor = microtime(true);

        // Obtener las propiedades reflectadas de la coleccion
        if ($this->DtoName != '' && $this->ReflectDto) {
            $this->EntityDefinition = EntityDefinition::Instance($this->DtoName);
        }

        Monitor::$KeyIndexExecutions++;
        Monitor::$KeyIndexExecutionTime += ($dtEndIndexMonitor - $dtStartIndexMonitor);
    }

    public function __clone() {
        // Obtenemos una copia de los valores
        $clonedValues = array();

        foreach($this->Values as $val) {

            $clonedItem = new $this->DtoName();
            // recorrer los valores
            foreach($val as $propname => $propvalue) {
                $clonedItem->$propname = $propvalue;
            }
            array_push($clonedValues, $clonedItem);
        }

        $this->Key = $this->Key;
        $this->DtoName = $this->DtoName;
        $this->Values = $clonedValues;
    }

    public function jsonSerialize() {

        return $this->Values;
        
    }

    public function getIterator() {
        return new ArrayIterator($this->Values);
    }

    public function CreateIndex(array $values, $key, $secondaryKey = "") {

        $outputArray = array();

        foreach($values as $idx => $value) {

            // Convertir el valor de la llave en un número para acelerar las búsquedas, de ser posible
            if (is_numeric($value->$key)) {
                $valKey = $value->$key * 1;
            }
            else {
                $valKey = $value->$key;
            }
            
            if ($secondaryKey == "") {
                // Si es sólo la llave principal, indexamos en el indice principal
                // el indice del arreglo asociado a dicha llave
                $outputArray[$valKey] = $idx;
            }
            else {
                // Si es un índice secundario, sólo indexamos el valor
                // de la llave principal como un subarreglo dentro
                // del índice asociado a la llave secundaria
                //
                // arr['idsecundario'] = [ 'key1', 'key2', 'key3', ... , 'keyN']
                //
                // Convertir el valor de la llave secundaria en un número para acelerar las búsquedas

                // obtener el valor multinivel en caso de existir
                $valueMultilevel = Mapper::EvaluateKeyMultilevel($value, $secondaryKey); // $this->GetMultiLevelKeyValue($value, $secondaryKey);

                if (is_numeric($valueMultilevel)) {
                    $secKey = $valueMultilevel * 1;
                }
                else {
                    $secKey = $valueMultilevel;                    
                }

                if ( !isset( $outputArray[$secKey] ) ) {
                    $outputArray[$secKey] = array();
                }

                // Agregar la llave principal como subitem (sólo si no existe)                
                if (!isset( $outputArray[ $secKey ] [ $valKey ] )) {
                    // asociamos el IDX del arreglo original en el indice secundario
                    $outputArray[$secKey][$valKey] = $idx;
                }
            }
        }

        // Para acelerar las búsquedas, debemos ordenar el arreglo por su llave
        ksort($outputArray);

        return $outputArray;
    }

    private function CreatePrimaryIndex(string $key) {        
        $this->Cache->PrimaryIndex = $this->CreateIndex($this->Values, $key);
    }

    private function CreateSecondaryIndexes(array $indices) {

        foreach($indices as $indice) {
            // Generar un índice secundario
            if (!isset($this->Cache->SecondaryIndexes[$indice])) {
                $this->Cache->SecondaryIndexes[$indice] = $this->CreateIndex($this->Values, $this->Key, $indice);
            }
        }

    }

    public function Reindex() {
        // Regenerar el indice principal
        if ($this->Key != "")
            $this->Cache->PrimaryIndex = $this->CreateIndex($this->Values, $this->Key);

        // Regenerar los indices secundarios
        foreach($this->Cache->SecondaryIndexes as $key => $indice) {            
            $this->Cache->SecondaryIndexes[$key] = $this->CreateIndex($this->Values, $this->Key, $key);
        }
    }

    /**
     * Ordena la colección según las propiedades del objeto
     *
     * @param string $fields
     * Especifica las propiedades y el orden en el que se realizará el ordenamiento.
     * Admite el uso de ASC y DESC.
     * 
     * Si no es especifica, el valor por defecto del orden de cada campo será ASC.
     * 
     * Ej:
     * 
     *      $mycollection->OrderBy("FechaRegistro DESC, IdComuna");
     * 
     * @author Claudio Gonzalez <claudioalfonso@gmail.com>
     * @return void
     */ 
    public function OrderBy($fields) {

        $orders = explode(",", trim($fields));

        $arrOrders = array();
        foreach($orders as $order) {            
            $parts = explode(' ', trim($order));
            $order = new \StdClass();
            $order->Fieldname = $parts[0];

            if (count($parts) == 1 || ( count($parts) == 2 && strtolower(trim($parts[1])) != "desc" )) {
                $order->Direction = "ASC";                
            }
            else if ( count($parts) == 2 && strtolower(trim($parts[1])) == "desc" ) {
                $order->Direction = "DESC";
            }

            $arrOrders[$parts[0]] = $order;
        }

        // Ordenar el arreglo
        usort($this->Values, function($a, $b) use($arrOrders) {

            // Comparar los valores en el orden para ir viendo el orden
            foreach($arrOrders as $order) {
                $fieldName = $order->Fieldname;
                $campoSource = $a->$fieldName;
                $campoCompare = $b->$fieldName;
                $direction = $order->Direction;

                if ($direction == "ASC") {
                    // Si a es mayor, va primero
                    if ($campoSource != $campoCompare) {
                        return ($campoSource > $campoCompare) ? 1 : -1;
                    }
                }
                else {
                    // Si a es menor, va primero
                    return ($campoSource < $campoCompare) ? 1 : -1;
                }
            }

            // No se encontraron diferencias
            return 0;
        });

        // Reindexar
        $this->Reindex();
    }

    /**
     * Crea una copia de la colección, ordenada según las propiedades del objeto
     *
     * @param string $fields
     * Especifica las propiedades y el orden en el que se realizará el ordenamiento.
     * Admite el uso de ASC y DESC.
     * 
     * Si no es especifica, el valor por defecto del orden de cada campo será ASC.
     * 
     * Ej:
     * 
     *      $newCollection = $mycollection->OrderedBy("FechaRegistro DESC, IdComuna");
     * 
     * 
     * @author Claudio Gonzalez <claudioalfonso@gmail.com>
     * @return GenericCollection
     */
    public function OrderedBy($fields) {

        // Revisar caché
        if ($fields != $this->Cache->LastOrderedByCondition) {

            // Actualizar caché
            $this->Cache->LastOrderedByCondition = $fields;

            $clone = null;

            $orders = explode(",", trim($fields));

            $arrOrders = array();
            foreach($orders as $order) {            
                $parts = explode(' ', trim($order));
                $order = new \StdClass();
                $order->Fieldname = $parts[0];

                if (count($parts) == 1 || ( count($parts) == 2 && strtolower(trim($parts[1])) != "desc" )) {
                    $order->Direction = "ASC";                
                }
                else if ( count($parts) == 2 && strtolower(trim($parts[1])) == "desc" ) {
                    $order->Direction = "DESC";
                }

                $arrOrders[$parts[0]] = $order;
            }

            // Clonamos los valores de la colección para no afectar los valores originales, los cuales
            // mantienen su orden inicial.
            $clone = clone $this;

            // Ordenar el arreglo
            usort($clone->Values, function($a, $b) use($arrOrders) {

                // Comparar los valores en el orden para ir viendo el orden
                foreach($arrOrders as $order) {
                    $fieldName = $order->Fieldname;
                    $campoSource = $a->$fieldName;
                    $campoCompare = $b->$fieldName;
                    $direction = $order->Direction;

                    if ($direction == "ASC") {
                        // Si a es mayor, va primero
                        if ($campoSource != $campoCompare) {
                            return ($campoSource > $campoCompare) ? 1 : -1;
                        }
                    }
                    else {
                        // Si a es menor, va primero
                        return ($campoSource < $campoCompare) ? 1 : -1;
                    }
                }

                // No se encontraron diferencias
                return 0;
            });

            // Actualizar caché
            $this->Cache->LastOrderedBy = $clone;
        }

        return $this->Cache->LastOrderedBy;
    }
    
    /**
    * Encuentra el primer elemento de la colección según el orden actual
    * 
    * 
    * @author Claudio Gonzalez <claudioalfonso@gmail.com>
    * @return object
    */
    public function First() {
        $cantidad = count($this->Values);

        if ($cantidad <= 0)
            return null;
        else
            return $this->Values[0];        
    }

    public function Last() {
        $cantidad = count($this->Values);

        if ($cantidad <= 0)
            return null;
        else
            return $this->Values[$cantidad-1];
    }

    public function Count() {

        if ($this->Cache->LastCount < 0) {
            // Actualizar caché
            $this->Cache->LastCount = count($this->Values);
        }

        return $this->Cache->LastCount;
    }

    private function RemoveItem($item) {
        $pos = -1;

        for ($i = 0; $i < count($this->Values); $i++) {
            if ($this->Values[$i] == $item) {
                $pos = $i;
                break;
            }
        }

        if ($pos >= 0) {
            \array_splice($this->Values, $pos, 1);
        }
    }

    public function Remove($item, $reIndex = true) {

        $this->RemoveItem($item);

        // Invalidar la caché
        $this->InvalidarCache();       

        // Reindexar        
        if ($reIndex)
            $this->Reindex();
    }

    public function RemoveWhere($condicion, $reIndex = true) {

        $condicion = $this->SingleEqualSignReplace($condicion);

        // Buscar variables dentro de la condicion
        $pivote = get_class_vars($this->DtoName); // new $this->DtoName();

        foreach($pivote as $propname => $propvalue) {
            $condicion = $this->TokenReplace($propname, "\$value->" . $propname, $condicion);
        }

        // Clonamos el arreglo
        $clone = clone $this;
        $deletedValues = array();

        $idx= 0;
        foreach($clone->Values as $value) {
            $resultado = eval('return (' . $condicion . ');');

            if ($resultado) {
                $deletedValues[$idx] = $value;
                $idx++;
            }
        }

        // Borrar los elementos encontrados
        foreach($deletedValues as $delete) {
            $this->RemoveItem($delete);
        }

        $this->InvalidarCache();
        // Reindexar        
        if ($reIndex)
            $this->Reindex();
    }

    private function AddItem($item, $first = false) {

        if ($first) {
            array_unshift($this->Values, $item);
        }
        else {
            array_push($this->Values, $item);
        }
    }

    public function Add($item, $reIndex = true, $first = false) {

        $this->AddItem($item, $first);
        $this->InvalidarCache();

        // Reindexar
        if ($reIndex)
            $this->Reindex();

        return $this;
    }    
    
    public function AddRange($items, $reIndex = true) {

        foreach($items as $item) {
            $this->AddItem($item);
        }

        $this->InvalidarCache();
        // Reindexar
        if ($reIndex)
            $this->Reindex();

        return $this;
    }

    private function InvalidarCache() {
        $this->Cache->LastOrderedBy = array();
        $this->Cache->LastOrderedByCondition = "";    
        $this->Cache->LastWhere = array();
        $this->Cache->LastWhereCondition = "";        
        $this->Cache->LastFirstWhere = null;
        $this->Cache->LastFirstWhereCondition = "";    
        $this->Cache->LastAvg = 0;
        $this->Cache->LastAvgField = "";    
        $this->Cache->LastSum = 0;
        $this->Cache->LastSumField = "";    
        $this->Cache->LastMax = 0;
        $this->Cache->LastMaxField = "";    
        $this->Cache->LastMin = 0;
        $this->Cache->LastMinField = "";    
        $this->Cache->LastItemMax = null;
        $this->Cache->LastItemMaxField = "";    
        $this->Cache->LastMinItem = null;
        $this->Cache->LastMinItemField = ""; 
        $this->Cache->LastCount = -1;
        $this->Cache->LastDistinctField = "";
        $this->Cache->LastDistinct = null;    
        $this->Cache->LastDistinctItemField = "";
        $this->Cache->LastDistinctItem = null;
    }

    public function Clear() {
        $this->Values = array();

        // Eliminar caché
        $this->InvalidarCache();
        
        // Reindexar
        $this->Reindex();
    }

    public function Where($condicion) {

        $condicion = $this->SingleEqualSignReplace($condicion);

        if ($this->Cache->LastWhereCondition != $condicion) {

            $condicion = $this->SingleEqualSignReplace($condicion);

            // Actualizar caché
            $this->Cache->LastWhereCondition = $condicion;

            // Buscar variables dentro de la condicion
            $pivote = get_class_vars($this->DtoName); // new $this->DtoName();

            foreach($pivote as $propname => $propvalue) {
                $condicion = $this->TokenReplace($propname, "\$value->" . $propname, $condicion);
            }
            
            $newValues = array();

            $idx= 0;
            // Los valores no se clonan, para que hereden cualquier modificacion
            // que se realice posterior a la llamada
            foreach($this->Values as $value) {
                $resultado = eval('return (' . $condicion . ');');

                if ($resultado) {
                    $newValues[$idx] = $value;
                    $idx++;
                }
            }

            // Actualizar caché
            $this->Cache->LastWhere = new GenericCollection(
                Key: $this->Key, 
                DtoName: $this->DtoName,
                Values: $newValues
            );
        }        

        return $this->Cache->LastWhere;
    }

    public function FirstWhere($condicion) {

        $condicion = $this->SingleEqualSignReplace($condicion);

        $dtStartMonitor = microtime(true);

        if ($this->Cache->LastFirstWhereCondition != $condicion) {

            $found = null;

            // Actualizar caché
            $this->Cache->LastFirstWhereCondition = $condicion;

            // Buscar variables dentro de la condicion
            $pivote = get_class_vars($this->DtoName); // new $this->DtoName();

            foreach($pivote as $propname => $propvalue) {
                $condicion = $this->TokenReplace($propname, "\$value->" . $propname, $condicion);
            }

            // Evitar la asignacion
            $condicion = str_replace(" = ", " == ", $condicion);

            $idx = 0;
            // precalcular para que no se calcule con cada ciclo for(;;)
            $cant = count($this->Values);

            // Recorremos en el orden del arreglo actual (no utilizaremos foreach)
            for($i = 0; $i < $cant; $i++) {
                $value = $this->Values[$i];
                $resultado = eval('return (' . $condicion . ');');

                if ($resultado) {
                    // retornamos el primer resultado que encontremos
                    $found = $value;
                    break;
                }
            }

            // Actualizar caché (podría ser null inclusive)
            $this->Cache->LastFirstWhere = $found;
        }

        $dtEndMonitor = microtime(true);
        
        Monitor::$FromTimeExecutions++;
        Monitor::$FromTimeExecutionTime += ($dtEndMonitor - $dtStartMonitor);

        return $this->Cache->LastFirstWhere;
    }



    public function Find($primaryIndexKey) {

        // Para acelerar el proceso, debemos indexar por la llave primaria!!, si es que existe
        if (!isset($this->Cache->PrimaryIndex) && $this->Key != "") {
            $this->CreatePrimaryIndex($this->Key);
        }

        if (isset($this->Cache->PrimaryIndex[$primaryIndexKey])) {
            $indexKey = $this->Cache->PrimaryIndex[$primaryIndexKey];
            return (isset($this->Values[$indexKey])) ? $this->Values[$indexKey] : null;
        }
        else {
            return null;
        }
        
    }

    public function Exists($primaryIndexKey) {
        // Para acelerar el proceso, debemos indexar por la llave primaria!!, si es que existe
        if (!isset($this->Cache->PrimaryIndex) && $this->Key != "") {
            $this->CreatePrimaryIndex($this->Key);
        }

        return (isset($this->Cache->PrimaryIndex[$primaryIndexKey]));
    }

    public function Attach(AttachDefinition $attachDefinition) {

        // Los valores deben ser una Coleccion basada en GenericCollection
        // el valor de $attachDefinition->Values, debe ser un objeto GenericCollection o una clase derivada
        if (!isset($attachDefinition->Values) || !($attachDefinition->Values instanceof GenericCollection))
            throw new \Exception("Debe especificar una coleccion basada en GenericCollection para atachar");

        if ($attachDefinition->AttachPrimaryKey != "") {
            $attachPrimaryKey = $attachDefinition->AttachPrimaryKey;
        }
        else if ($attachDefinition->Values->Key != "") {
            $attachPrimaryKey = $attachDefinition->Values->Key;
        }
        else if ( isset($attachDefinition->Values) && isset($attachDefinition->Values->EntityDefinition) && $attachDefinition->Values->EntityDefinition->KeyName != "") {
            $attachPrimaryKey = $attachDefinition->Values->EntityDefinition->KeyName;
        }
        
        // Foreign Key representa el campo del registro actual (al cual le atacharemos su PARENT row) que hace referencia a la primary key del PARENT
        $foreignKey = $attachDefinition->ForeignKey == "" ? $attachPrimaryKey : $attachDefinition->ForeignKey;

        $attachPropertyName = $attachDefinition->AttachPropertyName == "" ? $attachDefinition->Values->ClassName : $attachDefinition->AttachPropertyName;
        $values = $attachDefinition->Values;

        if ( $values->Count() <= 0)
            return;

        // Para acelerar las asignaciones, debemos crear un índice con la definición
        // de la entidad ATTACH
        if (!isset($this->Cache->SecondaryIndexes[$foreignKey]) && $this->Key != "") {
            $this->CreateSecondaryIndexes([
                $foreignKey
            ]);
        }

        // Comenzar el bucle de busqueda por cada referencia
        foreach($values as $attach) {

            
            $condicion = $foreignKey . " == " . $attach->$attachPrimaryKey;

            if (isset($condicion)) {
                // Buscar el elemento que aplique a esta condicion de referencia
                $found = $this->Where($condicion);

                if (isset($found)) {
                    foreach($found as $item) {
                        $item->$attachPropertyName = $attach;
                    }
                }
            }
        
        }
    }

    public function MergeChildren(MergeDefinition $mergeDefinition) {

        $dtStartMonitor = microtime(true);

        // Para acelerar el proceso, debemos indexar por la llave primaria!!, si es que existe
        if (!isset($this->Cache->PrimaryIndex) && $this->Key != "") {
            $this->CreatePrimaryIndex($this->Key);
        }

        // Los valores deben ser una Coleccion basada en GenericCollection
        if (!isset($mergeDefinition->Values) || !($mergeDefinition->Values instanceof GenericCollection))
            throw new \Exception("Debe especificar una coleccion basada en GenericCollection para realizar merge");
        
        $parentKey = $mergeDefinition->ParentKey == "" ? $this->Key : $mergeDefinition->ParentKey;
        $childReferenceKey = $mergeDefinition->ChildReferenceKey == "" ? $parentKey : $mergeDefinition->ChildReferenceKey;
        $parentCollectionName = $mergeDefinition->ParentCollectionName == "" ? $this->Pluralize($mergeDefinition->Values->ClassName) : $mergeDefinition->ParentCollectionName;
        $values = $mergeDefinition->Values;

        if (is_array($values) && count($values) <= 0)
            return;
        else if ( $values->Count() <= 0)
            return;

        // Almacenar resultados en un arreglo caché para acelerar
        // las búsquedas en grandes cantidades de elementos, cuando
        // el índice no existe
        $searchCache = array();
        $parentFound = null;

        // Comenzar el bucle de busqueda por cada child
        foreach($values as $child) {
            // Buscar el parent
            unset($parentFound);

            // SI EL ARREGLO ESTA INDEXADO usamos el indice
            if (isset($this->Cache->PrimaryIndex) && count($this->Cache->PrimaryIndex) > 0) {
                if (isset($this->Cache->PrimaryIndex[$child->$childReferenceKey])) {
                    $llaveBuscada = $this->Cache->PrimaryIndex[$child->$childReferenceKey];
                    if (isset($this->Values[$llaveBuscada]))
                        $parentFound = $this->Values[$llaveBuscada];
                }
            }

            if (!isset($parentFound)) {
            // SI NO EXISTE INDICE, buscamos por condicion, utilizamos caché para almacenar búsquedas previas
            //else {
                $condicion = null;
                
                if (isset($child->$childReferenceKey)) {
                    if (is_string($child->$childReferenceKey))
                        $condicion = $parentKey . " == '" . $child->$childReferenceKey . "'";
                    else
                        $condicion = $parentKey . " == " . $child->$childReferenceKey;
                }

                if (isset($condicion)) {
                    // Buscar el elemento padre que aplique a esta condicion de referencia
                    // Buscar primero en caché
                    if (isset($searchCache[$child->$childReferenceKey])) {
                        $parentFound = $searchCache[$child->$childReferenceKey];
                    }
                    else {
                        $parentFound = $this->FirstWhere($condicion);
                        // Agregar al cache
                        $searchCache[$child->$childReferenceKey] = $parentFound;
                    }
                }
            }

            if (isset($parentFound)) {
                if (!isset($parentFound->$parentCollectionName))
                    $parentFound->$parentCollectionName = [];

                array_push($parentFound->$parentCollectionName, $child);
            }
        }

        $dtEndMonitor = microtime(true);

        Monitor::$MergeChildrenExecutions++;
        Monitor::$MergeChildrenExecutionTime += ($dtEndMonitor - $dtStartMonitor);
    }

    private function GetClassName(string $dtoName) {

        $pos = strrpos($dtoName, '\\');

        if ($pos) {
            return str_replace('\\', '', substr($dtoName, $pos));
        }
    }

    private function Pluralize(string $definition) {

        $plural = "es";

        // Primero revisar el ultimo elemento        
        $lastChar = strtolower(substr($definition, -1, 1));

        if ($lastChar == 'a' || $lastChar == 'e' || $lastChar == 'o') {
            $plural = "s";
        }

        return $definition . $plural;
    }

    public function Avg($field) {

        if ($this->Cache->LastAvgField != $field) {

            // Actualizar caché
            $this->Cache->LastAvgField = $field;

            if ($this->Count() <= 0)
                return 0;

            $sum = $this->Sum($field);

            // Actualizar caché            
            $this->Cache->LastAvg = ($sum / $this->Count());
        }

        return $this->Cache->LastAvg;
    }

    public function Sum($field) {

        if ($this->Cache->LastSumField != $field) {
            // Actualizar caché
            $this->Cache->LastSumField = $field;
        
            $sum = 0;

            foreach($this->Values as $value) {
                $sum += $value->$field * 1;
            }

            // Actualizar caché
            $this->Cache->LastSum = $sum;
        }

        return $this->Cache->LastSum;
    }

    public function SumWhere($sumField, $condicion) {

        $sumResult = 0;

        $condicion = $this->SingleEqualSignReplace($condicion);

        // Buscar variables dentro de la condicion
        $pivote = get_class_vars($this->DtoName); // new $this->DtoName();

        foreach($pivote as $propname => $propvalue) {
            $condicion = $this->TokenReplace($propname, "\$value->" . $propname, $condicion);
        }

        // Evitar la asignacion
        $condicion = str_replace(" = ", " == ", $condicion);

        foreach($this->Values as $value) {

            $resultado = eval('return (' . $condicion . ');');

            if ($resultado) {
                $fieldValue = $value->$sumField;

                if (isset($fieldValue) && is_numeric($fieldValue)) {
                    $sumResult += $fieldValue * 1;
                }
            }
        }

        return $sumResult;
    }

    public function Max($field) {        

        if ($this->Cache->LastMaxField != $field) {

            // Revisar en el indice principal si el campo es la llave
            if ($this->Key != "" && $field == $this->Key) {

                if (!isset($this->Cache->PrimaryIndex)) {
                    // Crear el indice
                    $this->CreatePrimaryIndex($this->Key);
                }

                if (count($this->Cache->PrimaryIndex) > 0) {
                    // Buscar en el indice principal el primer elemento
                    $idx = array_key_last($this->Cache->PrimaryIndex);
                    
                    if (isset($idx)) {
                        $this->Cache->LastMax = $idx;
                    }
                }
                else {
                    $this->Cache->LastMax = null;
                }
            }
            // Probar utilizando el índice secundario del campo si no es la llave
            else {
                if (!isset($this->Cache->SecondaryIndexes[$field])) {
                    // Crear el indice
                    $this->CreateSecondaryIndexes([$field]);
                }

                $idx = array_key_last($this->Cache->SecondaryIndexes[$field]);
                
                if (isset($idx)) {
                    $this->Cache->LastMax = $idx;
                }
                else {
                    $this->Cache->LastMax = null;
                }
            }

            // Actualizar caché
            $this->Cache->LastMaxField = $field;
        }

        return $this->Cache->LastMax;
    }

    public function Min($field) {

        $dtStartMonitor = microtime(true);

        if ($this->Cache->LastMinField != $field) {

            // Revisar en el indice principal si el campo es la llave
            if ($this->Key != "" && $field == $this->Key) {

                if (!isset($this->Cache->PrimaryIndex)) {
                    // Crear el indice
                    $this->CreatePrimaryIndex($this->Key);
                }

                if (count($this->Cache->PrimaryIndex) > 0) {
                    // Buscar en el indice principal el primer elemento
                    $idx = array_key_first($this->Cache->PrimaryIndex);
                    
                    if (isset($idx)) {
                        $this->Cache->LastMin = $idx;
                    }
                }
                else {
                    $this->Cache->LastMin = null;
                }
            }
            // Probar utilizando el índice secundario del campo si no es la llave
            else {
                if (!isset($this->Cache->SecondaryIndexes[$field])) {
                    // Crear el indice
                    $this->CreateSecondaryIndexes([$field]);
                }

                $idx = array_key_first($this->Cache->SecondaryIndexes[$field]);
                
                if (isset($idx)) {
                    $this->Cache->LastMin = $idx;
                }
                else {
                    $this->Cache->LastMin = null;
                }
            }

            // Actualizar caché
            $this->Cache->LastMinField = $field;
        }

        $dtEndMonitor = microtime(true);

        Monitor::$MinExecutions++;
        Monitor::$MinExecutionTime += ($dtEndMonitor - $dtStartMonitor);
        
        return $this->Cache->LastMin;
    }

    public function IndexedMin($field) {

        $dtStartMonitor = microtime(true);

        if ($this->Cache->LastMinField != $field) {

            // Primero probar utilizando el índice del campo si este existe
            // (no se creará un índice automático puesto que el campo puede no ser de tipo INT y el índice no funcionaría)
            if (isset($this->Cache->SecondaryIndexes[$field])) {
            }

            // Actualizar caché
            $this->Cache->LastMinField = $field;

            $parts = explode(" ", $field);
            $fieldName = $parts[0];

            $order = $this->OrderedBy($fieldName);

            // Actualizar caché
            $this->Cache->LastMin = $order->First()->$fieldName;
        }

        $dtEndMonitor = microtime(true);

        Monitor::$IndexedMinExecutions++;
        Monitor::$IndexedMinExecutionTime += ($dtEndMonitor - $dtStartMonitor);
        
        return $this->Cache->LastMin;
    }

    public function ItemMax($field) {

        if ($this->Cache->LastItemMaxField != $field) {

            // Actualizar caché
            $this->Cache->LastItemMaxField = $field;

            $parts = explode(" ", $field);
            $fieldName = $parts[0];

            $order = $this->OrderedBy($fieldName);

            // Actualizar caché
            $this->Cache->LastItemMax = $order->Last();
        }

        return $this->Cache->LastItemMax;
    }

    public function ItemMin($field) {

        if ($this->Cache->LastMinItemField != $field) {

            // Actualizar caché
            $this->Cache->LastMinItemField = $field;

            $parts = explode(" ", $field);
            $fieldName = $parts[0];

            $order = $this->OrderedBy($fieldName);
            
            // Actualizar caché
            $this->Cache->LastMinItem = $order->First();
        }

        return $this->Cache->LastMinItem;
    }

    public function Distinct($field) {

        if ($this->Cache->LastDistinctField != $field) {
            
            // Actualizar caché
            $this->Cache->LastDistinctField = $field;

            // Enumerar la colección sin repetir el campo
            $result = array();

            foreach($this->Values as $value) {
                $itemFieldValue = $value->$field;

                if (!isset($result[$itemFieldValue])) {
                    $result[$itemFieldValue] = $value->$field;
                }
            }

            // Actualizar caché
            $this->Cache->LastDistinct = $result;
        }

        return $this->Cache->LastDistinct;
    }

    public function DistinctItem($field = "") {

        if ($this->Cache->LastDistinctItemField != $field) {
            
            // Actualizar caché
            $this->Cache->LastDistinctItemField = $field;

            // Enumerar la colección sin repetir el campo
            $result = array();

            foreach($this->Values as $value) {
                $itemFieldValue = $value->$field;

                if (!isset($result[$itemFieldValue])) {
                    $result[$itemFieldValue] = $value;
                }
            }

            // Actualizar caché
            $this->Cache->LastDistinctItem = new GenericCollection(
                Key: $this->Key,
                DtoName: $this->DtoName,
                Values: $result
            );
        }

        return $this->Cache->LastDistinctItem;
    }

    public function SingleEqualSignReplace($condicion) {
        $fase1 = str_replace("==", "<<DOBLE_EQUAL>>", $condicion);
        $fase2 = str_replace("=", "==", $fase1);

        return str_replace("<<DOBLE_EQUAL>>", "==", $fase2);
    }


    // Monitoring functions
    public static function ResetMonitor() {
        Monitor::$FromTimeExecutions = 0;
        Monitor::$FromTimeExecutionTime = 0;
        Monitor::$MergeChildrenExecutions = 0;
        Monitor::$MergeChildrenExecutionTime = 0;
        Monitor::$KeyIndexExecutions = 0;
        Monitor::$KeyIndexExecutionTime = 0;    
        Monitor::$MinExecutions = 0;
        Monitor::$MinExecutionTime = 0;
        Monitor::$IndexedMinExecutions = 0;
        Monitor::$IndexedMinExecutionTime = 0;
    }

    public static function GetMonitorVars() {
        return (object)[
            "FromTimeExecutions" => Monitor::$FromTimeExecutions,
            "FromTimeExecutionTime" => Monitor::$FromTimeExecutionTime,
            "FromTimeAverageTime" => (Monitor::$FromTimeExecutions == 0) ? 0 : (Monitor::$FromTimeExecutionTime / Monitor::$FromTimeExecutions),
            "MergeChildrenExecutions" => Monitor::$MergeChildrenExecutions,
            "MergeChildrenExecutionTime" => Monitor::$MergeChildrenExecutionTime,
            "MergeChildrenAverageTime" => (Monitor::$MergeChildrenExecutions == 0) ? 0 : (Monitor::$MergeChildrenExecutionTime / Monitor::$MergeChildrenExecutions),
            "KeyIndexExecutions" => Monitor::$KeyIndexExecutions,
            "KeyIndexExecutionTime" => Monitor::$KeyIndexExecutionTime,
            "MinExecutions" => Monitor::$MinExecutions,
            "MinExecutionTime" => Monitor::$MinExecutionTime,
            "MinExecutionAverageTime" => (Monitor::$MinExecutions == 0) ? 0 : (Monitor::$MinExecutionTime / Monitor::$MinExecutions),
            "IndexedMinExecutions" => Monitor::$IndexedMinExecutions,
            "IndexedMinExecutionTime" => Monitor::$IndexedMinExecutionTime,
            "IndexedMinExecutionAverageTime" => (Monitor::$IndexedMinExecutions == 0) ? 0 : (Monitor::$IndexedMinExecutionTime / Monitor::$IndexedMinExecutions)
        ];
    }

    // Getters
    // *******************************************
    public function GetValue($property) {
        return $this->$property;
    }

    /**
     * Reemplaza el token, teniendo la precaucion de realizar una comparación de palabra completa
     */
    public static function TokenReplace($search, $replace, $subject) {
        return preg_replace("/\b" . $search . "\b/i", $replace, $subject);
    }

    public function GetKey() {
        return $this->Key;
    }

    public function GetDtoName() {
        return $this->DtoName;
    }

}