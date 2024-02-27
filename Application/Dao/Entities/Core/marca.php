<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class marca
{
    use marcaT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idMarca = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}