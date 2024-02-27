<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class Rol
{
    use RolT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdRol = 0;
	public ?string $Descripcion = null;
	public ?int $Eliminado = null;
	public ?string $Codigo = null;
	public int $IdCategoriaRol = 0;

    function __construct()
    {
    }
}