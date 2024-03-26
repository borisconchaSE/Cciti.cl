<?php
namespace Application\BLL\Services\Core;

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Application\BLL\DataTransferObjects\Core\modeloDto;
use Intouch\Framework\Mapper\Mapper;

trait stockSvcT
{
    public $innerMappings = [
        'empresa'   => empresaDto::class,
        'marca'     => marcaDto::class,
        'modelo'    => modeloDto::class,
    ];

    public function BuscarStock(){
        return Mapper::ToDtos( $this->Dao->BuscarStock(), $this->DtoName, $this->innerMappings);
    }

    public function BuscarEntregado(){
        return Mapper::ToDtos( $this->Dao->BuscarEntregado(), $this->DtoName, $this->innerMappings);
    }
}