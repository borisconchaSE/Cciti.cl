<?php
namespace Application\BLL\DataTransferObjects\Core;

class precioUnitarioDto
{
    use precioUnitarioDtoT;

    public function __construct(
		public int $idunitario = 0,
		public string $Descripcion = '',
		public int $Valor_Actual = 0,
		public int $Valor_Anterior = 0
    ) {

    }
}