<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class Perfil
{
    use PerfilT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdPerfil = 0;
	public string $Descripcion = '';
	public int $Eliminado = 0;
	public string $LandingPage = '';
	public int $IndSeleccionable = 0;

    function __construct()
    {
    }
}