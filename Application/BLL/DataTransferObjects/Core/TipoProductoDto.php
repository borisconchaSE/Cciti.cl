<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoProductoDto
{
    use TipoProductoDtoT;

    public function __construct(
		public int $IdTipoProducto = 0,
		public string $Descripcion = ''
    ) {

    }
}