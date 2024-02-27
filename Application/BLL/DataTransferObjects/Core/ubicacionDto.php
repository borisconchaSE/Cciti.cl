<?php
namespace Application\BLL\DataTransferObjects\Core;

class ubicacionDto
{
    use ubicacionDtoT;

    public function __construct(
		public int $idubicacion = 0,
		public string $Descripcion = ''
    ) {

    }
}