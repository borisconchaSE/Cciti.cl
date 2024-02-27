<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class estadoOC
{
    use estadoOCT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idEstado_oc = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}