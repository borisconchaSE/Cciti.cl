<?php
namespace Application\BLL\DataTransferObjects\Core;

class marcaDto
{
    use marcaDtoT;

    public function __construct(
		public int $idMarca = 0,
		public string $Descripcion = ''
    ) {

    }
}