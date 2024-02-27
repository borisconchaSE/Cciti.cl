<?php
namespace Application\BLL\DataTransferObjects\Core;

class empresaDto
{
    use empresaDtoT;

    public function __construct(
		public int $IdEmpresa = 0,
		public string $Descripcion = ''
    ) {

    }
}