<?php
namespace Application\BLL\DataTransferObjects\Core;

class estadoOCDto
{
    use estadoOCDtoT;

    public function __construct(
		public int $idEstado_oc = 0,
		public string $Descripcion = ''
    ) {

    }
}