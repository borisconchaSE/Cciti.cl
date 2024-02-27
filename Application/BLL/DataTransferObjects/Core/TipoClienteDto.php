<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoClienteDto
{
    use TipoClienteDtoT;

    public function __construct(
		public int $IdTipoCliente = 0,
		public string $Descripcion = ''
    ) {

    }
}