<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class estadosactivos
{
    use estadosactivosT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdEstadoActivo = 0;
	public ?string $DescripcionActivo = null;

    function __construct()
    {
    }
}