<?php
namespace Application\BLL\DataTransferObjects\Core;

class estadosactivosDto
{
    use estadosactivosDtoT;

    public function __construct(
		public int $IdEstadoActivo = 0,
		public ?string $DescripcionActivo = null
    ) {

    }
}