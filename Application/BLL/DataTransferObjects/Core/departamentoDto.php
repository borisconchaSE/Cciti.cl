<?php
namespace Application\BLL\DataTransferObjects\Core;

class departamentoDto
{
    use departamentoDtoT;

    public function __construct(
		public int $idDepto = 0,
		public string $Descripcion = '',
		public ?int $IdEmpresa = null,
		public ?int $idubicacion = null
    ) {

    }
}