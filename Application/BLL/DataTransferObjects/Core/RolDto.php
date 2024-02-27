<?php
namespace Application\BLL\DataTransferObjects\Core;

class RolDto
{
    use RolDtoT;

    public function __construct(
		public int $IdRol = 0,
		public ?string $Descripcion = null,
		public ?int $Eliminado = null,
		public ?string $Codigo = null,
		public int $IdCategoriaRol = 0
    ) {

    }
}