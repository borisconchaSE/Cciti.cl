<?php
namespace Application\BLL\DataTransferObjects\Core;

class proveedorDto
{
    use proveedorDtoT;

    public function __construct(
		public int $idProveedor = 0,
		public string $Nombre = '',
		public ?string $Rut = null
    ) {

    }
}