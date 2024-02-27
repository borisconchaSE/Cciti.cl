<?php
namespace Application\BLL\DataTransferObjects\Core;

class ClienteDto
{
    use ClienteDtoT;

    public function __construct(
		public int $IdCliente = 0,
		public int $IdTipoCliente = 0,
		public string $RazonSocial = '',
		public string $Rut = '',
		public string $Direccion = '',
		public int $Eliminado = 0,
		public string $UriLogo = '',
		public int $IdTipoProducto = 0
    ) {

    }
}