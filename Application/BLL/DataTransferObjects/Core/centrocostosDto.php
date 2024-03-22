<?php
namespace Application\BLL\DataTransferObjects\Core;

class centrocostosDto
{
    use centrocostosDtoT;

    public function __construct(
		public int $idCentro = 0,
		public string $Descripcion = '',
		public ?int $idubicacion = null,
		public ?int $idDepto = null,
		public ?int $IdEmpresa = null
    ) {

    }
}