<?php

namespace Intouch\Framework\Dao;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Dao\Entity\EntityDefinition;

/**
 * Queryable
 * 
 * Clase contenedora de todos los parametros antes de ejecutar una consulta desde el DAO
 * 
 * Los nombres de cada propiedad de la entidad deben referir a los nombre en la entidad, no en la tabla
 * 
 * @package Dao
 * @author Claudio González <claudioalfonso@gmail.com>
 */
class Queryable {

    private static $ConnectionType = null;
    private $idxElement = 0;

    public static function Init(string $ConnectionType) {
        self::$ConnectionType = $ConnectionType;
    }

    private $Definitions            = [];
    private $FromDefinition         = null;
    private $JoinDefinitions        = null;
    private $LeftJoinDefinitions    = null;
    private $WithDefinitions        = null;
    private $WhereDefinitions       = null;
    private $Order                  = '';
    private int $Top                = 0;
    private bool $UseDistinct       = false;

    public function __construct(
    )
    {
        if (!isset(self::$ConnectionType)) {
            throw new \Exception('Debe inicializar la conexión por defecto antes de instanciar esta clase. Ej: Queryable::Init(ConnectionTypeEnum::MYSQL)');
        }

        $this->JoinDefinitions      = new GenericCollection();
        $this->LeftJoinDefinitions  = new GenericCollection();
        $this->WithDefinitions      = new GenericCollection();
        $this->WhereDefinitions     = new GenericCollection();
    }

    private function AddDefinition($definition, $definitionType) {

        $definition->Type = $definitionType;

        $this->Definitions[$this->idxElement] = $definition;
        $this->idxElement++;

    }

    /**
     * @param string $entity
     * 
     * Agrega la entidad principal de la consulta
     * 
     * @return Queryable
     */
    public function From(string $entity) {

        $this->FromDefinition = EntityDefinition::Instance($entity);

        return $this;
    }

    /**
     * @param string $entityClass
     * 
     * Agrega una inclusión Join de todas las entidades hijas con la entidad padre principal.
     * La entidad padre debe contener una propiedad que tenga el nombre de la entidad hija en plural.
     * Si no existe el nombre, se creará una propiedad nueva.
     * 
     * Ej:
     * 
     * class Mantencion {
     *      // Key AutoIncrement
     *      public $IdMantencion = 0;
     *      public $Descripcion = '';
     *      public $IdTipoEstado = '';
     *      public $Actividades = array();  << aca se agregará la coleccion JOIN
     * }
     * 
     * class Actividad {
     *      // Key, AutoIncrement
     *      public $IdActividad = 0;
     *      public $Description = '';
     *      // Foreign=Mantencion
     *      public $IdMantencion = 0;
     * }
     * 
     * $qry = (new Queryable())
     *      ->From(Mantencion::class)
     *      ->Join(Actividad::class);
     * 
     * @return Queryable
     */
    public function Join(string $entity, $foreignKey = '', $parentKey = '') {

        $entityDefinition = EntityDefinition::Instance($entity);

        if ($foreignKey != '')
            $entityDefinition->CustomForeignKey = $foreignKey;

        if ($parentKey != '')
            $entityDefinition->CustomParentKey = $parentKey;

        $this->JoinDefinitions->Add($entityDefinition);
        $this->AddDefinition($entityDefinition, DefinitionTypeEnum::JOIN);

        return $this;
    }

    

    /**
     * @param string $entityClass
     * 
     * Agrega una inclusión LEFT Join de todas las entidades hijas con la entidad padre principal.
     * La entidad padre debe contener una propiedad que tenga el nombre de la entidad hija en plural.
     * Si no existe el nombre, se creará una propiedad nueva.
     * 
     * Ej:
     * 
     * class Mantencion {
     *      // Key AutoIncrement
     *      public $IdMantencion = 0;
     *      public $Descripcion = '';
     *      public $IdTipoEstado = '';
     *      public $Actividades = array();  << aca se agregará la coleccion LEFT JOIN
     * }
     * 
     * class Actividad {
     *      // Key, AutoIncrement
     *      public $IdActividad = 0;
     *      public $Description = '';
     *      // Foreign=Mantencion
     *      public $IdMantencion = 0;
     * }
     * 
     * $qry = (new Queryable())
     *      ->From(Mantencion::class)
     *      ->LeftJoin(Actividad::class);
     * 
     * @return Queryable
     */
    public function LeftJoin(string $entity, $foreignKey = '', $parentKey = '') {

        $entityDefinition = EntityDefinition::Instance($entity);

        if ($foreignKey != '')
            $entityDefinition->CustomForeignKey = $foreignKey;

        if ($parentKey != '')
            $entityDefinition->CustomParentKey = $parentKey;

        $this->LeftJoinDefinitions->Add($entityDefinition);
        $this->AddDefinition($entityDefinition, DefinitionTypeEnum::LEFTJOIN);

        return $this;
    }

    /**
     * @param string $entity
     * 
     * Agrega una inclusión de la entidad parent dentro de la entidad hija principal como un único elemento. 
     * La entidad principal (hija) debe contener una propiedad que tenga el mismo nombre de la entidad referenciada (padre).
     * Si no existe el nombre, se creará una propiedad nueva.
     * 
     * Ej:
     * 
     * class Mantencion {             << esta es la entidad hija
     *      // Key AutoIncrement
     *      public $IdMantencion = 0;
     *      public $Descripcion = '';
     *      // ForeignKey=TipoEstado
     *      public $IdTipoEstado = '';
     *      public $TipoEstado = null;  << aca se agregará el elemento enlazado
     * }
     * 
     * class TipoEstado {             << esta es la entidad padre
     *      // Key, AutoIncrement
     *      public $IdTipoEstado = 0;
     *      public $Description = '';
     * }
     * 
     * $qry = (new Queryable())
     *      ->From('Mantencion')
     *      ->With('TipoEstado');
     * 
     * @return Queryable
    */    
    public function With(string $entity, $foreignKey = '', $parentKey = '') {

        $entityDefinition = EntityDefinition::Instance($entity);

        if ($foreignKey != '')
            $entityDefinition->CustomForeignKey = $foreignKey;

        if ($parentKey != '')
            $entityDefinition->CustomParentKey = $parentKey;

        $entityDefinition->LeftWith = false;

        $this->WithDefinitions->Add($entityDefinition);
        $this->AddDefinition($entityDefinition, DefinitionTypeEnum::WITH);

        return $this;
    }   

    public function LeftWith(string $entity, $foreignKey = '', $parentKey = '') {

        $entityDefinition = EntityDefinition::Instance($entity);

        if ($foreignKey != '')
            $entityDefinition->CustomForeignKey = $foreignKey;

        if ($parentKey != '')
            $entityDefinition->CustomParentKey = $parentKey;

        $entityDefinition->LeftWith = true;

        $this->WithDefinitions->Add($entityDefinition);
        $this->AddDefinition($entityDefinition, DefinitionTypeEnum::LEFTWITH);

        return $this;
    }

    /**
     * @param string $condition
     * @param ... $params
     * 
     * Agrega una condición de filtro a la entidad principal de la consulta.
     * Se pueden definir variables enlazadas con el símbolo "?". Para cada variable enlazada debe existir un parametro en la llamada a la funcion.
     * Las llamadas a WHERE se pueden encadenar (la serie de WHERE encadenados se incorporarán con AND)
     * 
     * Ej:
     * 
     * class Mantencion {
     *      // Key AutoIncrement
     *      public $IdMantencion = 0;
     *      public $Descripcion = '';
     *      // ForeignKey=TipoEstado
     *      public $IdTipoEstado = '';
     * }
     * 
     * $qry = (new Queryable())
     *      ->From('Mantencion')
     *      ->Where('IdTipoEstado = ? AND FechaInicioProgramada BETWEEN ? AND ?', 1, $dtInicio, new \DateTime())
     * 
     * @return Queryable
    */ 
    public function Where($condition, ...$params) {

        $where = array();

        $where['Condition'] = $condition;
        $whereParams = array();

        if (isset($params)) {
            $paramQ = count($params);
            $placeQ = substr_count($condition, "?");

            if ($paramQ != $placeQ) {
                throw new \Exception("La cantidad de parametros ($paramQ) no concuerda con la cantidad de variables ($placeQ)");
            }

            
            foreach($params as $p) {
                array_push($whereParams, $p);
            }
        }

        $where['Parameters'] = $whereParams;

        $this->WhereDefinitions->Add((object)$where);

        return $this;
    }

    public function OrderBy($condition) {
        $this->Order = $condition;
        return $this;
    }

    public function Top(int $recordNumber) {
        $this->Top = $recordNumber;
        return $this;
    }

    public function Distinct() {
        $this->UseDistinct = true;
        return $this; 
    }

    /**
     * 
     * Construye la sentencia de consulta en base a los parámetros especificados
     * 
     * 
     * @return object
    */ 
    public function Build() {

        if (!isset($this->FromDefinition)) {
            throw new \Exception("No se ha definido la entidad principal");
        }

        // $joins      = $this->GetJoins($this->JoinDefinitions, $this->FromDefinition, false);
        // $leftjoins  = $this->GetJoins($this->LeftJoinDefinitions, $this->FromDefinition, true);
        // $withs      = $this->GetWiths($this->WithDefinitions, $this->FromDefinition);

        //$joins->AddRange($withs);

        $qryJoin = "";
        $fieldsJoin = "";

        // Leer en orden
        foreach($this->Definitions as $definition) {

            switch ($definition->Type) {

                case DefinitionTypeEnum::JOIN:

                    $join = $this->GetJoin($definition, $this->FromDefinition);

                    break;

                case DefinitionTypeEnum::LEFTJOIN:                    

                    $join = $this->GetJoin($definition, $this->FromDefinition, true);
                    
                    break;
                    
                case DefinitionTypeEnum::WITH:                    

                    $join = $this->GetWith($definition, $this->FromDefinition);
                    
                    break;
                    
                case DefinitionTypeEnum::LEFTWITH:                    

                    $join = $this->GetWith($definition, $this->FromDefinition);
                    
                    break;
            }            

            if ($qryJoin != "") $qryJoin .= "\n";
            $qryJoin .= $join->JoinSentence;

            if ($fieldsJoin != "") 
                $fieldsJoin .= ",\n\t";
            $fieldsJoin .= $join->JoinFields;
        }
        
        // if ($joins->Count() > 0) {
        //     foreach($joins as $join) {
        //         if ($qryJoin != "") $qryJoin .= "\n";
        //         $qryJoin .= $join->JoinSentence;

        //         if ($fieldsJoin != "") 
        //             $fieldsJoin .= ",\n\t";
        //         $fieldsJoin .= $join->JoinFields;
        //     }
        // }
        
        // if ($leftjoins->Count() > 0) {
        //     foreach($leftjoins as $join) {
        //         if ($qryJoin != "") $qryJoin .= "\n";
        //         $qryJoin .= $join->JoinSentence;

        //         if ($fieldsJoin != "") 
        //             $fieldsJoin .= ",\n\t";
        //         $fieldsJoin .= $join->JoinFields;
        //     }
        // }
        
        // if ($withs->Count() > 0) {
        //     foreach($withs as $join) {
        //         if ($qryJoin != "") $qryJoin .= "\n";
        //         $qryJoin .= $join->JoinSentence;

        //         if ($fieldsJoin != "") 
        //             $fieldsJoin .= ",\n\t";
        //         $fieldsJoin .= $join->JoinFields;
        //     }
        // }


        // SELECT
        $top = "";
        if ($this->Top > 0 && self::$ConnectionType == 'sqlserver') {
            $top = " TOP " . $this->Top . " ";
        }
        $distinct = ($this->UseDistinct) ? ' DISTINCT ' : '';
        $qrySelect = "SELECT" . $top . $distinct . "\n\t" . $joinFields = trim(str_replace(", ", ",\n\t", $this->FromDefinition->NamedNotIgnoredFields));

        // JOIN y WITH fields
        if ($fieldsJoin != "")
            $qrySelect .= ",\n        $fieldsJoin ";

        // FROM
        $qrySelect .= "\nFROM\n\t" . $this->FromDefinition->NamedTable;

        // JOIN y WITH
        if ($qryJoin != "")
            $qrySelect .= "\n" . $qryJoin;
        
        // WHEREs
        $wheres = $this->GetWheres($this->WhereDefinitions);

        $whereSql = "";
        $bindingVars = new GenericCollection();

        foreach($wheres as $where) {
            if ($whereSql != "")
                $whereSql .= "\nAND\t";

            $whereSql .= "(" . $where->Condition . ")";
            $bindingVars->AddRange($where->Bindings);
        }

        if ($whereSql != "")
            $qrySelect .= "\nWHERE\n\t" . $whereSql;

        if ($this->Order != "") {
            $qrySelect .= "\nORDER BY\n\t" . $this->Order;
        }

        if ($this->Top > 0 && self::$ConnectionType == 'mysql') {
            $qrySelect .= "\nLIMIT " . $this->Top;
        }

        return (object)[
                "Statement"       => $qrySelect, 
                "Bindings"        => $bindingVars,
                "FromDefinition"  => $this->FromDefinition,
                "JoinDefinitions" => $this->JoinDefinitions,
                "LeftJoinDefinitions" => $this->LeftJoinDefinitions,
                "WithDefinitions" => $this->WithDefinitions
        ];
    }

    private function GetJoins(GenericCollection $joinDefinitions, EntityDefinition $mainEntityDefinition, bool $leftJoin = false) {

        $joins = new GenericCollection();

        foreach($joinDefinitions as $joinEntityDefinition) {
            $joins->Add($this->GetJoin($joinEntityDefinition, $mainEntityDefinition, $leftJoin));
        }

        return $joins;
    }

    private function GetJoin(EntityDefinition $joinEntityDefinition, EntityDefinition $mainEntityDefinition, bool $leftJoin = false) {
        
        // La llave primaria de la tabla referenciada
        $primaryKey = $mainEntityDefinition->TablePrefix . "." . $mainEntityDefinition->Key;
        $primaryKeyName = $joinEntityDefinition->KeyName;
        
        // La llave foránea de la tabla principal
        //$foreignProperty = $joinEntityDefinition->Properties[$joinEntityDefinition->KeyName];
        $foreignKey = $joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->Key;
        if (isset($joinEntityDefinition->CustomForeignKey) && $joinEntityDefinition->CustomForeignKey != '') {

            // tiene punto de separacion con una entidad?
            if (strlen($joinEntityDefinition->CustomForeignKey) != strlen( str_replace('.', '', $joinEntityDefinition->CustomForeignKey))) {
                $foreignKey = $joinEntityDefinition->CustomForeignKey;
            }
            else {
                // no tiene... asignamos la entidad principal
                $foreignKey = $mainEntityDefinition->TablePrefix . "." . $joinEntityDefinition->CustomForeignKey;
            }
        }
        else {
            //$foreignProperty = $mainEntityDefinition->Properties[$primaryKeyName];
            //$foreignKey = $mainEntityDefinition->TablePrefix . "." . $foreignProperty->ColumnName;
        }

        if (isset($joinEntityDefinition->CustomParentKey) && $joinEntityDefinition->CustomParentKey != '') {
            // tiene punto de separacion con una entidad?
            if (strlen($joinEntityDefinition->CustomParentKey) != strlen( str_replace('.', '', $joinEntityDefinition->CustomParentKey))) {
                $primaryKey = $joinEntityDefinition->CustomParentKey;
            }
            else {
                // no tiene... asignamos la entidad principal
                $primaryKey = $joinEntityDefinition->TablePrefix . "." . $joinEntityDefinition->CustomParentKey;
            }
        }


         // $foreignProperty->ColumnName;  
        //$foreignKey = $mainEntityDefinition->Properties[$joinEntityDefinition->KeyName]->ColumnName;

        // Obtener Campos, se debe ignorar el campo utilizado en la llave foranea,
        // dado que la entidad principal ya la está incluyendo
        $fields = $joinEntityDefinition->NamedRenamedNotIgnoredFields;
        // $fields = str_replace($joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->Key . " AS \"" . $joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->KeyName . "\", ", "", $fields);
        // $fields = str_replace($joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->Key . " AS \"" . $joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->KeyName . "\",", "", $fields);
        // $fields = str_replace(", ". $joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->Key . " AS \"" . $joinEntityDefinition->TablePrefix . "." . $mainEntityDefinition->KeyName . "\"", "", $fields);

        $joinFields = trim(str_replace(", ", ",\n\t", $fields));

        $reservedWord = ($leftJoin) ? 'LEFT JOIN' : 'JOIN';

        $joinSentence = "$reservedWord\t" . str_pad($joinEntityDefinition->NamedTable, 20, " ") . " ON " 
                                . $foreignKey . " = " 
                                . $primaryKey;

        return (object)[
            "Entity" => $joinEntityDefinition->Entity,
            "JoinFields" => $joinFields,
            "JoinSentence" => $joinSentence
        ];
    }

    private function GetWiths(GenericCollection $withDefinitions, EntityDefinition $mainEntityDefinition) {

        $withs = new GenericCollection();

        foreach($withDefinitions as $withEntityDefinition) {
            $withs->Add($this->GetWith($withEntityDefinition, $mainEntityDefinition));
        }

        return $withs;
    }

    private function GetWith(EntityDefinition $withEntityDefinition, EntityDefinition $mainEntityDefinition) {

        $primaryKey = $mainEntityDefinition->TablePrefix . "." . $withEntityDefinition->Key;
        $primaryKeyName = $withEntityDefinition->KeyName;
        
        // La llave foránea de la tabla principal
        //$foreignProperty = $joinEntityDefinition->Properties[$joinEntityDefinition->KeyName];
        $foreignKey = $withEntityDefinition->TablePrefix . "." . $withEntityDefinition->Key;
        if (isset($withEntityDefinition->CustomForeignKey) && $withEntityDefinition->CustomForeignKey != '') {

            // tiene punto de separacion con una entidad?
            if (strlen($withEntityDefinition->CustomForeignKey) != strlen( str_replace('.', '', $withEntityDefinition->CustomForeignKey))) {
                $foreignKey = $withEntityDefinition->CustomForeignKey;
            }
            else {
                // no tiene... asignamos la entidad principal
                $foreignKey = $mainEntityDefinition->TablePrefix . "." . $withEntityDefinition->CustomForeignKey;
            }
        }
        else {
            //$foreignProperty = $mainEntityDefinition->Properties[$primaryKeyName];
            //$foreignKey = $mainEntityDefinition->TablePrefix . "." . $foreignProperty->ColumnName;
        }

        if (isset($withEntityDefinition->CustomParentKey) && $withEntityDefinition->CustomParentKey != '') {
            // tiene punto de separacion con una entidad?
            if (strlen($withEntityDefinition->CustomParentKey) != strlen( str_replace('.', '', $withEntityDefinition->CustomParentKey))) {
                $primaryKey = $withEntityDefinition->CustomParentKey;
            }
            else {
                // no tiene... asignamos la entidad principal
                $primaryKey = $withEntityDefinition->TablePrefix . "." . $withEntityDefinition->CustomParentKey;
            }
        }
        

        // Obtener Campos, se debe ignorar el campo utilizado en la llave foranea,
        // dado que la entidad principal ya la está incluyendo
        $fields = $withEntityDefinition->NamedRenamedNotIgnoredFields;

        $joinFields = trim(str_replace(", ", ",\n\t", $fields));

        $joinSentence = (($withEntityDefinition->LeftWith) ? 'LEFT ' : '') . "JOIN\t" . str_pad($withEntityDefinition->NamedTable, 20, " ") . " ON " 
                                . $foreignKey . " = " 
                                . $primaryKey;

        return (object)[
            "Entity" => $withEntityDefinition->Entity,
            "JoinFields" => $joinFields,
            "JoinSentence" => $joinSentence
        ];
    }

    private function GetWheres(GenericCollection $whereDefinitions) {

        $wheres = array();

        $startFrom = 0;
        foreach($whereDefinitions as $whereDefinition) {

            $where = $this->GetWhere($whereDefinition, $startFrom);
            $startFrom += count($where->Bindings);

            array_push($wheres, $where);
        }
        
        return $wheres;
    }

    private function GetWhere($whereDefinition, $startFrom = 0) {

        // Condition, Parameters
        // ->Where('Mantencion.IdTipoEstado = ? AND Mantencion.FechaInicioProgramada BETWEEN ? AND ?', 1, $dtInicio, $dtFin)
        $where = $whereDefinition->Condition;

        // Primero obtenemos todos los elementos que se requieren reemplazar, 
        // es decir TablePrefix.Name se debe reemplazar por TablePrefix.ColumnName
        // -------------------------------------------------------------------------------------
        // FROM
        $where = $this->ReplaceProperties($this->FromDefinition->Properties, $this->FromDefinition->TablePrefix, $where);

        // JOINS
        foreach($this->JoinDefinitions as $joinDefinition) {
            $where = $this->ReplaceProperties($joinDefinition->Properties, $joinDefinition->TablePrefix, $where);
        }

        // Withs        
        foreach($this->WithDefinitions as $withDefinition) {
            $where = $this->ReplaceProperties($withDefinition->Properties, $withDefinition->TablePrefix, $where);
        }

        // Ahora, generamos los bindings
        $cantidadPlaceholder = substr_count($where, "?");
        $cantidadParametros = count($whereDefinition->Parameters);
        $bindings = array();

        for ($i=$startFrom; ( ($i-$startFrom) < $cantidadPlaceholder) && ( ($i-$startFrom) < $cantidadParametros); $i++) {
            $bindingVar = ":var" . $i;
            // reemplazamos el placeholder
            $where = $this->str_first_replace("?", $bindingVar, $where);
            // agregamos la variable binding
            array_push($bindings, (object)["BindingVariable" => $bindingVar, "BindingValue" => $whereDefinition->Parameters[$i-$startFrom]]);
        }

        // Resultado
        return (object)["Condition" => $where, "Bindings" => $bindings];
    }

    private function str_first_replace($search, $replace, $subject)
    {
        return ($pos=strpos($subject, $search))!==FALSE?substr_replace($subject, $replace, $pos, strlen($search)):$subject;
    }

    private function ReplaceProperties(array $properties, string $tablePrefix, string $whereCondition) {

        foreach($properties as $property) {
            $whereCondition = str_replace(
                $tablePrefix . "." . $property->Name,
                $tablePrefix . "." . $property->ColumnName,
                $whereCondition
            );

        }

        return $whereCondition;
    }

    /* LO QUE QUEREMOS LOGRAR:

    $qry = (new Queryable())
            ->From(Mantencion::class)
            ->Join(TipoEstado::class)
            ->With(Actividad:class)
            ->With(JefeTurno::class)
            ->Where('Mantencion.IdTipoEstado = ? AND Mantencion.FechaInicioProgramada BETWEEN ? AND ?', 1, $dtInicio, $dtFin)
            ->Build();

    */

}