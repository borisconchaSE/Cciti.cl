<?php
namespace Application\BLL\Services\Core;

use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\estadoFCDto;
use Application\BLL\DataTransferObjects\Core\estadoOCDto;
use Application\BLL\DataTransferObjects\Core\proveedorDto;
use Intouch\Framework\Mapper\Mapper;

trait ordenCompraSvcT
{
    public $innerMappings = [
        'proveedor'     => proveedorDto::class,
        'estadoOC'     => estadoOCDto::class,
        'estadoFC'     => estadoFCDto::class,
        'empresa'       => empresaDto::class,
    ];

    public function BuscarCompras()
    {
        return Mapper::ToDtos( $this->Dao->BuscarCompras(), $this->DtoName, $this->innerMappings);
    }

}