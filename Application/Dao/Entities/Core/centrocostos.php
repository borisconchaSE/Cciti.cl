<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class centrocostos
{
    use centrocostosT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idCentro = 0;
	public string $Descripcion = '';
	public ?int $idubicacion = null;

    function __construct()
    {
    }
}