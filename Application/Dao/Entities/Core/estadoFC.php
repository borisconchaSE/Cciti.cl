<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class estadoFC
{
    use estadoFCT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idEstado_FC = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}