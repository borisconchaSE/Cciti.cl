<?php
namespace Application\BLL\Services\Core;

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\marcaDto;
use Intouch\Framework\Mapper\Mapper;

trait stockSvcT
{
    public $innerMappings = [
        'empresa'   => empresaDto::class,
        'marca'     => marcaDto::class,
    ];

    public function BuscarStock(){
        return Mapper::ToDtos( $this->Dao->BuscarStock(), $this->DtoName, $this->innerMappings);
    }
}