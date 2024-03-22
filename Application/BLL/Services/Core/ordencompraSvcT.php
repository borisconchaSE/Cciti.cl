<?php
namespace Application\BLL\Services\Core;
use Application\BLL\DataTransferObjects\Core\empresaDto;
use Application\BLL\DataTransferObjects\Core\estadoFCDto;
use Application\BLL\DataTransferObjects\Core\estadoOCDto;
use Application\BLL\DataTransferObjects\Core\proveedorDto;
use Application\BLL\DataTransferObjects\Core\tipoproductoDto;
use Intouch\Framework\Mapper\Mapper;

trait ordencompraSvcT
{
    public $innerMappings = [
        'proveedor'     => proveedorDto::class,
        'estadoOC'      => estadoOCDto::class,
        'estadoFC'      => estadoFCDto::class,
        'empresa'       => empresaDto::class,
        'tipoProducto'  => tipoproductoDto::class, 
    ];

    public function BuscarCompras()
    {
        return Mapper::ToDtos( $this->Dao->BuscarCompras(), $this->DtoName, $this->innerMappings);
    }

    public function BuscarComprasGenerales()
    {
        return Mapper::ToDtos( $this->Dao->BuscarComprasGenerales(), $this->DtoName, $this->innerMappings);
    }

    public function BuscarGastos()
    {
        return Mapper::ToDtos( $this->Dao->BuscarGastos(), $this->DtoName, $this->innerMappings);
    }

}