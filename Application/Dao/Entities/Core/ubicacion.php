<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class ubicacion
{
    use ubicacionT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idubicacion = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}