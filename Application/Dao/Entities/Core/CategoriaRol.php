<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class CategoriaRol
{
    use CategoriaRolT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdCategoriaRol = 0;
	public string $Decripcion = '';

    function __construct()
    {
    }
}