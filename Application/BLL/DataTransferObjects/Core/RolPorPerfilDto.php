<?php
namespace Application\BLL\DataTransferObjects\Core;

class RolPorPerfilDto
{
    use RolPorPerfilDtoT;

    public function __construct(
		public int $IdRolPorPerfil = 0,
		public int $IdRol = 0,
		public int $IdPerfil = 0
    ) {

    }
}