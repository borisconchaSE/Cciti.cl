<?php
namespace Application\BLL\DataTransferObjects\Core;

class PerfilDto
{
    use PerfilDtoT;

    public function __construct(
		public int $IdPerfil = 0,
		public string $Descripcion = '',
		public int $Eliminado = 0,
		public string $LandingPage = '',
		public int $IndSeleccionable = 0
    ) {

    }
}