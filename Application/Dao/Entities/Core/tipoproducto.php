<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class tipoproducto
{
    use tipoproductoT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idTipoProducto = 0;
	public string $DescripcionProducto = '';

    function __construct()
    {
    }
}