<?php
namespace Application\BLL\DataTransferObjects\Core;

class UsuarioDto
{
    use UsuarioDtoT;

    public function __construct(
		public int $IdUsuario = 0,
		public ?int $IdCliente = null,
		public ?int $IdEmpresa = null,
		public ?string $LoginName = null,
		public string $Nombre = '',
		public ?string $Clave = null,
		public ?int $IdContacto = null,
		public ?string $Genero = null,
		public ?int $Eliminado = null,
		public ?int $IdPerfil = null,
		public int $IdTipoIdioma = 0,
		public ?string $FechaCreacion = null,
		public ?string $FechaUltimaSesion = null,
		public int $IdTipoClave = 0,
		public ?string $Sigla = null,
		public ?string $Cargo = null,
		public ?int $IdTIpoUsuario = null,
		public ?int $IdJefeDirecto = null
    ) {

    }
}