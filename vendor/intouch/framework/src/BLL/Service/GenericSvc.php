<?php
namespace Intouch\Framework\BLL\Service;

use Intouch\Framework\BLL\DataTransferObjects\RangoDto;
use Intouch\Framework\Mapper\Mapper;
use Intouch\Framework\Dao\ExecuteDao;
use Intouch\Framework\Dao\Queryable;

class GenericSvc
{
    protected $DtoName;
    protected $EntityName;
    protected $Dao;

    protected static $Transaction = null;

    function __construct($dtoName, $entityName, $dao)
    {
        // Get a new dao
        $this->Dao = $dao;
        $this->DtoName = $dtoName;
        $this->EntityName = $entityName;
    }

    public function GetDomain() {
        return $this->Dao->Domain;
    }

    public function GetDtoName() {
        return $this->DtoName;
    }

    public static function BeginMultipleOperations($domain) {
        // Obtener una transaccion
        self::$Transaction = ExecuteDao::OpenTransaction($domain);

        if (isset(self::$Transaction)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function SaveMultipleOperations() {
        ExecuteDao::CommitTransaction(self::$Transaction);
        self::$Transaction = null;
    }

    public static function UndoMultipleOperations() {
        ExecuteDao::RollbackTransaction(self::$Transaction);
        self::$Transaction = null;
    }

    public function Find($keyValue)
    {
        $dto = Mapper::ToDto($this->Dao->Find($keyValue), $this->DtoName);            
        return $dto;
    }

    public function FindBy($bindings) {
        return Mapper::ToDto($this->Dao->FindBy($bindings), $this->DtoName);
    }

    public function FindByForeign($foreignKey, $foreignKeyValue) {
        return Mapper::ToDto($this->Dao->FindByForeign($foreignKey, $foreignKeyValue), $this->DtoName);
    }

    public function GetAll($order = "", $avoidNull = false) {
        return Mapper::ToDtos(entityArray: $this->Dao->GetAll($order), dtoName: $this->DtoName, avoidNull: $avoidNull);
    }

    public function GetBy($bindings, $orderFields = '', $avoidNull = false) {
        return Mapper::ToDtos(entityArray: $this->Dao->GetBy($bindings, $orderFields), dtoName: $this->DtoName, avoidNull: $avoidNull);
    }

    public function GetByForeign($foreignKey, $foreignKeyValue, $orderFields = '', $avoidNull = false) {
        return Mapper::ToDtos(entityArray: $this->Dao->GetByForeign($foreignKey, $foreignKeyValue, $orderFields), dtoName: $this->DtoName, avoidNull: $avoidNull);
    }
    
    public function GetByCodigoRegistro($codigoRegistro, $avoidNull = false) {
        return Mapper::ToDtos(entityArray: $this->Dao->GetByCodigoRegistro($codigoRegistro), dtoName: $this->DtoName, avoidNull: $avoidNull );
    }

    public function Insert($dto) {

        if ($this->DtoName !== get_class($dto)) {
            throw new \Exception('El DTO a insertar debe ser una instancia de la clase: '. $this->DtoName);
            return null;
        }
        
        return Mapper::ToDto( $this->Dao->Insert(Mapper::ToEntity($dto, $this->EntityName), GenericSvc::$Transaction), $this->DtoName );
    }

    public function Update($dto) {

        if ($this->DtoName !== get_class($dto)) {
            throw new \Exception('El DTO a insertar debe ser una instancia de la clase: '. $this->DtoName);
            return null;
        }

        $this->Dao->Update(Mapper::ToEntity($dto, $this->EntityName), GenericSvc::$Transaction);
    }

    public function Delete($keyValue) {
        $this->Dao->Delete($keyValue);
    }

    public function DeleteBy($bindings) {
        $this->Dao->DeleteBy($bindings);
    }

    public function Count() {
        return $this->Dao->Count();
    }

    public function CountBy($bindings) {
        return $this->Dao->CountBy($bindings);
    }

    function SumFieldByForeign($fieldName, $foreignKeyName, $foreignKeyValue) {
        return $this->Dao->SumFieldByForeign($fieldName, $foreignKeyName, $foreignKeyValue);
    }

    function CountByForeign($foreignKeyName, $foreignKeyValue) {
        return $this->Dao->CountByForeign($foreignKeyName, $foreignKeyValue);
    }

    function Query(Queryable $query) {
        return Mapper::GetInstance()->map($this->Dao->Query($query), null, true, '', true);
    }

    function GetIN($foreignKey, $foreignKeyValue, $orderFields = '') {
        //return $this->Dao->GetIN($foreignKey, $foreignKeyValue, $orderFields);
        return Mapper::ToDtos($this->Dao->GetIN($foreignKey, $foreignKeyValue, $orderFields), $this->DtoName);
    }

    public function Max($field, $ignoreBlanks = false) {
        return $this->Dao->Max($field, ignoreBlanks: $ignoreBlanks);
    }

    public function Min($field, $ignoreBlanks = false) {
        return $this->Dao->Min(field: $field, ignoreBlanks: $ignoreBlanks);
    }

    public function Rango($field, $ignoreBlanks = false) {

        $menor = $this->Dao->Min($field, $ignoreBlanks = false);
        $mayor = $this->Dao->Max($field, $ignoreBlanks = false);

        if (!isset($menor)) $menor = 0;
        if (!isset($mayor)) $mayor = 0;

        return new RangoDto(
            Menor: $menor, Mayor: $mayor
        );
    }

    public function BuscarValoresUnicosColumna($NombrePropiedad){
        return $this->Dao->BuscarValoresUnicosColumna($NombrePropiedad);
    }
}