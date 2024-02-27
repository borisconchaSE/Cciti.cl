<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class TipoUsuario
{
    use TipoUsuarioT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoUsuario = 0;
	public ?string $Descripcion = null;
	public ?int $Orden = null;

    function __construct()
    {
    }
}