<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoUsuarioDto
{
    use TipoUsuarioDtoT;

    public function __construct(
		public int $IdTipoUsuario = 0,
		public ?string $Descripcion = null,
		public ?int $Orden = null
    ) {

    }
}