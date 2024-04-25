<?php
namespace Application\BLL\Services\Core;

use Intouch\Framework\Mapper\Mapper;

trait tipoproductoSvcT
{
    public $innerMappings = [];

    public function BuscarTipoCompra()
    {
        return Mapper::ToDtos( $this->Dao->BuscarTipoCompra(), $this->DtoName, $this->innerMappings);
    }

    public function BuscarTipoGenerales()
    {
        return Mapper::ToDtos( $this->Dao->BuscarTipoGenerales(), $this->DtoName, $this->innerMappings);
    }

    public function BuscarTipoGastos()
    {
        return Mapper::ToDtos( $this->Dao->BuscarTipoGastos(), $this->DtoName, $this->innerMappings);
    }

}