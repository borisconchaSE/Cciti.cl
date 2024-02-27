<?php
namespace Application\BLL\DataTransferObjects\Core;

class modeloDto
{
    use modeloDtoT;

    public function __construct(
		public int $idModelo = 0,
		public string $Descripcion = '',
		public ?int $idMarca = null
    ) {

    }
}