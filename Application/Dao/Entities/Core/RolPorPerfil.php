<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class RolPorPerfil
{
    use RolPorPerfilT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdRolPorPerfil = 0;
	public int $IdRol = 0;
	public int $IdPerfil = 0;

    function __construct()
    {
    }
}