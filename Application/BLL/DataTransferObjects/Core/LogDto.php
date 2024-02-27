<?php
namespace Application\BLL\DataTransferObjects\Core;

class LogDto
{
    use LogDtoT;

    public function __construct(
		public int $IdLog = 0,
		public int $IdTipoLog = 0,
		public int $IdUsuario = 0,
		public int $IdUsuarioAfectado = 0,
		public ?string $Descripcion = null,
		public ?string $Fecha = null
    ) {

    }
}