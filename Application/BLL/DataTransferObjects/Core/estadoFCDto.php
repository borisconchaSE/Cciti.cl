<?php
namespace Application\BLL\DataTransferObjects\Core;

class estadoFCDto
{
    use estadoFCDtoT;

    public function __construct(
		public int $idEstado_FC = 0,
		public string $Descripcion = ''
    ) {

    }
}