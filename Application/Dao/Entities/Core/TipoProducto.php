<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class TipoProducto
{
    use TipoProductoT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoProducto = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}