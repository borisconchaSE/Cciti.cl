<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class modelo
{
    use modeloT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idModelo = 0;
	public string $Descripcion = '';
	public ?int $idMarca = null;

    function __construct()
    {
    }
}