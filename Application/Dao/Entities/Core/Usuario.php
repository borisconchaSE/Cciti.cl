<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class Usuario
{
    use UsuarioT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdUsuario = 0;
	public ?int $IdCliente = null;
	public ?int $IdEmpresa = null;
	public ?string $LoginName = null;
	public string $Nombre = '';
	public ?string $Clave = null;
	public ?int $IdContacto = null;
	public ?string $Genero = null;
	public ?int $Eliminado = null;
	public ?int $IdPerfil = null;
	public int $IdTipoIdioma = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $FechaCreacion = null;
	#[EntityField(DataType: 'datetime')]
	public ?string $FechaUltimaSesion = null;
	public int $IdTipoClave = 0;
	public ?string $Sigla = null;
	public ?string $Cargo = null;
	public ?int $IdTIpoUsuario = null;
	public ?int $IdJefeDirecto = null;

    function __construct()
    {
    }
}