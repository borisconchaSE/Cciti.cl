<?php
namespace Application\BLL\DataTransferObjects\Clientes;

class ClientesDto
{
    use ClientesDtoT;

    public function __construct(
		public int $IdClientes = 0,
		public ?string $NombreCliente = null,
		public ?string $CorreoCliente = null,
		public ?string $TelefonoPrimario = null,
		public ?string $TelefonoSecundario = null,
		public ?string $TelefonoOpcional = null,
		public ?int $IdCiudad = null,
		public string $Rut = '',
		public ?int $Activo = null,
		public ?string $FechaCreacion = null
    ) {

    }
}