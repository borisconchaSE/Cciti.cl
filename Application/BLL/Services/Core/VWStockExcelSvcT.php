<?php
namespace Application\BLL\Services\Core;
use Intouch\Framework\Mapper\Mapper;

trait VWStockExcelSvcT
{
    public $innerMappings = [];

    public function BuscarStock(){
        return Mapper::ToDtos( $this->Dao->BuscarStock(), $this->DtoName, $this->innerMappings);
    }
}