<?php
namespace Application\BLL\DataTransferObjects\Core;

class EmpresaDto
{
    use EmpresaDtoT;

    public function __construct(
		public int $IdEmpresa = 0,
		public string $Descripcion = '',
		public int $IdCliente = 0,
		public string $Logo = '',
		public string $Alias = ''
    ) {

    }
}